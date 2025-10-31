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

class EventVenueSeeder extends Seeder
{
    /**
     * Run the event venue property type seeder.
     * Demonstrates the Universal Availability System for event and venue rentals.
     */
    public function run(): void
    {
        $this->command->info('🎪 Starting Event Venue Property Type Seeder...');

        DB::beginTransaction();
        
        try {
            // Step 1: Ensure categories exist
            $this->ensureEventCategories();
            
            // Step 2: Create venue owner users
            $owners = $this->createVenueOwners();
            
            // Step 3: Create venue stores
            $stores = $this->createVenueStores($owners);
            
            // Step 4: Create venue listings with space units
            $venues = $this->createVenueListings($stores);
            
            // Step 5: Create availability templates for venues
            $templates = $this->createVenueTemplates($venues);
            
            // Step 6: Generate venue availability using templates
            $this->generateVenueAvailability($venues, $templates);
            
            // Step 7: Create some bookings to show the system in action
            $this->createSampleBookings($venues);
            
            DB::commit();
            
            $this->command->info('✅ Event Venue Property Type Seeder completed successfully!');
            $this->printSummary($owners, $stores, $venues, $templates);
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Event venue seeder failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function ensureEventCategories(): void
    {
        $this->command->info('📂 Ensuring event venue categories exist...');
        
        // Create Events category if it doesn't exist
        $category = Category::firstOrCreate([
            'name' => 'Events & Venues'
        ]);

        // Create event venue subcategories
        $subcategories = [
            'Wedding Venue',
            'Conference Center',
            'Party Hall',
            'Outdoor Venue',
        ];

        foreach ($subcategories as $subcatName) {
            SubCategory::firstOrCreate([
                'name' => $subcatName,
                'category_id' => $category->id
            ]);
        }
    }

    private function createVenueOwners(): array
    {
        $this->command->info('👥 Creating venue owner users...');
        
        $owners = [];
        $venueOwners = [
            [
                'first_name' => 'Isabella',
                'last_name' => 'Martinez',
                'email' => 'isabella.martinez@grandballroom.com',
                'mobile_number' => '+1234567896'
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Johnson',
                'email' => 'robert.johnson@conferenceplus.com',
                'mobile_number' => '+1234567897'
            ],
            [
                'first_name' => 'Priya',
                'last_name' => 'Patel',
                'email' => 'priya.patel@gardenparty.com',
                'mobile_number' => '+1234567898'
            ]
        ];

        foreach ($venueOwners as $ownerData) {
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

    private function createVenueStores(array $owners): array
    {
        $this->command->info('🎪 Creating venue stores...');
        
        $stores = [];
        $eventsCategory = Category::where('name', 'Events & Venues')->first();
        
        $venueStores = [
            [
                'name' => 'Grand Ballroom Events',
                'description' => 'Elegant ballroom and wedding venue with full-service event planning and catering.',
                'subcategory' => 'Wedding Venue',
                'owner_index' => 0
            ],
            [
                'name' => 'Conference Plus Center',
                'description' => 'State-of-the-art conference and meeting facilities with advanced AV technology.',
                'subcategory' => 'Conference Center',
                'owner_index' => 1
            ],
            [
                'name' => 'Garden Party Venues',
                'description' => 'Beautiful outdoor venues and garden spaces perfect for any celebration.',
                'subcategory' => 'Outdoor Venue',
                'owner_index' => 2
            ]
        ];

        foreach ($venueStores as $storeData) {
            $subcategory = SubCategory::where('name', $storeData['subcategory'])
                ->where('category_id', $eventsCategory->id)
                ->first();

            $stores[] = Store::firstOrCreate([
                'name' => $storeData['name'],
                'user_id' => $owners[$storeData['owner_index']]->id
            ], [
                'url' => strtolower(str_replace(' ', '-', $storeData['name'])),
                'about' => $storeData['description'],
                'category_id' => $eventsCategory->id,
                'sub_category_id' => $subcategory->id,
                'address' => 'Events District, Metro City',
                'city' => 'Metro City',
                'state' => 'State',
                'zip_code' => '12345',
                'latitude' => 14.5995 + (rand(-100, 100) / 1000),
                'longitude' => 120.9842 + (rand(-100, 100) / 1000)
            ]);
        }

        return $stores;
    }

    private function createVenueListings(array $stores): array
    {
        $this->command->info('🏛️ Creating venue listings with event spaces...');
        
        $venues = [];
        $eventsCategory = Category::where('name', 'Events & Venues')->first();

        $venueListings = [
            [
                'title' => 'Grand Ballroom - Luxury Wedding Venue',
                'description' => 'Stunning ballroom with crystal chandeliers, marble floors, and capacity for up to 300 guests.',
                'store_index' => 0,
                'subcategory' => 'Wedding Venue',
                'base_hourly_price' => 250.00,
                'base_daily_price' => 3500.00,
                'inventory_type' => 'multiple',
                'total_units' => 5,
                'amenities' => [
                    'Full Catering Kitchen', 'Bridal Suite', 'Sound System', 'Dance Floor',
                    'Wedding Coordinator', 'Valet Parking', 'Photography Areas', 'Climate Control',
                    'Bar Service', 'Floral Arrangements'
                ],
                'spaces' => [
                    ['name' => 'Grand Ballroom', 'capacity' => 300, 'price_modifier' => 0],
                    ['name' => 'Garden Pavilion', 'capacity' => 150, 'price_modifier' => -200],
                    ['name' => 'Intimate Chapel', 'capacity' => 80, 'price_modifier' => -400],
                    ['name' => 'Rooftop Terrace', 'capacity' => 120, 'price_modifier' => -150],
                    ['name' => 'VIP Lounge', 'capacity' => 50, 'price_modifier' => -500]
                ]
            ],
            [
                'title' => 'Conference Plus - Professional Meeting Spaces',
                'description' => 'Modern conference facilities with cutting-edge technology and flexible room configurations.',
                'store_index' => 1,
                'subcategory' => 'Conference Center',
                'base_hourly_price' => 125.00,
                'base_daily_price' => 899.00,
                'inventory_type' => 'multiple',
                'total_units' => 8,
                'amenities' => [
                    'High-Speed WiFi', 'AV Equipment', 'Video Conferencing', 'Whiteboards',
                    'Coffee Service', 'Business Center', 'Parking', 'Catering Options',
                    'Technical Support', 'Flexible Seating'
                ],
                'spaces' => [
                    ['name' => 'Main Conference Hall', 'capacity' => 200, 'price_modifier' => 0],
                    ['name' => 'Executive Boardroom', 'capacity' => 20, 'price_modifier' => -200],
                    ['name' => 'Training Room A', 'capacity' => 50, 'price_modifier' => -300],
                    ['name' => 'Training Room B', 'capacity' => 50, 'price_modifier' => -300],
                    ['name' => 'Seminar Room', 'capacity' => 30, 'price_modifier' => -400],
                    ['name' => 'Workshop Space', 'capacity' => 40, 'price_modifier' => -350],
                    ['name' => 'Breakout Room 1', 'capacity' => 15, 'price_modifier' => -500],
                    ['name' => 'Breakout Room 2', 'capacity' => 15, 'price_modifier' => -500]
                ]
            ],
            [
                'title' => 'Garden Party - Outdoor Event Venues',
                'description' => 'Beautiful outdoor spaces with natural settings perfect for any celebration or gathering.',
                'store_index' => 2,
                'subcategory' => 'Outdoor Venue',
                'base_hourly_price' => 180.00,
                'base_daily_price' => 1299.00,
                'inventory_type' => 'multiple',
                'total_units' => 4,
                'amenities' => [
                    'Natural Landscaping', 'Outdoor Lighting', 'Weather Protection', 'Restroom Facilities',
                    'Parking Area', 'Catering Prep Area', 'Sound System', 'Seating Areas',
                    'Photography Backdrops', 'Event Coordination'
                ],
                'spaces' => [
                    ['name' => 'Rose Garden', 'capacity' => 180, 'price_modifier' => 0],
                    ['name' => 'Lakeside Pavilion', 'capacity' => 250, 'price_modifier' => 200],
                    ['name' => 'Woodland Grove', 'capacity' => 100, 'price_modifier' => -300],
                    ['name' => 'Sunset Terrace', 'capacity' => 120, 'price_modifier' => -100]
                ]
            ]
        ];

        foreach ($venueListings as $venueData) {
            $subcategory = SubCategory::where('name', $venueData['subcategory'])
                ->where('category_id', $eventsCategory->id)
                ->first();

            $venue = Listing::create([
                'user_id' => $stores[$venueData['store_index']]->user_id,
                'store_id' => $stores[$venueData['store_index']]->id,
                'category_id' => $eventsCategory->id,
                'sub_category_id' => $subcategory->id,
                'title' => $venueData['title'],
                'description' => $venueData['description'],
                'slug' => \Str::slug($venueData['title']),
                'base_hourly_price' => $venueData['base_hourly_price'],
                'base_daily_price' => $venueData['base_daily_price'],
                'inventory_type' => $venueData['inventory_type'],
                'total_units' => $venueData['total_units'],
                'amenities' => $venueData['amenities'],
                'address' => $stores[$venueData['store_index']]->address,
                'city' => $stores[$venueData['store_index']]->city,
                'province' => $stores[$venueData['store_index']]->state,
                'postal_code' => $stores[$venueData['store_index']]->zip_code,
                'latitude' => 14.5995 + (rand(-100, 100) / 1000),
                'longitude' => 120.9842 + (rand(-100, 100) / 1000),
                'status' => 'active',
                'is_featured' => true
            ]);

            // Create event space units
            foreach ($venueData['spaces'] as $space) {
                ListingUnit::create([
                    'listing_id' => $venue->id,
                    'unit_identifier' => strtolower(str_replace(' ', '-', $space['name'])),
                    'unit_name' => $space['name'],
                    'unit_features' => [
                        'space_type' => $space['name'],
                        'max_capacity' => $space['capacity'],
                        'setup_styles' => ['Theater', 'Classroom', 'Banquet', 'Reception', 'U-Shape', 'Boardroom'],
                        'square_footage' => $space['capacity'] * 8, // Rough estimate
                        'ceiling_height' => rand(10, 20) . ' feet',
                        'natural_light' => rand(0, 1) ? 'Yes' : 'No',
                        'accessibility' => 'ADA Compliant',
                        'setup_time_hours' => rand(2, 6),
                        'cleanup_time_hours' => rand(1, 3)
                    ],
                    'price_modifier' => $space['price_modifier'],
                    'status' => 'active',
                    'created_by' => $venue->user_id
                ]);
            }

            $venues[] = $venue;
        }

        return $venues;
    }

    private function createVenueTemplates(array $venues): array
    {
        $this->command->info('📋 Creating venue availability templates...');
        
        $templates = [];

        foreach ($venues as $venue) {
            // Full day event template
            $templates[] = AvailabilityTemplate::create([
                'listing_id' => $venue->id,
                'name' => 'Full Day Event',
                'days_of_week' => [0, 1, 2, 3, 4, 5, 6], // All days
                'start_time' => '08:00:00', // Setup starts
                'end_time' => '23:00:00', // Event ends
                'slot_duration_minutes' => 900, // 15-hour blocks
                'base_hourly_price' => $venue->base_daily_price, // Required field
                'base_daily_price' => $venue->base_daily_price,
                'booking_rules' => [
                    'min_duration_hours' => 6,
                    'max_duration_hours' => 15,
                    'setup_time_included' => true,
                    'cleanup_time_included' => true,
                    'advance_booking_days' => 365,
                    'cancellation_policy' => '30 days for full refund, 14 days for 50% refund',
                    'deposit_required' => '50% of total cost',
                    'final_payment_due' => '7 days before event',
                    'overtime_rate' => 150.00
                ],
                'is_active' => true,
                'created_by' => $venue->user_id
            ]);

            // Half day event template
            $templates[] = AvailabilityTemplate::create([
                'listing_id' => $venue->id,
                'name' => 'Half Day Event',
                'days_of_week' => [1, 2, 3, 4, 5], // Weekdays only
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'slot_duration_minutes' => 480, // 8-hour blocks
                'base_hourly_price' => $venue->base_daily_price * 0.6, // Required field
                'base_daily_price' => $venue->base_daily_price * 0.6, // 60% of full day rate
                'booking_rules' => [
                    'min_duration_hours' => 4,
                    'max_duration_hours' => 8,
                    'setup_time_included' => true,
                    'cleanup_time_included' => true,
                    'advance_booking_days' => 180,
                    'cancellation_policy' => '14 days for full refund, 7 days for 50% refund'
                ],
                'is_active' => true,
                'created_by' => $venue->user_id
            ]);

            // Hourly meeting template (for conference centers)
            if (strpos($venue->title, 'Conference') !== false) {
                $templates[] = AvailabilityTemplate::create([
                    'listing_id' => $venue->id,
                    'name' => 'Hourly Meeting Rental',
                    'days_of_week' => [1, 2, 3, 4, 5], // Weekdays
                    'start_time' => '08:00:00',
                    'end_time' => '18:00:00',
                    'slot_duration_minutes' => 60, // 1-hour slots
                    'base_hourly_price' => $venue->base_hourly_price,
                    'base_daily_price' => null,
                    'booking_rules' => [
                        'min_duration_hours' => 1,
                        'max_duration_hours' => 8,
                        'advance_booking_hours' => 24,
                        'cancellation_policy' => '4 hours for full refund',
                        'setup_time_minutes' => 15,
                        'cleanup_time_minutes' => 15
                    ],
                    'is_active' => true,
                    'created_by' => $venue->user_id
                ]);
            }

            // Weekend premium template
            $templates[] = AvailabilityTemplate::create([
                'listing_id' => $venue->id,
                'name' => 'Weekend Premium Events',
                'days_of_week' => [5, 6, 0], // Friday, Saturday, Sunday
                'start_time' => '08:00:00',
                'end_time' => '23:00:00',
                'slot_duration_minutes' => 900,
                'base_hourly_price' => $venue->base_daily_price * 1.4, // Required field
                'base_daily_price' => $venue->base_daily_price * 1.4, // 40% premium
                'booking_rules' => [
                    'min_duration_hours' => 8,
                    'max_duration_hours' => 15,
                    'advance_booking_days' => 365,
                    'cancellation_policy' => '60 days for full refund, 30 days for 50% refund',
                    'weekend_surcharge' => 'Included in rate',
                    'deposit_required' => '60% of total cost'
                ],
                'is_active' => true,
                'created_by' => $venue->user_id
            ]);
        }

        return $templates;
    }

    private function generateVenueAvailability(array $venues, array $templates): void
    {
        $this->command->info('📅 Generating venue availability for the next 120 days...');
        
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays(120);

        foreach ($venues as $venue) {
            $venueTemplates = array_filter($templates, fn($t) => $t->listing_id === $venue->id);
            
            // Get all space units for this venue
            $spaceUnits = $venue->units;
            
            $current = $startDate->copy();
            while ($current->lte($endDate)) {
                $dayOfWeek = $current->dayOfWeek;
                
                foreach ($venueTemplates as $template) {
                    if (in_array($dayOfWeek, $template->days_of_week)) {
                        // Create availability for each space unit
                        foreach ($spaceUnits as $unit) {
                            // Calculate pricing with unit modifier
                            $basePrice = $template->hourly_price ?? $template->daily_price;
                            $finalPrice = $basePrice + $unit->price_modifier;
                            
                            // Add holiday pricing for special dates
                            $holidayPrice = null;
                            if ($current->month === 12 && $current->day >= 20) { // Christmas season
                                $holidayPrice = $finalPrice * 1.6;
                            } elseif ($current->month === 6 && in_array($current->day, [15, 16, 17, 18, 19, 20, 21])) { // Wedding season
                                $holidayPrice = $finalPrice * 1.3;
                            } elseif ($current->month === 2 && $current->day === 14) { // Valentine's Day
                                $holidayPrice = $finalPrice * 1.4;
                            }
                            
                            // Determine if it's weekend pricing
                            $isWeekend = in_array($dayOfWeek, [5, 6, 0]);
                            $weekendPrice = $isWeekend && $template->name === 'Weekend Premium Events' ? $finalPrice : null;
                            
                            if ($template->name === 'Hourly Meeting Rental') {
                                // Create hourly slots for conference centers
                                $startTime = Carbon::parse($template->start_time);
                                $endTime = Carbon::parse($template->end_time);
                                
                                while ($startTime->lt($endTime)) {
                                    $slotEnd = $startTime->copy()->addHour();
                                    
                                    ListingAvailability::create([
                                        'listing_id' => $venue->id,
                                        'available_date' => $current->format('Y-m-d'),
                                        'start_time' => $startTime->format('H:i:s'),
                                        'end_time' => $slotEnd->format('H:i:s'),
                                        'unit_identifier' => $unit->unit_identifier,
                                        'status' => 'available',
                                        'duration_type' => 'hourly',
                                        'slot_duration_minutes' => 60,
                                        'available_units' => 1,
                                        'peak_hour_price' => $startTime->hour >= 16 ? $finalPrice * 1.15 : null, // Late afternoon peak
                                        'weekend_price' => $weekendPrice,
                                        'holiday_price' => $holidayPrice,
                                        'booking_rules' => $template->booking_rules,
                                        'metadata' => [
                                            'space_name' => $unit->unit_name,
                                            'max_capacity' => $unit->unit_features['max_capacity'] ?? 50,
                                            'space_type' => $unit->unit_features['space_type'] ?? 'Meeting Room',
                                            'setup_styles' => $unit->unit_features['setup_styles'] ?? ['Theater', 'Classroom'],
                                            'square_footage' => $unit->unit_features['square_footage'] ?? 400,
                                            'setup_time_hours' => $unit->unit_features['setup_time_hours'] ?? 1,
                                            'rental_type' => 'hourly'
                                        ],
                                        'created_by' => $venue->user_id
                                    ]);
                                    
                                    $startTime->addHour();
                                }
                            } else {
                                // Create daily/block slots
                                $durationType = $template->name === 'Half Day Event' ? 'daily' : 'daily';
                                
                                ListingAvailability::create([
                                    'listing_id' => $venue->id,
                                    'available_date' => $current->format('Y-m-d'),
                                    'start_time' => $template->start_time,
                                    'end_time' => $template->end_time,
                                    'unit_identifier' => $unit->unit_identifier,
                                    'status' => 'available',
                                    'duration_type' => $durationType,
                                    'slot_duration_minutes' => $template->slot_duration_minutes,
                                    'available_units' => 1,
                                    'weekend_price' => $weekendPrice,
                                    'holiday_price' => $holidayPrice,
                                    'booking_rules' => $template->booking_rules,
                                    'metadata' => [
                                        'space_name' => $unit->unit_name,
                                        'max_capacity' => $unit->unit_features['max_capacity'] ?? 100,
                                        'space_type' => $unit->unit_features['space_type'] ?? 'Event Space',
                                        'setup_styles' => $unit->unit_features['setup_styles'] ?? ['Banquet', 'Reception'],
                                        'square_footage' => $unit->unit_features['square_footage'] ?? 800,
                                        'setup_time_hours' => $unit->unit_features['setup_time_hours'] ?? 3,
                                        'cleanup_time_hours' => $unit->unit_features['cleanup_time_hours'] ?? 2,
                                        'event_duration' => $template->name,
                                        'rental_type' => 'event'
                                    ],
                                    'created_by' => $venue->user_id
                                ]);
                            }
                        }
                        
                        break; // Only use one template per day per space
                    }
                }
                
                $current->addDay();
            }
        }
    }

    private function createSampleBookings(array $venues): void
    {
        $this->command->info('📋 Creating sample bookings to demonstrate the system...');
        
        // Create some sample bookings for upcoming events
        $bookingDates = [
            Carbon::now()->addDays(10), // Corporate meeting
            Carbon::now()->addDays(25), // Wedding
            Carbon::now()->addDays(45)  // Conference
        ];

        foreach ($bookingDates as $date) {
            // Pick a random venue
            $venue = $venues[array_rand($venues)];
            
            // Get available slots for this date
            $availableSlots = $venue->availability()
                ->where('available_date', $date->format('Y-m-d'))
                ->where('status', 'available')
                ->limit(2) // Book 2 spaces
                ->get();

            foreach ($availableSlots as $slot) {
                $slot->update([
                    'status' => 'booked',
                    'updated_by' => $venue->user_id
                ]);
            }
        }
    }

    private function printSummary(array $owners, array $stores, array $venues, array $templates): void
    {
        $totalSpaces = 0;
        $totalAvailability = 0;
        $totalBookings = 0;

        foreach ($venues as $venue) {
            $totalSpaces += $venue->units->count();
            $totalAvailability += $venue->availability()->count();
            $totalBookings += $venue->availability()->where('status', 'booked')->count();
        }

        $this->command->info('');
        $this->command->info('🎉 ===== EVENT VENUE SEEDER SUMMARY =====');
        $this->command->info('👥 Venue Owners Created: ' . count($owners));
        $this->command->info('🎪 Venue Stores Created: ' . count($stores));
        $this->command->info('🏛️ Venue Listings Created: ' . count($venues));
        $this->command->info('🏢 Total Event Spaces: ' . $totalSpaces);
        $this->command->info('📋 Availability Templates: ' . count($templates));
        $this->command->info('📅 Total Availability Slots: ' . $totalAvailability);
        $this->command->info('📋 Sample Bookings Created: ' . $totalBookings);
        $this->command->info('');
        $this->command->info('✨ Event venues now demonstrate:');
        $this->command->info('   • Full-day and half-day event bookings');
        $this->command->info('   • Hourly meeting room rentals');
        $this->command->info('   • Multiple event spaces with different capacities');
        $this->command->info('   • Weekend and holiday premium pricing');
        $this->command->info('   • Event-specific metadata and policies');
        $this->command->info('   • Flexible setup and cleanup time management');
        $this->command->info('==========================================');
    }
}
