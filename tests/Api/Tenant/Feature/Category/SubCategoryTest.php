<?php

namespace Tests\Api\Tenant\Feature\Category;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase\TenantTestCase;

class SubCategoryTest extends TenantTestCase
{
    use RefreshDatabase;

    private string $baseUrl = 'http://main.renturo.test/api/v1';
    private string $clientBaseUrl = 'http://main.renturo.test/api/client/v1';
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
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

    /**
     * Test can fetch all subcategories
     */
    public function test_can_fetch_all_subcategories(): void
    {
        $category = Category::factory()->create(['name' => 'Test Category']);
        SubCategory::factory()->count(3)->create(['category_id' => $category->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/subcategories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'category_id',
                        'name',
                        'created_at',
                        'updated_at',
                        'category' => ['id', 'name', 'created_at', 'updated_at']
                    ]
                ]
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'SubCategories was successfully fetched.'
            ]);

        $this->assertGreaterThanOrEqual(3, count($response->json('data')));
    }

    /**
     * Test can create a subcategory
     */
    public function test_can_create_subcategory(): void
    {
        $category = Category::factory()->create();

        $subCategoryData = [
            'category_id' => $category->id,
            'name' => 'Test SubCategory'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson($this->clientBaseUrl . '/subcategories', $subCategoryData);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'SubCategory created successfully.',
                'data' => [
                    'category_id' => $category->id,
                    'name' => 'Test SubCategory'
                ]
            ])
            ->assertJsonStructure([
                'data' => ['id', 'category_id', 'name', 'created_at', 'updated_at']
            ]);

        $this->assertDatabaseHas('sub_categories', [
            'category_id' => $category->id,
            'name' => 'Test SubCategory'
        ]);
    }

    /**
     * Test cannot create subcategory without name
     */
    public function test_cannot_create_subcategory_without_name(): void
    {
        $category = Category::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson($this->clientBaseUrl . '/subcategories', [
                'category_id' => $category->id
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test cannot create subcategory without category_id
     */
    public function test_cannot_create_subcategory_without_category_id(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson($this->clientBaseUrl . '/subcategories', [
                'name' => 'Test SubCategory'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category_id']);
    }

    /**
     * Test cannot create subcategory with non-existent category
     */
    public function test_cannot_create_subcategory_with_non_existent_category(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson($this->clientBaseUrl . '/subcategories', [
                'category_id' => 99999,
                'name' => 'Test SubCategory'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category_id']);
    }

    /**
     * Test can show a specific subcategory
     */
    public function test_can_show_specific_subcategory(): void
    {
        $category = Category::factory()->create(['name' => 'Test Category']);
        $subCategory = SubCategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Test SubCategory'
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/subcategories/' . $subCategory->id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'SubCategory found.',
                'data' => [
                    'id' => $subCategory->id,
                    'category_id' => $category->id,
                    'name' => 'Test SubCategory'
                ]
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'category_id',
                    'name',
                    'created_at',
                    'updated_at',
                    'category' => ['id', 'name', 'created_at', 'updated_at']
                ]
            ]);
    }

    /**
     * Test returns 404 for non-existent subcategory
     */
    public function test_returns_404_for_non_existent_subcategory(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/subcategories/99999');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'SubCategory not found.'
            ]);
    }

    /**
     * Test can update a subcategory
     */
    public function test_can_update_subcategory(): void
    {
        $category = Category::factory()->create();
        $subCategory = SubCategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Original Name'
        ]);

        $updateData = [
            'name' => 'Updated Name'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson($this->clientBaseUrl . '/subcategories/' . $subCategory->id, $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'SubCategory updated successfully.',
                'data' => [
                    'id' => $subCategory->id,
                    'name' => 'Updated Name'
                ]
            ]);

        $this->assertDatabaseHas('sub_categories', [
            'id' => $subCategory->id,
            'name' => 'Updated Name'
        ]);
    }

    /**
     * Test can update subcategory category_id
     */
    public function test_can_update_subcategory_category_id(): void
    {
        $category1 = Category::factory()->create(['name' => 'Category 1']);
        $category2 = Category::factory()->create(['name' => 'Category 2']);
        
        $subCategory = SubCategory::factory()->create([
            'category_id' => $category1->id,
            'name' => 'Test SubCategory'
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson($this->clientBaseUrl . '/subcategories/' . $subCategory->id, [
                'category_id' => $category2->id
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('sub_categories', [
            'id' => $subCategory->id,
            'category_id' => $category2->id
        ]);
    }

    /**
     * Test returns 404 when updating non-existent subcategory
     */
    public function test_returns_404_when_updating_non_existent_subcategory(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson($this->clientBaseUrl . '/subcategories/99999', [
                'name' => 'Updated Name'
            ]);

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'SubCategory not found.'
            ]);
    }

    /**
     * Test can delete a subcategory
     */
    public function test_can_delete_subcategory(): void
    {
        $subCategory = SubCategory::factory()->create(['name' => 'SubCategory to Delete']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson($this->clientBaseUrl . '/subcategories/' . $subCategory->id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'SubCategory deleted successfully.'
            ]);

        $this->assertSoftDeleted('sub_categories', [
            'id' => $subCategory->id
        ]);
    }

    /**
     * Test returns 404 when deleting non-existent subcategory
     */
    public function test_returns_404_when_deleting_non_existent_subcategory(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson($this->clientBaseUrl . '/subcategories/99999');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'SubCategory not found.'
            ]);
    }

    /**
     * Test can get subcategories by category
     */
    public function test_can_get_subcategories_by_category(): void
    {
        $category1 = Category::factory()->create(['name' => 'Category 1']);
        $category2 = Category::factory()->create(['name' => 'Category 2']);
        
        SubCategory::factory()->count(3)->create(['category_id' => $category1->id]);
        SubCategory::factory()->count(2)->create(['category_id' => $category2->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/categories/' . $category1->id . '/subcategories');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'SubCategories found.'
            ])
            ->assertJsonCount(3, 'data');

        $data = $response->json('data');
        foreach ($data as $subCategory) {
            $this->assertEquals($category1->id, $subCategory['category_id']);
        }
    }

    /**
     * Test returns 404 when getting subcategories for non-existent category
     */
    public function test_returns_404_when_getting_subcategories_for_non_existent_category(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/categories/99999/subcategories');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Category not found.'
            ]);
    }

    /**
     * Test deleted subcategories are not shown in list
     */
    public function test_deleted_subcategories_are_not_shown_in_list(): void
    {
        $category = Category::factory()->create();
        $activeSubCategory = SubCategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Active SubCategory'
        ]);
        $deletedSubCategory = SubCategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Deleted SubCategory'
        ]);
        $deletedSubCategory->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/subcategories');

        $response->assertStatus(200);

        $subCategoryNames = collect($response->json('data'))->pluck('name')->toArray();
        
        $this->assertContains('Active SubCategory', $subCategoryNames);
        $this->assertNotContains('Deleted SubCategory', $subCategoryNames);
    }

    /**
     * Test deleted subcategory cannot be shown
     */
    public function test_deleted_subcategory_cannot_be_shown(): void
    {
        $subCategory = SubCategory::factory()->create();
        $subCategory->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/subcategories/' . $subCategory->id);

        $response->assertStatus(404);
    }

    /**
     * Test deleted subcategory cannot be updated
     */
    public function test_deleted_subcategory_cannot_be_updated(): void
    {
        $subCategory = SubCategory::factory()->create();
        $subCategory->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson($this->clientBaseUrl . '/subcategories/' . $subCategory->id, [
                'name' => 'Updated Name'
            ]);

        $response->assertStatus(404);
    }
}

