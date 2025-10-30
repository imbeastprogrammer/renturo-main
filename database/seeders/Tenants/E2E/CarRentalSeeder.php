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

class CarRentalSeeder extends Seeder
{
    /**
     * Run the car rental property type seeder.
     * Demonstrates the Universal Availability System for transportation/vehicle rentals.
     */
    public function run(): void
    {
        $this->command->info('🚗 Starting Car Rental Property Type Seeder...');

        DB::beginTransaction();
        
        try {
            // Step 1: Ensure categories exist
            $this->ensureTransportationCategories();
            
            // Step 2: Create car rental company owners
            $owners = $this->createCarRentalOwners();
            
            // Step 3: Create car rental stores
            $stores = $this->createCarRentalStores($owners);
            
            // Step 4: Create car rental listings with vehicle units
            $carRentals = $this->createCarRentalListings($stores);
            
            // Step 5: Create availability templates for car rentals
            $templates = $this->createCarRentalTemplates($carRentals);
            
            // Step 6: Generate car rental availability using templates
            $this->generateCarRentalAvailability($carRentals, $templates);
            
            // Step 7: Create some bookings to show the system in action
            $this->createSampleBookings($carRentals);
            
            DB::commit();
            
            $this->command->info('✅ Car Rental Property Type Seeder completed successfully!');
            $this->printSummary($owners, $stores, $carRentals, $templates);
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Car rental seeder failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function ensureTransportationCategories(): void
    {
        $this->command->info('📂 Ensuring transportation categories exist...');
        
        // Create Transportation category if it doesn't exist
        $category = Category::firstOrCreate([
            'name' => 'Transportation'
        ]);

        // Create car rental subcategories
        $subcategories = [
            'Economy Car',
            'Luxury Car',
            'SUV Rental',
            'Van Rental',
        ];

        foreach ($subcategories as $subcatName) {
            SubCategory::firstOrCreate([
                'name' => $subcatName,
                'category_id' => $category->id
            ]);
        }
    }

    private function createCarRentalOwners(): array
    {
        $this->command->info('👥 Creating car rental company owners...');
        
        $owners = [];
        $rentalOwners = [
            [
                'first_name' => 'Michael',
                'last_name' => 'Thompson',
                'email' => 'michael.thompson@speedyrentals.com',
                'mobile_number' => '+1234567893'
            ],
            [
                'first_name' => 'Elena',
                'last_name' => 'Vasquez',
                'email' => 'elena.vasquez@luxuryrides.com',
                'mobile_number' => '+1234567894'
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Kim',
                'email' => 'david.kim@familyvans.com',
                'mobile_number' => '+1234567895'
            ]
        ];

        foreach ($rentalOwners as $ownerData) {
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

    private function createCarRentalStores(array $owners): array
    {
        $this->command->info('🚗 Creating car rental stores...');
        
        $stores = [];
        $transportationCategory = Category::where('name', 'Transportation')->first();
        
        $rentalStores = [
            [
                'name' => 'Speedy Car Rentals',
                'description' => 'Fast and affordable car rentals with a wide selection of economy and mid-size vehicles.',
                'subcategory' => 'Economy Car',
                'owner_index' => 0
            ],
            [
                'name' => 'Luxury Rides Premium',
                'description' => 'Premium luxury car rental service featuring exotic and high-end vehicles.',
                'subcategory' => 'Luxury Car',
                'owner_index' => 1
            ],
            [
                'name' => 'Family Van Rentals',
                'description' => 'Spacious vans and SUVs perfect for family trips and group transportation.',
                'subcategory' => 'Van Rental',
                'owner_index' => 2
            ]
        ];

        foreach ($rentalStores as $storeData) {
            $subcategory = SubCategory::where('name', $storeData['subcategory'])
                ->where('category_id', $transportationCategory->id)
                ->first();

            $stores[] = Store::firstOrCreate([
                'name' => $storeData['name'],
                'user_id' => $owners[$storeData['owner_index']]->id
            ], [
                'url' => strtolower(str_replace(' ', '-', $storeData['name'])),
                'about' => $storeData['description'],
                'category_id' => $transportationCategory->id,
                'sub_category_id' => $subcategory->id,
                'address' => 'Airport District, Metro City',
                'city' => 'Metro City',
                'state' => 'State',
                'zip_code' => '12345',
                'latitude' => 14.5995 + (rand(-100, 100) / 1000),
                'longitude' => 120.9842 + (rand(-100, 100) / 1000)
            ]);
        }

        return $stores;
    }

    private function createCarRentalListings(array $stores): array
    {
        $this->command->info('🚙 Creating car rental listings with vehicle units...');
        
        $carRentals = [];
        $transportationCategory = Category::where('name', 'Transportation')->first();

        $rentalListings = [
            [
                'title' => 'Speedy Economy Car Fleet',
                'description' => 'Reliable and fuel-efficient economy cars perfect for city driving and short trips.',
                'store_index' => 0,
                'subcategory' => 'Economy Car',
                'base_hourly_price' => 12.00,
                'base_daily_price' => 89.00,
                'inventory_type' => 'multiple',
                'total_units' => 25,
                'amenities' => [
                    'GPS Navigation', 'Bluetooth Connectivity', 'Air Conditioning', 'Automatic Transmission',
                    'Fuel Efficient', '24/7 Roadside Assistance', 'Insurance Included', 'Free Pickup/Dropoff'
                ],
                'vehicles' => [
                    ['model' => 'Toyota Corolla', 'count' => 8, 'price_modifier' => 0],
                    ['model' => 'Honda Civic', 'count' => 7, 'price_modifier' => 5],
                    ['model' => 'Nissan Sentra', 'count' => 6, 'price_modifier' => 3],
                    ['model' => 'Hyundai Elantra', 'count' => 4, 'price_modifier' => 2]
                ]
            ],
            [
                'title' => 'Luxury Premium Vehicle Collection',
                'description' => 'Exclusive luxury vehicles for special occasions and premium travel experiences.',
                'store_index' => 1,
                'subcategory' => 'Luxury Car',
                'base_hourly_price' => 85.00,
                'base_daily_price' => 599.00,
                'inventory_type' => 'multiple',
                'total_units' => 12,
                'amenities' => [
                    'Premium Sound System', 'Leather Interior', 'Heated Seats', 'Sunroof',
                    'Advanced Safety Features', 'Concierge Service', 'White Glove Delivery', 'Premium Insurance'
                ],
                'vehicles' => [
                    ['model' => 'BMW 5 Series', 'count' => 3, 'price_modifier' => 0],
                    ['model' => 'Mercedes E-Class', 'count' => 3, 'price_modifier' => 50],
                    ['model' => 'Audi A6', 'count' => 2, 'price_modifier' => 25],
                    ['model' => 'Tesla Model S', 'count' => 2, 'price_modifier' => 100],
                    ['model' => 'Porsche Panamera', 'count' => 2, 'price_modifier' => 200]
                ]
            ],
            [
                'title' => 'Family Van & SUV Fleet',
                'description' => 'Spacious vans and SUVs ideal for family vacations and group transportation needs.',
                'store_index' => 2,
                'subcategory' => 'Van Rental',
                'base_hourly_price' => 25.00,
                'base_daily_price' => 179.00,
                'inventory_type' => 'multiple',
                'total_units' => 18,
                'amenities' => [
                    'Seating for 7-8 People', 'Large Cargo Space', 'Entertainment System', 'USB Charging Ports',
                    'Child Seat Compatible', 'All-Weather Capability', 'Towing Package Available', 'Easy Loading'
                ],
                'vehicles' => [
                    ['model' => 'Honda Pilot', 'count' => 5, 'price_modifier' => 0],
                    ['model' => 'Toyota Sienna', 'count' => 4, 'price_modifier' => 10],
                    ['model' => 'Chevrolet Suburban', 'count' => 4, 'price_modifier' => 30],
                    ['model' => 'Ford Transit Van', 'count' => 3, 'price_modifier' => 20],
                    ['model' => 'Mercedes Sprinter', 'count' => 2, 'price_modifier' => 50]
                ]
            ]
        ];

        foreach ($rentalListings as $rentalData) {
            $subcategory = SubCategory::where('name', $rentalData['subcategory'])
                ->where('category_id', $transportationCategory->id)
                ->first();

            $rental = Listing::create([
                'user_id' => $stores[$rentalData['store_index']]->user_id,
                'category_id' => $transportationCategory->id,
                'sub_category_id' => $subcategory->id,
                'title' => $rentalData['title'],
                'description' => $rentalData['description'],
                'slug' => \Str::slug($rentalData['title']),
                'base_hourly_price' => $rentalData['base_hourly_price'],
                'base_daily_price' => $rentalData['base_daily_price'],
                'inventory_type' => $rentalData['inventory_type'],
                'total_units' => $rentalData['total_units'],
                'amenities' => $rentalData['amenities'],
                'address' => $stores[$rentalData['store_index']]->address,
                'city' => $stores[$rentalData['store_index']]->city,
                'province' => $stores[$rentalData['store_index']]->state,
                'postal_code' => $stores[$rentalData['store_index']]->zip_code,
                'latitude' => 14.5995 + (rand(-100, 100) / 1000),
                'longitude' => 120.9842 + (rand(-100, 100) / 1000),
                'status' => 'active',
                'is_featured' => true
            ]);

            // Create vehicle units
            foreach ($rentalData['vehicles'] as $vehicleType) {
                for ($i = 1; $i <= $vehicleType['count']; $i++) {
                    $year = rand(2020, 2024);
                    $licensePlate = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3)) . '-' . rand(100, 999);
                    
                    ListingUnit::create([
                        'listing_id' => $rental->id,
                        'unit_identifier' => strtolower(str_replace(' ', '-', $vehicleType['model'])) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                        'unit_name' => $year . ' ' . $vehicleType['model'] . ' (' . $licensePlate . ')',
                        'unit_features' => [
                            'make_model' => $vehicleType['model'],
                            'year' => $year,
                            'license_plate' => $licensePlate,
                            'color' => ['White', 'Black', 'Silver', 'Blue', 'Red'][rand(0, 4)],
                            'fuel_type' => $vehicleType['model'] === 'Tesla Model S' ? 'Electric' : 'Gasoline',
                            'transmission' => 'Automatic',
                            'seating_capacity' => strpos($vehicleType['model'], 'Van') !== false || strpos($vehicleType['model'], 'Suburban') !== false ? 8 : (strpos($vehicleType['model'], 'Pilot') !== false || strpos($vehicleType['model'], 'Sienna') !== false ? 7 : 5),
                            'mileage' => rand(5000, 50000),
                            'last_service' => Carbon::now()->subDays(rand(1, 90))->format('Y-m-d')
                        ],
                        'price_modifier' => $vehicleType['price_modifier'],
                        'status' => 'active',
                        'created_by' => $rental->user_id
                    ]);
                }
            }

            $carRentals[] = $rental;
        }

        return $carRentals;
    }

    private function createCarRentalTemplates(array $carRentals): array
    {
        $this->command->info('📋 Creating car rental availability templates...');
        
        $templates = [];

        foreach ($carRentals as $rental) {
            // Hourly rental template (business hours)
            $templates[] = AvailabilityTemplate::create([
                'listing_id' => $rental->id,
                'name' => 'Hourly Rental - Business Hours',
                'days_of_week' => [1, 2, 3, 4, 5], // Monday to Friday
                'start_time' => '08:00:00',
                'end_time' => '18:00:00',
                'slot_duration_minutes' => 60, // 1-hour slots
                'base_hourly_price' => $rental->base_hourly_price,
                'base_daily_price' => null,
                'booking_rules' => [
                    'min_rental_hours' => 2,
                    'max_rental_hours' => 24,
                    'advance_booking_hours' => 2,
                    'cancellation_policy' => '2 hours',
                    'pickup_locations' => ['Airport', 'Downtown', 'Hotel Delivery'],
                    'fuel_policy' => 'Return with same fuel level',
                    'mileage_limit' => 'Unlimited local, 200 miles/day for long distance',
                    'driver_age_requirement' => 21,
                    'additional_driver_fee' => 15.00
                ],
                'is_active' => true,
                'created_by' => $rental->user_id
            ]);

            // Daily rental template (all days)
            $templates[] = AvailabilityTemplate::create([
                'listing_id' => $rental->id,
                'name' => 'Daily Rental - Full Service',
                'days_of_week' => [0, 1, 2, 3, 4, 5, 6], // All days
                'start_time' => '09:00:00', // Pickup time
                'end_time' => '09:00:00', // Return time (next day)
                'slot_duration_minutes' => 1440, // Daily slots
                'base_hourly_price' => $rental->base_daily_price, // Required field
                'base_daily_price' => $rental->base_daily_price,
                'booking_rules' => [
                    'min_rental_days' => 1,
                    'max_rental_days' => 30,
                    'advance_booking_days' => 30,
                    'cancellation_policy' => '24 hours',
                    'pickup_time' => '09:00',
                    'return_time' => '09:00',
                    'late_return_fee' => 25.00,
                    'fuel_policy' => 'Return with same fuel level',
                    'mileage_limit' => 'Unlimited'
                ],
                'is_active' => true,
                'created_by' => $rental->user_id
            ]);

            // Weekend premium template
            $templates[] = AvailabilityTemplate::create([
                'listing_id' => $rental->id,
                'name' => 'Weekend Premium Rates',
                'days_of_week' => [5, 6, 0], // Friday, Saturday, Sunday
                'start_time' => '09:00:00',
                'end_time' => '09:00:00',
                'slot_duration_minutes' => 1440,
                'base_hourly_price' => $rental->base_daily_price * 1.25, // Required field
                'base_daily_price' => $rental->base_daily_price * 1.25, // 25% premium
                'booking_rules' => [
                    'min_rental_days' => 2, // Minimum 2 days on weekends
                    'max_rental_days' => 30,
                    'advance_booking_days' => 30,
                    'cancellation_policy' => '48 hours',
                    'weekend_surcharge' => 'Included in rate'
                ],
                'is_active' => true,
                'created_by' => $rental->user_id
            ]);
        }

        return $templates;
    }

    private function generateCarRentalAvailability(array $carRentals, array $templates): void
    {
        $this->command->info('📅 Generating car rental availability for the next 60 days...');
        
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays(60);

        foreach ($carRentals as $rental) {
            $rentalTemplates = array_filter($templates, fn($t) => $t->listing_id === $rental->id);
            
            // Get all vehicle units for this rental
            $vehicleUnits = $rental->units;
            
            $current = $startDate->copy();
            while ($current->lte($endDate)) {
                $dayOfWeek = $current->dayOfWeek;
                
                foreach ($rentalTemplates as $template) {
                    if (in_array($dayOfWeek, $template->days_of_week)) {
                        // Create availability for each vehicle unit
                        foreach ($vehicleUnits as $unit) {
                            // Calculate pricing with unit modifier
                            $basePrice = $template->hourly_price ?? $template->daily_price;
                            $finalPrice = $basePrice + $unit->price_modifier;
                            
                            // Add holiday pricing for special dates
                            $holidayPrice = null;
                            if ($current->month === 12 && $current->day >= 20) { // Christmas season
                                $holidayPrice = $finalPrice * 1.4;
                            } elseif ($current->month === 7 && $current->day === 4) { // July 4th
                                $holidayPrice = $finalPrice * 1.3;
                            }
                            
                            // Determine if it's weekend pricing
                            $isWeekend = in_array($dayOfWeek, [5, 6, 0]);
                            $weekendPrice = $isWeekend && $template->name === 'Weekend Premium Rates' ? $finalPrice : null;
                            
                            if ($template->name === 'Hourly Rental - Business Hours') {
                                // Create hourly slots
                                $startTime = Carbon::parse($template->start_time);
                                $endTime = Carbon::parse($template->end_time);
                                
                                while ($startTime->lt($endTime)) {
                                    $slotEnd = $startTime->copy()->addHour();
                                    
                                    ListingAvailability::create([
                                        'listing_id' => $rental->id,
                                        'available_date' => $current->format('Y-m-d'),
                                        'start_time' => $startTime->format('H:i:s'),
                                        'end_time' => $slotEnd->format('H:i:s'),
                                        'unit_identifier' => $unit->unit_identifier,
                                        'status' => 'available',
                                        'duration_type' => 'hourly',
                                        'slot_duration_minutes' => 60,
                                        'available_units' => 1,
                                        'peak_hour_price' => $startTime->hour >= 17 ? $finalPrice * 1.2 : null, // Evening peak
                                        'weekend_price' => $weekendPrice,
                                        'holiday_price' => $holidayPrice,
                                        'booking_rules' => $template->booking_rules,
                                        'metadata' => [
                                            'vehicle_make_model' => $unit->unit_features['make_model'] ?? 'Unknown',
                                            'year' => $unit->unit_features['year'] ?? 2023,
                                            'license_plate' => $unit->unit_features['license_plate'] ?? 'N/A',
                                            'color' => $unit->unit_features['color'] ?? 'Unknown',
                                            'fuel_type' => $unit->unit_features['fuel_type'] ?? 'Gasoline',
                                            'seating_capacity' => $unit->unit_features['seating_capacity'] ?? 5,
                                            'pickup_location' => 'Main Office',
                                            'rental_type' => 'hourly'
                                        ],
                                        'created_by' => $rental->user_id
                                    ]);
                                    
                                    $startTime->addHour();
                                }
                            } else {
                                // Create daily slots
                                ListingAvailability::create([
                                    'listing_id' => $rental->id,
                                    'available_date' => $current->format('Y-m-d'),
                                    'start_time' => $template->start_time,
                                    'end_time' => $template->end_time,
                                    'unit_identifier' => $unit->unit_identifier,
                                    'status' => 'available',
                                    'duration_type' => 'daily',
                                    'slot_duration_minutes' => 1440,
                                    'available_units' => 1,
                                    'weekend_price' => $weekendPrice,
                                    'holiday_price' => $holidayPrice,
                                    'booking_rules' => $template->booking_rules,
                                    'metadata' => [
                                        'vehicle_make_model' => $unit->unit_features['make_model'] ?? 'Unknown',
                                        'year' => $unit->unit_features['year'] ?? 2023,
                                        'license_plate' => $unit->unit_features['license_plate'] ?? 'N/A',
                                        'color' => $unit->unit_features['color'] ?? 'Unknown',
                                        'fuel_type' => $unit->unit_features['fuel_type'] ?? 'Gasoline',
                                        'seating_capacity' => $unit->unit_features['seating_capacity'] ?? 5,
                                        'pickup_time' => '09:00',
                                        'return_time' => '09:00',
                                        'rental_type' => 'daily'
                                    ],
                                    'created_by' => $rental->user_id
                                ]);
                            }
                        }
                        
                        break; // Only use one template per day
                    }
                }
                
                $current->addDay();
            }
        }
    }

    private function createSampleBookings(array $carRentals): void
    {
        $this->command->info('📋 Creating sample bookings to demonstrate the system...');
        
        // Create some sample bookings for the next few days
        $bookingDates = [
            Carbon::now()->addDays(1),
            Carbon::now()->addDays(3),
            Carbon::now()->addDays(7)
        ];

        foreach ($bookingDates as $date) {
            // Pick a random car rental
            $rental = $carRentals[array_rand($carRentals)];
            
            // Get available slots for this date (both hourly and daily)
            $availableSlots = $rental->availability()
                ->where('available_date', $date->format('Y-m-d'))
                ->where('status', 'available')
                ->limit(5) // Book 5 slots
                ->get();

            foreach ($availableSlots as $slot) {
                $slot->update([
                    'status' => 'booked',
                    'updated_by' => $rental->user_id
                ]);
            }
        }
    }

    private function printSummary(array $owners, array $stores, array $carRentals, array $templates): void
    {
        $totalVehicles = 0;
        $totalAvailability = 0;
        $totalBookings = 0;

        foreach ($carRentals as $rental) {
            $totalVehicles += $rental->units->count();
            $totalAvailability += $rental->availability()->count();
            $totalBookings += $rental->availability()->where('status', 'booked')->count();
        }

        $this->command->info('');
        $this->command->info('🎉 ===== CAR RENTAL SEEDER SUMMARY =====');
        $this->command->info('👥 Rental Company Owners: ' . count($owners));
        $this->command->info('🚗 Rental Stores Created: ' . count($stores));
        $this->command->info('🏢 Rental Listings Created: ' . count($carRentals));
        $this->command->info('🚙 Total Vehicle Units: ' . $totalVehicles);
        $this->command->info('📋 Availability Templates: ' . count($templates));
        $this->command->info('📅 Total Availability Slots: ' . $totalAvailability);
        $this->command->info('📋 Sample Bookings Created: ' . $totalBookings);
        $this->command->info('');
        $this->command->info('✨ Car rentals now demonstrate:');
        $this->command->info('   • Hourly and daily vehicle rentals');
        $this->command->info('   • Multiple vehicle types with different pricing');
        $this->command->info('   • Peak hour and weekend premium pricing');
        $this->command->info('   • Holiday season pricing');
        $this->command->info('   • Multi-vehicle inventory management');
        $this->command->info('   • Transportation-specific metadata and policies');
        $this->command->info('==========================================');
    }
}
