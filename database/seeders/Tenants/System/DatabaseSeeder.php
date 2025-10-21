<?php

namespace Database\Seeders\Tenants\System;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting to seed Tenant Database...');

        $this->call([
            \Database\Seeders\Tenants\System\FormSystemSeeder::class,
            \Database\Seeders\Tenants\Client\PostSeeder::class
        ]);

        $this->command->info('Seeding completed for Tenant Database.');
    }
}
