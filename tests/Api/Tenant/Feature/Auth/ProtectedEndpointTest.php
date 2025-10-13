<?php

namespace Tests\Api\Tenant\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase\TenantTestCase;

class ProtectedEndpointTest extends TenantTestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Helper function to get a valid token and verification code
     */
    private function getValidToken(): array
    {
        $response = $this->postJson('http://main.renturo.test/api/v1/login', [
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
     * Helper function to verify mobile number
     */
    private function verifyMobileNumber(string $token, string $verificationCode): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('http://main.renturo.test/api/v1/verify/mobile', [
                'code' => $verificationCode
            ]);
        $response->assertStatus(200);
    }

    /**
     * Test protected endpoint requires authentication
     */
    public function test_protected_endpoint_requires_authentication(): void
    {
        // Test cases for invalid authentication
        $testCases = [
            'no_token' => [
                'token' => '',
                'expected_status' => 401,
                'description' => 'No token provided'
            ],
            'malformed_token' => [
                'token' => 'not-a-valid-token',
                'expected_status' => 401,
                'description' => 'Malformed token'
            ],
            'bearer_prefix_only' => [
                'token' => 'Bearer ',
                'expected_status' => 401,
                'description' => 'Bearer prefix without token'
            ],
            'invalid_token_format' => [
                'token' => 'Bearer abc123',
                'expected_status' => 401,
                'description' => 'Invalid token format'
            ],
        ];

        foreach ($testCases as $case) {
            // Attempt to access protected endpoint with invalid token
            $headers = $case['token'] ? ['Authorization' => $case['token']] : [];
            $response = $this->withHeaders($headers)
                ->getJson('http://main.renturo.test/api/v1/user');

            // Assert unauthorized
            $response->assertStatus($case['expected_status'], $case['description']);
        }
    }

    /**
     * Test protected endpoint with valid token returns user data
     */
    public function test_protected_endpoint_with_valid_token_returns_user_data(): void
    {
        // Get token and verify mobile
        $auth = $this->getValidToken();
        $this->verifyMobileNumber($auth['token'], $auth['verificationCode']);

        // Access protected endpoint with token
        $response = $this->withHeader('Authorization', 'Bearer ' . $auth['token'])
            ->getJson('http://main.renturo.test/api/v1/user');

        // Assert successful response with user data
        $response->assertStatus(200)
            ->assertJson([
                'email' => 'test.owner@renturo.test',
                'role' => User::ROLE_OWNER,
            ])
            ->assertJsonStructure([
                'id',
                'first_name',
                'last_name',
                'email',
                'username',
                'mobile_number',
                'role',
                'email_verified_at',
                'mobile_verified_at',
                'created_at',
                'updated_at'
            ]);
    }

    /**
     * Test protected endpoint requires verified mobile number
     */
    public function test_protected_endpoint_requires_verified_mobile(): void
    {
        // Get token without verifying mobile
        $auth = $this->getValidToken();

        // Attempt to access protected endpoint without verifying mobile
        $response = $this->withHeader('Authorization', 'Bearer ' . $auth['token'])
            ->getJson('http://main.renturo.test/api/v1/user');

        // Assert forbidden (unverified mobile)
        $response->assertStatus(403)
            ->assertJson([
                'message' => 'failed',
                'body' => [
                    'message' => 'The mobile number has not been verified yet. Please check your mobile for the verification code.'
                ]
            ]);
    }

    /**
     * Test protected endpoint with expired token
     */
    public function test_protected_endpoint_with_expired_token(): void
    {
        // Get token and verify mobile
        $auth = $this->getValidToken();
        $this->verifyMobileNumber($auth['token'], $auth['verificationCode']);

        // Fast forward time to expire the token (tokens typically expire in 1 hour)
        $this->travel(2)->hours();

        // Attempt to access protected endpoint with expired token
        $response = $this->withHeader('Authorization', 'Bearer ' . $auth['token'])
            ->getJson('http://main.renturo.test/api/v1/user');

        // Assert unauthorized
        $response->assertStatus(401);
    }

    /**
     * Test protected endpoint with revoked token
     */
    public function test_protected_endpoint_with_revoked_token(): void
    {
        // Get token and verify mobile
        $auth = $this->getValidToken();
        $this->verifyMobileNumber($auth['token'], $auth['verificationCode']);

        // Logout to revoke the token
        $response = $this->withHeader('Authorization', 'Bearer ' . $auth['token'])
            ->deleteJson('http://main.renturo.test/api/v1/logout');
        $response->assertStatus(204);

        // Attempt to access protected endpoint with revoked token
        $response = $this->withHeader('Authorization', 'Bearer ' . $auth['token'])
            ->getJson('http://main.renturo.test/api/v1/user');

        // Assert unauthorized
        $response->assertStatus(401);
    }

    /**
     * Test protected endpoint with token from different tenant
     */
    public function test_protected_endpoint_with_different_tenant_token(): void
    {
        // Get token and verify mobile from current tenant
        $auth = $this->getValidToken();
        $this->verifyMobileNumber($auth['token'], $auth['verificationCode']);

        // Create a new tenant
        $this->tearDown();
        $this->setUp();

        // Attempt to use the token from the previous tenant
        $response = $this->withHeader('Authorization', 'Bearer ' . $auth['token'])
            ->getJson('http://main.renturo.test/api/v1/user');

        // Assert unauthorized
        $response->assertStatus(401);
    }
}
