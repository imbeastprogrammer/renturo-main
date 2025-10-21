<?php

namespace Database\Seeders\Tenants\E2E;

use Illuminate\Database\Seeder;

class BasketballCourtSeeder extends Seeder
{
    /**
     * Complete End-to-End Seeder for Basketball Court Listing Flow
     * 
     * This seeder demonstrates the complete user journey from admin setup to listing creation:
     * 
     * FLOW DEMONSTRATION:
     * 1. TenantCategorySeeder - Admin creates categories & subcategories (Sports Venues → Basketball Courts)
     * 2. TenantDynamicFormSeeder - Admin creates dynamic forms for various categories
     * 3. ListingSeeder - Complete flow: Owners register, create stores, and list basketball courts
     * 
     * This simulates the COMPLETE Christmas 2025 MVP workflow for Renturo's
     * basketball court rental marketplace.
     * 
     * @return void
     */
    public function run()
    {
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('   RENTURO END-TO-END BASKETBALL COURT SEEDER');
        $this->command->info('   Simulating Complete Admin → Owner → Listing Flow');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('');

        // Step 0: Ensure Admin User Exists
        $this->command->info('Step 0: Ensuring admin user exists...');
        $admin = \App\Models\User::where('role', \App\Models\User::ROLE_ADMIN)->first();
        if (!$admin) {
            $admin = \App\Models\User::create([
                'first_name' => 'Renturo',
                'last_name' => 'Admin',
                'username' => 'renturoadmin',
                'email' => 'admin@renturo.ph',
                'mobile_number' => '+639171234567',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => \App\Models\User::ROLE_ADMIN,
            ]);
            $this->command->info('  ✓ Created admin user (admin@renturo.ph)');
        } else {
            $this->command->info('  ✓ Admin user already exists');
        }
        $this->command->info('');

        // Step 1: Admin creates Categories & Subcategories
        $this->command->info('Step 1: Admin creates categories and subcategories...');
        $this->call(\Database\Seeders\Tenants\Admin\CategorySeeder::class);
        $this->command->info('  ✓ 12 Categories with 111+ Subcategories created');
        $this->command->info('');

        // Step 2: Admin creates Dynamic Forms with Pages and Fields
        $this->command->info('Step 2: Admin creates basketball booking forms...');
        $this->call(\Database\Seeders\Tenants\Admin\BasketballFormSeeder::class);
        $this->command->info('  ✓ Dynamic form with 3 pages and booking fields created');
        $this->command->info('');

         // Step 3: Court Owners Fill Out Forms (Dynamic Form Submissions)
        $this->command->info('Step 3: Court owners register and fill out forms...');
        $this->call(\Database\Seeders\Tenants\Client\DynamicFormSubmissionSeeder::class);
        $this->command->info('  ✓ 4 Court owners with form submissions created');
        $this->command->info('');

        // Step 4: Complete Listing Flow (Convert Submissions to Listings)
        $this->command->info('Step 4: Creating basketball court listings...');
        $this->command->info('  This includes:');
        $this->command->info('  • Converting form submissions to listings');
        $this->command->info('  • Adding professional photos');
        $this->command->info('  • Setting weekly availability schedules');
        $this->command->info('  • Publishing courts for booking');
        $this->call(\Database\Seeders\Tenants\Client\ListingSeeder::class);
        $this->command->info('  ✓ 4 Complete basketball court listings created');
        $this->command->info('');

        // Success Summary
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('   END-TO-END SEEDER COMPLETE!');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('SUCCESS! Complete basketball court rental system is seeded:');
        $this->command->info('');
        $this->command->info('✓ 12 Categories & 111+ Subcategories');
        $this->command->info('✓ Dynamic Forms with Pages & Fields:');
        $this->command->info('  • Basketball Arena booking form (3 pages)');
        $this->command->info('  • Contact Information page (Name, Phone, Email)');
        $this->command->info('  • Booking Details page (Date, Time, Duration)');
        $this->command->info('  • Court & Activity page (Court Type, Activity, Players)');
        $this->command->info('✓ 4 Court Owners & Form Submissions:');
        $this->command->info('  • Owners registered with complete profiles');
        $this->command->info('  • Venue stores created');
        $this->command->info('  • Form submissions with realistic data');
        $this->command->info('✓ 4 Premium Basketball Court Listings with:');
        $this->command->info('  • Complete property details (title, description, address)');
        $this->command->info('  • Professional court photos (5 per listing)');
        $this->command->info('  • Weekly availability (Mon-Sun with time slots)');  
        $this->command->info('  • Pricing (₱1,500-2,000/hour, ₱10,000-15,000/day)');
        $this->command->info('  • Amenities (WiFi, AC, parking, showers, lockers, etc.)');
        $this->command->info('  • Geolocation (Manila, Quezon City, Makati, BGC)');
        $this->command->info('');
        $this->command->info('Featured Courts:');
        $this->command->info('  1. Premium Indoor Basketball Court - Manila');
        $this->command->info('  2. Elite Basketball Arena - Quezon City');
        $this->command->info('  3. Pro Hoops Center - Makati');
        $this->command->info('  4. Championship Basketball Court - BGC');
        $this->command->info('');
        $this->command->info('Ready for Christmas 2025 Launch!');
        $this->command->info('');
        $this->command->info('Next Steps for Testing:');
        $this->command->info('  1. GET /api/client/v1/listings - View all courts');
        $this->command->info('  2. GET /api/client/v1/listings/featured - Featured courts');
        $this->command->info('  3. GET /api/client/v1/listings/{id} - Court details');
        $this->command->info('  4. Filter by city, price, amenities, etc.');
        $this->command->info('  5. Test booking flow in mobile app');
        $this->command->info('  6. Review Swagger docs at /api/documentation');
        $this->command->info('');
        $this->command->info('Database Contents:');
        $this->command->info('  • categories (12 records)');
        $this->command->info('  • sub_categories (111+ records)');
        $this->command->info('  • dynamic_forms (1 Basketball Arena form)');
        $this->command->info('  • dynamic_form_pages (3 pages: Contact, Booking, Court)');
        $this->command->info('  • dynamic_form_fields (10+ booking form fields)');
        $this->command->info('  • dynamic_form_submissions (4 owner submissions) ← NEW!');
        $this->command->info('  • users (4 court owners + admin)');
        $this->command->info('  • stores (4 venue records)');
        $this->command->info('  • listings (4 basketball courts)');
        $this->command->info('  • listing_photos (14-20 photos total)');
        $this->command->info('  • listing_availability (28+ time schedules)');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('Merry Christmas 2025 - Renturo MVP is Ready!');
        $this->command->info('═══════════════════════════════════════════════════════════');
    }
}
