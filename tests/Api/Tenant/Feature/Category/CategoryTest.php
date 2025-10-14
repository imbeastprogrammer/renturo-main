<?php

namespace Tests\Api\Tenant\Feature\Category;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase\TenantTestCase;

class CategoryTest extends TenantTestCase
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
     * Test can create a category
     */
    public function test_can_create_category(): void
    {
        $categoryData = [
            'name' => 'Test Category'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson($this->clientBaseUrl . '/categories', $categoryData);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Category created successfully.',
                'data' => [
                    'name' => 'Test Category'
                ]
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['id', 'name', 'created_at', 'updated_at']
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category'
        ]);
    }

    /**
     * Test cannot create category with duplicate name
     */
    public function test_cannot_create_category_with_duplicate_name(): void
    {
        Category::factory()->create(['name' => 'Existing Category']);

        $categoryData = [
            'name' => 'Existing Category'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson($this->clientBaseUrl . '/categories', $categoryData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test cannot create category without name
     */
    public function test_cannot_create_category_without_name(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson($this->clientBaseUrl . '/categories', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test can show a specific category
     */
    public function test_can_show_specific_category(): void
    {
        $category = Category::factory()->create(['name' => 'Test Category']);
        SubCategory::factory()->count(3)->create(['category_id' => $category->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/categories/' . $category->id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Category found.',
                'data' => [
                    'id' => $category->id,
                    'name' => 'Test Category'
                ]
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                    'sub_categories' => [
                        '*' => ['id', 'category_id', 'name', 'created_at', 'updated_at']
                    ]
                ]
            ]);

        $this->assertCount(3, $response->json('data.sub_categories'));
    }

    /**
     * Test returns 404 for non-existent category
     */
    public function test_returns_404_for_non_existent_category(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/categories/99999');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Category not found.'
            ]);
    }

    /**
     * Test can update a category
     */
    public function test_can_update_category(): void
    {
        $category = Category::factory()->create(['name' => 'Original Name']);

        $updateData = [
            'name' => 'Updated Name'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson($this->clientBaseUrl . '/categories/' . $category->id, $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Category updated successfully.',
                'data' => [
                    'id' => $category->id,
                    'name' => 'Updated Name'
                ]
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name'
        ]);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
            'name' => 'Original Name'
        ]);
    }

    /**
     * Test cannot update category to duplicate name
     */
    public function test_cannot_update_category_to_duplicate_name(): void
    {
        $category1 = Category::factory()->create(['name' => 'Category 1']);
        $category2 = Category::factory()->create(['name' => 'Category 2']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson($this->clientBaseUrl . '/categories/' . $category1->id, [
                'name' => 'Category 2'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test can update category with same name (no change)
     */
    public function test_can_update_category_with_same_name(): void
    {
        $category = Category::factory()->create(['name' => 'Test Category']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson($this->clientBaseUrl . '/categories/' . $category->id, [
                'name' => 'Test Category'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Category updated successfully.'
            ]);
    }

    /**
     * Test returns 404 when updating non-existent category
     */
    public function test_returns_404_when_updating_non_existent_category(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson($this->clientBaseUrl . '/categories/99999', [
                'name' => 'Updated Name'
            ]);

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Category not found.'
            ]);
    }

    /**
     * Test can delete a category
     */
    public function test_can_delete_category(): void
    {
        $category = Category::factory()->create(['name' => 'Category to Delete']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson($this->clientBaseUrl . '/categories/' . $category->id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Category deleted successfully.'
            ]);

        $this->assertSoftDeleted('categories', [
            'id' => $category->id
        ]);
    }

    /**
     * Test deleting category does not delete subcategories
     */
    public function test_deleting_category_does_not_delete_subcategories(): void
    {
        $category = Category::factory()->create();
        $subCategory = SubCategory::factory()->create(['category_id' => $category->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson($this->clientBaseUrl . '/categories/' . $category->id);

        $response->assertStatus(200);

        $this->assertSoftDeleted('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('sub_categories', [
            'id' => $subCategory->id,
            'deleted_at' => null
        ]);
    }

    /**
     * Test returns 404 when deleting non-existent category
     */
    public function test_returns_404_when_deleting_non_existent_category(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson($this->clientBaseUrl . '/categories/99999');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Category not found.'
            ]);
    }

    /**
     * Test deleted categories are not shown in list
     */
    public function test_deleted_categories_are_not_shown_in_list(): void
    {
        $activeCategory = Category::factory()->create(['name' => 'Active Category']);
        $deletedCategory = Category::factory()->create(['name' => 'Deleted Category']);
        $deletedCategory->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/categories');

        $response->assertStatus(200);

        $categoryNames = collect($response->json('data'))->pluck('name')->toArray();
        
        $this->assertContains('Active Category', $categoryNames);
        $this->assertNotContains('Deleted Category', $categoryNames);
    }

    /**
     * Test deleted category cannot be shown
     */
    public function test_deleted_category_cannot_be_shown(): void
    {
        $category = Category::factory()->create();
        $category->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/categories/' . $category->id);

        $response->assertStatus(404);
    }

    /**
     * Test deleted category cannot be updated
     */
    public function test_deleted_category_cannot_be_updated(): void
    {
        $category = Category::factory()->create();
        $category->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson($this->clientBaseUrl . '/categories/' . $category->id, [
                'name' => 'Updated Name'
            ]);

        $response->assertStatus(404);
    }

    // ========================================
    // SEARCH TESTS
    // ========================================

    /**
     * Test can search categories by name
     */
    public function test_can_search_categories_by_name(): void
    {
        Category::factory()->create(['name' => 'Residential']);
        Category::factory()->create(['name' => 'Commercial']);
        Category::factory()->create(['name' => 'Vehicles']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/categories/search?query=Res');

        $response->assertStatus(200)
            ->assertJsonCount(1);

        $data = $response->json();
        $this->assertEquals('Residential', $data[0]['name']);
    }

    /**
     * Test can search categories by subcategory name
     */
    public function test_can_search_categories_by_subcategory_name(): void
    {
        $category = Category::factory()->create(['name' => 'Residential']);
        SubCategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Apartment'
        ]);

        $otherCategory = Category::factory()->create(['name' => 'Vehicles']);
        SubCategory::factory()->create([
            'category_id' => $otherCategory->id,
            'name' => 'Car'
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/categories/search?query=Apart');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertGreaterThanOrEqual(1, count($data));
        
        $foundCategory = collect($data)->firstWhere('name', 'Residential');
        $this->assertNotNull($foundCategory);
    }

    /**
     * Test search requires minimum 3 characters
     */
    public function test_search_requires_minimum_3_characters(): void
    {
        Category::factory()->create(['name' => 'Residential']);

        // Test with 2 characters
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/categories/search?query=Re');

        $response->assertStatus(200)
            ->assertJsonCount(0);

        // Test with 1 character
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/categories/search?query=R');

        $response->assertStatus(200)
            ->assertJsonCount(0);
    }

    /**
     * Test search returns empty array for no matches
     */
    public function test_search_returns_empty_array_for_no_matches(): void
    {
        Category::factory()->create(['name' => 'Residential']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/categories/search?query=NonExistent');

        $response->assertStatus(200)
            ->assertJsonCount(0);
    }

    /**
     * Test search limits results to 10
     */
    public function test_search_limits_results_to_10(): void
    {
        // Create 15 categories
        for ($i = 1; $i <= 15; $i++) {
            Category::factory()->create(['name' => "Test Category {$i}"]);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/categories/search?query=Test');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertLessThanOrEqual(10, count($data));
    }

    /**
     * Test search is case insensitive
     */
    public function test_search_is_case_insensitive(): void
    {
        Category::factory()->create(['name' => 'Residential']);

        // Test uppercase
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/categories/search?query=RESIDENTIAL');

        $response->assertStatus(200)
            ->assertJsonCount(1);

        // Test lowercase
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/categories/search?query=residential');

        $response->assertStatus(200)
            ->assertJsonCount(1);

        // Test mixed case
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/categories/search?query=ReSiDeNtIaL');

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    /**
     * Test empty query parameter returns empty array
     */
    public function test_empty_query_parameter_returns_empty_array(): void
    {
        Category::factory()->create(['name' => 'Residential']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/categories/search?query=');

        $response->assertStatus(200)
            ->assertJsonCount(0);
    }

    /**
     * Test search with special characters
     */
    public function test_search_with_special_characters(): void
    {
        Category::factory()->create(['name' => 'Equipment & Tools']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson($this->clientBaseUrl . '/categories/search?query=Equipment & Tools');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertGreaterThanOrEqual(1, count($data));
    }
}

