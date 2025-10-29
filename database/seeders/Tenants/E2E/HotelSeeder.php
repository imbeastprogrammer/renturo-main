<?php

namespace Database\Seeders\Tenants\E2E;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Store;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Listing;
use App\Models\ListingPhoto;
use App\Models\ListingAvailability;
use App\Models\ListingUnit;
use App\Models\AvailabilityTemplate;
use App\Models\DynamicForm;
use App\Models\DynamicFormSubmission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HotelSeeder extends Seeder
{
    /**
     * Run the hotel property type seeder.
     * Demonstrates the Universal Availability System for hotel/accommodation properties.
     */
    public function run(): void
    {
        $this->command->info('🏨 Starting Hotel Property Type Seeder...');

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
            
            // Step 5: Create availability templates for hotels
            $templates = $this->createHotelTemplates($hotels);
            
            // Step 6: Generate hotel availability using templates
            $this->generateHotelAvailability($hotels, $templates);
            
            // Step 7: Create some bookings to show the system in action
            $this->createSampleBookings($hotels);
            
            DB::commit();
            
            $this->command->info('✅ Hotel Property Type Seeder completed successfully!');
            $this->printSummary($owners, $stores, $hotels, $templates);
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Hotel seeder failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function ensureHotelCategories(): void
    {
        $this->command->info('📂 Ensuring hotel categories exist...');
        
        // Create Accommodation category if it doesn't exist
        $category = Category::firstOrCreate([
            'name' => 'Accommodation'
        ]);

        // Create hotel subcategories
        $subcategories = [
            'Luxury Hotel',
            'Business Hotel', 
            'Boutique Hotel',
            'Resort',
        ];

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
                'mobile' => '+1234567890'
            ],
            [
                'first_name' => 'James',
                'last_name' => 'Wilson',
                'email' => 'james.wilson@businessinn.com',
                'mobile' => '+1234567891'
            ],
            [
                'first_name' => 'Sophie',
                'last_name' => 'Chen',
                'email' => 'sophie.chen@boutiquesuites.com',
                'mobile' => '+1234567892'
            ]
        ];

        foreach ($hotelOwners as $ownerData) {
            $owners[] = User::firstOrCreate([
                'email' => $ownerData['email']
            ], array_merge($ownerData, [
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
                'role' => 'owner'
            ]));
        }

        return $owners;
    }

    private function createHotelStores(array $owners): array
    {
        $this->command->info('🏨 Creating hotel stores...');
        
        $stores = [];
        $accommodationCategory = Category::where('name', 'Accommodation')->first();
        
        $hotelStores = [
            [
                'name' => 'Grand Plaza Hotel',
                'description' => 'Luxury 5-star hotel in the heart of the city with world-class amenities and service.',
                'subcategory' => 'Luxury Hotel',
                'owner_index' => 0
            ],
            [
                'name' => 'Business Inn Downtown',
                'description' => 'Modern business hotel perfect for corporate travelers and conferences.',
                'subcategory' => 'Business Hotel',
                'owner_index' => 1
            ],
            [
                'name' => 'Boutique Suites & Spa',
                'description' => 'Intimate boutique hotel with personalized service and unique design.',
                'subcategory' => 'Boutique Hotel',
                'owner_index' => 2
            ]
        ];

        foreach ($hotelStores as $storeData) {
            $subcategory = SubCategory::where('name', $storeData['subcategory'])
                ->where('category_id', $accommodationCategory->id)
                ->first();

            $stores[] = Store::firstOrCreate([
                'name' => $storeData['name'],
                'user_id' => $owners[$storeData['owner_index']]->id
            ], [
                'description' => $storeData['description'],
                'category_id' => $accommodationCategory->id,
                'sub_category_id' => $subcategory->id,
                'address' => 'Downtown District, Metro City',
                'city' => 'Metro City',
                'state' => 'State',
                'country' => 'Country',
                'postal_code' => '12345',
                'phone' => '+1234567890',
                'email' => strtolower(str_replace(' ', '', $storeData['name'])) . '@hotel.com',
                'website' => 'https://' . strtolower(str_replace(' ', '', $storeData['name'])) . '.com',
                'is_active' => true
            ]);
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
                'base_daily_price' => 299.00,
                'inventory_type' => 'multiple',
                'total_units' => 50,
                'amenities' => [
                    'Free WiFi', '24/7 Concierge', 'Spa & Wellness Center', 'Fine Dining Restaurant',
                    'Business Center', 'Valet Parking', 'Room Service', 'Fitness Center',
                    'Swimming Pool', 'Airport Shuttle'
                ],
                'rooms' => [
                    ['type' => 'Standard King', 'count' => 20, 'price_modifier' => 0],
                    ['type' => 'Deluxe Suite', 'count' => 15, 'price_modifier' => 100],
                    ['type' => 'Executive Suite', 'count' => 10, 'price_modifier' => 200],
                    ['type' => 'Presidential Suite', 'count' => 5, 'price_modifier' => 500]
                ]
            ],
            [
                'title' => 'Business Inn Downtown - Corporate Comfort',
                'description' => 'Modern business hotel designed for the corporate traveler with meeting facilities and high-speed internet.',
                'store_index' => 1,
                'subcategory' => 'Business Hotel',
                'base_daily_price' => 179.00,
                'inventory_type' => 'multiple',
                'total_units' => 80,
                'amenities' => [
                    'Free WiFi', 'Business Center', 'Meeting Rooms', 'Express Check-in/out',
                    'Fitness Center', 'Continental Breakfast', 'Parking', 'Laundry Service'
                ],
                'rooms' => [
                    ['type' => 'Standard Room', 'count' => 40, 'price_modifier' => 0],
                    ['type' => 'Business Room', 'count' => 25, 'price_modifier' => 50],
                    ['type' => 'Junior Suite', 'count' => 15, 'price_modifier' => 100]
                ]
            ],
            [
                'title' => 'Boutique Suites & Spa - Unique Experience',
                'description' => 'Intimate boutique hotel with individually designed suites and personalized spa services.',
                'store_index' => 2,
                'subcategory' => 'Boutique Hotel',
                'base_daily_price' => 249.00,
                'inventory_type' => 'multiple',
                'total_units' => 25,
                'amenities' => [
                    'Free WiFi', 'Spa Services', 'Personalized Concierge', 'Gourmet Restaurant',
                    'Wine Bar', 'Rooftop Terrace', 'Yoga Studio', 'Organic Breakfast'
                ],
                'rooms' => [
                    ['type' => 'Garden Suite', 'count' => 10, 'price_modifier' => 0],
                    ['type' => 'City View Suite', 'count' => 8, 'price_modifier' => 75],
                    ['type' => 'Penthouse Suite', 'count' => 7, 'price_modifier' => 150]
                ]
            ]
        ];

        foreach ($hotelListings as $hotelData) {
            $subcategory = SubCategory::where('name', $hotelData['subcategory'])
                ->where('category_id', $accommodationCategory->id)
                ->first();

            $hotel = Listing::create([
                'store_id' => $stores[$hotelData['store_index']]->id,
                'category_id' => $accommodationCategory->id,
                'sub_category_id' => $subcategory->id,
                'title' => $hotelData['title'],
                'description' => $hotelData['description'],
                'slug' => \Str::slug($hotelData['title']),
                'base_daily_price' => $hotelData['base_daily_price'],
                'inventory_type' => $hotelData['inventory_type'],
                'total_units' => $hotelData['total_units'],
                'amenities' => $hotelData['amenities'],
                'address' => $stores[$hotelData['store_index']]->address,
                'city' => $stores[$hotelData['store_index']]->city,
                'state' => $stores[$hotelData['store_index']]->state,
                'country' => $stores[$hotelData['store_index']]->country,
                'postal_code' => $stores[$hotelData['store_index']]->postal_code,
                'latitude' => 40.7128 + (rand(-100, 100) / 1000),
                'longitude' => -74.0060 + (rand(-100, 100) / 1000),
                'is_active' => true,
                'is_featured' => true
            ]);

            // Create room units
            foreach ($hotelData['rooms'] as $roomType) {
                for ($i = 1; $i <= $roomType['count']; $i++) {
                    ListingUnit::create([
                        'listing_id' => $hotel->id,
                        'unit_identifier' => strtolower(str_replace(' ', '-', $roomType['type'])) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                        'unit_name' => $roomType['type'] . ' #' . $i,
                        'unit_features' => [
                            'room_type' => $roomType['type'],
                            'max_occupancy' => $roomType['type'] === 'Presidential Suite' ? 4 : ($roomType['type'] === 'Executive Suite' ? 3 : 2),
                            'bed_type' => in_array($roomType['type'], ['Presidential Suite', 'Executive Suite']) ? 'King + Sofa Bed' : 'King',
                            'room_size' => $roomType['type'] === 'Presidential Suite' ? '800 sq ft' : ($roomType['type'] === 'Executive Suite' ? '600 sq ft' : '400 sq ft'),
                            'view' => rand(0, 1) ? 'City View' : 'Garden View'
                        ],
                        'price_modifier' => $roomType['price_modifier'],
                        'status' => 'available'
                    ]);
                }
            }

            $hotels[] = $hotel;
        }

        return $hotels;
    }

    private function createHotelTemplates(array $hotels): array
    {
        $this->command->info('📋 Creating hotel availability templates...');
        
        $templates = [];

        foreach ($hotels as $hotel) {
            // Standard availability template (all days)
            $templates[] = AvailabilityTemplate::create([
                'listing_id' => $hotel->id,
                'name' => 'Standard Hotel Availability',
                'days_of_week' => [0, 1, 2, 3, 4, 5, 6], // All days
                'start_time' => '15:00:00', // Check-in time
                'end_time' => '11:00:00', // Check-out time (next day)
                'slot_duration_minutes' => 1440, // Daily slots
                'base_hourly_price' => null, // Not used for daily
                'hourly_price' => null,
                'daily_price' => $hotel->base_daily_price,
                'booking_rules' => [
                    'min_stay_nights' => 1,
                    'max_stay_nights' => 30,
                    'advance_booking_days' => 365,
                    'cancellation_policy' => '24 hours',
                    'check_in_time' => '15:00',
                    'check_out_time' => '11:00',
                    'extra_guest_fee' => 25.00,
                    'pet_policy' => 'Pets allowed with $50 fee'
                ],
                'is_active' => true
            ]);

            // Weekend premium template
            $templates[] = AvailabilityTemplate::create([
                'listing_id' => $hotel->id,
                'name' => 'Weekend Premium Rates',
                'days_of_week' => [5, 6], // Friday, Saturday
                'start_time' => '15:00:00',
                'end_time' => '11:00:00',
                'slot_duration_minutes' => 1440,
                'base_hourly_price' => null,
                'hourly_price' => null,
                'daily_price' => $hotel->base_daily_price * 1.3, // 30% premium
                'booking_rules' => [
                    'min_stay_nights' => 2, // Minimum 2 nights on weekends
                    'max_stay_nights' => 30,
                    'advance_booking_days' => 365,
                    'cancellation_policy' => '48 hours',
                    'check_in_time' => '15:00',
                    'check_out_time' => '11:00'
                ],
                'is_active' => true
            ]);
        }

        return $templates;
    }

    private function generateHotelAvailability(array $hotels, array $templates): void
    {
        $this->command->info('📅 Generating hotel availability for the next 90 days...');
        
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays(90);

        foreach ($hotels as $hotel) {
            $hotelTemplates = array_filter($templates, fn($t) => $t->listing_id === $hotel->id);
            
            // Get all room units for this hotel
            $roomUnits = $hotel->units;
            
            $current = $startDate->copy();
            while ($current->lte($endDate)) {
                $dayOfWeek = $current->dayOfWeek;
                
                // Check if it's weekend (use weekend template) or weekday (use standard template)
                $isWeekend = in_array($dayOfWeek, [5, 6]); // Friday, Saturday
                $template = null;
                
                foreach ($hotelTemplates as $t) {
                    if ($isWeekend && $t->name === 'Weekend Premium Rates') {
                        $template = $t;
                        break;
                    } elseif (!$isWeekend && $t->name === 'Standard Hotel Availability') {
                        $template = $t;
                        break;
                    }
                }
                
                if ($template && in_array($dayOfWeek, $template->days_of_week)) {
                    // Create availability for each room unit
                    foreach ($roomUnits as $unit) {
                        // Calculate pricing with unit modifier
                        $basePrice = $template->daily_price;
                        $finalPrice = $basePrice + $unit->price_modifier;
                        
                        // Add holiday pricing for special dates
                        $holidayPrice = null;
                        if ($current->month === 12 && $current->day >= 20) { // Christmas season
                            $holidayPrice = $finalPrice * 1.5;
                        } elseif ($current->month === 1 && $current->day === 1) { // New Year
                            $holidayPrice = $finalPrice * 1.8;
                        }
                        
                        ListingAvailability::create([
                            'listing_id' => $hotel->id,
                            'available_date' => $current->format('Y-m-d'),
                            'start_time' => $template->start_time,
                            'end_time' => $template->end_time,
                            'unit_identifier' => $unit->unit_identifier,
                            'status' => 'available',
                            'duration_type' => 'daily',
                            'slot_duration_minutes' => 1440,
                            'available_units' => 1,
                            'weekend_price' => $isWeekend ? $finalPrice : null,
                            'holiday_price' => $holidayPrice,
                            'booking_rules' => $template->booking_rules,
                            'metadata' => [
                                'room_type' => $unit->unit_features['room_type'] ?? 'Standard',
                                'max_occupancy' => $unit->unit_features['max_occupancy'] ?? 2,
                                'bed_type' => $unit->unit_features['bed_type'] ?? 'King',
                                'room_size' => $unit->unit_features['room_size'] ?? '400 sq ft',
                                'view' => $unit->unit_features['view'] ?? 'City View',
                                'check_in_time' => '15:00',
                                'check_out_time' => '11:00'
                            ],
                            'created_by' => $hotel->store->user_id
                        ]);
                    }
                }
                
                $current->addDay();
            }
        }
    }

    private function createSampleBookings(array $hotels): void
    {
        $this->command->info('📋 Creating sample bookings to demonstrate the system...');
        
        // Create some sample bookings for the next few days
        $bookingDates = [
            Carbon::now()->addDays(2),
            Carbon::now()->addDays(5),
            Carbon::now()->addDays(8)
        ];

        foreach ($bookingDates as $date) {
            // Pick a random hotel
            $hotel = $hotels[array_rand($hotels)];
            
            // Get available slots for this date
            $availableSlots = $hotel->availability()
                ->where('available_date', $date->format('Y-m-d'))
                ->where('status', 'available')
                ->limit(3) // Book 3 rooms
                ->get();

            foreach ($availableSlots as $slot) {
                $slot->update([
                    'status' => 'booked',
                    'updated_by' => $hotel->store->user_id
                ]);
            }
        }
    }

    private function printSummary(array $owners, array $stores, array $hotels, array $templates): void
    {
        $totalRooms = 0;
        $totalAvailability = 0;
        $totalBookings = 0;

        foreach ($hotels as $hotel) {
            $totalRooms += $hotel->units->count();
            $totalAvailability += $hotel->availability()->count();
            $totalBookings += $hotel->availability()->where('status', 'booked')->count();
        }

        $this->command->info('');
        $this->command->info('🎉 ===== HOTEL SEEDER SUMMARY =====');
        $this->command->info('👥 Hotel Owners Created: ' . count($owners));
        $this->command->info('🏨 Hotel Stores Created: ' . count($stores));
        $this->command->info('🏢 Hotel Listings Created: ' . count($hotels));
        $this->command->info('🚪 Total Room Units: ' . $totalRooms);
        $this->command->info('📋 Availability Templates: ' . count($templates));
        $this->command->info('📅 Total Availability Slots: ' . $totalAvailability);
        $this->command->info('📋 Sample Bookings Created: ' . $totalBookings);
        $this->command->info('');
        $this->command->info('✨ Hotel properties now demonstrate:');
        $this->command->info('   • Daily room bookings with check-in/out times');
        $this->command->info('   • Multiple room types with different pricing');
        $this->command->info('   • Weekend premium pricing');
        $this->command->info('   • Holiday season pricing');
        $this->command->info('   • Multi-unit inventory management');
        $this->command->info('   • Hotel-specific amenities and policies');
        $this->command->info('=====================================');
    }
}
