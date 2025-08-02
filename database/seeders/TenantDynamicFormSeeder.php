<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DynamicForm;
use App\Models\SubCategory;

class TenantDynamicFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting to seed TenantDynamicFormSeeder data...');

        // Get existing subcategories (these should be created by TenantCategorySeeder first)
        $basketballSubcategory = SubCategory::where('name', 'Basketball')->first();
        $restaurantSubcategory = SubCategory::where('name', 'Restaurants')->first();
        $salonSubcategory = SubCategory::where('name', 'Hair Salons')->first();
        $gymSubcategory = SubCategory::where('name', 'Gyms')->first();
        $eventVenueSubcategory = SubCategory::where('name', 'Event Venues')->first();
        $tennisSubcategory = SubCategory::where('name', 'Tennis')->first();
        $swimmingSubcategory = SubCategory::where('name', 'Swimming')->first();
        $spaSubcategory = SubCategory::where('name', 'Spa & Massage')->first();
        $cateringSubcategory = SubCategory::where('name', 'Catering')->first();
        $conferenceSubcategory = SubCategory::where('name', 'Conference Centers')->first();

        // Fallback to create subcategories if they don't exist
        if (!$basketballSubcategory) {
            $basketballSubcategory = SubCategory::firstOrCreate(
                ['name' => 'Basketball'],
                ['category_id' => 1, 'description' => 'Basketball courts and facilities']
            );
        }
        if (!$restaurantSubcategory) {
            $restaurantSubcategory = SubCategory::firstOrCreate(
                ['name' => 'Restaurants'],
                ['category_id' => 2, 'description' => 'Restaurant services']
            );
        }
        if (!$salonSubcategory) {
            $salonSubcategory = SubCategory::firstOrCreate(
                ['name' => 'Hair Salons'],
                ['category_id' => 3, 'description' => 'Hair salon services']
            );
        }
        if (!$gymSubcategory) {
            $gymSubcategory = SubCategory::firstOrCreate(
                ['name' => 'Gyms'],
                ['category_id' => 4, 'description' => 'Gym and fitness services']
            );
        }
        if (!$eventVenueSubcategory) {
            $eventVenueSubcategory = SubCategory::firstOrCreate(
                ['name' => 'Event Venues'],
                ['category_id' => 5, 'description' => 'Event venue services']
            );
        }
        if (!$tennisSubcategory) {
            $tennisSubcategory = SubCategory::firstOrCreate(
                ['name' => 'Tennis'],
                ['category_id' => 1, 'description' => 'Tennis courts and facilities']
            );
        }
        if (!$swimmingSubcategory) {
            $swimmingSubcategory = SubCategory::firstOrCreate(
                ['name' => 'Swimming'],
                ['category_id' => 1, 'description' => 'Swimming pools and lessons']
            );
        }
        if (!$spaSubcategory) {
            $spaSubcategory = SubCategory::firstOrCreate(
                ['name' => 'Spa & Massage'],
                ['category_id' => 3, 'description' => 'Spa and massage services']
            );
        }
        if (!$cateringSubcategory) {
            $cateringSubcategory = SubCategory::firstOrCreate(
                ['name' => 'Catering'],
                ['category_id' => 2, 'description' => 'Catering services']
            );
        }
        if (!$conferenceSubcategory) {
            $conferenceSubcategory = SubCategory::firstOrCreate(
                ['name' => 'Conference Centers'],
                ['category_id' => 5, 'description' => 'Conference and meeting facilities']
            );
        }

        // Create dynamic forms
        $dynamicForms = [
            [
                'name' => 'Basketball Arena',
                'description' => 'Basketball Arena Dynamic Form - Book courts, schedule games, and manage basketball activities',
                'subcategory_id' => $basketballSubcategory->id
            ],
            [
                'name' => 'Restaurant Reservation',
                'description' => 'Restaurant reservation and table booking form',
                'subcategory_id' => $restaurantSubcategory->id
            ],
            [
                'name' => 'Salon Appointment',
                'description' => 'Beauty salon appointment booking form',
                'subcategory_id' => $salonSubcategory->id
            ],
            [
                'name' => 'Gym Membership',
                'description' => 'Gym membership registration and class booking form',
                'subcategory_id' => $gymSubcategory->id
            ],
            [
                'name' => 'Event Venue Booking',
                'description' => 'Event venue booking and party planning form',
                'subcategory_id' => $eventVenueSubcategory->id
            ],
            [
                'name' => 'Tennis Court Booking',
                'description' => 'Tennis court reservation and coaching session booking',
                'subcategory_id' => $tennisSubcategory->id
            ],
            [
                'name' => 'Swimming Pool',
                'description' => 'Swimming pool booking and swimming lesson registration',
                'subcategory_id' => $swimmingSubcategory->id
            ],
            [
                'name' => 'Spa Treatment',
                'description' => 'Spa treatment booking and wellness consultation form',
                'subcategory_id' => $spaSubcategory->id
            ],
            [
                'name' => 'Catering Service',
                'description' => 'Catering service booking and menu selection form',
                'subcategory_id' => $cateringSubcategory->id
            ],
            [
                'name' => 'Conference Room',
                'description' => 'Conference room booking and meeting space reservation',
                'subcategory_id' => $conferenceSubcategory->id
            ]
        ];

        foreach ($dynamicForms as $formData) {
            DynamicForm::firstOrCreate(
                ['name' => $formData['name']],
                $formData
            );
        }

        $this->command->info('Seeding completed for TenantDynamicFormSeeder data.');
        $this->command->info('Created ' . count($dynamicForms) . ' dynamic forms.');
    }
} 