<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Central\User;
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
        Artisan::call("passport:client --personal");

        User::create([
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => 'super-admin@renturo.test',
            'role' => 'SUPER-ADMIN',
            'email_verified_at' => now(),
            'password' => 'password',
            'remember_token' => Str::random(10),
            'mobile_number' => '0000 0000 000'
        ]);

        $this->call([
            TenantSeeder::class
        ]);
    }
}
