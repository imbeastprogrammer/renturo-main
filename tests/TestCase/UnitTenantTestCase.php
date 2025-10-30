<?php

namespace Tests\TestCase;

use Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use App\Models\Central\Tenant;

abstract class UnitTenantTestCase extends TestCase
{
    protected Tenant $tenant;
    protected string $testDomain;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->initializeTenant();
    }

    protected function initializeTenant(): void
    {
        // Create a test tenant
        $this->tenant = Tenant::create([
            'id' => Str::lower(Str::random(6)),
            'company' => Faker::create()->company,
            'status' => 'active',
            'plan_type' => 'demo',
            // Let the tenant ID determine the database name (tenant_[id])
        ]);

        // Create domain for test tenant
        $centralDomain = config('tenancy.central_domains')[2] ?? 'renturo.test';
        $this->tenant->domains()->create([
            'domain' => 'main.' . $centralDomain
        ]);

        // Initialize tenancy
        tenancy()->initialize($this->tenant);

        // Set test domain
        $this->testDomain = 'http://main.' . $centralDomain;
        Config::set('app.url', $this->testDomain);
        Config::set('app.asset_url', $this->testDomain);

        // Set shorter token TTL for testing
        Config::set('passport.token_ttl', 5); // 5 minutes

        // Install Passport keys and clients for the test tenant
        $this->tenant->run(function () {
            // Generate encryption keys
            $this->artisan('passport:keys', ['--force' => true]);

            // Create personal access client
            $this->artisan('passport:client', [
                '--personal' => true,
                '--name' => 'Test Personal Access Client',
                '--no-interaction' => true
            ]);
        });

        // NOTE: No seeder called for Unit Tests - we create our own test data
    }

    /**
     * Get the full URL for the given path
     */
    protected function getTestUrl(string $path = ''): string
    {
        $path = ltrim($path, '/');
        return $this->testDomain . ($path ? '/' . $path : '');
    }

    protected function tearDown(): void
    {
        // Clean up tenant if created
        if ($this->tenant) {
            tenancy()->end();
            $this->tenant->forceDelete();
        }

        parent::tearDown();
    }
}


