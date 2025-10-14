<?php

namespace Database\Seeders\Tenants;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DynamicForm;
use App\Models\DynamicFormPage;
use App\Models\DynamicFormField;
use App\Models\SubCategory;
use App\Models\User;

class TenantBasketballArenaFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting to seed TenantBasketballArenaFormSeeder data...');

        // Get the admin user (should be created by TenantSeeder)
        $user = User::where('email', 'admin@main.renturo.test')->first();
        if (!$user) {
            $this->command->error('Admin user not found. Please run TenantSeeder first.');
            return;
        }

        // Get the Basketball subcategory
        $basketballSubcategory = SubCategory::where('name', 'Basketball')->first();
        if (!$basketballSubcategory) {
            $this->command->error('Basketball subcategory not found. Please run TenantCategorySeeder first.');
            return;
        }

        // Get or create the Basketball Arena dynamic form
        $basketballForm = DynamicForm::firstOrCreate(
            ['name' => 'Basketball Arena'],
            [
                'name' => 'Basketball Arena',
                'description' => 'Basketball Arena Dynamic Form - Book courts, schedule games, and manage basketball activities',
                'subcategory_id' => $basketballSubcategory->id
            ]
        );

        $this->command->info("Using Basketball Arena form ID: {$basketballForm->id}");

        // Create form pages
        $pages = [
            [
                'title' => 'Contact Information',
                'sort_no' => 1,
                'fields' => [
                    [
                        'input_field_label' => 'Contact Person Name',
                        'input_field_name' => 'contact_name',
                        'input_field_type' => 'text',
                        'is_required' => true,
                        'sort_no' => 1,
                        'data' => [
                            'placeholder' => 'Enter your full name',
                            'validation' => 'required|string|max:255'
                        ]
                    ],
                    [
                        'input_field_label' => 'Phone Number',
                        'input_field_name' => 'phone_number',
                        'input_field_type' => 'text',
                        'is_required' => true,
                        'sort_no' => 2,
                        'data' => [
                            'placeholder' => 'Enter your phone number',
                            'validation' => 'required|string|max:20'
                        ]
                    ],
                    [
                        'input_field_label' => 'Email Address',
                        'input_field_name' => 'email',
                        'input_field_type' => 'email',
                        'is_required' => true,
                        'sort_no' => 3,
                        'data' => [
                            'placeholder' => 'Enter your email address',
                            'validation' => 'required|email'
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Booking Details',
                'sort_no' => 2,
                'fields' => [
                    [
                        'input_field_label' => 'Preferred Date',
                        'input_field_name' => 'booking_date',
                        'input_field_type' => 'date',
                        'is_required' => true,
                        'sort_no' => 1,
                        'data' => [
                            'min_date' => 'today',
                            'validation' => 'required|date|after_or_equal:today'
                        ]
                    ],
                    [
                        'input_field_label' => 'Start Time',
                        'input_field_name' => 'start_time',
                        'input_field_type' => 'time',
                        'is_required' => true,
                        'sort_no' => 2,
                        'data' => [
                            'validation' => 'required'
                        ]
                    ],
                    [
                        'input_field_label' => 'End Time',
                        'input_field_name' => 'end_time',
                        'input_field_type' => 'time',
                        'is_required' => true,
                        'sort_no' => 3,
                        'data' => [
                            'validation' => 'required|after:start_time'
                        ]
                    ],
                    [
                        'input_field_label' => 'Duration (hours)',
                        'input_field_name' => 'duration',
                        'input_field_type' => 'number',
                        'is_required' => true,
                        'sort_no' => 4,
                        'data' => [
                            'min' => 1,
                            'max' => 8,
                            'placeholder' => 'Enter duration in hours',
                            'validation' => 'required|integer|min:1|max:8'
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Court & Activity',
                'sort_no' => 3,
                'fields' => [
                    [
                        'input_field_label' => 'Court Type',
                        'input_field_name' => 'court_type',
                        'input_field_type' => 'select',
                        'is_required' => true,
                        'sort_no' => 1,
                        'data' => [
                            'options' => [
                                'Indoor Full Court',
                                'Indoor Half Court',
                                'Outdoor Full Court',
                                'Outdoor Half Court'
                            ],
                            'validation' => 'required'
                        ]
                    ],
                    [
                        'input_field_label' => 'Court Number',
                        'input_field_name' => 'court_number',
                        'input_field_type' => 'number',
                        'is_required' => false,
                        'sort_no' => 2,
                        'data' => [
                            'min' => 1,
                            'max' => 10,
                            'placeholder' => 'Enter court number (optional)',
                            'validation' => 'nullable|integer|min:1|max:10'
                        ]
                    ],
                    [
                        'input_field_label' => 'Booking Type',
                        'input_field_name' => 'booking_type',
                        'input_field_type' => 'select',
                        'is_required' => true,
                        'sort_no' => 3,
                        'data' => [
                            'options' => [
                                'Practice Session',
                                'Game/Match',
                                'Tournament',
                                'Training Session'
                            ],
                            'validation' => 'required'
                        ]
                    ],
                    [
                        'input_field_label' => 'Number of Players',
                        'input_field_name' => 'player_count',
                        'input_field_type' => 'number',
                        'is_required' => true,
                        'sort_no' => 4,
                        'data' => [
                            'min' => 1,
                            'max' => 20,
                            'placeholder' => 'Enter number of players',
                            'validation' => 'required|integer|min:1|max:20'
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Equipment & Services',
                'sort_no' => 4,
                'fields' => [
                    [
                        'input_field_label' => 'Equipment Needed',
                        'input_field_name' => 'equipment_needed',
                        'input_field_type' => 'checkbox',
                        'is_required' => false,
                        'sort_no' => 1,
                        'data' => [
                            'options' => [
                                'Basketballs',
                                'Scoreboard',
                                'Referee',
                                'First Aid Kit',
                                'Water Bottles',
                                'Towels'
                            ],
                            'validation' => 'nullable|array'
                        ]
                    ],
                    [
                        'input_field_label' => 'Skill Level',
                        'input_field_name' => 'skill_level',
                        'input_field_type' => 'radio',
                        'is_required' => true,
                        'sort_no' => 2,
                        'data' => [
                            'options' => [
                                'Beginner',
                                'Intermediate',
                                'Advanced',
                                'Professional'
                            ],
                            'validation' => 'required'
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Additional Information',
                'sort_no' => 5,
                'fields' => [
                    [
                        'input_field_label' => 'Special Requirements',
                        'input_field_name' => 'special_requirements',
                        'input_field_type' => 'textarea',
                        'is_required' => false,
                        'sort_no' => 1,
                        'data' => [
                            'placeholder' => 'Enter any special requirements or requests',
                            'rows' => 4,
                            'validation' => 'nullable|string|max:1000'
                        ]
                    ],
                    [
                        'input_field_label' => 'Additional Notes',
                        'input_field_name' => 'additional_notes',
                        'input_field_type' => 'textarea',
                        'is_required' => false,
                        'sort_no' => 2,
                        'data' => [
                            'placeholder' => 'Any additional information or comments',
                            'rows' => 3,
                            'validation' => 'nullable|string|max:500'
                        ]
                    ],
                    [
                        'input_field_label' => 'Team Roster',
                        'input_field_name' => 'team_roster',
                        'input_field_type' => 'file',
                        'is_required' => false,
                        'sort_no' => 3,
                        'data' => [
                            'file_types' => ['pdf', 'doc', 'docx'],
                            'max_size' => '5MB',
                            'validation' => 'nullable|file|mimes:pdf,doc,docx|max:5120'
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Agreement',
                'sort_no' => 6,
                'fields' => [
                    [
                        'input_field_label' => 'Terms and Conditions',
                        'input_field_name' => 'terms_conditions',
                        'input_field_type' => 'checkbox',
                        'is_required' => true,
                        'sort_no' => 1,
                        'data' => [
                            'options' => [
                                'I agree to the terms and conditions'
                            ],
                            'validation' => 'required|accepted'
                        ]
                    ],
                    [
                        'input_field_label' => 'Arena Rules Agreement',
                        'input_field_name' => 'arena_rules',
                        'input_field_type' => 'checkbox',
                        'is_required' => true,
                        'sort_no' => 2,
                        'data' => [
                            'options' => [
                                'I agree to follow all arena rules and regulations'
                            ],
                            'validation' => 'required|accepted'
                        ]
                    ],
                    [
                        'input_field_label' => 'Court Quality Rating',
                        'input_field_name' => 'court_rating',
                        'input_field_type' => 'rating',
                        'is_required' => false,
                        'sort_no' => 3,
                        'data' => [
                            'max_rating' => 5,
                            'validation' => 'nullable|integer|min:1|max:5'
                        ]
                    ]
                ]
            ]
        ];

        // Create pages and fields
        foreach ($pages as $pageData) {
            // Create the page
            $page = DynamicFormPage::create([
                'user_id' => $user->id,
                'dynamic_form_id' => $basketballForm->id,
                'title' => $pageData['title'],
                'sort_no' => $pageData['sort_no']
            ]);

            $this->command->info("Created page: {$page->title}");

            // Create fields for this page
            foreach ($pageData['fields'] as $fieldData) {
                DynamicFormField::create([
                    'user_id' => $user->id,
                    'dynamic_form_page_id' => $page->id,
                    'input_field_label' => $fieldData['input_field_label'],
                    'input_field_name' => $fieldData['input_field_name'],
                    'input_field_type' => $fieldData['input_field_type'],
                    'is_required' => $fieldData['is_required'],
                    'sort_no' => $fieldData['sort_no'],
                    'data' => $fieldData['data']
                ]);

                $this->command->info("  - Created field: {$fieldData['input_field_label']}");
            }
        }

        $this->command->info('Seeding completed for TenantBasketballArenaFormSeeder data.');
        $this->command->info("Created {$basketballForm->name} form with " . count($pages) . " pages and multiple fields.");
    }
} 