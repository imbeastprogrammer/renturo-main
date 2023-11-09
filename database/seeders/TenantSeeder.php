<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Central\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Artisan;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tenantId = Str::lower(Str::random(6));

        $tenant = Tenant::create([
            'id' => $tenantId,
            'name' => fake()->company(),
            'status' => Tenant::ACTIVE_STATUS,
            'plan_type' => Tenant::PLAN_TYPES[0]
        ]);

        $tenant->domains()->create([
            'domain' => 'main.' . config('tenancy.central_domains')[2]
        ]);

        $tenant->run(function () use ($tenant) {
            $admin = User::factory()->create([
                'email' => 'admin@main.renturo.test',
                'role' => User::ROLE_ADMIN
            ]);

            $admin->mobileVerification()->create([
                'mobile_no' => fake()->phoneNumber(),
                'code' => rand(1000, 9999),
                'verified_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addSeconds(300),
            ]);

            $owner = User::factory()->create([
                'email' => 'owner@main.renturo.test',
                'role' => User::ROLE_OWNER
            ]);

            $owner->mobileVerification()->create([
                'mobile_no' => fake()->phoneNumber(),
                'code' => rand(1000, 9999),
                'verified_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addSeconds(300)
            ]);

            $user = User::factory()->create([
                'email' => 'user@main.renturo.test',
                'role' => User::ROLE_USER
            ]);

            $user->mobileVerification()->create([
                'mobile_no' => fake()->phoneNumber(),
                'code' => rand(1000, 9999),
                'verified_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addSeconds(300)
            ]);

            //Passport install
            Artisan::call("tenants:run passport:client --option='personal=personal' --option='name={$tenant->id}' --tenants={$tenant->id}");
        });
    }
}
