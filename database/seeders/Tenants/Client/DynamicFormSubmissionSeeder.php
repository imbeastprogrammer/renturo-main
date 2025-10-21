<?php

namespace Database\Seeders\Tenants\Client;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Store;
use App\Models\DynamicForm;
use App\Models\DynamicFormSubmission;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Hash;

class DynamicFormSubmissionSeeder extends Seeder
{
    /**
     * Seed Dynamic Form Submissions for Basketball Courts
     * 
     * This seeder creates realistic form submissions from court owners
     * who are registering their basketball courts on Renturo.
     * 
     * Flow:
     * 1. Create court owner users (if not exist)
     * 2. Create their venue stores
     * 3. Create form submissions with realistic data
     * 
     * These submissions can then be converted to listings.
     *
     * @return void
     */
    public function run(): void
    {
        $this->command->info('Seeding dynamic form submissions for basketball courts...');

        // Get the basketball form (created by Admin/BasketballFormSeeder)
        $basketballForm = DynamicForm::where('name', 'like', '%Basketball%')->first();
        
        if (!$basketballForm) {
            $this->command->warn('Basketball form not found. Please run Admin/BasketballFormSeeder first.');
            return;
        }

        // Get Sports category and Basketball subcategory
        $sportsCategory = Category::where('name', 'like', '%Sports%')->first();
        $basketballSubcategory = SubCategory::where('name', 'like', '%Basketball%')->first();

        if (!$sportsCategory || !$basketballSubcategory) {
            $this->command->warn('Categories not found. Please run Admin/CategorySeeder first.');
            return;
        }

        // Court Owner 1: Premium Indoor Court - BGC
        $owner1 = User::firstOrCreate(
            ['email' => 'elite.sports@renturo.ph'],
            [
                'first_name' => 'Michael',
                'last_name' => 'Rodriguez',
                'username' => 'elitesportsmanila',
                'password' => Hash::make('password'),
                'mobile_number' => '+639171234501',
                'role' => User::ROLE_OWNER,
            ]
        );

        $store1 = Store::firstOrCreate(
            ['user_id' => $owner1->id],
            [
                'name' => 'Elite Sports Complex',
                'url' => 'elite-sports-' . uniqid(),
                'category_id' => $sportsCategory->id,
                'sub_category_id' => $basketballSubcategory->id,
            ]
        );

        $submission1 = DynamicFormSubmission::create([
            'user_id' => $owner1->id,
            'store_id' => $store1->id,
            'dynamic_form_id' => $basketballForm->id,
            'name' => 'Elite Sports Complex - Premium Indoor Court Registration',
            'about' => 'Professional NBA-standard indoor basketball court with premium facilities',
            'data' => json_encode([
                'contact_information' => [
                    'Contact Person Name' => 'Michael Rodriguez',
                    'Phone Number' => '+639171234501',
                    'Email Address' => 'elite.sports@renturo.ph',
                ],
                'booking_details' => [
                    'Court Type' => 'Indoor Full Court',
                    'Court Number' => '1',
                    'Booking Type' => 'Game/Match',
                ],
                'court_and_activity' => [
                    'Floor Material' => 'Professional Hardwood',
                    'Ceiling Height' => '10 meters',
                    'Air Conditioning' => 'Yes',
                    'Locker Rooms' => 'Yes',
                    'Shower Facilities' => 'Yes',
                    'Parking Spaces' => '50',
                    'Lighting' => 'Professional LED',
                    'Sound System' => 'Yes',
                    'Scoreboard' => 'Electronic LED',
                    'Seating Capacity' => '200',
                    'Maximum Players' => '30',
                    'Equipment Rental' => 'Basketballs, jerseys, training equipment',
                    'Special Features' => 'FIBA-certified court, instant replay system, professional referee services',
                ],
            ]),
        ]);

        $this->command->info("  ✓ Created submission for {$store1->name}");

        // Court Owner 2: Outdoor Community Court - Quezon City
        $owner2 = User::firstOrCreate(
            ['email' => 'sunshine.courts@renturo.ph'],
            [
                'first_name' => 'Jose',
                'last_name' => 'Santos',
                'username' => 'sunshinecourts',
                'password' => Hash::make('password'),
                'mobile_number' => '+639171234502',
                'role' => User::ROLE_OWNER,
            ]
        );

        $store2 = Store::firstOrCreate(
            ['user_id' => $owner2->id],
            [
                'name' => 'Sunshine Outdoor Courts',
                'url' => 'sunshine-courts-' . uniqid(),
                'category_id' => $sportsCategory->id,
                'sub_category_id' => $basketballSubcategory->id,
            ]
        );

        $submission2 = DynamicFormSubmission::create([
            'user_id' => $owner2->id,
            'store_id' => $store2->id,
            'dynamic_form_id' => $basketballForm->id,
            'name' => 'Sunshine Outdoor Courts - Main Court Registration',
            'about' => 'Full-size outdoor basketball court with excellent lighting and maintained surface',
            'data' => json_encode([
                'contact_information' => [
                    'Contact Person Name' => 'Jose Santos',
                    'Phone Number' => '+639171234502',
                    'Email Address' => 'sunshine.courts@renturo.ph',
                ],
                'booking_details' => [
                    'Court Type' => 'Outdoor Full Court',
                    'Court Number' => '1',
                    'Booking Type' => 'Practice Session',
                ],
                'court_and_activity' => [
                    'Floor Material' => 'Premium Concrete',
                    'Ceiling Height' => 'Outdoor (Open)',
                    'Air Conditioning' => 'No',
                    'Locker Rooms' => 'No',
                    'Shower Facilities' => 'No',
                    'Parking Spaces' => '15',
                    'Lighting' => 'LED Floodlights',
                    'Sound System' => 'No',
                    'Scoreboard' => 'Manual',
                    'Seating Capacity' => '30',
                    'Maximum Players' => '20',
                    'Equipment Rental' => 'Basketballs available',
                    'Special Features' => 'Covered spectator area, water fountain, first aid kit',
                ],
            ]),
        ]);

        $this->command->info("  ✓ Created submission for {$store2->name}");

        // Court Owner 3: Training Facility - Makati
        $owner3 = User::firstOrCreate(
            ['email' => 'pro.training@renturo.ph'],
            [
                'first_name' => 'Carlos',
                'last_name' => 'Reyes',
                'username' => 'protrainingcenter',
                'password' => Hash::make('password'),
                'mobile_number' => '+639171234503',
                'role' => User::ROLE_OWNER,
            ]
        );

        $store3 = Store::firstOrCreate(
            ['user_id' => $owner3->id],
            [
                'name' => 'Pro Training Half-Court Center',
                'url' => 'pro-training-' . uniqid(),
                'category_id' => $sportsCategory->id,
                'sub_category_id' => $basketballSubcategory->id,
            ]
        );

        $submission3 = DynamicFormSubmission::create([
            'user_id' => $owner3->id,
            'store_id' => $store3->id,
            'dynamic_form_id' => $basketballForm->id,
            'name' => 'Pro Training Center - Half Court Registration',
            'about' => 'Professional training facility with specialized equipment for skills development',
            'data' => json_encode([
                'contact_information' => [
                    'Contact Person Name' => 'Carlos Reyes',
                    'Phone Number' => '+639171234503',
                    'Email Address' => 'pro.training@renturo.ph',
                ],
                'booking_details' => [
                    'Court Type' => 'Indoor Half Court',
                    'Court Number' => '1',
                    'Booking Type' => 'Training Session',
                ],
                'court_and_activity' => [
                    'Floor Material' => 'Rubberized Sports Flooring',
                    'Ceiling Height' => '8 meters',
                    'Air Conditioning' => 'Yes',
                    'Locker Rooms' => 'Yes',
                    'Shower Facilities' => 'Yes',
                    'Parking Spaces' => '20',
                    'Lighting' => 'Professional LED',
                    'Sound System' => 'Yes',
                    'Scoreboard' => 'Digital',
                    'Seating Capacity' => '20',
                    'Maximum Players' => '12',
                    'Equipment Rental' => 'Training cones, agility ladders, resistance bands, basketballs',
                    'Special Features' => 'Wall mirrors, video recording system, professional coaching available',
                ],
            ]),
        ]);

        $this->command->info("  ✓ Created submission for {$store3->name}");

        // Court Owner 4: Budget Community Court - Pasig
        $owner4 = User::firstOrCreate(
            ['email' => 'barangay.courts@renturo.ph'],
            [
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'username' => 'barangaycourts',
                'password' => Hash::make('password'),
                'mobile_number' => '+639171234504',
                'role' => User::ROLE_OWNER,
            ]
        );

        $store4 = Store::firstOrCreate(
            ['user_id' => $owner4->id],
            [
                'name' => 'Barangay Community Court',
                'url' => 'barangay-court-' . uniqid(),
                'category_id' => $sportsCategory->id,
                'sub_category_id' => $basketballSubcategory->id,
            ]
        );

        $submission4 = DynamicFormSubmission::create([
            'user_id' => $owner4->id,
            'store_id' => $store4->id,
            'dynamic_form_id' => $basketballForm->id,
            'name' => 'Barangay Court - Community Registration',
            'about' => 'Budget-friendly outdoor court perfect for casual games and community events',
            'data' => json_encode([
                'contact_information' => [
                    'Contact Person Name' => 'Juan Dela Cruz',
                    'Phone Number' => '+639171234504',
                    'Email Address' => 'barangay.courts@renturo.ph',
                ],
                'booking_details' => [
                    'Court Type' => 'Outdoor Full Court',
                    'Court Number' => '1',
                    'Booking Type' => 'Game/Match',
                ],
                'court_and_activity' => [
                    'Floor Material' => 'Concrete',
                    'Ceiling Height' => 'Outdoor (Open)',
                    'Air Conditioning' => 'No',
                    'Locker Rooms' => 'No',
                    'Shower Facilities' => 'No',
                    'Parking Spaces' => '10',
                    'Lighting' => 'Basic Floodlights',
                    'Sound System' => 'No',
                    'Scoreboard' => 'None',
                    'Seating Capacity' => '20',
                    'Maximum Players' => '20',
                    'Equipment Rental' => 'No',
                    'Special Features' => 'Clean and safe, regular maintenance, community-friendly pricing',
                ],
            ]),
        ]);

        $this->command->info("  ✓ Created submission for {$store4->name}");

        $this->command->info('');
        $this->command->info('Successfully seeded 4 dynamic form submissions!');
        $this->command->info('Summary:');
        $this->command->info("  • Form: {$basketballForm->name}");
        $this->command->info('  • Submissions: 4 court registrations');
        $this->command->info('  • Owners: 4 court operators created');
        $this->command->info('  • Stores: 4 venue profiles created');
        $this->command->info('');
        $this->command->info('These submissions can now be converted to listings!');
    }
}

