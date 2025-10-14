<?php

namespace Database\Seeders\Tenants;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\SubCategory;

class TenantCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting to seed Categories and SubCategories...');

        $categories = [
            'Residential' => [
                'Apartment',
                'House',
                'Condo',
                'Studio',
                'Townhouse',
                'Duplex',
                'Loft',
                'Villa',
            ],
            'Commercial' => [
                'Office Space',
                'Retail Space',
                'Warehouse',
                'Restaurant Space',
                'Coworking Space',
                'Shop',
                'Mall Space',
                'Industrial Space',
            ],
            'Vacation Rentals' => [
                'Beach House',
                'Mountain Cabin',
                'Resort',
                'Cottage',
                'Bungalow',
                'Chalet',
                'Farmhouse',
            ],
            'Shared Spaces' => [
                'Room in Apartment',
                'Room in House',
                'Shared Apartment',
                'Dormitory',
                'Hostel',
                'Co-living',
            ],
            'Parking' => [
                'Garage',
                'Covered Parking',
                'Open Parking',
                'Underground Parking',
                'Street Parking',
            ],
            'Storage' => [
                'Storage Unit',
                'Locker',
                'Container',
                'Shed',
            ],
            'Land' => [
                'Residential Land',
                'Commercial Land',
                'Agricultural Land',
                'Industrial Land',
                'Mixed-use Land',
            ],
            'Vehicles' => [
                'Car',
                'SUV',
                'Van',
                'Truck',
                'Motorcycle',
                'Scooter',
                'Bike/Bicycle',
                'Electric Bike',
                'ATV',
                'RV/Camper',
                'Boat',
                'Jet Ski',
            ],
            'Sports Venues' => [
                'Basketball Court',
                'Tennis Court',
                'Badminton Court',
                'Soccer Field',
                'Football Field',
                'Volleyball Court',
                'Swimming Pool',
                'Gym/Fitness Center',
                'Boxing Ring',
                'Skating Rink',
                'Golf Course',
                'Squash Court',
                'Table Tennis Area',
                'Cricket Ground',
            ],
            'Event Spaces' => [
                'Event Hall',
                'Meeting Room',
                'Conference Hall',
                'Banquet Hall',
                'Party Venue',
                'Wedding Venue',
                'Exhibition Space',
                'Auditorium',
            ],
            'Equipment & Tools' => [
                'Construction Equipment',
                'Camping Gear',
                'Photography Equipment',
                'DJ Equipment',
                'Sound System',
                'Projector',
                'Gaming Console',
                'Party Equipment',
                'Gardening Tools',
                'Power Tools',
            ],
            'Professional Services' => [
                'Photographer',
                'Videographer',
                'Doctor',
                'Lawyer',
                'Accountant',
                'Consultant',
                'Freelancer/Developer',
                'Graphic Designer',
                'Event Planner',
                'Referee/Umpire',
                'Coach/Trainer',
                'Tutor/Teacher',
                'Translator',
                'Makeup Artist',
                'Hair Stylist',
                'Personal Chef',
                'Nutritionist',
                'Therapist/Counselor',
                'Mechanic',
                'Electrician',
                'Plumber',
                'Carpenter',
                'DJ/Entertainer',
                'Musician',
            ],
        ];

        foreach ($categories as $categoryName => $subCategories) {
            $this->command->info("Creating category: {$categoryName}");
            
            $category = Category::create([
                'name' => $categoryName,
            ]);

            foreach ($subCategories as $subCategoryName) {
                SubCategory::create([
                    'category_id' => $category->id,
                    'name' => $subCategoryName,
                ]);
            }

            $this->command->info("  ✓ Created {$categoryName} with " . count($subCategories) . " subcategories");
        }

        $totalCategories = Category::count();
        $totalSubCategories = SubCategory::count();

        $this->command->info("Seeding completed!");
        $this->command->info("Total Categories: {$totalCategories}");
        $this->command->info("Total SubCategories: {$totalSubCategories}");
    }
} 