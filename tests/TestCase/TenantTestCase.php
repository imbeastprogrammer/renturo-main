<?php

namespace Tests\TestCase;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use App\Models\Central\Tenant;

abstract class TenantTestCase extends TestCase
{
    protected Tenant $tenant;

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
        $this->tenant->domains()->create([
            'domain' => 'main.' . config('tenancy.central_domains')[2]
        ]);

        // Initialize tenancy
        tenancy()->initialize($this->tenant);

        // Set test domain
        Config::set('app.url', 'http://main.renturo.test');
        Config::set('app.asset_url', 'http://main.renturo.test');

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

        // Run test seeder
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\TestDatabaseSeeder']);
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
