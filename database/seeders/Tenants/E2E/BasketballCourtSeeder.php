<?php

namespace Database\Seeders\Tenants\E2E;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Store;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\DynamicForm;
use App\Models\DynamicFormPage;
use App\Models\DynamicFormField;
use App\Models\Listing;
use App\Models\ListingPricing;
use App\Models\DynamicFormSubmission;
use App\Models\ListingPhoto;
use App\Models\AvailabilityTemplate;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class BasketballCourtSeeder extends Seeder
{
    /**
     * Complete End-to-End Basketball Court Rental Flow
     * 
     * This demonstrates:
     * 1. Admin creates dynamic form for basketball courts
     * 2. Client creates listing-level (facility/parent)
     * 3. Client creates unit-level (individual courts)
     * 4. User books a specific listing unit
     */
    public function run()
    {
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════════');
        $this->command->info('   BASKETBALL COURT RENTAL - COMPLETE FLOW DEMONSTRATION');
        $this->command->info('═══════════════════════════════════════════════════════════════');
        $this->command->info('');

        // ========================================
        // STEP 1: ADMIN CREATES DYNAMIC FORM
        // ========================================
        $this->command->info('STEP 1: Admin creates dynamic form for basketball courts');
        $this->command->info('--------------------------------------------------------');
        
        $admin = $this->createAdmin();
        $category = $this->createCategory();
        $subCategory = $this->createSubCategory($category);
        $dynamicForm = $this->createDynamicForm($admin, $subCategory);
        
        $this->command->info('✓ Admin created dynamic form with unit fields');
        $this->command->info('  - Form: Basketball Court Booking Form');
        $this->command->info('  - Fields: unit_name, price_per_hour, max_capacity, amenities, etc.');
        $this->command->info('');

        // ========================================
        // STEP 2: CLIENT CREATES LISTING-LEVEL (FACILITY/PARENT)
        // ========================================
        $this->command->info('STEP 2: Client creates listing-level data (facility/parent)');
        $this->command->info('-----------------------------------------------------------');
        
        $client = $this->createClient();
        $store = $this->createStore($client, $category, $subCategory);
        $listing = $this->createListing($client, $store, $category, $subCategory);
        $listingPricing = $this->createListingPricing($listing);
        
        $this->command->info('✓ Client created parent listing (facility)');
        $this->command->info('  - Listing: Elite Sports Complex');
        $this->command->info('  - Type: Multiple units (4 courts)');
        $this->command->info('  - Location: BGC, Taguig');
        $this->command->info('  - Price Range: ₱1,500 - ₱2,500/hour');
        $this->command->info('');

        // ========================================
        // STEP 3: CLIENT CREATES UNIT-LEVEL (INDIVIDUAL COURTS)
        // ========================================
        $this->command->info('STEP 3: Client creates unit-level data (individual courts)');
        $this->command->info('-----------------------------------------------------------');
        
        $units = $this->createUnits($client, $store, $listing, $dynamicForm);
        
        $this->command->info('✓ Client created 4 individual court units');
        foreach ($units as $index => $unit) {
            $unitData = $unit->data;
            $this->command->info('  ' . ($index + 1) . '. ' . ($unitData['unit_name'] ?? 'Court') . 
                ' - ₱' . number_format($unitData['price_per_hour'] ?? 0, 2) . '/hour' .
                ' (' . ($unitData['court_type'] ?? 'Indoor') . ')');
        }
        $this->command->info('');

        // Create photos and availability for each unit
        $this->createPhotosAndAvailability($listing, $units);
        
        $this->command->info('✓ Added photos and availability schedules');
        $this->command->info('  - 5 photos per court');
        $this->command->info('  - Courts A & B: Fixed Slots Mode (1-hour blocks on the hour)');
        $this->command->info('  - Courts C & D: Flexible Mode (any 30-min start time, 1-4 hour duration)');
        $this->command->info('  - Mon-Fri & Weekend templates with different pricing');
        $this->command->info('');

        // ========================================
        // STEP 4: USER BOOKS A LISTING UNIT
        // ========================================
        $this->command->info('STEP 4: User books a specific listing unit');
        $this->command->info('-------------------------------------------');
        
        $renterUser = $this->createRenter();
        $booking = $this->createBooking($renterUser, $client, $listing, $units[0]);
        
        $this->command->info('✓ User successfully booked a court');
        $this->command->info('  - Booking #: ' . $booking->booking_number);
        $this->command->info('  - Court: ' . ($units[0]->data['unit_name'] ?? 'Court A'));
        $this->command->info('  - Date: ' . $booking->check_in_date->format('M d, Y'));
        $this->command->info('  - Time: ' . Carbon::parse($booking->check_in_time)->format('g:i A') . 
            ' - ' . Carbon::parse($booking->check_out_time)->format('g:i A'));
        $this->command->info('  - Duration: ' . $booking->duration_hours . ' hours');
        $this->command->info('  - Total: ₱' . number_format($booking->total_price, 2));
        $this->command->info('  - Status: ' . strtoupper($booking->status));
        $this->command->info('');

        // ========================================
        // SUMMARY
        // ========================================
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════════');
        $this->command->info('   COMPLETE FLOW DEMONSTRATION SUMMARY');
        $this->command->info('═══════════════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('✓ Step 1: Admin created dynamic form for basketball courts');
        $this->command->info('✓ Step 2: Client created listing-level data (Elite Sports Complex)');
        $this->command->info('✓ Step 3: Client created 4 unit-level data (individual courts)');
        $this->command->info('✓ Step 4: User booked Court A for 2 hours');
        $this->command->info('');
        $this->command->info('DATABASE RECORDS CREATED:');
        $this->command->info('  • 1 Admin user');
        $this->command->info('  • 1 Client user (owner)');
        $this->command->info('  • 1 Renter user');
        $this->command->info('  • 1 Store (Elite Sports Complex)');
        $this->command->info('  • 1 Category (Sports Venues)');
        $this->command->info('  • 1 SubCategory (Basketball Courts)');
        $this->command->info('  • 1 Dynamic Form (for unit fields)');
        $this->command->info('  • 1 Listing (facility/parent)');
        $this->command->info('  • 1 ListingPricing record');
        $this->command->info('  • 4 DynamicFormSubmissions (units)');
        $this->command->info('  • 20 ListingPhotos (5 per court)');
        $this->command->info('  • 6 AvailabilityTemplates (demonstrating both modes):');
        $this->command->info('    - Courts A & B: Fixed Slots Mode (weekday + weekend templates)');
        $this->command->info('    - Courts C & D: Flexible Mode (all week template)');
        $this->command->info('  • 1 Booking (confirmed)');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════════');
        $this->command->info('');
    }

    private function createAdmin(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin@renturo.ph'],
            [
                'first_name' => 'Renturo',
                'last_name' => 'Admin',
                'mobile_number' => '+639171234567',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ]
        );
    }

    private function createClient(): User
    {
        return User::firstOrCreate(
            ['email' => 'owner@elitesports.ph'],
            [
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'mobile_number' => '+639171234568',
                'password' => Hash::make('password'),
                'role' => User::ROLE_OWNER,
            ]
        );
    }

    private function createRenter(): User
    {
        return User::firstOrCreate(
            ['email' => 'player@gmail.com'],
            [
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'mobile_number' => '+639171234569',
                'password' => Hash::make('password'),
                'role' => User::ROLE_USER,
            ]
        );
    }

    private function createCategory(): Category
    {
        return Category::firstOrCreate(
            ['name' => 'Sports Venues']
        );
    }

    private function createSubCategory(Category $category): SubCategory
    {
        return SubCategory::firstOrCreate(
            ['name' => 'Basketball Courts', 'category_id' => $category->id]
        );
    }

    private function createDynamicForm(User $admin, SubCategory $subCategory): DynamicForm
    {
        $form = DynamicForm::firstOrCreate(
            ['name' => 'Basketball Court Unit Form'],
            [
                'description' => 'Form for creating individual basketball court units with pricing and details',
                'subcategory_id' => $subCategory->id,
            ]
        );

        // Create a page for unit details
        $page = DynamicFormPage::firstOrCreate(
            ['dynamic_form_id' => $form->id, 'title' => 'Court Unit Details'],
            [
                'user_id' => $admin->id,
                'sort_no' => 1,
            ]
        );

        // Create fields for unit-specific data
        $fields = [
            ['label' => 'Court Name', 'name' => 'unit_name', 'type' => 'text', 'required' => true],
            ['label' => 'Court Type', 'name' => 'court_type', 'type' => 'select', 'required' => true],
            ['label' => 'Price per Hour', 'name' => 'price_per_hour', 'type' => 'number', 'required' => true],
            ['label' => 'Price per Day', 'name' => 'price_per_day', 'type' => 'number', 'required' => false],
            ['label' => 'Maximum Capacity', 'name' => 'max_capacity', 'type' => 'number', 'required' => true],
            ['label' => 'Court Size', 'name' => 'court_size', 'type' => 'select', 'required' => true],
            ['label' => 'Amenities', 'name' => 'amenities', 'type' => 'checkbox', 'required' => false],
            ['label' => 'Description', 'name' => 'description', 'type' => 'textarea', 'required' => false],
        ];

        foreach ($fields as $index => $fieldData) {
            DynamicFormField::firstOrCreate(
                [
                    'dynamic_form_page_id' => $page->id,
                    'input_field_name' => $fieldData['name'],
                ],
                [
                    'user_id' => $admin->id,
                    'input_field_label' => $fieldData['label'],
                    'input_field_type' => $fieldData['type'],
                    'is_required' => $fieldData['required'],
                    'sort_no' => $index + 1,
                    'data' => [],
                ]
            );
        }

        return $form;
    }

    private function createStore(User $client, Category $category, SubCategory $subCategory): Store
    {
        return Store::firstOrCreate(
            ['user_id' => $client->id, 'name' => 'Elite Sports Complex'],
            [
                'url' => 'elite-sports-complex',
                'category_id' => $category->id,
                'sub_category_id' => $subCategory->id,
                'address' => '123 Sports Avenue, Bonifacio Global City',
                'city' => 'Taguig',
                'state' => 'Metro Manila',
                'zip_code' => '1634',
                'latitude' => 14.5547,
                'longitude' => 121.0511,
                'about' => 'Premier sports facility with world-class basketball courts',
            ]
        );
    }

    private function createListing(User $client, Store $store, Category $category, SubCategory $subCategory): Listing
    {
        return Listing::create([
            'user_id' => $client->id,
            'store_id' => $store->id,
            'category_id' => $category->id,
            'sub_category_id' => $subCategory->id,
            'listing_type' => Listing::TYPE_SPORTS,
            'dynamic_form_id' => null,
            'dynamic_form_submission_id' => null,
            
            // Parent/Facility level information
            'title' => 'Elite Sports Complex - Basketball Courts',
            'description' => 'Experience world-class basketball at our state-of-the-art facility featuring 4 premium courts. Perfect for competitive games, training sessions, or recreational play.',
            'slug' => 'elite-sports-complex-bgc-' . uniqid(),
            
            // Location
            'address' => '123 Sports Avenue, Bonifacio Global City',
            'city' => 'Taguig',
            'province' => 'Metro Manila',
            'postal_code' => '1634',
            'latitude' => 14.5547,
            'longitude' => 121.0511,
            
            // Inventory (multiple units)
            'inventory_type' => 'multiple',
            'total_units' => 4,
            
            // Status
            'status' => Listing::STATUS_ACTIVE,
            'visibility' => Listing::VISIBILITY_PUBLIC,
            'is_featured' => true,
            'is_verified' => true,
            
            // Stats
            'views_count' => 523,
            'bookings_count' => 42,
            'average_rating' => 4.8,
            'reviews_count' => 28,
            
            // Published
            'published_at' => now(),
        ]);
    }

    private function createListingPricing(Listing $listing): ListingPricing
    {
        return ListingPricing::create([
            'listing_id' => $listing->id,
            'currency' => 'PHP',
            'price_min' => 1500.00,
            'price_max' => 2500.00,
            'pricing_model' => 'fixed', // Options: fixed, dynamic, tiered, seasonal
            'base_hourly_price' => 2000.00,
            'base_daily_price' => 15000.00,
            'service_fee_percentage' => 10.00,
            'platform_fee_percentage' => 5.00,
            'security_deposit' => 5000.00,
            'tax_percentage' => 12.00,
            'tax_included' => false,
        ]);
    }

    private function createUnits(User $client, Store $store, Listing $listing, DynamicForm $dynamicForm): array
    {
        $courts = [
            [
                'unit_name' => 'Court A - Indoor Full Court',
                'court_type' => 'Indoor Full Court',
                'price_per_hour' => 2500.00,
                'price_per_day' => 18000.00,
                'max_capacity' => 20,
                'court_size' => 'Full Court (28m x 15m)',
                'floor_type' => 'Professional Hardwood',
                'amenities' => ['Air Conditioning', 'Electronic Scoreboard', 'Shot Clock', 'Professional Lighting', 'Sound System', 'WiFi'],
                'description' => 'Premium indoor full court with professional-grade wooden flooring. Perfect for competitive games and tournaments.',
            ],
            [
                'unit_name' => 'Court B - Indoor Full Court',
                'court_type' => 'Indoor Full Court',
                'price_per_hour' => 2500.00,
                'price_per_day' => 18000.00,
                'max_capacity' => 20,
                'court_size' => 'Full Court (28m x 15m)',
                'floor_type' => 'Professional Hardwood',
                'amenities' => ['Air Conditioning', 'Electronic Scoreboard', 'Shot Clock', 'Professional Lighting', 'WiFi'],
                'description' => 'Premium indoor full court identical to Court A. Great for simultaneous tournaments.',
            ],
            [
                'unit_name' => 'Court C - Indoor Half Court',
                'court_type' => 'Indoor Half Court',
                'price_per_hour' => 1500.00,
                'price_per_day' => 10000.00,
                'max_capacity' => 10,
                'court_size' => 'Half Court (14m x 15m)',
                'floor_type' => 'Synthetic Flooring',
                'amenities' => ['Air Conditioning', 'Manual Scoreboard', 'LED Lighting', 'WiFi'],
                'description' => 'Indoor half court perfect for training sessions and small group games.',
            ],
            [
                'unit_name' => 'Court D - Outdoor Full Court',
                'court_type' => 'Outdoor Full Court',
                'price_per_hour' => 1800.00,
                'price_per_day' => 12000.00,
                'max_capacity' => 20,
                'court_size' => 'Full Court (28m x 15m)',
                'floor_type' => 'Acrylic Surface',
                'amenities' => ['LED Flood Lights', 'Electronic Scoreboard', 'Covered Bleachers', 'WiFi'],
                'description' => 'Outdoor full court with premium acrylic surface. Available for day and night games.',
            ],
        ];

        $units = [];
        foreach ($courts as $courtData) {
            $unit = DynamicFormSubmission::create([
                'listing_id' => $listing->id,
                'dynamic_form_id' => $dynamicForm->id,
                'user_id' => $client->id,
                'store_id' => $store->id,
                'status' => 'active',
                'data' => $courtData,
            ]);
            $units[] = $unit;
        }

        return $units;
    }

    private function createPhotosAndAvailability(Listing $listing, array $units): void
    {
        $photoCategories = ['main', 'court_view', 'facilities', 'amenities', 'exterior'];
        
        foreach ($units as $unitIndex => $unit) {
            // Create 5 photos per unit
            foreach ($photoCategories as $index => $category) {
                ListingPhoto::create([
                    'listing_id' => $listing->id,
                    'photo_url' => "https://picsum.photos/800/600?random=" . ($unitIndex * 10 + $index),
                    'caption' => ucfirst(str_replace('_', ' ', $category)) . ' - ' . $unit->data['unit_name'],
                    'is_primary' => $index === 0,
                    'sort_order' => $index + 1,
                ]);
            }

            // Create availability templates
            $price = $unit->data['price_per_hour'] ?? 2000;
            
            // Demonstrate both booking modes
            // Courts A & B: Fixed Slots Mode (traditional sports booking)
            // Courts C & D: Flexible Mode (more flexible rental)
            
            if ($unitIndex < 2) {
                // Fixed Slots Mode for Courts A & B
                $this->createFixedSlotsTemplate($listing, $unit, $price);
            } else {
                // Flexible Mode for Courts C & D
                $this->createFlexibleTemplate($listing, $unit, $price);
            }
        }
    }

    private function createFixedSlotsTemplate(Listing $listing, DynamicFormSubmission $unit, $price): void
    {
        // Weekday - Fixed 1-hour slots
        AvailabilityTemplate::create([
            'listing_id' => $listing->id,
            'unit_ids' => [$unit->id],
            'name' => $unit->data['unit_name'] . ' - Weekday (Fixed Slots)',
            'description' => 'Monday to Friday - Fixed 1-hour time slots',
            'days_of_week' => [1, 2, 3, 4, 5], // Mon-Fri
            'start_time' => '06:00:00',
            'end_time' => '22:00:00',
            'booking_mode' => 'fixed_slots',
            'slot_duration_minutes' => 60,
            'slot_start_offset' => 0, // Starts on the hour
            'base_hourly_price' => $price,
            'base_daily_price' => $price * 8,
            'duration_type' => 'hourly',
            'is_active' => true,
            'priority' => 1,
            'created_by' => $unit->user_id,
        ]);
        
        // Weekend - Fixed 1-hour slots with premium
        AvailabilityTemplate::create([
            'listing_id' => $listing->id,
            'unit_ids' => [$unit->id],
            'name' => $unit->data['unit_name'] . ' - Weekend (Fixed Slots)',
            'description' => 'Saturday and Sunday - Fixed 1-hour time slots',
            'days_of_week' => [6, 7], // Sat-Sun
            'start_time' => '06:00:00',
            'end_time' => '22:00:00',
            'booking_mode' => 'fixed_slots',
            'slot_duration_minutes' => 60,
            'slot_start_offset' => 0,
            'base_hourly_price' => $price * 1.2, // 20% weekend premium
            'base_daily_price' => $price * 8 * 1.2,
            'weekend_multiplier' => 1.20,
            'duration_type' => 'hourly',
            'is_active' => true,
            'priority' => 2,
            'created_by' => $unit->user_id,
        ]);
    }

    private function createFlexibleTemplate(Listing $listing, DynamicFormSubmission $unit, $price): void
    {
        // All week - Flexible booking
        AvailabilityTemplate::create([
            'listing_id' => $listing->id,
            'unit_ids' => [$unit->id],
            'name' => $unit->data['unit_name'] . ' - All Week (Flexible)',
            'description' => 'Flexible booking - Choose your own start time and duration',
            'days_of_week' => [1, 2, 3, 4, 5, 6, 7], // All days
            'start_time' => '06:00:00',
            'end_time' => '22:00:00',
            'booking_mode' => 'flexible',
            'time_increment_minutes' => 30, // Can start every 30 minutes
            'min_duration_minutes' => 60, // Minimum 1 hour
            'max_duration_minutes' => 240, // Maximum 4 hours
            'base_hourly_price' => $price,
            'base_daily_price' => $price * 8,
            'duration_type' => 'hourly',
            'is_active' => true,
            'priority' => 1,
            'created_by' => $unit->user_id,
        ]);
    }

    private function createBooking(User $renter, User $owner, Listing $listing, DynamicFormSubmission $unit): Booking
    {
        $checkInDate = now()->addDays(3)->setTime(14, 0); // 3 days from now at 2 PM
        $checkOutDate = $checkInDate->copy()->addHours(2); // 2-hour booking
        $pricePerHour = $unit->data['price_per_hour'] ?? 2000;
        $duration = 2;
        $subtotal = $pricePerHour * $duration;
        $serviceFee = $subtotal * 0.10;
        $total = $subtotal + $serviceFee;

        return Booking::create([
            'booking_number' => 'BK-' . now()->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'booking_type' => 'rental',
            
            // Relations
            'listing_id' => $listing->id,
            'dynamic_form_submission_id' => $unit->id,
            'user_id' => $renter->id,
            'owner_id' => $owner->id,
            
            // Dates and times
            'booking_date' => now(),
            'check_in_date' => $checkInDate->toDateString(),
            'check_out_date' => $checkOutDate->toDateString(),
            'check_in_time' => $checkInDate->toTimeString(),
            'check_out_time' => $checkOutDate->toTimeString(),
            'duration_hours' => $duration,
            'duration_type' => 'hourly',
            
            // Pricing
            'base_price' => $pricePerHour,
            'subtotal' => $subtotal,
            'service_fee' => $serviceFee,
            'total_price' => $total,
            'currency' => 'PHP',
            
            // Guest info
            'number_of_guests' => 10,
            'number_of_players' => 10,
            
            // Status
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'confirmed_at' => now(),
            'payment_completed_at' => now(),
            
            // Other
            'auto_confirmed' => false,
            'requires_approval' => false,
            'booking_source' => 'mobile_app',
            'platform' => 'ios',
        ]);
    }
}
