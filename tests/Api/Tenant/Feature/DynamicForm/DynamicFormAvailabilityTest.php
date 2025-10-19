<?php

namespace Tests\Api\Tenant\Feature\DynamicForm;

use App\Models\DynamicFormAvailability;
use App\Models\DynamicForm;
use App\Models\Store;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase\TenantTestCase;

class DynamicFormAvailabilityTest extends TenantTestCase
{
    use RefreshDatabase;

    private string $baseUrl;
    private string $token;
    private User $user;
    private DynamicForm $dynamicForm;
    private Store $store;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up URLs
        $this->baseUrl = $this->getTestUrl('/api/v1');
        
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

        $this->user = User::where('email', 'test.owner@renturo.test')->first();

        // Create test data
        $category = Category::factory()->create(['name' => 'Sports']);
        $subCategory = SubCategory::factory()->create([
            'name' => 'Basketball',
            'category_id' => $category->id
        ]);

        $this->dynamicForm = DynamicForm::factory()->create([
            'name' => 'Basketball Court Form',
            'sub_category_id' => $subCategory->id
        ]);

        $this->store = Store::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Test Store'
        ]);
    }

    /** @test */
    public function it_can_create_form_availability()
    {
        $availabilityData = [
            'dynamic_form_id' => $this->dynamicForm->id,
            'store_id' => $this->store->id,
            'recurring' => [
                'monday' => ['09:00-12:00', '14:00-17:00'],
                'tuesday' => ['09:00-17:00'],
                'wednesday' => ['09:00-12:00'],
            ]
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson($this->baseUrl . '/form-availability', $availabilityData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'body' => [
                    'message',
                    'data' => [
                        'id',
                        'dynamic_form_id',
                        'store_id',
                        'recurring',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ])
            ->assertJsonFragment([
                'message' => 'Dynamic Form Availability Created Successfully'
            ]);

        $this->assertDatabaseHas('dynamic_form_availabilities', [
            'dynamic_form_id' => $this->dynamicForm->id,
            'store_id' => $this->store->id,
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_availability()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson($this->baseUrl . '/form-availability', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['dynamic_form_id', 'store_id']);
    }

    /** @test */
    public function it_validates_dynamic_form_exists()
    {
        $availabilityData = [
            'dynamic_form_id' => 99999, // Non-existent
            'store_id' => $this->store->id,
            'recurring' => [
                'monday' => ['09:00-12:00']
            ]
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson($this->baseUrl . '/form-availability', $availabilityData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['dynamic_form_id']);
    }

    /** @test */
    public function it_validates_store_exists()
    {
        $availabilityData = [
            'dynamic_form_id' => $this->dynamicForm->id,
            'store_id' => 99999, // Non-existent
            'recurring' => [
                'monday' => ['09:00-12:00']
            ]
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson($this->baseUrl . '/form-availability', $availabilityData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['store_id']);
    }

    /** @test */
    public function it_can_list_all_availabilities()
    {
        // Create multiple availabilities
        DynamicFormAvailability::factory()->count(3)->create([
            'dynamic_form_id' => $this->dynamicForm->id,
            'store_id' => $this->store->id
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->baseUrl . '/form-availability');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'body' => [
                    'message',
                    'data' => [
                        '*' => [
                            'id',
                            'dynamic_form_id',
                            'store_id',
                            'recurring',
                            'created_at',
                            'updated_at'
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_show_specific_availability()
    {
        $availability = DynamicFormAvailability::factory()->create([
            'dynamic_form_id' => $this->dynamicForm->id,
            'store_id' => $this->store->id,
            'recurring' => json_encode([
                'monday' => ['09:00-12:00', '14:00-17:00'],
                'friday' => ['09:00-15:00']
            ])
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->baseUrl . '/form-availability/' . $availability->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'body' => [
                    'message',
                    'data' => [
                        'id',
                        'dynamic_form_id',
                        'store_id',
                        'recurring',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ])
            ->assertJsonFragment([
                'id' => $availability->id,
                'dynamic_form_id' => $this->dynamicForm->id,
                'store_id' => $this->store->id
            ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_availability()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->baseUrl . '/form-availability/99999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_update_availability()
    {
        $availability = DynamicFormAvailability::factory()->create([
            'dynamic_form_id' => $this->dynamicForm->id,
            'store_id' => $this->store->id,
            'recurring' => json_encode([
                'monday' => ['09:00-12:00']
            ])
        ]);

        $updateData = [
            'recurring' => [
                'monday' => ['09:00-17:00'],
                'tuesday' => ['09:00-17:00'],
                'wednesday' => ['09:00-17:00']
            ]
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson($this->baseUrl . '/form-availability/' . $availability->id, $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Form availability updated successfully'
            ]);

        $availability->refresh();
        $recurring = json_decode($availability->recurring, true);
        $this->assertArrayHasKey('monday', $recurring);
        $this->assertArrayHasKey('tuesday', $recurring);
        $this->assertArrayHasKey('wednesday', $recurring);
    }

    /** @test */
    public function it_can_delete_availability()
    {
        $availability = DynamicFormAvailability::factory()->create([
            'dynamic_form_id' => $this->dynamicForm->id,
            'store_id' => $this->store->id
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson($this->baseUrl . '/form-availability/' . $availability->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Form availability deleted successfully'
            ]);

        $this->assertDatabaseMissing('dynamic_form_availabilities', [
            'id' => $availability->id
        ]);
    }

    /** @test */
    public function it_returns_404_when_deleting_nonexistent_availability()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson($this->baseUrl . '/form-availability/99999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_requires_authentication_to_create_availability()
    {
        $availabilityData = [
            'dynamic_form_id' => $this->dynamicForm->id,
            'store_id' => $this->store->id,
            'recurring' => [
                'monday' => ['09:00-12:00']
            ]
        ];

        $response = $this->postJson($this->baseUrl . '/form-availability', $availabilityData);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_requires_authentication_to_list_availabilities()
    {
        $response = $this->getJson($this->baseUrl . '/form-availability');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_requires_authentication_to_update_availability()
    {
        $availability = DynamicFormAvailability::factory()->create([
            'dynamic_form_id' => $this->dynamicForm->id,
            'store_id' => $this->store->id
        ]);

        $response = $this->putJson($this->baseUrl . '/form-availability/' . $availability->id, [
            'recurring' => ['monday' => ['09:00-17:00']]
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_requires_authentication_to_delete_availability()
    {
        $availability = DynamicFormAvailability::factory()->create([
            'dynamic_form_id' => $this->dynamicForm->id,
            'store_id' => $this->store->id
        ]);

        $response = $this->deleteJson($this->baseUrl . '/form-availability/' . $availability->id);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_can_create_availability_with_complex_schedule()
    {
        $availabilityData = [
            'dynamic_form_id' => $this->dynamicForm->id,
            'store_id' => $this->store->id,
            'recurring' => [
                'monday' => ['06:00-09:00', '12:00-14:00', '18:00-21:00'],
                'tuesday' => ['06:00-21:00'],
                'wednesday' => ['06:00-09:00', '12:00-14:00', '18:00-21:00'],
                'thursday' => ['06:00-21:00'],
                'friday' => ['06:00-09:00', '12:00-14:00', '18:00-21:00'],
                'saturday' => ['06:00-22:00'],
                'sunday' => ['08:00-20:00']
            ]
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson($this->baseUrl . '/form-availability', $availabilityData);

        $response->assertStatus(201);

        $availability = DynamicFormAvailability::latest()->first();
        $recurring = json_decode($availability->recurring, true);
        
        $this->assertCount(7, $recurring);
        $this->assertCount(3, $recurring['monday']);
        $this->assertCount(1, $recurring['tuesday']);
    }

    /** @test */
    public function it_stores_recurring_as_json_string()
    {
        $availabilityData = [
            'dynamic_form_id' => $this->dynamicForm->id,
            'store_id' => $this->store->id,
            'recurring' => [
                'monday' => ['09:00-12:00']
            ]
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson($this->baseUrl . '/form-availability', $availabilityData);

        $response->assertStatus(201);

        $availability = DynamicFormAvailability::latest()->first();
        
        // Verify it's stored as JSON string
        $this->assertIsString($availability->recurring);
        
        // Verify it can be decoded
        $decoded = json_decode($availability->recurring, true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('monday', $decoded);
    }

    /** @test */
    public function it_can_filter_availabilities_by_store()
    {
        $store2 = Store::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Another Store'
        ]);

        // Create availabilities for different stores
        DynamicFormAvailability::factory()->count(2)->create([
            'dynamic_form_id' => $this->dynamicForm->id,
            'store_id' => $this->store->id
        ]);

        DynamicFormAvailability::factory()->count(3)->create([
            'dynamic_form_id' => $this->dynamicForm->id,
            'store_id' => $store2->id
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->baseUrl . '/form-availability?store_id=' . $this->store->id);

        $response->assertStatus(200);
        
        $data = $response->json('body.data');
        $this->assertCount(2, array_filter($data, function($item) {
            return $item['store_id'] === $this->store->id;
        }));
    }

    /** @test */
    public function it_can_filter_availabilities_by_form()
    {
        $form2 = DynamicForm::factory()->create([
            'name' => 'Another Form',
            'sub_category_id' => $this->dynamicForm->sub_category_id
        ]);

        // Create availabilities for different forms
        DynamicFormAvailability::factory()->count(2)->create([
            'dynamic_form_id' => $this->dynamicForm->id,
            'store_id' => $this->store->id
        ]);

        DynamicFormAvailability::factory()->create([
            'dynamic_form_id' => $form2->id,
            'store_id' => $this->store->id
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->baseUrl . '/form-availability?dynamic_form_id=' . $this->dynamicForm->id);

        $response->assertStatus(200);
        
        $data = $response->json('body.data');
        foreach ($data as $item) {
            if (isset($item['dynamic_form_id'])) {
                $this->assertEquals($this->dynamicForm->id, $item['dynamic_form_id']);
            }
        }
    }
}

