<?php

namespace Tests\Api\Tenant\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase\TenantTestCase;
use Carbon\Carbon;

class LogoutTest extends TenantTestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Helper function to get a valid token and verification code
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
     * Helper function to verify mobile number
     */
    private function verifyMobileNumber(string $token, string $verificationCode): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson($this->getTestUrl('/api/v1/verify/mobile'), [
                'code' => $verificationCode
            ]);
        $response->assertStatus(200);
    }

    /**
     * Test API logout revokes access token
     */
    public function test_api_logout_revokes_access_token(): void
    {
        // Get token and verify mobile
        $auth = $this->getValidToken();
        $this->verifyMobileNumber($auth['token'], $auth['verificationCode']);

        // Perform logout with token
        $response = $this->withHeader('Authorization', 'Bearer ' . $auth['token'])
            ->deleteJson($this->getTestUrl('/api/v1/logout'));

        // Assert successful logout
        $response->assertStatus(204); // No content

        // Try to use the token again - should fail
        $response = $this->withHeader('Authorization', 'Bearer ' . $auth['token'])
            ->getJson($this->getTestUrl('/api/v1/user'));
        $response->assertStatus(401);
    }

    /**
     * Test API logout requires authentication
     */
    public function test_api_logout_requires_authentication(): void
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
        ];

        foreach ($testCases as $case) {
            // Attempt logout with invalid token
            $headers = $case['token'] ? ['Authorization' => $case['token']] : [];
            $response = $this->withHeaders($headers)
                ->deleteJson($this->getTestUrl('/api/v1/logout'));

            // Assert unauthorized
            $response->assertStatus($case['expected_status'], $case['description']);
        }
    }

    /**
     * Test API logout requires verified mobile number
     */
    public function test_api_logout_requires_verified_mobile(): void
    {
        // Login with admin user who has unverified mobile in seeder
        $response = $this->postJson($this->getTestUrl('/api/v1/login'), [
            'email' => 'test.admin@renturo.test',
            'password' => 'password',
        ]);
        $response->assertStatus(201);
        $token = $response->json('body.access_token');

        // Attempt logout without verifying mobile
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson($this->getTestUrl('/api/v1/logout'));

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
     * Test API logout with expired token
     */
    public function test_api_logout_with_expired_token(): void
    {
        // Get token and verify mobile
        $auth = $this->getValidToken();
        $this->verifyMobileNumber($auth['token'], $auth['verificationCode']);

        // Fast forward time to expire the token (tokens typically expire in 1 hour)
        $this->travel(6)->minutes();

        // Attempt logout with expired token
        $response = $this->withHeader('Authorization', 'Bearer ' . $auth['token'])
            ->deleteJson($this->getTestUrl('/api/v1/logout'));

        // Assert unauthorized
        $response->assertStatus(401);
    }
}
