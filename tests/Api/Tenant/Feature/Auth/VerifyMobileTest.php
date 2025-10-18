<?php

namespace Tests\Api\Tenant\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase\TenantTestCase;
use Carbon\Carbon;

class VerifyMobileTest extends TenantTestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Helper function to get a valid token
     */
    private function getValidToken(): array
    {
        $response = $this->postJson($this->getTestUrl('/api/v1/login'), [
            'email' => 'test.owner@renturo.test',
            'password' => 'password',
        ]);

        $response->assertStatus(201);
        return [
            'token' => $response->json('body.access_token'),
            'verificationCode' => $response->json('body.verification_code'),
        ];
    }

    /**
     * Test mobile verification with valid code succeeds
     */
    public function test_mobile_verification_with_valid_code_succeeds(): void
    {
        // Get token from login
        $auth = $this->getValidToken();

        // Verify mobile with the code from login
        $response = $this->withHeader('Authorization', 'Bearer ' . $auth['token'])
            ->putJson($this->getTestUrl('/api/v1/verify/mobile'), [
                'code' => $auth['verificationCode']
            ]);

        // Assert successful verification
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'success',
                'body' => [
                    'message' => 'Your mobile phone number has been successfully verified.'
                ]
            ]);

        // Assert verification was saved to database
        $this->assertDatabaseHas('mobile_verifications', [
            'code' => $auth['verificationCode'],
        ]);

        // Assert verified_at is not null
        $this->assertDatabaseMissing('mobile_verifications', [
            'code' => $auth['verificationCode'],
            'verified_at' => null,
        ]);
    }

    /**
     * Test mobile verification with invalid code fails
     */
    public function test_mobile_verification_with_invalid_code_fails(): void
    {
        // Get token from login
        $auth = $this->getValidToken();

        // Try to verify with wrong code
        $response = $this->withHeader('Authorization', 'Bearer ' . $auth['token'])
            ->putJson($this->getTestUrl('/api/v1/verify/mobile'), [
                'code' => '0000' // Wrong code
            ]);

        // Assert error response
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'failed',
                'body' => [
                    'message' => 'The code is either not valid or has expired.'
                ]
            ]);
    }

    /**
     * Test mobile verification with expired code fails
     */
    public function test_mobile_verification_with_expired_code_fails(): void
    {
        // Get token from login
        $auth = $this->getValidToken();

        // Manually expire the verification code in database by setting expires_at to the past
        \DB::table('mobile_verifications')
            ->where('code', $auth['verificationCode'])
            ->update(['expires_at' => Carbon::now()->subSeconds(1)]);

        // Try to verify with expired code
        $response = $this->withHeader('Authorization', 'Bearer ' . $auth['token'])
            ->putJson($this->getTestUrl('/api/v1/verify/mobile'), [
                'code' => $auth['verificationCode']
            ]);

        // Assert error response
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'failed',
                'body' => [
                    'message' => 'The code is either not valid or has expired.'
                ]
            ]);
    }

    /**
     * Test mobile verification requires authentication
     */
    public function test_mobile_verification_requires_authentication(): void
    {
        // Try to verify without authentication
        $response = $this->putJson($this->getTestUrl('/api/v1/verify/mobile'), [
            'code' => '1234'
        ]);

        // Assert unauthorized
        $response->assertStatus(401);
    }

    /**
     * Test resend verification code succeeds
     */
    public function test_resend_verification_code_succeeds(): void
    {
        // Get token from login
        $auth = $this->getValidToken();

        // Delete the pre-seeded verified verification and manually expire the login verification
        \DB::table('mobile_verifications')
            ->where('mobile_number', '+639222222222')
            ->update(['expires_at' => Carbon::now()->subSeconds(1)]);

        // Resend verification code
        $response = $this->withHeader('Authorization', 'Bearer ' . $auth['token'])
            ->postJson($this->getTestUrl('/api/v1/resend/mobile/verification'));

        // Assert successful resend
        $response->assertStatus(201)
            ->assertJson([
                'message' => 'success',
                'body' => [
                    'message' => 'The verification code for your mobile has been sent to the number +639222222222.',
                ]
            ]);
    }

    /**
     * Test resend verification code is rate limited
     */
    public function test_resend_verification_code_is_rate_limited(): void
    {
        // Get token from login (this creates a verification code)
        $auth = $this->getValidToken();

        // Try to resend immediately (should be rate limited)
        $response = $this->withHeader('Authorization', 'Bearer ' . $auth['token'])
            ->postJson($this->getTestUrl('/api/v1/resend/mobile/verification'));

        // Assert rate limit error
        $response->assertStatus(429)
            ->assertJson([
                'message' => 'failed',
                'body' => [
                    'message' => 'Too many request for verification code. Please retry after waiting for 300 seconds.'
                ]
            ]);
    }

    /**
     * Test resend verification code requires authentication
     */
    public function test_resend_verification_code_requires_authentication(): void
    {
        // Try to resend without authentication
        $response = $this->postJson($this->getTestUrl('/api/v1/resend/mobile/verification'));

        // Assert unauthorized
        $response->assertStatus(401);
    }

    /**
     * Test cannot verify already verified code
     */
    public function test_cannot_verify_already_verified_code(): void
    {
        // Get token and verify mobile
        $auth = $this->getValidToken();
        
        // First verification
        $response = $this->withHeader('Authorization', 'Bearer ' . $auth['token'])
            ->putJson($this->getTestUrl('/api/v1/verify/mobile'), [
                'code' => $auth['verificationCode']
            ]);
        $response->assertStatus(200);

        // Try to verify the same code again
        $response = $this->withHeader('Authorization', 'Bearer ' . $auth['token'])
            ->putJson($this->getTestUrl('/api/v1/verify/mobile'), [
                'code' => $auth['verificationCode']
            ]);

        // Assert error (code already used)
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'failed',
                'body' => [
                    'message' => 'The code is either not valid or has expired.'
                ]
            ]);
    }

    /**
     * Test verified user can access protected endpoints
     */
    public function test_verified_user_can_access_protected_endpoints(): void
    {
        // Get token and verify mobile
        $auth = $this->getValidToken();
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $auth['token'])
            ->putJson($this->getTestUrl('/api/v1/verify/mobile'), [
                'code' => $auth['verificationCode']
            ]);
        $response->assertStatus(200);

        // Try to access protected endpoint
        $response = $this->withHeader('Authorization', 'Bearer ' . $auth['token'])
            ->getJson($this->getTestUrl('/api/v1/user'));

        // Assert successful access
        $response->assertStatus(200)
            ->assertJson([
                'email' => 'test.owner@renturo.test',
            ]);
    }

    /**
     * Test unverified user cannot access protected endpoints
     */
    public function test_unverified_user_cannot_access_protected_endpoints(): void
    {
        // Login with admin user who has unverified mobile in seeder
        $response = $this->postJson($this->getTestUrl('/api/v1/login'), [
            'email' => 'test.admin@renturo.test',
            'password' => 'password',
        ]);
        $response->assertStatus(201);
        $token = $response->json('body.access_token');

        // Try to access protected endpoint without verification
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($this->getTestUrl('/api/v1/user'));

        // Assert forbidden
        $response->assertStatus(403)
            ->assertJson([
                'message' => 'failed',
                'body' => [
                    'message' => 'The mobile number has not been verified yet. Please check your mobile for the verification code.'
                ]
            ]);
    }
}

