<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Central\User;
use Illuminate\Support\Facades\Log;
use Artisan;
use Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->command->info('Starting to seed DatabaseSeeder data...');

        Artisan::call('passport:client', [
            '--personal' => true,
            '--name' => 'Renturo Personal Access Client'  // Provide a default name to avoid user interaction
        ]);
        
        $output = Artisan::output();
        $this->command->info("Passport Command DatabaseSeeder Output: " . $output);

        // Artisan::call("passport:client --personal");

        User::create(
            [
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => 'super-admin@renturo.test',
                'role' => 'SUPER-ADMIN',
                'email_verified_at' => now(),
                'password' => 'password',
                'remember_token' => Str::random(10),
                'mobile_number' => '0000 0000 001'
            ],
        );

        $this->call([
            \Database\Seeders\Tenants\TenantSeeder::class
        ]);

        $this->command->info('Seeding completed for DatabaseSeeder data.');

    }
}
