<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Central\Tenant;
use App\Models\User;
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
        $tenant = Tenant::create([
            'id' => Str::lower(Str::random(6)),
            'name' => 'main',
            'status' => 'active'
        ]);

        $tenant->domains()->create([
            'domain' => 'main.' . config('tenancy.central_domains')[2]
        ]);

        $tenant->run(function () use ($tenant) {
            User::factory()->create([
                'email' => 'admin@main.renturo.test',
                'role' => User::ROLE_ADMIN
            ]);

            User::factory()->create([
                'email' => 'owner@main.renturo.test',
                'role' => User::ROLE_OWNER
            ]);

            User::factory()->create([
                'email' => 'user@main.renturo.test',
                'role' => User::ROLE_USER
            ]);
        });

        //Passport install
        Artisan::call("tenants:run passport:client --option='personal=personal' --option='name={$tenant->id}' --tenants={$tenant->id}");
    }
}
