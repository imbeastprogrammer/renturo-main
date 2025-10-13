<?php

namespace Tests\Web\Tenant\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase\TenantTestCase;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class VerifyMobileTest extends TenantTestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Helper function to login and get verification code
     */
    private function loginAndGetCode(): array
    {
        Mail::fake();

        // Login to get verification code
        $this->post('http://main.renturo.test/login', [
            'email' => 'test.owner@renturo.test',
            'password' => 'password',
        ]);

        // Get the verification code from database
        $verification = \DB::table('mobile_verifications')
            ->where('mobile_number', '+639222222222')
            ->whereNull('verified_at')
            ->orderBy('id', 'desc')
            ->first();

        return [
            'code' => $verification->code,
        ];
    }

    /**
     * Test OTP verification page is displayed after login
     */
    public function test_otp_verification_page_is_displayed_after_login(): void
    {
        $this->loginAndGetCode();

        $response = $this->get('http://main.renturo.test/login/otp');

        $response->assertStatus(200);
    }

    /**
     * Test mobile verification with valid code succeeds
     */
    public function test_mobile_verification_with_valid_code_succeeds(): void
    {
        $auth = $this->loginAndGetCode();

        // Verify mobile with the code
        $response = $this->put('http://main.renturo.test/verify/mobile', [
            'code' => $auth['code']
        ]);

        // Assert redirect to appropriate home
        $response->assertRedirect(RouteServiceProvider::CLIENT_HOME);

        // Assert verification was saved to database
        $this->assertDatabaseHas('mobile_verifications', [
            'code' => $auth['code'],
        ]);

        // Assert verified_at is not null
        $this->assertDatabaseMissing('mobile_verifications', [
            'code' => $auth['code'],
            'verified_at' => null,
        ]);
    }

    /**
     * Test mobile verification with invalid code fails
     */
    public function test_mobile_verification_with_invalid_code_fails(): void
    {
        $this->loginAndGetCode();

        // Try to verify with wrong code
        $response = $this->put('http://main.renturo.test/verify/mobile', [
            'code' => '0000' // Wrong code
        ]);

        // Assert error in session
        $response->assertSessionHasErrors(['otp']);
    }

    /**
     * Test mobile verification with expired code fails
     */
    public function test_mobile_verification_with_expired_code_fails(): void
    {
        $auth = $this->loginAndGetCode();

        // Manually expire the verification code in database
        \DB::table('mobile_verifications')
            ->where('code', $auth['code'])
            ->update(['expires_at' => Carbon::now()->subSeconds(1)]);

        // Try to verify with expired code
        $response = $this->put('http://main.renturo.test/verify/mobile', [
            'code' => $auth['code']
        ]);

        // Assert error in session
        $response->assertSessionHasErrors(['otp']);
    }

    /**
     * Test mobile verification requires OTP
     */
    public function test_mobile_verification_requires_otp(): void
    {
        $this->loginAndGetCode();

        // Try to verify without code
        $response = $this->put('http://main.renturo.test/verify/mobile', []);

        // Assert validation error
        $response->assertSessionHasErrors(['code']);
    }

    /**
     * Test resend verification code succeeds
     */
    public function test_resend_verification_code_succeeds(): void
    {
        Mail::fake();

        $auth = $this->loginAndGetCode();

        // Manually expire ALL verifications for this mobile number
        \DB::table('mobile_verifications')
            ->where('mobile_number', '+639222222222')
            ->update(['expires_at' => Carbon::now()->subSeconds(1)]);

        // Resend verification code
        $response = $this->post('http://main.renturo.test/resend/mobile/verification');

        // Assert success message in session
        $response->assertSessionHas('success');

        // Assert new verification code was created
        $newVerification = \DB::table('mobile_verifications')
            ->where('mobile_number', '+639222222222')
            ->whereNull('verified_at')
            ->where('expires_at', '>', Carbon::now())
            ->orderBy('id', 'desc')
            ->first();

        $this->assertNotNull($newVerification);
        $this->assertNotEquals($auth['code'], $newVerification->code);
    }

    /**
     * Test resend verification code is rate limited
     */
    public function test_resend_verification_code_is_rate_limited(): void
    {
        Mail::fake();

        $this->loginAndGetCode();

        // Try to resend immediately (should be rate limited)
        $response = $this->post('http://main.renturo.test/resend/mobile/verification');

        // Assert error in session
        $response->assertSessionHasErrors(['otp']);
    }

    /**
     * Test cannot verify already verified code
     */
    public function test_cannot_verify_already_verified_code(): void
    {
        $auth = $this->loginAndGetCode();

        // First verification
        $response = $this->put('http://main.renturo.test/verify/mobile', [
            'code' => $auth['code']
        ]);
        $response->assertRedirect(RouteServiceProvider::CLIENT_HOME);

        // Try to verify the same code again (simulate logout and login again)
        $this->post('http://main.renturo.test/logout');
        
        // Login again
        Mail::fake();
        $this->post('http://main.renturo.test/login', [
            'email' => 'test.owner@renturo.test',
            'password' => 'password',
        ]);

        // Try to use old verified code
        $response = $this->put('http://main.renturo.test/verify/mobile', [
            'code' => $auth['code']
        ]);

        // Assert error (code already used)
        $response->assertSessionHasErrors(['otp']);
    }

    /**
     * Test verification redirects admin to admin home
     */
    public function test_verification_redirects_admin_to_admin_home(): void
    {
        Mail::fake();

        // Login with admin user
        $this->post('http://main.renturo.test/login', [
            'email' => 'test.admin@renturo.test',
            'password' => 'password',
        ]);

        // Get verification code
        $verification = \DB::table('mobile_verifications')
            ->where('mobile_number', '+639111111111')
            ->whereNull('verified_at')
            ->orderBy('id', 'desc')
            ->first();

        // Verify mobile
        $response = $this->put('http://main.renturo.test/verify/mobile', [
            'code' => $verification->code
        ]);

        // Assert redirect to admin home
        $response->assertRedirect(RouteServiceProvider::ADMIN_HOME);
    }

    /**
     * Test verification redirects user to user home
     */
    public function test_verification_redirects_user_to_user_home(): void
    {
        Mail::fake();

        // Login with regular user
        $this->post('http://main.renturo.test/login', [
            'email' => 'test.user@renturo.test',
            'password' => 'password',
        ]);

        // Get verification code
        $verification = \DB::table('mobile_verifications')
            ->where('mobile_number', '+639333333333')
            ->whereNull('verified_at')
            ->orderBy('id', 'desc')
            ->first();

        // Verify mobile
        $response = $this->put('http://main.renturo.test/verify/mobile', [
            'code' => $verification->code
        ]);

        // Assert redirect to user home
        $response->assertRedirect(RouteServiceProvider::USER_HOME);
    }
}

