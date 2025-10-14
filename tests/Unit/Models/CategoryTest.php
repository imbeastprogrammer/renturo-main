<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase\TenantTestCase;

class CategoryTest extends TenantTestCase
{
    use RefreshDatabase;

    /**
     * Test category can be created
     */
    public function test_category_can_be_created(): void
    {
        $category = Category::create([
            'name' => 'Test Category'
        ]);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('Test Category', $category->name);
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category'
        ]);
    }

    /**
     * Test category has fillable attributes
     */
    public function test_category_has_fillable_attributes(): void
    {
        $category = new Category();
        $fillable = $category->getFillable();

        $this->assertContains('name', $fillable);
    }

    /**
     * Test category uses soft deletes
     */
    public function test_category_uses_soft_deletes(): void
    {
        $category = Category::factory()->create();
        
        $category->delete();

        $this->assertSoftDeleted('categories', [
            'id' => $category->id
        ]);

        // Verify it's not in regular queries
        $this->assertNull(Category::find($category->id));

        // Verify it exists in trashed queries
        $this->assertNotNull(Category::withTrashed()->find($category->id));
    }

    /**
     * Test category can be restored after soft delete
     */
    public function test_category_can_be_restored(): void
    {
        $category = Category::factory()->create();
        
        $category->delete();
        $this->assertSoftDeleted('categories', ['id' => $category->id]);

        $category->restore();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'deleted_at' => null
        ]);
    }

    /**
     * Test category has many subcategories relationship
     */
    public function test_category_has_many_subcategories(): void
    {
        $category = Category::factory()->create();
        
        $subCategory1 = SubCategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'SubCategory 1'
        ]);
        
        $subCategory2 = SubCategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'SubCategory 2'
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $category->subCategories);
        $this->assertCount(2, $category->subCategories);
        $this->assertTrue($category->subCategories->contains($subCategory1));
        $this->assertTrue($category->subCategories->contains($subCategory2));
    }

    /**
     * Test category eager loads subcategories
     */
    public function test_category_eager_loads_subcategories(): void
    {
        $category = Category::factory()->create();
        SubCategory::factory()->count(3)->create([
            'category_id' => $category->id
        ]);

        // Fresh query to test eager loading
        $loadedCategory = Category::find($category->id);

        // Check that subCategories are loaded (not lazy loaded)
        $this->assertTrue($loadedCategory->relationLoaded('subCategories'));
        $this->assertCount(3, $loadedCategory->subCategories);
    }

    /**
     * Test category name is required
     */
    public function test_category_name_is_required(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Category::create([
            'name' => null
        ]);
    }

    /**
     * Test category can have stores
     */
    public function test_category_has_many_stores(): void
    {
        $category = Category::factory()->create();

        $relation = $category->stores();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
        $this->assertEquals('category_id', $relation->getForeignKeyName());
    }

    /**
     * Test category factory creates valid category
     */
    public function test_category_factory_creates_valid_category(): void
    {
        $category = Category::factory()->create();

        $this->assertInstanceOf(Category::class, $category);
        $this->assertNotEmpty($category->name);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id
        ]);
    }

    /**
     * Test category factory can create residential category
     */
    public function test_category_factory_can_create_residential(): void
    {
        $category = Category::factory()->residential()->create();

        $this->assertEquals('Residential', $category->name);
    }

    /**
     * Test category factory can create commercial category
     */
    public function test_category_factory_can_create_commercial(): void
    {
        $category = Category::factory()->commercial()->create();

        $this->assertEquals('Commercial', $category->name);
    }

    /**
     * Test category factory can create vehicles category
     */
    public function test_category_factory_can_create_vehicles(): void
    {
        $category = Category::factory()->vehicles()->create();

        $this->assertEquals('Vehicles', $category->name);
    }

    /**
     * Test deleting category does not delete subcategories (cascade behavior)
     */
    public function test_deleting_category_soft_deletes_category_only(): void
    {
        $category = Category::factory()->create();
        $subCategory = SubCategory::factory()->create([
            'category_id' => $category->id
        ]);

        $category->delete();

        $this->assertSoftDeleted('categories', ['id' => $category->id]);
        // SubCategory should still exist (not cascaded)
        $this->assertDatabaseHas('sub_categories', [
            'id' => $subCategory->id,
            'deleted_at' => null
        ]);
    }
}

