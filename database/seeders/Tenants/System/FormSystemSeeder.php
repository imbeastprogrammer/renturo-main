<?php

namespace Database\Seeders\Tenants\System;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FormSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting to seed TenantFormSystemSeeder data...');
        $this->command->info('This will create users, categories, subcategories, and dynamic forms in the correct order.');

        // Step 1: Categories are already created by TenantSetupSeeder
        $this->command->info('Step 1: Categories already created by TenantSetupSeeder...');
        // $this->call(\Database\Seeders\Tenants\Admin\CategorySeeder::class);

        // Step 2: Create dynamic forms (which depend on subcategories)
        $this->command->info('Step 2: Creating dynamic forms...');
        $this->call(\Database\Seeders\Tenants\Admin\DynamicFormSeeder::class);

        $this->command->info('Seeding completed for TenantFormSystemSeeder data.');
        $this->command->info('Users, categories, subcategories, and dynamic forms have been created successfully!');
    }
} 