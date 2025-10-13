<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use App\Models\Central\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

abstract class TenantTestCase extends BaseTestCase
{
    use CreatesApplication;

    protected ?TenantWithDatabase $tenant = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Create and initialize test tenant
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
        Config::set('app.url', 'http://test.renturo.test');
        Config::set('app.asset_url', 'http://test.renturo.test');

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
