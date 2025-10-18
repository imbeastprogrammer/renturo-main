<?php

namespace Tests\Api\Tenant\Feature\DynamicForm;

use App\Models\DynamicForm;
use App\Models\DynamicFormPage;
use App\Models\DynamicFormField;
use App\Models\DynamicFormSubmission;
use App\Models\SubCategory;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase\TenantTestCase;

class DynamicFormSubmissionTest extends TenantTestCase
{
    use RefreshDatabase;

    private string $baseUrl;
    private string $clientBaseUrl;
    private string $token;
    private $form;
    private $page;
    private $field;
    private $store;

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

        // Create test data
        $category = Category::factory()->create(['name' => 'Test Category']);
        $subcategory = SubCategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Test Subcategory'
        ]);

        $this->form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $subcategory->id
        ]);

        $this->page = DynamicFormPage::create([
            'dynamic_form_id' => $this->form->id,
            'user_id' => 1,
            'title' => 'Test Page',
            'sort_no' => 1
        ]);

        $this->field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => 1,
            'input_field_label' => 'Test Field',
            'input_field_name' => 'test_field',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 1,
            'data' => json_encode(['placeholder' => 'Enter text'])
        ]);

        // Create a store for the authenticated user
        $this->store = Store::create([
            'user_id' => auth()->id(),
            'name' => 'Test Store',
            'description' => 'Test Store Description'
        ]);
    }

    /** @test */
    public function it_can_submit_form()
    {
        $submissionData = [
            'store_id' => $this->store->id,
            'dynamic_form_id' => $this->form->id,
            'name' => 'Test Submission',
            'about' => 'Test Submission Description',
            'dynamic_form_pages' => [
                [
                    'dynamic_form_page_id' => $this->page->id,
                    'dynamic_form_fields' => [
                        [
                            'field_id' => $this->field->id,
                            'value' => 'Test Value'
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson($this->clientBaseUrl . '/forms/' . $this->form->id . '/submit', $submissionData);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'success',
                'body' => [
                    'message' => 'Form was submitted successfully.'
                ]
            ]);

        $this->assertDatabaseHas('dynamic_form_submissions', [
            'dynamic_form_id' => $this->form->id,
            'user_id' => auth()->id(),
            'store_id' => $this->store->id,
            'name' => 'Test Submission',
            'about' => 'Test Submission Description'
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $submissionData = [
            'store_id' => $this->store->id,
            'dynamic_form_id' => $this->form->id,
            'dynamic_form_pages' => [
                [
                    'dynamic_form_page_id' => $this->page->id,
                    'dynamic_form_fields' => [
                        [
                            'field_id' => $this->field->id,
                            'value' => '' // Empty value for required field
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson($this->clientBaseUrl . '/forms/' . $this->form->id . '/submit', $submissionData);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_prevents_duplicate_submissions()
    {
        $submissionData = [
            'store_id' => $this->store->id,
            'dynamic_form_id' => $this->form->id,
            'name' => 'Test Submission',
            'dynamic_form_pages' => [
                [
                    'dynamic_form_page_id' => $this->page->id,
                    'dynamic_form_fields' => [
                        [
                            'field_id' => $this->field->id,
                            'value' => 'Test Value'
                        ]
                    ]
                ]
            ]
        ];

        // First submission
        $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson($this->clientBaseUrl . '/forms/' . $this->form->id . '/submit', $submissionData);

        // Try to submit again
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson($this->clientBaseUrl . '/forms/' . $this->form->id . '/submit', $submissionData);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'failed',
                'errors' => 'You have already submitted this form.'
            ]);
    }

    /** @test */
    public function it_can_get_user_submissions()
    {
        // Create a submission
        $submission = DynamicFormSubmission::create([
            'dynamic_form_id' => $this->form->id,
            'user_id' => auth()->id(),
            'store_id' => $this->store->id,
            'name' => 'Test Submission',
            'about' => 'Test Description',
            'data' => json_encode([
                [
                    'dynamic_form_page_id' => $this->page->id,
                    'dynamic_form_fields' => [
                        [
                            'field_id' => $this->field->id,
                            'value' => 'Test Value'
                        ]
                    ]
                ]
            ])
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/forms/user/' . auth()->id());

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'success',
                'body' => [
                    'message' => 'Form submissions were fetched successfully.',
                    'data' => [
                        [
                            'form_name' => 'Test Form',
                            'name' => 'Test Submission',
                            'about' => 'Test Description'
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_get_submissions_by_store()
    {
        // Create a submission
        $submission = DynamicFormSubmission::create([
            'dynamic_form_id' => $this->form->id,
            'user_id' => auth()->id(),
            'store_id' => $this->store->id,
            'name' => 'Test Submission',
            'data' => json_encode([])
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/forms/user/' . auth()->id() . '/store/' . $this->store->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'body' => [
                    'message',
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'form_description',
                            'category',
                            'sub_category',
                            'dynamic_form_submission'
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_prevents_accessing_other_users_submissions()
    {
        $otherUserId = 999;

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/forms/user/' . $otherUserId);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'failed',
                'errors' => 'Unauthorized. You can only view your own submissions.'
            ]);
    }

    /** @test */
    public function it_can_delete_submission()
    {
        $submission = DynamicFormSubmission::create([
            'dynamic_form_id' => $this->form->id,
            'user_id' => auth()->id(),
            'store_id' => $this->store->id,
            'name' => 'Test Submission',
            'data' => json_encode([])
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson($this->clientBaseUrl . '/forms/submissions/' . $submission->id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'body' => [
                    'message' => 'Form submission was successfully deleted.'
                ]
            ]);

        $this->assertSoftDeleted($submission);
    }

    /** @test */
    public function it_prevents_deleting_other_users_submissions()
    {
        // Create a submission for another user
        $submission = DynamicFormSubmission::create([
            'dynamic_form_id' => $this->form->id,
            'user_id' => 999, // Different user
            'store_id' => $this->store->id,
            'name' => 'Test Submission',
            'data' => json_encode([])
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson($this->clientBaseUrl . '/forms/submissions/' . $submission->id);

        $response->assertStatus(404); // 404 instead of 403 for security
    }

    /** @test */
    public function it_validates_store_ownership()
    {
        // Create a store owned by another user
        $otherStore = Store::create([
            'user_id' => 999,
            'name' => 'Other Store',
            'description' => 'Other Store Description'
        ]);

        $submissionData = [
            'store_id' => $otherStore->id, // Try to submit for another user's store
            'dynamic_form_id' => $this->form->id,
            'name' => 'Test Submission',
            'dynamic_form_pages' => [
                [
                    'dynamic_form_page_id' => $this->page->id,
                    'dynamic_form_fields' => [
                        [
                            'field_id' => $this->field->id,
                            'value' => 'Test Value'
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson($this->clientBaseUrl . '/forms/' . $this->form->id . '/submit', $submissionData);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'store_id' => ['You are not authorized to add a submission for this store.']
                ]
            ]);
    }
}
