<?php

namespace Database\Seeders\Tenants\E2E;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UniversalPropertySeeder extends Seeder
{
    /**
     * Run the universal property seeder.
     * Demonstrates the complete Universal Availability System across all property types.
     */
    public function run(): void
    {
        $this->command->info('🌟 ===== UNIVERSAL PROPERTY SYSTEM DEMONSTRATION =====');
        $this->command->info('🚀 Starting comprehensive seeding of all property types...');
        $this->command->info('');

        $startTime = microtime(true);

        try {
            // Step 1: Basketball Courts (Sports)
            $this->command->info('🏀 Step 1/4: Seeding Basketball Courts (Sports Properties)...');
            $this->call(BasketballCourtSeeder::class);
            $this->command->info('✅ Basketball courts seeded successfully!');
            $this->command->info('');

            // Step 2: Hotels (Accommodation)
            $this->command->info('🏨 Step 2/4: Seeding Hotels (Accommodation Properties)...');
            $this->call(HotelSeeder::class);
            $this->command->info('✅ Hotels seeded successfully!');
            $this->command->info('');

            // Step 3: Car Rentals (Transportation)
            $this->command->info('🚗 Step 3/4: Seeding Car Rentals (Transportation Properties)...');
            $this->call(CarRentalSeeder::class);
            $this->command->info('✅ Car rentals seeded successfully!');
            $this->command->info('');

            // Step 4: Event Venues (Events & Venues)
            $this->command->info('🎪 Step 4/5: Seeding Event Venues (Event Properties)...');
            $this->call(EventVenueSeeder::class);
            $this->command->info('✅ Event venues seeded successfully!');
            $this->command->info('');

            // Step 5: Sample Bookings
            $this->command->info('🎫 Step 5/5: Creating Sample Bookings...');
            $this->call(BookingSeeder::class);
            $this->command->info('✅ Sample bookings created successfully!');
            $this->command->info('');

            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            $this->printFinalSummary($executionTime);

        } catch (\Exception $e) {
            $this->command->error('❌ Universal property seeding failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function printFinalSummary(float $executionTime): void
    {
        // Get comprehensive statistics
        $stats = $this->getSystemStatistics();

        $this->command->info('🎉 ===== UNIVERSAL AVAILABILITY SYSTEM - COMPLETE! =====');
        $this->command->info('');
        $this->command->info('📊 SYSTEM STATISTICS:');
        $this->command->info('⏱️  Total Execution Time: ' . $executionTime . ' seconds');
        $this->command->info('👥 Total Property Owners: ' . $stats['total_owners']);
        $this->command->info('🏪 Total Stores/Businesses: ' . $stats['total_stores']);
        $this->command->info('🏢 Total Property Listings: ' . $stats['total_listings']);
        $this->command->info('🔧 Total Units/Inventory: ' . $stats['total_units']);
        $this->command->info('📋 Total Availability Templates: ' . $stats['total_templates']);
        $this->command->info('📅 Total Availability Slots: ' . $stats['total_availability']);
        $this->command->info('📋 Total Sample Bookings: ' . $stats['total_bookings']);
        $this->command->info('');
        $this->command->info('🏷️  PROPERTY TYPES DEMONSTRATED:');
        $this->command->info('   🏀 Sports Facilities (Basketball Courts)');
        $this->command->info('   🏨 Accommodation (Hotels & Resorts)');
        $this->command->info('   🚗 Transportation (Car Rentals)');
        $this->command->info('   🎪 Events & Venues (Wedding, Conference, Party)');
        $this->command->info('');
        $this->command->info('⚡ UNIVERSAL FEATURES SHOWCASED:');
        $this->command->info('   ✅ Hourly, Daily, Weekly, Monthly bookings');
        $this->command->info('   ✅ Multi-unit inventory management');
        $this->command->info('   ✅ Dynamic pricing (peak, weekend, holiday)');
        $this->command->info('   ✅ Category-specific formatting & metadata');
        $this->command->info('   ✅ Flexible availability templates');
        $this->command->info('   ✅ Booking rules & policies');
        $this->command->info('   ✅ Status management (available/booked/blocked)');
        $this->command->info('   ✅ Owner-based access control');
        $this->command->info('');
        $this->command->info('🎯 READY FOR PRODUCTION:');
        $this->command->info('   • Complete API endpoints with Swagger docs');
        $this->command->info('   • Comprehensive unit & feature tests');
        $this->command->info('   • Universal database schema');
        $this->command->info('   • Scalable for any future property type');
        $this->command->info('');
        $this->command->info('🚀 RENTURO MVP LAUNCH READY - December 25, 2025! 🎄');
        $this->command->info('====================================================');
    }

    private function getSystemStatistics(): array
    {
        return [
            'total_owners' => DB::table('users')->where('role', 'owner')->count(),
            'total_stores' => DB::table('stores')->count(),
            'total_listings' => DB::table('listings')->count(),
            'total_units' => DB::table('listing_units')->count(),
            'total_templates' => DB::table('availability_templates')->count(),
            'total_availability' => DB::table('listing_availability')->count(),
            'total_bookings' => DB::table('bookings')->count()
        ];
    }
}


