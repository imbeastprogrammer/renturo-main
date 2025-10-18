<?php

namespace Tests\Api\Tenant\Feature\DynamicForm;

use App\Models\DynamicForm;
use App\Models\DynamicFormPage;
use App\Models\DynamicFormField;
use App\Models\DynamicFormSubmission;
use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase\TenantTestCase;

class DynamicFormTest extends TenantTestCase
{
    use RefreshDatabase;

    private string $baseUrl;
    private string $clientBaseUrl;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        
        // Set up URLs
        $this->baseUrl = $this->getTestUrl('/api/v1');
        $this->clientBaseUrl = $this->getTestUrl('/api/client/v1');
        
        // Get authenticated token for testing
        $response = $this->postJson($this->baseUrl . '/login', [
            'email' => 'test.owner@renturo.test',
            'password' => 'password',
        ]);

        $this->token = $response->json('body.access_token');
        $verificationCode = $response->json('body.verification_code');

        // Verify mobile number
        $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson($this->baseUrl . '/verify/mobile', [
                'code' => $verificationCode
            ]);
    }

    /** @test */
    public function it_can_list_all_forms()
    {
        $category = Category::factory()->create(['name' => 'Test Category']);
        $subcategory = SubCategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Test Subcategory'
        ]);

        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $subcategory->id
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/forms');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'body' => [
                    'message',
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'subcategory' => [
                                'id',
                                'name',
                                'category' => [
                                    'id',
                                    'name'
                                ]
                            ]
                        ]
                    ]
                ]
            ])
            ->assertJson([
                'message' => 'success',
                'body' => [
                    'message' => 'Forms fetched successfully.',
                    'data' => [
                        [
                            'id' => $form->id,
                            'name' => 'Test Form',
                            'description' => 'Test Description',
                            'subcategory' => [
                                'id' => $subcategory->id,
                                'name' => 'Test Subcategory',
                                'category' => [
                                    'id' => $category->id,
                                    'name' => 'Test Category'
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_get_forms_by_subcategory()
    {
        $category = Category::factory()->create(['name' => 'Test Category']);
        $subcategory = SubCategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Test Subcategory'
        ]);

        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $subcategory->id
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/forms/subcategory/' . $subcategory->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'success',
                'body' => [
                    'message' => 'Forms fetched successfully.',
                    'data' => [
                        [
                            'id' => $form->id,
                            'name' => 'Test Form',
                            'description' => 'Test Description'
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_subcategory()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/forms/subcategory/999999');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'failed',
                'errors' => 'No forms found for this subcategory.'
            ]);
    }

    /** @test */
    public function it_can_show_form_details()
    {
        $category = Category::factory()->create(['name' => 'Test Category']);
        $subcategory = SubCategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Test Subcategory'
        ]);

        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $subcategory->id
        ]);

        $page = DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => 1,
            'title' => 'Test Page',
            'sort_no' => 1
        ]);

        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $page->id,
            'user_id' => 1,
            'input_field_label' => 'Test Field',
            'input_field_name' => 'test_field',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 1,
            'data' => json_encode(['placeholder' => 'Enter text'])
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/forms/' . $form->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'body' => [
                    'message',
                    'data' => [
                        'dynamic_form_id',
                        'name',
                        'description',
                        'subcategory' => [
                            'id',
                            'name',
                            'category' => [
                                'id',
                                'name'
                            ]
                        ],
                        'dynamic_form_pages' => [
                            '*' => [
                                'dynamic_form_page_id',
                                'title',
                                'sort_no',
                                'dynamic_form_fields' => [
                                    '*' => [
                                        'field_id',
                                        'input_field_label',
                                        'input_field_name',
                                        'input_field_type',
                                        'is_required',
                                        'sort_no',
                                        'data',
                                        'value'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_form()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/forms/999999');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'failed',
                'errors' => 'Form not found.'
            ]);
    }

    /** @test */
    public function it_shows_form_with_submitted_values_if_exists()
    {
        $category = Category::factory()->create(['name' => 'Test Category']);
        $subcategory = SubCategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Test Subcategory'
        ]);

        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $subcategory->id
        ]);

        $page = DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => 1,
            'title' => 'Test Page',
            'sort_no' => 1
        ]);

        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $page->id,
            'user_id' => 1,
            'input_field_label' => 'Test Field',
            'input_field_name' => 'test_field',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 1,
            'data' => json_encode(['placeholder' => 'Enter text'])
        ]);

        // Create a submission
        $submissionData = [
            'dynamic_form_pages' => [
                [
                    'dynamic_form_page_id' => $page->id,
                    'dynamic_form_fields' => [
                        [
                            'field_id' => $field->id,
                            'value' => 'Test Value'
                        ]
                    ]
                ]
            ]
        ];

        DynamicFormSubmission::create([
            'dynamic_form_id' => $form->id,
            'user_id' => auth()->id(),
            'data' => json_encode($submissionData)
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/forms/' . $form->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'success',
                'body' => [
                    'message' => 'Form was fetched successfully.',
                    'data' => [
                        'dynamic_form_id' => $form->id,
                        'name' => 'Test Form',
                        'description' => 'Test Description',
                        'dynamic_form_pages' => [
                            [
                                'dynamic_form_page_id' => $page->id,
                                'title' => 'Test Page',
                                'sort_no' => 1,
                                'dynamic_form_fields' => [
                                    [
                                        'field_id' => $field->id,
                                        'input_field_label' => 'Test Field',
                                        'input_field_name' => 'test_field',
                                        'input_field_type' => 'text',
                                        'is_required' => true,
                                        'sort_no' => 1,
                                        'value' => 'Test Value'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_returns_405_for_edit_method()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/forms/1/edit');

        $response->assertStatus(405)
            ->assertJson([
                'message' => 'failed',
                'errors' => 'Method not supported.'
            ]);
    }

    /** @test */
    public function it_returns_403_for_update_method()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson($this->clientBaseUrl . '/forms/1', [
                'name' => 'Updated Form'
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'failed',
                'errors' => 'Forms can only be managed by administrators.'
            ]);
    }

    /** @test */
    public function it_returns_403_for_destroy_method()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson($this->clientBaseUrl . '/forms/1');

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'failed',
                'errors' => 'Forms can only be managed by administrators.'
            ]);
    }
}
