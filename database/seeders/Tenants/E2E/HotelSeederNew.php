<?php

namespace Database\Seeders\Tenants\E2E;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Store;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Listing;
use App\Models\ListingPricing;
use App\Models\ListingPhoto;
use App\Models\ListingAvailability;
use App\Models\AvailabilityTemplate;
use App\Models\DynamicFormSubmission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HotelSeederNew extends Seeder
{
    /**
     * Run the hotel property type seeder with NEW ARCHITECTURE.
     * Demonstrates the Universal Availability System for hotel/accommodation properties.
     */
    public function run(): void
    {
        $this->command->info('🏨 Starting Hotel Property Type Seeder (New Architecture)...');

        DB::beginTransaction();
        
        try {
            // Step 1: Ensure categories exist
            $this->ensureHotelCategories();
            
            // Step 2: Create hotel owner users
            $owners = $this->createHotelOwners();
            
            // Step 3: Create hotel stores
            $stores = $this->createHotelStores($owners);
            
            // Step 4: Create hotel listings with units
            $hotels = $this->createHotelListings($stores);
            
            DB::commit();
            
            $this->command->info('✅ Hotel Property Type Seeder completed successfully!');
            $this->printSummary($owners, $stores, $hotels);
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Hotel seeder failed: ' . $e->getMessage());
            $this->command->error($e->getTraceAsString());
            throw $e;
        }
    }

    private function ensureHotelCategories(): void
    {
        $this->command->info('📂 Ensuring hotel categories exist...');
        
        // Create Accommodation category if it doesn't exist
        $category = Category::firstOrCreate(['name' => 'Accommodation']);

        // Create hotel subcategories
        $subcategories = ['Luxury Hotel', 'Business Hotel', 'Boutique Hotel', 'Resort'];

        foreach ($subcategories as $subcatName) {
            SubCategory::firstOrCreate([
                'name' => $subcatName,
                'category_id' => $category->id
            ]);
        }
    }

    private function createHotelOwners(): array
    {
        $this->command->info('👥 Creating hotel owner users...');
        
        $owners = [];
        $hotelOwners = [
            [
                'first_name' => 'Maria',
                'last_name' => 'Rodriguez',
                'email' => 'maria.rodriguez@grandhotel.com',
                'mobile_number' => '+639171234501',
            ],
            [
                'first_name' => 'Carlos',
                'last_name' => 'Santos',
                'email' => 'carlos.santos@businessinn.com',
                'mobile_number' => '+639171234502',
            ],
            [
                'first_name' => 'Isabella',
                'last_name' => 'Cruz',
                'email' => 'isabella.cruz@boutiquesuites.com',
                'mobile_number' => '+639171234503',
            ],
        ];

        foreach ($hotelOwners as $ownerData) {
            $owners[] = User::firstOrCreate(
                ['email' => $ownerData['email']],
                array_merge($ownerData, [
                    'username' => explode('@', $ownerData['email'])[0],
                    'password' => bcrypt('password123'),
                    'role' => User::ROLE_CLIENT,
                    'status' => 'active',
                ])
            );
        }

        return $owners;
    }

    private function createHotelStores(array $owners): array
    {
        $this->command->info('🏪 Creating hotel stores...');
        
        $stores = [];
        $storeData = [
            [
                'name' => 'Grand Plaza Hotel',
                'url' => 'grand-plaza-hotel',
                'address' => '1234 Roxas Boulevard, Pasay City',
                'city' => 'Pasay',
                'state' => 'Metro Manila',
                'zip_code' => '1300',
                'about' => 'Luxury 5-star hotel offering premium accommodations.',
                'owner_index' => 0,
            ],
            [
                'name' => 'Business Inn Downtown',
                'url' => 'business-inn-downtown',
                'address' => '567 Ayala Avenue, Makati City',
                'city' => 'Makati',
                'state' => 'Metro Manila',
                'zip_code' => '1223',
                'about' => 'Modern business hotel in the heart of the financial district.',
                'owner_index' => 1,
            ],
            [
                'name' => 'Boutique Suites & Spa',
                'url' => 'boutique-suites-spa',
                'address' => '890 Jupiter Street, Makati City',
                'city' => 'Makati',
                'state' => 'Metro Manila',
                'zip_code' => '1209',
                'about' => 'Intimate boutique hotel with personalized service.',
                'owner_index' => 2,
            ],
        ];

        $accommodationCategory = Category::where('name', 'Accommodation')->first();

        foreach ($storeData as $data) {
            $stores[] = Store::firstOrCreate(
                ['url' => $data['url']],
                [
                    'user_id' => $owners[$data['owner_index']]->id,
                    'name' => $data['name'],
                    'category_id' => $accommodationCategory->id,
                    'address' => $data['address'],
                    'city' => $data['city'],
                    'state' => $data['state'],
                    'zip_code' => $data['zip_code'],
                    'latitude' => 14.5995 + (rand(-100, 100) / 1000),
                    'longitude' => 120.9842 + (rand(-100, 100) / 1000),
                    'about' => $data['about'],
                ]
            );
        }

        return $stores;
    }

    private function createHotelListings(array $stores): array
    {
        $this->command->info('🏢 Creating hotel listings with room units...');
        
        $hotels = [];
        $accommodationCategory = Category::where('name', 'Accommodation')->first();

        $hotelListings = [
            [
                'title' => 'Grand Plaza Hotel - Premium Accommodations',
                'description' => 'Experience luxury at its finest with our premium hotel rooms featuring city views, marble bathrooms, and 24/7 concierge service.',
                'store_index' => 0,
                'subcategory' => 'Luxury Hotel',
                'rooms' => [
                    [
                        'unit_name' => 'Standard King Room',
                        'count' => 20,
                        'price_per_day' => 299.00,
                        'max_guests' => 2,
                        'bed_type' => 'King',
                        'room_size' => '400 sq ft',
                        'amenities' => ['Free WiFi', 'Mini Bar', 'City View', 'Work Desk', 'Smart TV'],
                    ],
                    [
                        'unit_name' => 'Deluxe Suite',
                        'count' => 15,
                        'price_per_day' => 399.00,
                        'max_guests' => 3,
                        'bed_type' => 'King + Sofa Bed',
                        'room_size' => '600 sq ft',
                        'amenities' => ['Free WiFi', 'Mini Bar', 'Ocean View', 'Jacuzzi', 'Living Room', 'Smart TV'],
                    ],
                    [
                        'unit_name' => 'Executive Suite',
                        'count' => 10,
                        'price_per_day' => 499.00,
                        'max_guests' => 3,
                        'bed_type' => 'King + Sofa Bed',
                        'room_size' => '800 sq ft',
                        'amenities' => ['Free WiFi', 'Premium Bar', 'Panoramic View', 'Jacuzzi', 'Dining Area', '75" Smart TV', 'Private Balcony'],
                    ],
                    [
                        'unit_name' => 'Presidential Suite',
                        'count' => 5,
                        'price_per_day' => 799.00,
                        'max_guests' => 4,
                        'bed_type' => 'King + 2 Queens',
                        'room_size' => '1200 sq ft',
                        'amenities' => ['Free WiFi', 'Premium Bar', '360° View', 'Jacuzzi', 'Full Kitchen', 'Home Theater', 'Private Terrace', 'Butler Service'],
                    ],
                ],
            ],
            [
                'title' => 'Business Inn Downtown - Corporate Comfort',
                'description' => 'Modern business hotel designed for the corporate traveler with meeting facilities and high-speed internet.',
                'store_index' => 1,
                'subcategory' => 'Business Hotel',
                'rooms' => [
                    [
                        'unit_name' => 'Standard Room',
                        'count' => 40,
                        'price_per_day' => 179.00,
                        'max_guests' => 2,
                        'bed_type' => 'Queen',
                        'room_size' => '300 sq ft',
                        'amenities' => ['Free WiFi', 'Work Desk', 'Coffee Maker', 'Smart TV'],
                    ],
                    [
                        'unit_name' => 'Business Room',
                        'count' => 25,
                        'price_per_day' => 229.00,
                        'max_guests' => 2,
                        'bed_type' => 'King',
                        'room_size' => '400 sq ft',
                        'amenities' => ['Free WiFi', 'Executive Desk', 'Printer', 'Coffee Maker', 'Smart TV', 'Meeting Area'],
                    ],
                    [
                        'unit_name' => 'Junior Suite',
                        'count' => 15,
                        'price_per_day' => 279.00,
                        'max_guests' => 3,
                        'bed_type' => 'King + Sofa Bed',
                        'room_size' => '500 sq ft',
                        'amenities' => ['Free WiFi', 'Executive Desk', 'Printer', 'Mini Bar', 'Smart TV', 'Separate Living Room'],
                    ],
                ],
            ],
            [
                'title' => 'Boutique Suites & Spa - Unique Experience',
                'description' => 'Intimate boutique hotel with individually designed suites and personalized spa services.',
                'store_index' => 2,
                'subcategory' => 'Boutique Hotel',
                'rooms' => [
                    [
                        'unit_name' => 'Garden Suite',
                        'count' => 10,
                        'price_per_day' => 249.00,
                        'max_guests' => 2,
                        'bed_type' => 'Queen',
                        'room_size' => '450 sq ft',
                        'amenities' => ['Free WiFi', 'Garden View', 'Rain Shower', 'Organic Toiletries', 'Smart TV', 'Yoga Mat'],
                    ],
                    [
                        'unit_name' => 'City View Suite',
                        'count' => 8,
                        'price_per_day' => 324.00,
                        'max_guests' => 2,
                        'bed_type' => 'King',
                        'room_size' => '550 sq ft',
                        'amenities' => ['Free WiFi', 'City View', 'Spa Bath', 'Organic Toiletries', 'Smart TV', 'Meditation Corner'],
                    ],
                    [
                        'unit_name' => 'Penthouse Suite',
                        'count' => 7,
                        'price_per_day' => 399.00,
                        'max_guests' => 3,
                        'bed_type' => 'King + Sofa Bed',
                        'room_size' => '700 sq ft',
                        'amenities' => ['Free WiFi', 'Rooftop Terrace', 'Private Jacuzzi', 'Organic Toiletries', 'Smart TV', 'Wellness Area', 'Complimentary Spa Service'],
                    ],
                ],
            ],
        ];

        foreach ($hotelListings as $hotelData) {
            $subcategory = SubCategory::where('name', $hotelData['subcategory'])
                ->where('category_id', $accommodationCategory->id)
                ->first();
            
            $store = $stores[$hotelData['store_index']];
            $totalUnits = array_sum(array_column($hotelData['rooms'], 'count'));

            // Create the listing (parent/facility)
            $hotel = Listing::create([
                'user_id' => $store->user_id,
                'store_id' => $store->id,
                'category_id' => $accommodationCategory->id,
                'sub_category_id' => $subcategory->id,
                'listing_type' => 'accommodation',
                'title' => $hotelData['title'],
                'description' => $hotelData['description'],
                'slug' => \Str::slug($hotelData['title']),
                'address' => $store->address,
                'city' => $store->city,
                'province' => $store->state,
                'postal_code' => $store->zip_code,
                'latitude' => $store->latitude,
                'longitude' => $store->longitude,
                'inventory_type' => 'multiple',
                'total_units' => $totalUnits,
                'status' => 'active',
                'visibility' => 'public',
                'is_featured' => true,
                'is_verified' => true,
                'published_at' => now()->subMonths(2),
            ]);

            // Create pricing for the hotel
            $allPrices = array_column($hotelData['rooms'], 'price_per_day');
            $pricing = ListingPricing::create([
                'listing_id' => $hotel->id,
                'currency' => 'USD',
                'price_min' => min($allPrices),
                'price_max' => max($allPrices),
                'pricing_model' => 'dynamic',
                'service_fee_percentage' => 10.00,
                'cleaning_fee' => 25.00,
                'tax_percentage' => 12.00,
                'tax_included' => false,
            ]);

            // Add photos
            for ($i = 1; $i <= 5; $i++) {
                ListingPhoto::create([
                    'listing_id' => $hotel->id,
                    'photo_url' => 'https://via.placeholder.com/1920x1080/2196F3/ffffff?text=Hotel+' . $i,
                    'thumbnail_url' => 'https://via.placeholder.com/400x300/2196F3/ffffff?text=Hotel+' . $i,
                    'sort_order' => $i,
                    'is_primary' => $i === 1,
                    'storage_disk' => 'public',
                ]);
            }

            // Create room units (dynamic_form_submissions)
            foreach ($hotelData['rooms'] as $roomType) {
                for ($i = 1; $i <= $roomType['count']; $i++) {
                    $unit = DynamicFormSubmission::create([
                        'listing_id' => $hotel->id,
                        'dynamic_form_id' => null,
                        'user_id' => $store->user_id,
                        'store_id' => $store->id,
                        'status' => 'active',
                        'data' => [
                            'unit_name' => $roomType['unit_name'] . ' #' . str_pad($i, 3, '0', STR_PAD_LEFT),
                            'description' => $roomType['unit_name'] . ' with ' . $roomType['bed_type'] . ' bed',
                            'price_per_day' => $roomType['price_per_day'],
                            'max_guests' => $roomType['max_guests'],
                            'bed_type' => $roomType['bed_type'],
                            'room_size' => $roomType['room_size'],
                            'amenities' => $roomType['amenities'],
                            'check_in_time' => '15:00',
                            'check_out_time' => '11:00',
                            'min_booking_days' => 1,
                            'max_booking_days' => 30,
                            'instant_booking' => true,
                            'cancellation_policy' => 'flexible',
                        ],
                    ]);

                    // Create availability template for this room
                    $template = AvailabilityTemplate::create([
                        'listing_id' => $hotel->id,
                        'name' => $roomType['unit_name'] . ' #' . $i . ' Availability',
                        'description' => 'Daily availability for ' . $roomType['unit_name'],
                        'days_of_week' => [0,1,2,3,4,5,6],
                        'start_time' => '15:00',
                        'end_time' => '11:00',
                        'slot_duration_minutes' => 1440, // Daily
                        'base_hourly_price' => $roomType['price_per_day'] / 24,
                        'base_daily_price' => $roomType['price_per_day'],
                        'weekend_price' => $roomType['price_per_day'] * 1.2,
                        'is_recurring' => true,
                        'is_active' => true,
                        'unit_identifier' => $unit->id,
                        'created_by' => $store->user_id,
                    ]);

                    // Generate availability for next 60 days
                    $template->applyToDateRange(Carbon::today(), Carbon::today()->addDays(60));
                }
            }

            $this->command->info("✅ Created hotel '{$hotel->title}' with {$totalUnits} rooms");
            $hotels[] = $hotel;
        }

        return $hotels;
    }

    private function printSummary(array $owners, array $stores, array $hotels): void
    {
        $this->command->info("\n" . str_repeat('=', 60));
        $this->command->info('📊 HOTEL SEEDER SUMMARY (NEW ARCHITECTURE)');
        $this->command->info(str_repeat('=', 60));
        $this->command->info("👥 Hotel Owners Created: " . count($owners));
        $this->command->info("🏪 Hotel Stores Created: " . count($stores));
        $this->command->info("🏨 Hotel Listings Created: " . count($hotels));
        
        $totalRooms = 0;
        foreach ($hotels as $hotel) {
            $totalRooms += $hotel->units()->count();
        }
        $this->command->info("🛏️  Total Room Units: " . $totalRooms);
        $this->command->info(str_repeat('=', 60) . "\n");
    }
}

