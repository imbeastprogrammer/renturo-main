<?php

namespace Database\Seeders\Tenants\Client;

use Illuminate\Database\Seeder;
use App\Models\Listing;
use App\Models\ListingPricing;
use App\Models\ListingPhoto;
use App\Models\ListingAvailability;
use App\Models\AvailabilityTemplate;
use App\Models\DynamicFormSubmission;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use App\Models\Store;
use Carbon\Carbon;

class ListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get basketball category (assuming it exists from TenantCategorySeeder)
        $basketballCategory = Category::where('name', 'like', '%Sports%')->first();
        $basketballSubCategory = SubCategory::where('name', 'like', '%Basketball%')->first();

        // Get admin or first user
        $user = User::where('role', User::ROLE_ADMIN)->first() ?? User::first();

        if (!$user) {
            $this->command->warn('No users found. Please run user seeder first.');
            return;
        }

        if (!$basketballCategory) {
            $this->command->warn('Basketball category not found. Please run category seeder first.');
            return;
        }

        // Get or create a default store for basketball listings
        $store = Store::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Elite Sports Complex'],
            [
                'url' => 'elite-sports-' . uniqid(),
                'category_id' => $basketballCategory->id,
                'sub_category_id' => $basketballSubCategory?->id,
                'address' => '123 Sports Avenue, Bonifacio Global City',
                'city' => 'Taguig',
                'state' => 'Metro Manila',
                'zip_code' => '1634',
                'latitude' => 14.5547,
                'longitude' => 121.0511,
                'about' => 'Premier sports facility offering world-class basketball courts and amenities.',
            ]
        );

        $this->command->info('Seeding basketball court listings with new architecture...');

        // Clean up existing listings from this seeder (to allow re-running)
        $existingListings = Listing::where('slug', 'like', '%basketball-court%')->pluck('id');
        if ($existingListings->isNotEmpty()) {
            ListingPhoto::whereIn('listing_id', $existingListings)->delete();
            ListingAvailability::whereIn('listing_id', $existingListings)->delete();
            ListingPricing::whereIn('listing_id', $existingListings)->delete();
            DynamicFormSubmission::whereIn('listing_id', $existingListings)->delete();
            Listing::whereIn('id', $existingListings)->delete();
        }

        // ========================================
        // LISTING 1: Multi-Unit Facility (4 Courts)
        // ========================================
        $listing1 = Listing::create([
            'user_id' => $user->id,
            'store_id' => $store->id,
            'category_id' => $basketballCategory->id,
            'sub_category_id' => $basketballSubCategory?->id,
            'listing_type' => Listing::TYPE_SPORTS,
            'title' => 'Elite Sports Complex - Basketball Courts',
            'description' => 'Experience world-class basketball at our state-of-the-art facility featuring 4 premium courts. Our complex offers both indoor and outdoor options with professional-grade equipment and amenities.

Perfect for competitive games, training sessions, or recreational play. The facility includes modern locker rooms with hot showers, secure storage lockers, a spectator area with comfortable seating, and complimentary high-speed WiFi.

All courts meet official FIBA standards and are equipped with electronic scoreboards, shot clocks, and premium sound systems. Whether you\'re hosting a tournament, conducting team practice, or playing with friends, this is Manila\'s premier basketball destination.',
            'slug' => 'elite-sports-complex-basketball-courts-manila',
            'address' => '123 Sports Avenue, Bonifacio Global City',
            'city' => 'Taguig',
            'province' => 'Metro Manila',
            'postal_code' => '1634',
            'latitude' => 14.5547,
            'longitude' => 121.0511,
            'inventory_type' => 'multiple', // Multi-unit property
            'total_units' => 4,
            'status' => Listing::STATUS_ACTIVE,
            'visibility' => Listing::VISIBILITY_PUBLIC,
            'is_featured' => true,
            'is_verified' => true,
            'views_count' => 1234,
            'bookings_count' => 156,
            'average_rating' => 4.9,
            'reviews_count' => 89,
            'published_at' => now()->subMonths(3),
        ]);

        // Create pricing for listing 1
        $pricing1 = ListingPricing::create([
            'listing_id' => $listing1->id,
            'currency' => 'PHP',
            'price_min' => 800.00,  // Will be calculated from units
            'price_max' => 1500.00, // Will be calculated from units
            'pricing_model' => 'dynamic',
            'service_fee_percentage' => 5.00,
            'platform_fee_percentage' => 0.00,
            'tax_percentage' => 12.00,
            'tax_included' => false,
        ]);

        // Add photos for listing 1
        for ($i = 1; $i <= 5; $i++) {
            ListingPhoto::create([
                'listing_id' => $listing1->id,
                'photo_url' => 'https://via.placeholder.com/1920x1080/4CAF50/ffffff?text=Elite+Sports+Complex+' . $i,
                'thumbnail_url' => 'https://via.placeholder.com/400x300/4CAF50/ffffff?text=Elite+Sports+Complex+' . $i,
                'sort_order' => $i,
                'is_primary' => $i === 1,
                'storage_disk' => 'public',
            ]);
        }

        // Create 4 units (courts) for listing 1
        $courts = [
            [
                'unit_name' => 'Premium Indoor Court A',
                'description' => 'State-of-the-art indoor court with professional hardwood flooring, climate control, and premium LED lighting.',
                'price_per_hour' => 1500.00,
                'price_per_day' => 10000.00,
                'max_players' => 30,
                'is_airconditioned' => true,
                'floor_material' => 'Professional Hardwood',
                'lighting' => 'LED Professional',
                'has_scoreboard' => true,
                'has_sound_system' => true,
                'amenities' => ['AC', 'Scoreboard', 'Sound System', 'WiFi', 'Lockers', 'Parking'],
                'min_booking_hours' => 2,
                'max_booking_hours' => 8,
                'instant_booking' => true,
                'cancellation_policy' => 'flexible',
            ],
            [
                'unit_name' => 'Indoor Court B',
                'description' => 'Professional indoor court with excellent ventilation and modern facilities.',
                'price_per_hour' => 1200.00,
                'price_per_day' => 8000.00,
                'max_players' => 30,
                'is_airconditioned' => true,
                'floor_material' => 'Hardwood',
                'lighting' => 'LED',
                'has_scoreboard' => true,
                'has_sound_system' => true,
                'amenities' => ['AC', 'Scoreboard', 'WiFi', 'Lockers', 'Parking'],
                'min_booking_hours' => 2,
                'max_booking_hours' => 8,
                'instant_booking' => true,
                'cancellation_policy' => 'flexible',
            ],
            [
                'unit_name' => 'Outdoor Court C',
                'description' => 'Well-maintained outdoor court with excellent flooring and night lighting.',
                'price_per_hour' => 1000.00,
                'price_per_day' => 6500.00,
                'max_players' => 28,
                'is_airconditioned' => false,
                'floor_material' => 'Rubberized',
                'lighting' => 'Floodlights',
                'has_scoreboard' => true,
                'has_sound_system' => false,
                'amenities' => ['Scoreboard', 'WiFi', 'Parking', 'Water Station'],
                'min_booking_hours' => 2,
                'max_booking_hours' => 6,
                'instant_booking' => true,
                'cancellation_policy' => 'flexible',
            ],
            [
                'unit_name' => 'Outdoor Court D',
                'description' => 'Standard outdoor court, perfect for casual games and practice sessions.',
                'price_per_hour' => 800.00,
                'price_per_day' => 5000.00,
                'max_players' => 28,
                'is_airconditioned' => false,
                'floor_material' => 'Concrete',
                'lighting' => 'Basic Floodlights',
                'has_scoreboard' => false,
                'has_sound_system' => false,
                'amenities' => ['Parking', 'Water Station'],
                'min_booking_hours' => 1,
                'max_booking_hours' => 6,
                'instant_booking' => true,
                'cancellation_policy' => 'moderate',
            ],
        ];

        foreach ($courts as $courtData) {
            $unit = DynamicFormSubmission::create([
                'listing_id' => $listing1->id,
                'dynamic_form_id' => null, // Would be set when using actual dynamic forms
                'user_id' => $user->id,
                'store_id' => $store->id,
                'status' => 'active',
                'data' => $courtData,
            ]);

            // Create availability template for this unit
            $template = AvailabilityTemplate::create([
                'listing_id' => $listing1->id,
                'name' => $courtData['unit_name'] . ' Schedule',
                'description' => 'Daily schedule for ' . $courtData['unit_name'],
                'days_of_week' => [0,1,2,3,4,5,6], // All days
                'start_time' => '06:00',
                'end_time' => '23:00',
                'slot_duration_minutes' => 60,
                'base_hourly_price' => $courtData['price_per_hour'],
                'base_daily_price' => $courtData['price_per_day'],
                'peak_hour_price' => $courtData['price_per_hour'] * 1.3,
                'weekend_price' => $courtData['price_per_hour'] * 1.2,
                'is_recurring' => true,
                'is_active' => true,
                'unit_identifier' => $unit->id,
                'created_by' => $user->id,
            ]);

            // Generate availability for next 30 days
            $template->applyToDateRange(Carbon::today(), Carbon::today()->addDays(30));
        }

        // Update price range from units
        $pricing1->updatePriceRangeFromUnits();

        $this->command->info("✅ Created listing '{$listing1->title}' with 4 courts");

        // ========================================
        // LISTING 2: Single-Unit Property
        // ========================================
        $listing2 = Listing::create([
            'user_id' => $user->id,
            'store_id' => $store->id,
            'category_id' => $basketballCategory->id,
            'sub_category_id' => $basketballSubCategory?->id,
            'listing_type' => Listing::TYPE_SPORTS,
            'title' => 'Downtown Basketball Arena - Full Court',
            'description' => 'Premium indoor basketball court in the heart of Manila\'s business district. Perfect for corporate leagues, private events, and competitive games.

Features professional-grade hardwood flooring, climate control, and championship-level equipment. The venue includes VIP seating for up to 100 spectators, professional sound system, and live streaming capabilities.',
            'slug' => 'downtown-basketball-arena-full-court-manila',
            'address' => '456 Makati Avenue, Makati City',
            'city' => 'Makati',
            'province' => 'Metro Manila',
            'postal_code' => '1223',
            'latitude' => 14.5547,
            'longitude' => 121.0244,
            'inventory_type' => 'single', // Single-unit property
            'total_units' => 1,
            'status' => Listing::STATUS_ACTIVE,
            'visibility' => Listing::VISIBILITY_PUBLIC,
            'is_featured' => true,
            'is_verified' => true,
            'views_count' => 856,
            'bookings_count' => 92,
            'average_rating' => 4.8,
            'reviews_count' => 54,
            'published_at' => now()->subMonths(2),
        ]);

        // Create pricing for listing 2
        $pricing2 = ListingPricing::create([
            'listing_id' => $listing2->id,
            'currency' => 'PHP',
            'price_min' => 2000.00,
            'price_max' => 2000.00,
            'pricing_model' => 'fixed',
            'base_hourly_price' => 2000.00,
            'base_daily_price' => 15000.00,
            'service_fee_percentage' => 5.00,
            'security_deposit' => 5000.00,
            'tax_percentage' => 12.00,
            'tax_included' => false,
        ]);

        // Add photos for listing 2
        for ($i = 1; $i <= 4; $i++) {
            ListingPhoto::create([
                'listing_id' => $listing2->id,
                'photo_url' => 'https://via.placeholder.com/1920x1080/2196F3/ffffff?text=Downtown+Arena+' . $i,
                'thumbnail_url' => 'https://via.placeholder.com/400x300/2196F3/ffffff?text=Downtown+Arena+' . $i,
                'sort_order' => $i,
                'is_primary' => $i === 1,
                'storage_disk' => 'public',
            ]);
        }

        // Create single unit for listing 2
        $singleUnit = DynamicFormSubmission::create([
            'listing_id' => $listing2->id,
            'dynamic_form_id' => null,
            'user_id' => $user->id,
            'store_id' => $store->id,
            'status' => 'active',
            'data' => [
                'unit_name' => 'Main Arena Court',
                'description' => 'Championship-level full court with VIP facilities',
                'price_per_hour' => 2000.00,
                'price_per_day' => 15000.00,
                'max_players' => 40,
                'is_airconditioned' => true,
                'floor_material' => 'Championship Hardwood',
                'lighting' => 'Professional LED Array',
                'has_scoreboard' => true,
                'has_sound_system' => true,
                'has_streaming' => true,
                'seating_capacity' => 100,
                'amenities' => ['AC', 'Scoreboard', 'Sound System', 'Live Streaming', 'VIP Lounge', 'WiFi', 'Lockers', 'Parking'],
                'min_booking_hours' => 3,
                'max_booking_hours' => 12,
                'instant_booking' => false,
                'cancellation_policy' => 'strict',
            ],
        ]);

        // Create availability template for single unit
        $singleTemplate = AvailabilityTemplate::create([
            'listing_id' => $listing2->id,
            'name' => 'Downtown Arena Schedule',
            'description' => 'Daily schedule for Downtown Arena',
            'days_of_week' => [0,1,2,3,4,5,6],
            'start_time' => '08:00',
            'end_time' => '22:00',
            'slot_duration_minutes' => 60,
            'base_hourly_price' => 2000.00,
            'base_daily_price' => 15000.00,
            'peak_hour_price' => 2500.00,
            'weekend_price' => 2400.00,
            'is_recurring' => true,
            'is_active' => true,
            'unit_identifier' => $singleUnit->id,
            'created_by' => $user->id,
        ]);

        // Generate availability for next 30 days
        $singleTemplate->applyToDateRange(Carbon::today(), Carbon::today()->addDays(30));

        $this->command->info("✅ Created listing '{$listing2->title}' (single unit)");

        $this->command->info('✅ Basketball court listings seeded successfully with new architecture!');
    }
}
