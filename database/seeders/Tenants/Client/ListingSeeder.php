<?php

namespace Database\Seeders\Tenants\Client;

use Illuminate\Database\Seeder;
use App\Models\Listing;
use App\Models\ListingPhoto;
use App\Models\ListingAvailability;
use App\Models\AvailabilityTemplate;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
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
        
        // Use basketball subcategory for all listings
        $indoorSubCategory = $basketballSubCategory;
        $outdoorSubCategory = $basketballSubCategory;

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

        $this->command->info('Seeding basketball court listings...');

        // Clean up existing listings from this seeder (to allow re-running)
        $existingListings = Listing::where('slug', 'like', '%basketball-court%')->pluck('id');
        if ($existingListings->isNotEmpty()) {
            ListingPhoto::whereIn('listing_id', $existingListings)->delete();
            ListingAvailability::whereIn('listing_id', $existingListings)->delete();
            Listing::whereIn('id', $existingListings)->delete();
        }

        // Featured Premium Indoor Basketball Court
        $listing1 = Listing::create([
            'user_id' => $user->id,
            'category_id' => $basketballCategory->id,
            'sub_category_id' => $indoorSubCategory?->id,
            'listing_type' => Listing::TYPE_SPORTS,
            'title' => 'Elite Sports Complex - Premium Indoor Basketball Court',
            'description' => 'Experience world-class basketball at our state-of-the-art indoor facility. Our premium full-court features professional-grade hardwood flooring, adjustable LED lighting, and climate control for optimal playing conditions year-round.

Perfect for competitive games, training sessions, or recreational play. The facility includes modern locker rooms with hot showers, secure storage lockers, a spectator area with comfortable seating, and complimentary high-speed WiFi.

Our court meets official FIBA standards and is equipped with electronic scoreboards, shot clocks, and a premium sound system. Whether you\'re hosting a tournament, conducting team practice, or playing with friends, this is Manila\'s premier basketball destination.

Amenities include free parking for up to 50 vehicles, on-site sports shop for equipment and gear, certified referees available on request, and a cafe serving refreshments and light meals.',
            'slug' => 'elite-sports-complex-premium-indoor-basketball-court-manila',
            'address' => '123 Sports Avenue, Bonifacio Global City',
            'city' => 'Taguig',
            'province' => 'Metro Manila',
            'postal_code' => '1634',
            'latitude' => 14.5547,
            'longitude' => 121.0511,
            'price_per_hour' => 1200.00,
            'price_per_day' => 8000.00,
            'currency' => 'PHP',
            'max_capacity' => 30,
            'amenities' => [
                'air_conditioning',
                'parking',
                'restroom',
                'changing_room',
                'lockers',
                'wifi',
                'scoreboard',
                'sound_system',
                'seating',
                'water_station',
                'referee',
                'lighting',
                'security',
                'cctv',
            ],
            'status' => Listing::STATUS_ACTIVE,
            'visibility' => Listing::VISIBILITY_PUBLIC,
            'is_featured' => true,
            'is_verified' => true,
            'instant_booking' => true,
            'minimum_booking_hours' => 2,
            'maximum_booking_hours' => 8,
            'advance_booking_days' => 30,
            'cancellation_hours' => 24,
            'views_count' => 1234,
            'bookings_count' => 156,
            'average_rating' => 4.9,
            'reviews_count' => 89,
            'published_at' => now()->subMonths(3),
        ]);

        // Add photos for listing 1
        for ($i = 1; $i <= 5; $i++) {
            ListingPhoto::create([
                'listing_id' => $listing1->id,
                'photo_url' => 'https://via.placeholder.com/1920x1080/4CAF50/ffffff?text=Premium+Indoor+Court+' . $i,
                'thumbnail_url' => 'https://via.placeholder.com/400x300/4CAF50/ffffff?text=Premium+Indoor+Court+' . $i,
                'sort_order' => $i,
                'is_primary' => $i === 1,
                'storage_disk' => 'public',
            ]);
        }

        // Create availability template for listing 1 (PROPER WAY)
        $premiumTemplate = AvailabilityTemplate::create([
            'listing_id' => $listing1->id,
            'name' => 'Premium Indoor Court Schedule',
            'description' => 'Full-day schedule for premium indoor basketball court',
            'days_of_week' => [0,1,2,3,4,5,6], // All days
            'start_time' => '06:00',
            'end_time' => '23:00',
            'slot_duration_minutes' => 60,
            'base_hourly_price' => 1200.00,
            'base_daily_price' => 8000.00,
            'peak_hour_multiplier' => 1.25, // 25% more during peak hours
            'weekend_multiplier' => 1.15, // 15% more on weekends
            'peak_start_time' => '18:00',
            'peak_end_time' => '22:00',
            'duration_type' => 'hourly',
            'min_duration_hours' => 2,
            'max_duration_hours' => 8,
            'advance_booking_hours' => 24,
            'cancellation_hours' => 24,
            'booking_rules' => [
                'min_booking_hours' => 2,
                'max_booking_hours' => 8,
                'advance_booking_required' => true,
                'cancellation_policy' => '24 hours notice required',
                'peak_hour_surcharge' => '25% during 6-10 PM'
            ],
            'is_active' => true,
            'auto_apply' => true,
            'auto_apply_days_ahead' => 30,
            'created_by' => $user->id,
        ]);

        // Apply template to generate actual availability slots
        $premiumTemplate->applyToDateRange(
            Carbon::now(),
            Carbon::now()->addDays(30)
        );

        // Outdoor Community Basketball Court
        $listing2 = Listing::create([
            'user_id' => $user->id,
            'category_id' => $basketballCategory->id,
            'sub_category_id' => $outdoorSubCategory?->id,
            'listing_type' => Listing::TYPE_SPORTS,
            'title' => 'Sunshine Outdoor Basketball Court - Quezon City',
            'description' => 'Enjoy the game under the open sky at our well-maintained outdoor basketball court. Perfect for casual games with friends, weekend tournaments, or regular practice sessions.

Our full-size court features high-quality outdoor flooring, professional-grade basketball hoops with breakaway rims, and excellent night lighting for evening play. The surrounding area is fenced for security and includes covered seating areas for spectators.

Located in a safe, accessible neighborhood with plenty of parking space. The facility is regularly maintained and cleaned, ensuring a great playing experience every time. Ideal for community leagues, school events, or just shooting hoops with friends.

Basic amenities include restrooms, drinking water, and nearby parking. Security personnel on-site during operating hours.',
            'slug' => 'sunshine-outdoor-basketball-court-quezon-city',
            'address' => '456 Community Road, Commonwealth',
            'city' => 'Quezon City',
            'province' => 'Metro Manila',
            'postal_code' => '1121',
            'latitude' => 14.6760,
            'longitude' => 121.0437,
            'price_per_hour' => 400.00,
            'price_per_day' => 2500.00,
            'currency' => 'PHP',
            'max_capacity' => 20,
            'amenities' => [
                'parking',
                'restroom',
                'water_station',
                'lighting',
                'seating',
                'security',
            ],
            'status' => Listing::STATUS_ACTIVE,
            'visibility' => Listing::VISIBILITY_PUBLIC,
            'is_featured' => false,
            'is_verified' => true,
            'instant_booking' => true,
            'minimum_booking_hours' => 1,
            'maximum_booking_hours' => 6,
            'advance_booking_days' => 14,
            'cancellation_hours' => 12,
            'views_count' => 567,
            'bookings_count' => 89,
            'average_rating' => 4.5,
            'reviews_count' => 45,
            'published_at' => now()->subMonths(2),
        ]);

        // Add photos for listing 2
        for ($i = 1; $i <= 3; $i++) {
            ListingPhoto::create([
                'listing_id' => $listing2->id,
                'photo_url' => 'https://via.placeholder.com/1920x1080/FF9800/ffffff?text=Outdoor+Court+' . $i,
                'thumbnail_url' => 'https://via.placeholder.com/400x300/FF9800/ffffff?text=Outdoor+Court+' . $i,
                'sort_order' => $i,
                'is_primary' => $i === 1,
                'storage_disk' => 'public',
            ]);
        }

        // Create availability template for listing 2 (PROPER WAY)
        $outdoorTemplate = AvailabilityTemplate::create([
            'listing_id' => $listing2->id,
            'name' => 'Outdoor Court Daily Schedule',
            'description' => 'Standard daily schedule for outdoor basketball court',
            'days_of_week' => [0,1,2,3,4,5,6], // All days
            'start_time' => '06:00',
            'end_time' => '22:00',
            'slot_duration_minutes' => 60,
            'base_hourly_price' => 400.00,
            'base_daily_price' => 2500.00,
            'duration_type' => 'hourly',
            'min_duration_hours' => 1,
            'max_duration_hours' => 6,
            'advance_booking_hours' => 2,
            'cancellation_hours' => 12,
            'booking_rules' => [
                'min_booking_hours' => 1,
                'max_booking_hours' => 6,
                'advance_booking_required' => true,
                'cancellation_policy' => '12 hours notice required'
            ],
            'is_active' => true,
            'auto_apply' => true,
            'auto_apply_days_ahead' => 30,
            'created_by' => $user->id,
        ]);

        // Apply template to generate actual availability slots
        $outdoorTemplate->applyToDateRange(
            Carbon::now(),
            Carbon::now()->addDays(30)
        );

        // Half-Court Training Facility
        $listing3 = Listing::create([
            'user_id' => $user->id,
            'category_id' => $basketballCategory->id,
            'sub_category_id' => $indoorSubCategory?->id,
            'listing_type' => Listing::TYPE_SPORTS,
            'title' => 'Pro Training Half-Court - Skills Development Center',
            'description' => 'Specialized half-court facility designed for intensive training and skills development. Perfect for personal training sessions, small group workouts, or focused practice.

Our facility features rubberized sports flooring for joint protection, adjustable basketball hoops, training aids including cones and agility equipment, and wall-mounted shooting guides. The compact space is ideal for drills, shooting practice, and one-on-one coaching.

Air-conditioned for comfort, with mirrors on one wall for form checking and self-assessment. Video recording equipment available for technique analysis. Professional trainers and coaches can be arranged upon request.

Amenities include changing room, secure storage, refreshments vending machine, and training equipment rental.',
            'slug' => 'pro-training-half-court-skills-development-center-makati',
            'address' => '789 Fitness Street, Salcedo Village',
            'city' => 'Makati',
            'province' => 'Metro Manila',
            'postal_code' => '1227',
            'latitude' => 14.5598,
            'longitude' => 121.0234,
            'price_per_hour' => 800.00,
            'price_per_day' => 5500.00,
            'currency' => 'PHP',
            'max_capacity' => 10,
            'amenities' => [
                'air_conditioning',
                'parking',
                'restroom',
                'changing_room',
                'lockers',
                'water_station',
                'training_equipment',
                'mirrors',
                'video_recording',
            ],
            'status' => Listing::STATUS_ACTIVE,
            'visibility' => Listing::VISIBILITY_PUBLIC,
            'is_featured' => true,
            'is_verified' => true,
            'instant_booking' => false, // Requires approval for training sessions
            'minimum_booking_hours' => 1,
            'maximum_booking_hours' => 4,
            'advance_booking_days' => 21,
            'cancellation_hours' => 24,
            'views_count' => 890,
            'bookings_count' => 123,
            'average_rating' => 4.8,
            'reviews_count' => 67,
            'published_at' => now()->subMonths(1),
        ]);

        // Add photos for listing 3
        for ($i = 1; $i <= 4; $i++) {
            ListingPhoto::create([
                'listing_id' => $listing3->id,
                'photo_url' => 'https://via.placeholder.com/1920x1080/2196F3/ffffff?text=Training+Facility+' . $i,
                'thumbnail_url' => 'https://via.placeholder.com/400x300/2196F3/ffffff?text=Training+Facility+' . $i,
                'sort_order' => $i,
                'is_primary' => $i === 1,
                'storage_disk' => 'public',
            ]);
        }

        // Add availability for listing 3 (next 30 days, morning and evening sessions only)
        for ($i = 0; $i < 30; $i++) {
            $date = now()->addDays($i);
            
            // Skip Sundays for training facility
            if ($date->dayOfWeek === 0) {
                continue;
            }
            
            // Morning session
            ListingAvailability::create([
                'listing_id' => $listing3->id,
                'available_date' => $date->format('Y-m-d'),
                'start_time' => '06:00',
                'end_time' => '12:00',
                'slot_duration_minutes' => 60,
                'duration_type' => 'hourly',
                'status' => 'available',
                'created_by' => $user->id,
            ]);

            // Evening session
            ListingAvailability::create([
                'listing_id' => $listing3->id,
                'available_date' => $date->format('Y-m-d'),
                'start_time' => '18:00',
                'end_time' => '22:00',
                'slot_duration_minutes' => 60,
                'duration_type' => 'hourly',
                'status' => 'available',
                'created_by' => $user->id,
            ]);
        }

        // Budget-Friendly Community Court
        $listing4 = Listing::create([
            'user_id' => $user->id,
            'category_id' => $basketballCategory->id,
            'sub_category_id' => $outdoorSubCategory?->id,
            'listing_type' => Listing::TYPE_SPORTS,
            'title' => 'Barangay Basketball Court - Budget Friendly',
            'description' => 'Affordable outdoor basketball court perfect for casual games and community events. Well-maintained concrete court with standard basketball hoops and basic amenities.

Great for neighborhood games, friendly matches, or practice sessions on a budget. The court is regularly cleaned and properly lit for evening games. Safe and accessible location with nearby parking along the street.

Simple facilities include a basic restroom and water source. Security through community watch program. Perfect for regular weekend games with friends or organizing community tournaments.

Affordable rates make it accessible to everyone who loves the game.',
            'slug' => 'barangay-basketball-court-budget-friendly-pasig',
            'address' => '321 Barangay Street, Kapitolyo',
            'city' => 'Pasig',
            'province' => 'Metro Manila',
            'postal_code' => '1603',
            'latitude' => 14.5688,
            'longitude' => 121.0648,
            'price_per_hour' => 200.00,
            'price_per_day' => 1200.00,
            'currency' => 'PHP',
            'max_capacity' => 15,
            'amenities' => [
                'restroom',
                'water_station',
                'lighting',
                'seating',
            ],
            'status' => Listing::STATUS_ACTIVE,
            'visibility' => Listing::VISIBILITY_PUBLIC,
            'is_featured' => false,
            'is_verified' => true,
            'instant_booking' => true,
            'minimum_booking_hours' => 1,
            'maximum_booking_hours' => 4,
            'advance_booking_days' => 7,
            'cancellation_hours' => 6,
            'views_count' => 345,
            'bookings_count' => 67,
            'average_rating' => 4.2,
            'reviews_count' => 28,
            'published_at' => now()->subWeeks(3),
        ]);

        // Add photos for listing 4
        for ($i = 1; $i <= 2; $i++) {
            ListingPhoto::create([
                'listing_id' => $listing4->id,
                'photo_url' => 'https://via.placeholder.com/1920x1080/9C27B0/ffffff?text=Community+Court+' . $i,
                'thumbnail_url' => 'https://via.placeholder.com/400x300/9C27B0/ffffff?text=Community+Court+' . $i,
                'sort_order' => $i,
                'is_primary' => $i === 1,
                'storage_disk' => 'public',
            ]);
        }

        // Add availability for listing 4 (next 30 days, 7 AM to 9 PM)
        for ($i = 0; $i < 30; $i++) {
            $date = now()->addDays($i);
            ListingAvailability::create([
                'listing_id' => $listing4->id,
                'available_date' => $date->format('Y-m-d'),
                'start_time' => '07:00',
                'end_time' => '21:00',
                'slot_duration_minutes' => 60,
                'duration_type' => 'hourly',
                'status' => 'available',
                'created_by' => $user->id,
            ]);
        }

        $this->command->info('Successfully seeded ' . Listing::count() . ' basketball court listings with photos and availability!');
    }
}
