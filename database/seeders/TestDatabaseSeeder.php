<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MobileVerification;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users for each role
        $admin = User::create([
            'first_name' => 'Test',
            'last_name' => 'Admin',
            'email' => 'test.admin@renturo.test',
            'username' => 'testadmin',
            'mobile_number' => '+639111111111',
            'role' => User::ROLE_ADMIN,
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        $owner = User::create([
            'first_name' => 'Test',
            'last_name' => 'Owner',
            'email' => 'test.owner@renturo.test',
            'username' => 'testowner',
            'mobile_number' => '+639222222222',
            'role' => User::ROLE_OWNER,
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test.user@renturo.test',
            'username' => 'testuser',
            'mobile_number' => '+639333333333',
            'role' => User::ROLE_USER,
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        $partner = User::create([
            'first_name' => 'Test',
            'last_name' => 'Partner',
            'email' => 'test.partner@renturo.test',
            'username' => 'testpartner',
            'mobile_number' => '+639444444444',
            'role' => User::ROLE_PARTNER,
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        // Create verified mobile numbers for some users
        MobileVerification::create([
            'user_id' => $owner->id,
            'mobile_number' => $owner->mobile_number,
            'code' => '1234',
            'verified_at' => now(),
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        MobileVerification::create([
            'user_id' => $user->id,
            'mobile_number' => $user->mobile_number,
            'code' => '1234',
            'verified_at' => now(),
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // Create unverified mobile number for testing verification
        MobileVerification::create([
            'user_id' => $admin->id,
            'mobile_number' => $admin->mobile_number,
            'code' => '1234',
            'verified_at' => null,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // Seed tenant-specific data
        $this->call([
            Tenants\TenantCategorySeeder::class,
            Tenants\ListingSeeder::class,
            Tenants\EndToEndBasketballCourtSeeder::class, // Complete end-to-end flow
        ]);
    }
}