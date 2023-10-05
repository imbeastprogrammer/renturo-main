<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Central\Tenant;

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
    }
}
