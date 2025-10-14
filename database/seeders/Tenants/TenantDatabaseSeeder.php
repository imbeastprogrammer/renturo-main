<?php

namespace Database\Seeders\Tenants;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting to seed TenantDatabaseSeeder data...');

        $this->call([
            \Database\Seeders\Tenants\TenantFormSystemSeeder::class,
            \Database\Seeders\Tenants\TenantPostSeeder::class
        ]);

        $this->command->info('Seeding completed for TenantDatabaseSeeder data.');
    }
}
