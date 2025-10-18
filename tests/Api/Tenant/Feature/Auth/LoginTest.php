<?php

namespace Tests\Api\Tenant\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase\TenantTestCase;

class LoginTest extends TenantTestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test API login with valid credentials returns access token
     */
    public function test_api_login_with_valid_credentials_returns_access_token(): void
    {
        // Use seeded owner account
        $response = $this->postJson($this->getTestUrl('/api/v1/login'), [
            'email' => 'test.owner@renturo.test',
            'password' => 'password',
        ]);

        // Assert successful response
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'body' => [
                    'message',
                    'user' => [
                        'id',
                        'email',
                        'role',
                    ],
                    'verification_code',
                    'access_token',
                ],
            ]);

        $this->assertEquals('success', $response->json('message'));
        $this->assertNotNull($response->json('body.access_token'));
    }

    /**
     * Test API login with invalid password returns error
     */
    public function test_api_login_with_invalid_password_returns_error(): void
    {
        // Attempt login with wrong password
        $response = $this->postJson($this->getTestUrl('/api/v1/login'), [
            'email' => 'test.owner@renturo.test',
            'password' => 'wrong-password',
        ]);

        // Assert error response
        $response->assertStatus(422);
        $this->assertArrayHasKey('errors', $response->json());
    }

    /**
     * Test API login with non-existent email returns error
     */
    public function test_api_login_with_non_existent_email_returns_error(): void
    {
        // Attempt login with non-existent email
        $response = $this->postJson($this->getTestUrl('/api/v1/login'), [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        // Assert error response
        $response->assertStatus(422);
    }

    /**
     * Test API login with email requires password
     */
    public function test_api_login_requires_password(): void
    {
        $response = $this->postJson($this->getTestUrl('/api/v1/login'), [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test API login with password requires email
     */
    public function test_api_login_requires_email(): void
    {
        $response = $this->postJson($this->getTestUrl('/api/v1/login'), [
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test API login with username (instead of email) works
     */
    public function test_api_login_with_username_works(): void
    {
        // Attempt login with username (seeded user)
        $response = $this->postJson($this->getTestUrl('/api/v1/login'), [
            'email' => 'testowner', // Backend accepts username in email field
            'password' => 'password',
        ]);

        // Assert successful response
        $response->assertStatus(201);
        $this->assertEquals('success', $response->json('message'));
    }

    /**
     * Test API login with phone number works
     */
    public function test_api_login_with_phone_number_works(): void
    {
        // Attempt login with phone number (seeded user)
        $response = $this->postJson($this->getTestUrl('/api/v1/login'), [
            'email' => '+639222222222', // Backend accepts phone in email field
            'password' => 'password',
        ]);

        // Assert successful response
        $response->assertStatus(201);
        $this->assertEquals('success', $response->json('message'));
    }

    /**
     * Test API login creates OTP verification record
     */
    public function test_api_login_creates_otp_verification(): void
    {
        // Login with seeded user
        $response = $this->postJson($this->getTestUrl('/api/v1/login'), [
            'email' => 'test.owner@renturo.test',
            'password' => 'password',
        ]);

        $response->assertStatus(201);

        // Assert OTP verification was created
        $this->assertDatabaseHas('mobile_verifications', [
            'mobile_number' => '+639222222222',
        ]);

        // Assert verification code is returned (temporary)
        $this->assertNotNull($response->json('body.verification_code'));
        $this->assertEquals(4, strlen($response->json('body.verification_code')));
    }

    /**
     * Test API login works for all user roles
     */
    public function test_api_login_works_for_all_user_roles(): void
    {
        $users = [
            ['email' => 'test.admin@renturo.test', 'role' => User::ROLE_ADMIN],
            ['email' => 'test.owner@renturo.test', 'role' => User::ROLE_OWNER],
            ['email' => 'test.user@renturo.test', 'role' => User::ROLE_USER],
            ['email' => 'test.partner@renturo.test', 'role' => User::ROLE_PARTNER],
        ];

        foreach ($users as $userData) {
            $response = $this->postJson($this->getTestUrl('/api/v1/login'), [
                'email' => $userData['email'],
                'password' => 'password',
            ]);

            $response->assertStatus(201);
            $this->assertEquals($userData['role'], $response->json('body.user.role'));
        }
    }

    /**
     * Test API login response includes correct user information
     */
    public function test_api_login_response_includes_correct_user_information(): void
    {
        // Login with seeded owner
        $response = $this->postJson($this->getTestUrl('/api/v1/login'), [
            'email' => 'test.owner@renturo.test',
            'password' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'body' => [
                    'user' => [
                        'first_name' => 'Test',
                        'last_name' => 'Owner',
                        'email' => 'test.owner@renturo.test',
                        'username' => 'testowner',
                        'role' => User::ROLE_OWNER,
                    ],
                ],
            ]);
    }
}
