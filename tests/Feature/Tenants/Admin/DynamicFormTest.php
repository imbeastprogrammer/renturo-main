<?php

namespace Tests\Feature\Tenants\Admin;

use App\Models\DynamicForm;
use App\Models\DynamicFormPage;
use App\Models\DynamicFormSubmission;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use App\Models\MobileVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase\TenantTestCase;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Support\Facades\Gate;

class DynamicFormTest extends TenantTestCase
{
    use RefreshDatabase;

    private $user;
    private $category;
    private $subcategory;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user with ADMIN role
        $this->user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'mobile_number' => '1234567890',
            'mobile_verified_at' => now(),
            'role' => User::ROLE_ADMIN
        ]);

        // Create mobile verification record
        MobileVerification::create([
            'user_id' => $this->user->id,
            'mobile_number' => '1234567890',
            'code' => '123456',
            'expires_at' => now()->addMinutes(10),
            'verified_at' => now()
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

        // Authenticate user
        $this->actingAs($this->user);
        
        // Define Gates for testing
        Gate::define('create-forms', function ($user) {
            return true;
        });
        Gate::define('edit-forms', function ($user) {
            return true;
        });
        Gate::define('delete-forms', function ($user) {
            return true;
        });
    }

    /** @test */
    public function it_can_list_forms()
    {
        $form1 = DynamicForm::create([
            'name' => 'Test Form 1',
            'description' => 'Test Description 1',
            'subcategory_id' => $this->subcategory->id,
            'user_id' => $this->user->id
        ]);

        $form2 = DynamicForm::create([
            'name' => 'Test Form 2',
            'description' => 'Test Description 2',
            'subcategory_id' => $this->subcategory->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->get($this->getTestUrl('/admin/form'));
        
        $response->assertInertia(fn (Assert $page) => $page
            ->component('tenants/admin/post-management/dynamic-forms/index')
            ->has('forms', 2)
            ->where('forms.0.name', 'Test Form 1')
            ->where('forms.1.name', 'Test Form 2')
        );
    }

    /** @test */
    public function it_can_filter_forms_by_search()
    {
        DynamicForm::create([
            'name' => 'Test Form 1',
            'description' => 'Test Description 1',
            'subcategory_id' => $this->subcategory->id
        ]);

        DynamicForm::create([
            'name' => 'Another Form',
            'description' => 'Another Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $response = $this->get($this->getTestUrl('/admin/form?search=Test'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('tenants/admin/post-management/dynamic-forms/index')
            ->has('forms.data', 1)
            ->where('forms.data.0.name', 'Test Form 1')
        );
    }

    /** @test */
    public function it_can_filter_forms_by_subcategory()
    {
        $anotherSubcategory = SubCategory::create([
            'category_id' => $this->category->id,
            'name' => 'Another Subcategory',
            'description' => 'Another Description',
            'is_active' => true
        ]);

        DynamicForm::create([
            'name' => 'Test Form 1',
            'description' => 'Test Description 1',
            'subcategory_id' => $this->subcategory->id
        ]);

        DynamicForm::create([
            'name' => 'Test Form 2',
            'description' => 'Test Description 2',
            'subcategory_id' => $anotherSubcategory->id
        ]);

        $response = $this->get($this->getTestUrl('/admin/form?subcategory=' . $this->subcategory->id));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('tenants/admin/post-management/dynamic-forms/index')
            ->has('forms.data', 1)
            ->where('forms.data.0.name', 'Test Form 1')
        );
    }

    /** @test */
    public function it_can_create_form()
    {
        $response = $this->post($this->getTestUrl('/admin/form'), [
            'name' => 'New Test Form',
            'description' => 'New Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Dynamic form created successfully.');

        $this->assertDatabaseHas('dynamic_forms', [
            'name' => 'New Test Form',
            'description' => 'New Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);
    }

    /** @test */
    public function it_validates_unique_form_name_within_subcategory()
    {
        DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $response = $this->post($this->getTestUrl('/admin/form'), [
            'name' => 'Test Form',
            'description' => 'Another Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function it_can_show_form()
    {
        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $page = DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => $this->user->id,
            'title' => 'Test Page',
            'sort_no' => 0
        ]);

        $response = $this->get("/admin/form/{$form->id}");

        $response->assertInertia(fn (Assert $page) => $page
            ->component('tenants/admin/post-management/dynamic-forms/show')
            ->where('form.id', $form->id)
            ->where('form.name', 'Test Form')
            ->has('form.dynamicFormPages', 1)
        );
    }

    /** @test */
    public function it_can_update_form()
    {
        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $response = $this->put("/admin/form/{$form->id}", [
            'name' => 'Updated Form',
            'description' => 'Updated Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Dynamic form updated successfully.');

        $this->assertDatabaseHas('dynamic_forms', [
            'id' => $form->id,
            'name' => 'Updated Form',
            'description' => 'Updated Description'
        ]);
    }

    /** @test */
    public function it_can_delete_form()
    {
        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $page = DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => $this->user->id,
            'title' => 'Test Page',
            'sort_no' => 0
        ]);

        $submission = DynamicFormSubmission::create([
            'dynamic_form_id' => $form->id,
            'user_id' => $this->user->id,
            'data' => json_encode(['field1' => 'value1']),
            'status' => 'submitted'
        ]);

        $response = $this->delete("/admin/form/{$form->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Dynamic form deleted successfully.');

        $this->assertSoftDeleted($form);
        $this->assertSoftDeleted($page);
        $this->assertSoftDeleted($submission);
    }

    /** @test */
    public function it_can_restore_form()
    {
        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $page = DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => $this->user->id,
            'title' => 'Test Page',
            'sort_no' => 0
        ]);

        $form->delete();

        $response = $this->post("/admin/form/restore/{$form->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Dynamic form restored successfully.');

        $this->assertNotSoftDeleted($form);
        $this->assertNotSoftDeleted($page);
    }

    /** @test */
    public function it_shows_trashed_forms()
    {
        $form1 = DynamicForm::create([
            'name' => 'Active Form',
            'description' => 'Active Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $form2 = DynamicForm::create([
            'name' => 'Deleted Form',
            'description' => 'Deleted Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $form2->delete();

        $response = $this->get($this->getTestUrl('/admin/form?trashed=true'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('tenants/admin/post-management/dynamic-forms/index')
            ->has('forms.data', 1)
            ->where('forms.data.0.name', 'Deleted Form')
        );
    }

    /** @test */
    public function it_can_get_form_pages_and_fields()
    {
        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $page1 = DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => $this->user->id,
            'title' => 'Page 1',
            'sort_no' => 0
        ]);

        $page2 = DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => $this->user->id,
            'title' => 'Page 2',
            'sort_no' => 1
        ]);

        $response = $this->getJson("/form/all/{$form->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Dynamic form fetched successfully.',
                'data' => [
                    'id' => $form->id,
                    'name' => 'Test Form',
                    'description' => 'Test Description',
                    'subcategory' => [
                        'id' => $this->subcategory->id,
                        'name' => 'Test Subcategory',
                        'category' => [
                            'id' => $this->category->id,
                            'name' => 'Test Category'
                        ]
                    ],
                    'pages' => [
                        [
                            'id' => $page1->id,
                            'title' => 'Page 1',
                            'sort_no' => 0
                        ],
                        [
                            'id' => $page2->id,
                            'title' => 'Page 2',
                            'sort_no' => 1
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_update_form_pages_and_fields()
    {
        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $page1 = DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => $this->user->id,
            'title' => 'Page 1',
            'sort_no' => 0
        ]);

        $page2 = DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => $this->user->id,
            'title' => 'Page 2',
            'sort_no' => 1
        ]);

        $response = $this->putJson("/form/all/{$form->id}", [
            'name' => 'Updated Form',
            'description' => 'Updated Description',
            'dynamic_form_pages' => [
                [
                    'id' => $page1->id,
                    'title' => 'Updated Page 1',
                    'dynamic_form_fields' => [
                        [
                            'input_field_label' => 'Field 1',
                            'input_field_type' => 'text',
                            'is_required' => true,
                            'data' => null
                        ]
                    ]
                ],
                [
                    'id' => $page2->id,
                    'title' => 'Updated Page 2',
                    'dynamic_form_fields' => [
                        [
                            'input_field_label' => 'Field 2',
                            'input_field_type' => 'select',
                            'is_required' => true,
                            'data' => [
                                'options' => ['Option 1', 'Option 2']
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Dynamic form updated successfully.'
            ]);

        $this->assertDatabaseHas('dynamic_forms', [
            'id' => $form->id,
            'name' => 'Updated Form',
            'description' => 'Updated Description'
        ]);

        $this->assertDatabaseHas('dynamic_form_pages', [
            'id' => $page1->id,
            'title' => 'Updated Page 1'
        ]);

        $this->assertDatabaseHas('dynamic_form_pages', [
            'id' => $page2->id,
            'title' => 'Updated Page 2'
        ]);

        $this->assertDatabaseHas('dynamic_form_fields', [
            'dynamic_form_page_id' => $page1->id,
            'input_field_label' => 'Field 1',
            'input_field_type' => 'text',
            'is_required' => true
        ]);

        $this->assertDatabaseHas('dynamic_form_fields', [
            'dynamic_form_page_id' => $page2->id,
            'input_field_label' => 'Field 2',
            'input_field_type' => 'select',
            'is_required' => true
        ]);
    }

    /** @test */
    public function it_validates_duplicate_page_titles()
    {
        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $response = $this->putJson("/form/all/{$form->id}", [
            'name' => 'Updated Form',
            'description' => 'Updated Description',
            'dynamic_form_pages' => [
                [
                    'title' => 'Same Title',
                    'dynamic_form_fields' => []
                ],
                [
                    'title' => 'Same Title', // Duplicate title
                    'dynamic_form_fields' => []
                ]
            ]
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'Failed to update dynamic form.',
                'error' => 'Duplicate page title: Same Title'
            ]);
    }

    /** @test */
    public function it_validates_field_types_and_data()
    {
        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $response = $this->putJson("/form/all/{$form->id}", [
            'name' => 'Updated Form',
            'description' => 'Updated Description',
            'dynamic_form_pages' => [
                [
                    'title' => 'Test Page',
                    'dynamic_form_fields' => [
                        [
                            'input_field_label' => 'Invalid Field',
                            'input_field_type' => 'invalid_type', // Invalid field type
                            'is_required' => true,
                            'data' => null
                        ]
                    ]
                ]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'The given data was invalid.'
            ]);
    }

    /** @test */
    public function it_handles_missing_form()
    {
        $response = $this->getJson($this->getTestUrl('/form/all/999999'));

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Dynamic form not found.'
            ]);
    }

    /** @test */
    public function it_handles_deleted_form()
    {
        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $form->delete();

        $response = $this->getJson("/form/all/{$form->id}");

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Dynamic form not found.'
            ]);

        $response = $this->getJson("/form/all/{$form->id}?with_trashed=true");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Dynamic form fetched successfully.',
                'data' => [
                    'id' => $form->id,
                    'name' => 'Test Form',
                    'description' => 'Test Description'
                ]
            ]);
    }
}
