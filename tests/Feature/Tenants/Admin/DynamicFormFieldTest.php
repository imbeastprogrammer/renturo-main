<?php

namespace Tests\Feature\Tenants\Admin;

use App\Models\DynamicForm;
use App\Models\DynamicFormField;
use App\Models\DynamicFormPage;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase\TenantTestCase;

class DynamicFormFieldTest extends TenantTestCase
{
    use RefreshDatabase;

    private $user;
    private $category;
    private $subcategory;
    private $form;
    private $page;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'mobile_number' => '1234567890'
        ]);

        // Create category and subcategory
        $this->category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test Category Description',
            'is_active' => true
        ]);

        $this->subcategory = SubCategory::create([
            'category_id' => $this->category->id,
            'name' => 'Test Subcategory',
            'description' => 'Test Subcategory Description',
            'is_active' => true
        ]);

        // Create form and page
        $this->form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $this->page = DynamicFormPage::create([
            'dynamic_form_id' => $this->form->id,
            'user_id' => $this->user->id,
            'title' => 'Test Page',
            'sort_no' => 0
        ]);

        // Authenticate user
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_list_fields()
    {
        $field1 = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Field 1',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $field2 = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Field 2',
            'input_field_type' => 'select',
            'is_required' => false,
            'sort_no' => 1
        ]);

        $response = $this->getJson("/form/fields?page_id={$this->page->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Dynamic form fields fetched successfully.',
                'data' => [
                    [
                        'id' => $field1->id,
                        'label' => 'Field 1',
                        'type' => 'text',
                        'required' => true,
                        'sort_no' => 0
                    ],
                    [
                        'id' => $field2->id,
                        'label' => 'Field 2',
                        'type' => 'select',
                        'required' => false,
                        'sort_no' => 1
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_create_fields()
    {
        $response = $this->postJson('/form/fields', [
            'dynamic_form_page_id' => $this->page->id,
            'fields' => [
                [
                    'input_field_label' => 'New Field 1',
                    'input_field_type' => 'text',
                    'is_required' => true,
                    'data' => null
                ],
                [
                    'input_field_label' => 'New Field 2',
                    'input_field_type' => 'select',
                    'is_required' => true,
                    'data' => [
                        'options' => ['Option 1', 'Option 2']
                    ]
                ]
            ]
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Dynamic form fields created successfully.'
            ]);

        $this->assertDatabaseHas('dynamic_form_fields', [
            'dynamic_form_page_id' => $this->page->id,
            'input_field_label' => 'New Field 1',
            'input_field_name' => 'new_field_1',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $this->assertDatabaseHas('dynamic_form_fields', [
            'dynamic_form_page_id' => $this->page->id,
            'input_field_label' => 'New Field 2',
            'input_field_name' => 'new_field_2',
            'input_field_type' => 'select',
            'is_required' => true,
            'sort_no' => 1
        ]);
    }

    /** @test */
    public function it_validates_field_types()
    {
        $response = $this->postJson('/form/fields', [
            'dynamic_form_page_id' => $this->page->id,
            'fields' => [
                [
                    'input_field_label' => 'Invalid Field',
                    'input_field_type' => 'invalid_type',
                    'is_required' => true,
                    'data' => null
                ]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fields.0.input_field_type']);
    }

    /** @test */
    public function it_validates_field_data()
    {
        $response = $this->postJson('/form/fields', [
            'dynamic_form_page_id' => $this->page->id,
            'fields' => [
                [
                    'input_field_label' => 'Select Field',
                    'input_field_type' => 'select',
                    'is_required' => true,
                    'data' => [] // Missing options array
                ]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fields.0.data']);
    }

    /** @test */
    public function it_can_update_fields()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Original Field',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $response = $this->putJson("/form/fields/{$this->page->id}", [
            'dynamic_form_page_id' => $this->page->id,
            'fields' => [
                [
                    'id' => $field->id,
                    'input_field_label' => 'Updated Field',
                    'input_field_type' => 'textarea',
                    'is_required' => false,
                    'data' => [
                        'rows' => 5,
                        'cols' => 40
                    ]
                ]
            ]
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Dynamic form fields updated successfully.'
            ]);

        $this->assertDatabaseHas('dynamic_form_fields', [
            'id' => $field->id,
            'input_field_label' => 'Updated Field',
            'input_field_type' => 'textarea',
            'is_required' => false
        ]);
    }

    /** @test */
    public function it_can_delete_field()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Field',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $response = $this->deleteJson("/form/fields/{$field->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Dynamic form field deleted successfully.'
            ]);

        $this->assertSoftDeleted($field);
    }

    /** @test */
    public function it_can_restore_field()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Field',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $field->delete();

        $response = $this->postJson("/form/fields/restore/{$field->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Dynamic form field restored successfully.'
            ]);

        $this->assertNotSoftDeleted($field);
    }

    /** @test */
    public function it_reorders_fields_after_deletion()
    {
        $field1 = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Field 1',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $field2 = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Field 2',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 1
        ]);

        $field3 = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Field 3',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 2
        ]);

        $this->deleteJson("/form/fields/{$field2->id}");

        $this->assertDatabaseHas('dynamic_form_fields', [
            'id' => $field1->id,
            'sort_no' => 0
        ]);

        $this->assertDatabaseHas('dynamic_form_fields', [
            'id' => $field3->id,
            'sort_no' => 1
        ]);
    }

    /** @test */
    public function it_handles_missing_field()
    {
        $response = $this->getJson('/form/fields/999999');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Form field not found.'
            ]);
    }

    /** @test */
    public function it_handles_deleted_field()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Field',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $field->delete();

        $response = $this->getJson("/form/fields/{$field->id}");

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Form field not found.'
            ]);

        $response = $this->getJson("/form/fields/{$field->id}?with_trashed=true");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Dynamic form field fetched successfully.',
                'data' => [
                    'id' => $field->id,
                    'label' => 'Test Field',
                    'type' => 'text'
                ]
            ]);
    }

    /** @test */
    public function it_validates_field_name_uniqueness_within_page()
    {
        DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Existing Field',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $response = $this->postJson('/form/fields', [
            'dynamic_form_page_id' => $this->page->id,
            'fields' => [
                [
                    'input_field_label' => 'Existing Field',
                    'input_field_type' => 'text',
                    'is_required' => true
                ]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fields.0.input_field_label']);
    }

    /** @test */
    public function it_validates_field_type_specific_data()
    {
        $response = $this->postJson('/form/fields', [
            'dynamic_form_page_id' => $this->page->id,
            'fields' => [
                [
                    'input_field_label' => 'Invalid Rating',
                    'input_field_type' => 'rating',
                    'is_required' => true,
                    'data' => [
                        'max' => 'not a number'
                    ]
                ],
                [
                    'input_field_label' => 'Invalid File',
                    'input_field_type' => 'file',
                    'is_required' => true,
                    'data' => [
                        'maxSize' => 'not a number',
                        'accept' => 123
                    ]
                ]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'fields.0.data.max',
                'fields.1.data.maxSize',
                'fields.1.data.accept'
            ]);
    }

    /** @test */
    public function it_handles_bulk_field_operations()
    {
        // Create multiple fields
        $response = $this->postJson('/form/fields', [
            'dynamic_form_page_id' => $this->page->id,
            'fields' => [
                [
                    'input_field_label' => 'Field 1',
                    'input_field_type' => 'text',
                    'is_required' => true
                ],
                [
                    'input_field_label' => 'Field 2',
                    'input_field_type' => 'select',
                    'is_required' => true,
                    'data' => ['options' => ['a', 'b']]
                ]
            ]
        ]);

        $response->assertStatus(201);
        $fields = DynamicFormField::where('dynamic_form_page_id', $this->page->id)->get();
        $this->assertCount(2, $fields);

        // Update multiple fields
        $response = $this->putJson("/form/fields/{$this->page->id}", [
            'dynamic_form_page_id' => $this->page->id,
            'fields' => [
                [
                    'id' => $fields[0]->id,
                    'input_field_label' => 'Updated Field 1',
                    'is_required' => false
                ],
                [
                    'id' => $fields[1]->id,
                    'input_field_label' => 'Updated Field 2',
                    'data' => ['options' => ['x', 'y', 'z']]
                ]
            ]
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('dynamic_form_fields', [
            'id' => $fields[0]->id,
            'input_field_label' => 'Updated Field 1',
            'is_required' => false
        ]);
        $this->assertDatabaseHas('dynamic_form_fields', [
            'id' => $fields[1]->id,
            'input_field_label' => 'Updated Field 2'
        ]);

        // Delete multiple fields
        $response = $this->deleteJson("/form/fields/bulk", [
            'ids' => [$fields[0]->id, $fields[1]->id]
        ]);

        $response->assertStatus(200);
        $this->assertSoftDeleted('dynamic_form_fields', ['id' => $fields[0]->id]);
        $this->assertSoftDeleted('dynamic_form_fields', ['id' => $fields[1]->id]);
    }

    /** @test */
    public function it_maintains_field_order_after_bulk_operations()
    {
        // Create fields with specific order
        $fields = [];
        for ($i = 0; $i < 5; $i++) {
            $fields[] = DynamicFormField::create([
                'dynamic_form_page_id' => $this->page->id,
                'user_id' => $this->user->id,
                'input_field_label' => "Field {$i}",
                'input_field_type' => 'text',
                'is_required' => true,
                'sort_no' => $i
            ]);
        }

        // Delete fields in the middle
        $response = $this->deleteJson("/form/fields/bulk", [
            'ids' => [$fields[1]->id, $fields[2]->id]
        ]);

        $response->assertStatus(200);

        // Check remaining fields are reordered
        $this->assertDatabaseHas('dynamic_form_fields', [
            'id' => $fields[0]->id,
            'sort_no' => 0
        ]);
        $this->assertDatabaseHas('dynamic_form_fields', [
            'id' => $fields[3]->id,
            'sort_no' => 1
        ]);
        $this->assertDatabaseHas('dynamic_form_fields', [
            'id' => $fields[4]->id,
            'sort_no' => 2
        ]);
    }

    /** @test */
    public function it_validates_field_dependencies()
    {
        $response = $this->postJson('/form/fields', [
            'dynamic_form_page_id' => $this->page->id,
            'fields' => [
                [
                    'input_field_label' => 'Dependent Field',
                    'input_field_type' => 'text',
                    'is_required' => true,
                    'data' => [
                        'depends_on' => 999999, // Non-existent field ID
                        'show_if' => 'equals',
                        'value' => 'test'
                    ]
                ]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fields.0.data.depends_on']);
    }

    /** @test */
    public function it_handles_field_value_formatting()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Field',
            'input_field_type' => 'number',
            'is_required' => true,
            'sort_no' => 0,
            'data' => [
                'min' => 0,
                'max' => 100,
                'step' => 0.01
            ]
        ]);

        $response = $this->putJson("/form/fields/{$this->page->id}", [
            'dynamic_form_page_id' => $this->page->id,
            'fields' => [
                [
                    'id' => $field->id,
                    'value' => '42.500' // Should be formatted to 42.50
                ]
            ]
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('dynamic_form_fields', [
            'id' => $field->id,
            'value' => '42.50'
        ]);
    }

    /** @test */
    public function it_handles_field_dependencies_correctly()
    {
        // Create parent field
        $parentField = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Parent Field',
            'input_field_type' => 'select',
            'is_required' => true,
            'sort_no' => 0,
            'data' => [
                'options' => ['yes', 'no']
            ]
        ]);

        // Create dependent field
        $response = $this->postJson('/form/fields', [
            'dynamic_form_page_id' => $this->page->id,
            'fields' => [
                [
                    'input_field_label' => 'Dependent Field',
                    'input_field_type' => 'text',
                    'is_required' => true,
                    'data' => [
                        'depends_on' => $parentField->id,
                        'show_if' => 'equals',
                        'value' => 'yes'
                    ]
                ]
            ]
        ]);

        $response->assertStatus(201);

        // Update parent field value
        $response = $this->putJson("/form/fields/{$this->page->id}", [
            'dynamic_form_page_id' => $this->page->id,
            'fields' => [
                [
                    'id' => $parentField->id,
                    'value' => 'yes'
                ]
            ]
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function it_handles_repeater_fields_correctly()
    {
        // Create repeater field
        $response = $this->postJson('/form/fields', [
            'dynamic_form_page_id' => $this->page->id,
            'fields' => [
                [
                    'input_field_label' => 'Repeater Field',
                    'input_field_type' => 'repeater',
                    'is_required' => true,
                    'data' => [
                        'min_items' => 1,
                        'max_items' => 3,
                        'fields' => [
                            [
                                'label' => 'Name',
                                'type' => 'text',
                                'required' => true
                            ],
                            [
                                'label' => 'Age',
                                'type' => 'number',
                                'required' => true
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $response->assertStatus(201);
        $field = DynamicFormField::latest()->first();

        // Test repeater field validation
        $response = $this->putJson("/form/fields/{$this->page->id}", [
            'dynamic_form_page_id' => $this->page->id,
            'fields' => [
                [
                    'id' => $field->id,
                    'value' => [
                        [
                            'name' => 'John',
                            'age' => 25
                        ],
                        [
                            'name' => 'Jane',
                            'age' => 30
                        ]
                    ]
                ]
            ]
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function it_handles_matrix_fields_correctly()
    {
        // Create matrix field
        $response = $this->postJson('/form/fields', [
            'dynamic_form_page_id' => $this->page->id,
            'fields' => [
                [
                    'input_field_label' => 'Matrix Field',
                    'input_field_type' => 'matrix',
                    'is_required' => true,
                    'data' => [
                        'rows' => ['Quality', 'Service', 'Price'],
                        'columns' => ['Poor', 'Fair', 'Good', 'Excellent']
                    ]
                ]
            ]
        ]);

        $response->assertStatus(201);
        $field = DynamicFormField::latest()->first();

        // Test matrix field validation
        $response = $this->putJson("/form/fields/{$this->page->id}", [
            'dynamic_form_page_id' => $this->page->id,
            'fields' => [
                [
                    'id' => $field->id,
                    'value' => [
                        'Quality' => 'Good',
                        'Service' => 'Excellent',
                        'Price' => 'Fair'
                    ]
                ]
            ]
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function it_handles_file_upload_fields_correctly()
    {
        // Create file upload field
        $response = $this->postJson('/form/fields', [
            'dynamic_form_page_id' => $this->page->id,
            'fields' => [
                [
                    'input_field_label' => 'Document Upload',
                    'input_field_type' => 'file',
                    'is_required' => true,
                    'data' => [
                        'accept' => '.pdf,.doc,.docx',
                        'maxSize' => 5 * 1024 * 1024 // 5MB
                    ]
                ]
            ]
        ]);

        $response->assertStatus(201);
        $field = DynamicFormField::latest()->first();

        // Test file validation rules
        $rules = $field->getValidationRules();
        $this->assertContains('file', $rules);
        $this->assertContains('mimes:pdf,doc,docx', $rules);
        $this->assertContains('max:5120', $rules); // 5MB in KB
    }

    /** @test */
    public function it_handles_field_visibility_conditions()
    {
        // Create fields with visibility conditions
        $response = $this->postJson('/form/fields', [
            'dynamic_form_page_id' => $this->page->id,
            'fields' => [
                [
                    'input_field_label' => 'Employment Status',
                    'input_field_type' => 'select',
                    'is_required' => true,
                    'data' => [
                        'options' => ['Employed', 'Self-employed', 'Unemployed']
                    ]
                ]
            ]
        ]);

        $response->assertStatus(201);
        $parentField = DynamicFormField::latest()->first();

        $response = $this->postJson('/form/fields', [
            'dynamic_form_page_id' => $this->page->id,
            'fields' => [
                [
                    'input_field_label' => 'Company Name',
                    'input_field_type' => 'text',
                    'is_required' => false,
                    'data' => [
                        'depends_on' => $parentField->id,
                        'show_if' => 'equals',
                        'value' => 'Employed'
                    ]
                ],
                [
                    'input_field_label' => 'Business Type',
                    'input_field_type' => 'text',
                    'is_required' => false,
                    'data' => [
                        'depends_on' => $parentField->id,
                        'show_if' => 'equals',
                        'value' => 'Self-employed'
                    ]
                ]
            ]
        ]);

        $response->assertStatus(201);
    }

    /** @test */
    public function it_handles_field_calculations()
    {
        // Create fields with calculations
        $response = $this->postJson('/form/fields', [
            'dynamic_form_page_id' => $this->page->id,
            'fields' => [
                [
                    'input_field_label' => 'Quantity',
                    'input_field_type' => 'number',
                    'is_required' => true,
                    'data' => [
                        'min' => 1,
                        'max' => 100
                    ]
                ],
                [
                    'input_field_label' => 'Unit Price',
                    'input_field_type' => 'currency',
                    'is_required' => true,
                    'data' => [
                        'min' => 0,
                        'currency' => 'USD'
                    ]
                ],
                [
                    'input_field_label' => 'Total',
                    'input_field_type' => 'currency',
                    'is_required' => true,
                    'data' => [
                        'calculated' => true,
                        'formula' => 'quantity * unit_price',
                        'currency' => 'USD'
                    ]
                ]
            ]
        ]);

        $response->assertStatus(201);
    }
}
