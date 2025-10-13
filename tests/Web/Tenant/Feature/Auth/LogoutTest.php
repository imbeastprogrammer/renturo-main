<?php

namespace Tests\Web\Tenant\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase\TenantTestCase;
use Illuminate\Support\Facades\Mail;

class LogoutTest extends TenantTestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test user can logout
     */
    public function test_user_can_logout(): void
    {
        Mail::fake();

        // Login first
        $this->post('http://main.renturo.test/login', [
            'email' => 'test.owner@renturo.test',
            'password' => 'password',
        ]);

        // Assert user is authenticated
        $this->assertAuthenticated('web');

        // Logout
        $response = $this->post('http://main.renturo.test/logout');

        // Assert redirect to login
        $response->assertRedirect('/login');

        // Assert user is no longer authenticated
        $this->assertGuest('web');
    }

    /**
     * Test logout invalidates session
     */
    public function test_logout_invalidates_session(): void
    {
        Mail::fake();

        // Login first
        $this->post('http://main.renturo.test/login', [
            'email' => 'test.owner@renturo.test',
            'password' => 'password',
        ]);

        // Store session ID
        $sessionId = session()->getId();

        // Logout
        $this->post('http://main.renturo.test/logout');

        // Assert session was invalidated (new session ID)
        $this->assertNotEquals($sessionId, session()->getId());
    }

    /**
     * Test logout regenerates CSRF token
     */
    public function test_logout_regenerates_csrf_token(): void
    {
        Mail::fake();

        // Login first
        $this->post('http://main.renturo.test/login', [
            'email' => 'test.owner@renturo.test',
            'password' => 'password',
        ]);

        // Get CSRF token before logout
        $tokenBefore = csrf_token();

        // Logout
        $this->post('http://main.renturo.test/logout');

        // Get CSRF token after logout
        $tokenAfter = csrf_token();

        // Assert token was regenerated
        $this->assertNotEquals($tokenBefore, $tokenAfter);
    }

    /**
     * Test unauthenticated user cannot logout
     */
    public function test_unauthenticated_user_cannot_logout(): void
    {
        // Try to logout without being logged in
        $response = $this->post('http://main.renturo.test/logout');

        // Assert redirect (middleware should catch this)
        $this->assertTrue($response->isRedirect());
    }
}

