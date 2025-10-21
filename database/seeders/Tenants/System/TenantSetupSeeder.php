<?php

namespace Database\Seeders\Tenants\System;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Central\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Artisan;

class TenantSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->command->info('Starting to seed TenantSeeder data...');

        $tenantId = Str::lower(Str::random(6));

        $tenant = Tenant::create([
            'id' => $tenantId,
            'company' => fake()->company(),
            'status' => Tenant::ACTIVE_STATUS,
            'plan_type' => Tenant::PLAN_TYPES[0]
        ]);

        $tenant->domains()->create([
            'domain' => 'main.' . config('tenancy.central_domains')[2]
        ]);

        $tenant->run(function () use ($tenant) {
            $admin = User::factory()->create([
                'username' => 'beastadmin1234',
                'mobile_number' => fake()->phoneNumber(),
                'email' => 'admin@main.renturo.test',
                'role' => User::ROLE_ADMIN
            ]);

            $admin->mobileVerification()->create([
                'mobile_number' => fake()->phoneNumber(),
                'code' => rand(1000, 9999),
                'verified_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addSeconds(300),
            ]);

            $owner = User::factory()->create([
                'username' => 'beastowner1234',
                'mobile_number' => fake()->phoneNumber(),
                'email' => 'owner@main.renturo.test',
                'role' => User::ROLE_OWNER
            ]);

            $owner->mobileVerification()->create([
                'mobile_number' => fake()->phoneNumber(),
                'code' => rand(1000, 9999),
                'verified_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addSeconds(300)
            ]);

            $user = User::factory()->create([
                'username' => 'beastuser1234',
                'mobile_number' => fake()->phoneNumber(),
                'email' => 'user@main.renturo.test',
                'role' => User::ROLE_USER
            ]);

            $user->mobileVerification()->create([
                'mobile_number' => fake()->phoneNumber(),
                'code' => rand(1000, 9999),
                'verified_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addSeconds(300)
            ]);

            $user = User::factory()->create([
                'username' => 'beastpartner1234',
                'mobile_number' => fake()->phoneNumber(),
                'email' => 'ads-partner@main.renturo.test',
                'role' => USER::ROLE_PARTNER
            ]);

            $user->mobileVerification()->create([
                'mobile_number' => fake()->phoneNumber(),
                'code' => rand(1000, 9999),
                'verified_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addSeconds(300)
            ]);

            //Passport install
            Artisan::call("tenants:run passport:client --option='personal=personal' --option='name={$tenant->id}' --tenants={$tenant->id}");
            $output = Artisan::output();
            $this->command->info("Passport Command TenantSeeder Output: " . $output);

            // Seed categories and subcategories
            $this->call([
                \Database\Seeders\Tenants\Admin\CategorySeeder::class
            ]);

            $this->command->info('Seeding completed for TenantSeeder data.');
        });
    }
}
