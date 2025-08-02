<?php

namespace Database\Seeders;

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
        $this->command->info('Starting to seed TenantCategorySeeder data...');

        // Create main categories
        $categories = [
            [
                'name' => 'Sports & Recreation',
                'subcategories' => [
                    'Basketball',
                    'Tennis',
                    'Swimming',
                    'Soccer',
                    'Golf',
                    'Volleyball',
                    'Badminton',
                    'Table Tennis',
                    'Rock Climbing',
                    'Martial Arts'
                ]
            ],
            [
                'name' => 'Food & Dining',
                'subcategories' => [
                    'Restaurants',
                    'Cafes',
                    'Fast Food',
                    'Catering',
                    'Food Delivery',
                    'Bars & Pubs',
                    'Bakeries',
                    'Food Trucks',
                    'Fine Dining',
                    'Buffet'
                ]
            ],
            [
                'name' => 'Beauty & Wellness',
                'subcategories' => [
                    'Hair Salons',
                    'Nail Salons',
                    'Spa & Massage',
                    'Beauty Clinics',
                    'Barber Shops',
                    'Makeup Artists',
                    'Tattoo Studios',
                    'Tanning Salons',
                    'Wellness Centers',
                    'Aromatherapy'
                ]
            ],
            [
                'name' => 'Health & Fitness',
                'subcategories' => [
                    'Gyms',
                    'Yoga Studios',
                    'Pilates',
                    'CrossFit',
                    'Personal Training',
                    'Dance Studios',
                    'Swimming Lessons',
                    'Boxing & MMA',
                    'Physical Therapy',
                    'Nutrition Centers'
                ]
            ],
            [
                'name' => 'Events & Entertainment',
                'subcategories' => [
                    'Event Venues',
                    'Wedding Venues',
                    'Conference Centers',
                    'Party Planning',
                    'Photography',
                    'DJ Services',
                    'Live Music',
                    'Theaters',
                    'Cinemas',
                    'Amusement Parks'
                ]
            ],
            [
                'name' => 'Professional Services',
                'subcategories' => [
                    'Legal Services',
                    'Accounting',
                    'Consulting',
                    'Real Estate',
                    'Insurance',
                    'Financial Services',
                    'Marketing',
                    'IT Services',
                    'Translation',
                    'Tutoring'
                ]
            ],
            [
                'name' => 'Automotive',
                'subcategories' => [
                    'Car Dealerships',
                    'Auto Repair',
                    'Car Wash',
                    'Auto Parts',
                    'Towing Services',
                    'Car Rental',
                    'Auto Detailing',
                    'Tire Services',
                    'Oil Change',
                    'Auto Insurance'
                ]
            ],
            [
                'name' => 'Home & Garden',
                'subcategories' => [
                    'Home Improvement',
                    'Landscaping',
                    'Cleaning Services',
                    'Plumbing',
                    'Electrical',
                    'HVAC Services',
                    'Pest Control',
                    'Moving Services',
                    'Furniture Stores',
                    'Garden Centers'
                ]
            ],
            [
                'name' => 'Education',
                'subcategories' => [
                    'Schools',
                    'Universities',
                    'Training Centers',
                    'Language Schools',
                    'Music Lessons',
                    'Art Classes',
                    'Online Courses',
                    'Test Preparation',
                    'Special Education',
                    'Vocational Training'
                ]
            ],
            [
                'name' => 'Travel & Tourism',
                'subcategories' => [
                    'Hotels',
                    'Travel Agencies',
                    'Tour Guides',
                    'Car Rentals',
                    'Vacation Rentals',
                    'Adventure Tours',
                    'Sightseeing',
                    'Transportation',
                    'Travel Insurance',
                    'Cruise Lines'
                ]
            ]
        ];

        foreach ($categories as $categoryData) {
            // Create or get the category
            $category = Category::firstOrCreate(
                ['name' => $categoryData['name']],
                ['name' => $categoryData['name']]
            );

            $this->command->info("Created category: {$category->name}");

            // Create subcategories for this category
            foreach ($categoryData['subcategories'] as $subcategoryName) {
                SubCategory::firstOrCreate(
                    [
                        'name' => $subcategoryName,
                        'category_id' => $category->id
                    ],
                    [
                        'name' => $subcategoryName,
                        'category_id' => $category->id
                    ]
                );

                $this->command->info("  - Created subcategory: {$subcategoryName}");
            }
        }

        $this->command->info('Seeding completed for TenantCategorySeeder data.');
        $this->command->info('Created ' . count($categories) . ' categories with multiple subcategories each.');
    }
} 