<?php

namespace Tests\Web\Tenant\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase\TenantTestCase;
use Illuminate\Support\Facades\Mail;

class LoginTest extends TenantTestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test login page is displayed
     */
    public function test_login_page_is_displayed(): void
    {
        $response = $this->get('http://main.renturo.test/login');

        $response->assertStatus(200);
    }

    /**
     * Test web login with valid credentials succeeds
     */
    public function test_web_login_with_valid_credentials_succeeds(): void
    {
        Mail::fake();

        // Login with owner user
        $response = $this->post('http://main.renturo.test/login', [
            'email' => 'test.owner@renturo.test',
            'password' => 'password',
        ]);

        // Assert redirect to appropriate home
        $response->assertRedirect(RouteServiceProvider::CLIENT_HOME);

        // Assert user is authenticated
        $this->assertAuthenticated('web');
    }

    /**
     * Test web login with invalid password fails
     */
    public function test_web_login_with_invalid_password_fails(): void
    {
        $response = $this->post('http://main.renturo.test/login', [
            'email' => 'test.owner@renturo.test',
            'password' => 'wrong-password',
        ]);

        // Assert validation error
        $response->assertSessionHasErrors(['email']);

        // Assert user is not authenticated
        $this->assertGuest('web');
    }

    /**
     * Test web login with non-existent email fails
     */
    public function test_web_login_with_non_existent_email_fails(): void
    {
        $response = $this->post('http://main.renturo.test/login', [
            'email' => 'nonexistent@renturo.test',
            'password' => 'password',
        ]);

        // Assert validation error
        $response->assertSessionHasErrors(['email']);

        // Assert user is not authenticated
        $this->assertGuest('web');
    }

    /**
     * Test web login requires email
     */
    public function test_web_login_requires_email(): void
    {
        $response = $this->post('http://main.renturo.test/login', [
            'password' => 'password',
        ]);

        // Assert validation error
        $response->assertSessionHasErrors(['email']);

        // Assert user is not authenticated
        $this->assertGuest('web');
    }

    /**
     * Test web login requires password
     */
    public function test_web_login_requires_password(): void
    {
        $response = $this->post('http://main.renturo.test/login', [
            'email' => 'test.owner@renturo.test',
        ]);

        // Assert validation error
        $response->assertSessionHasErrors(['password']);

        // Assert user is not authenticated
        $this->assertGuest('web');
    }

    /**
     * Test web login creates OTP verification
     */
    public function test_web_login_creates_otp_verification(): void
    {
        Mail::fake();

        // Login with owner user
        $response = $this->post('http://main.renturo.test/login', [
            'email' => 'test.owner@renturo.test',
            'password' => 'password',
        ]);

        // Assert successful login
        $response->assertRedirect(RouteServiceProvider::CLIENT_HOME);

        // Assert OTP verification was created
        $this->assertDatabaseHas('mobile_verifications', [
            'mobile_number' => '+639222222222',
        ]);

        // Assert email was sent
        Mail::assertSent(\App\Mail\Tenants\Auth\SendMobileVerificationCode::class);
    }

    /**
     * Test web login redirects admin to admin home
     */
    public function test_web_login_redirects_admin_to_admin_home(): void
    {
        Mail::fake();

        // Login with admin user
        $response = $this->post('http://main.renturo.test/login', [
            'email' => 'test.admin@renturo.test',
            'password' => 'password',
        ]);

        // Assert redirect to admin home
        $response->assertRedirect(RouteServiceProvider::ADMIN_HOME);

        // Assert user is authenticated
        $this->assertAuthenticatedAs(User::where('email', 'test.admin@renturo.test')->first(), 'web');
    }

    /**
     * Test web login redirects owner to client home
     */
    public function test_web_login_redirects_owner_to_client_home(): void
    {
        Mail::fake();

        // Login with owner user
        $response = $this->post('http://main.renturo.test/login', [
            'email' => 'test.owner@renturo.test',
            'password' => 'password',
        ]);

        // Assert redirect to client home
        $response->assertRedirect(RouteServiceProvider::CLIENT_HOME);

        // Assert user is authenticated
        $this->assertAuthenticatedAs(User::where('email', 'test.owner@renturo.test')->first(), 'web');
    }

    /**
     * Test web login redirects user to user home
     */
    public function test_web_login_redirects_user_to_user_home(): void
    {
        Mail::fake();

        // Login with regular user
        $response = $this->post('http://main.renturo.test/login', [
            'email' => 'test.user@renturo.test',
            'password' => 'password',
        ]);

        // Assert redirect to user home
        $response->assertRedirect(RouteServiceProvider::USER_HOME);

        // Assert user is authenticated
        $this->assertAuthenticatedAs(User::where('email', 'test.user@renturo.test')->first(), 'web');
    }

    /**
     * Test web login is rate limited after 5 failed attempts
     */
    public function test_web_login_is_rate_limited(): void
    {
        // Attempt login 5 times with wrong password
        for ($i = 0; $i < 5; $i++) {
            $this->post('http://main.renturo.test/login', [
                'email' => 'test.owner@renturo.test',
                'password' => 'wrong-password',
            ]);
        }

        // 6th attempt should be rate limited
        $response = $this->post('http://main.renturo.test/login', [
            'email' => 'test.owner@renturo.test',
            'password' => 'wrong-password',
        ]);

        // Assert rate limit error
        $response->assertSessionHasErrors(['email']);
        
        // Check that the error message contains throttle information
        $errors = session('errors');
        $this->assertNotNull($errors);
        $emailErrors = $errors->get('email');
        $this->assertNotEmpty($emailErrors);
        $this->assertStringContainsString('Too many login attempts', $emailErrors[0]);
    }

    /**
     * Test web login with remember me
     */
    public function test_web_login_with_remember_me(): void
    {
        Mail::fake();

        // Login with remember me
        $response = $this->post('http://main.renturo.test/login', [
            'email' => 'test.owner@renturo.test',
            'password' => 'password',
            'remember' => true,
        ]);

        // Assert successful login
        $response->assertRedirect(RouteServiceProvider::CLIENT_HOME);

        // Assert user is authenticated
        $this->assertAuthenticated('web');

        // Assert remember token was set
        $user = User::where('email', 'test.owner@renturo.test')->first();
        $this->assertNotNull($user->remember_token);
    }

    /**
     * Test web login email validation requires valid email format
     */
    public function test_web_login_requires_valid_email_format(): void
    {
        $response = $this->post('http://main.renturo.test/login', [
            'email' => 'not-an-email',
            'password' => 'password',
        ]);

        // Assert validation error
        $response->assertSessionHasErrors(['email']);

        // Assert user is not authenticated
        $this->assertGuest('web');
    }
}

