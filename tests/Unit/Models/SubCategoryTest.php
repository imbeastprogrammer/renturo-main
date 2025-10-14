<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\DynamicForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase\TenantTestCase;

class SubCategoryTest extends TenantTestCase
{
    use RefreshDatabase;

    /**
     * Test subcategory can be created
     */
    public function test_subcategory_can_be_created(): void
    {
        $category = Category::factory()->create();
        
        $subCategory = SubCategory::create([
            'category_id' => $category->id,
            'name' => 'Test SubCategory'
        ]);

        $this->assertInstanceOf(SubCategory::class, $subCategory);
        $this->assertEquals('Test SubCategory', $subCategory->name);
        $this->assertEquals($category->id, $subCategory->category_id);
        $this->assertDatabaseHas('sub_categories', [
            'name' => 'Test SubCategory',
            'category_id' => $category->id
        ]);
    }

    /**
     * Test subcategory has fillable attributes
     */
    public function test_subcategory_has_fillable_attributes(): void
    {
        $subCategory = new SubCategory();
        $fillable = $subCategory->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('category_id', $fillable);
    }

    /**
     * Test subcategory uses soft deletes
     */
    public function test_subcategory_uses_soft_deletes(): void
    {
        $subCategory = SubCategory::factory()->create();
        
        $subCategory->delete();

        $this->assertSoftDeleted('sub_categories', [
            'id' => $subCategory->id
        ]);

        // Verify it's not in regular queries
        $this->assertNull(SubCategory::find($subCategory->id));

        // Verify it exists in trashed queries
        $this->assertNotNull(SubCategory::withTrashed()->find($subCategory->id));
    }

    /**
     * Test subcategory can be restored after soft delete
     */
    public function test_subcategory_can_be_restored(): void
    {
        $subCategory = SubCategory::factory()->create();
        
        $subCategory->delete();
        $this->assertSoftDeleted('sub_categories', ['id' => $subCategory->id]);

        $subCategory->restore();
        $this->assertDatabaseHas('sub_categories', [
            'id' => $subCategory->id,
            'deleted_at' => null
        ]);
    }

    /**
     * Test subcategory belongs to category relationship
     */
    public function test_subcategory_belongs_to_category(): void
    {
        $category = Category::factory()->create(['name' => 'Parent Category']);
        $subCategory = SubCategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Child SubCategory'
        ]);

        $this->assertInstanceOf(Category::class, $subCategory->category);
        $this->assertEquals($category->id, $subCategory->category->id);
        $this->assertEquals('Parent Category', $subCategory->category->name);
    }

    /**
     * Test subcategory has many dynamic forms relationship
     */
    public function test_subcategory_has_many_dynamic_forms(): void
    {
        $subCategory = SubCategory::factory()->create();

        $relation = $subCategory->dynamicForms();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
        $this->assertEquals('subcategory_id', $relation->getForeignKeyName());
    }

    /**
     * Test subcategory name is required
     */
    public function test_subcategory_name_is_required(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        $category = Category::factory()->create();
        SubCategory::create([
            'category_id' => $category->id,
            'name' => null
        ]);
    }

    /**
     * Test subcategory category_id is required
     */
    public function test_subcategory_category_id_is_required(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        SubCategory::create([
            'category_id' => null,
            'name' => 'Test SubCategory'
        ]);
    }

    /**
     * Test subcategory factory creates valid subcategory
     */
    public function test_subcategory_factory_creates_valid_subcategory(): void
    {
        $subCategory = SubCategory::factory()->create();

        $this->assertInstanceOf(SubCategory::class, $subCategory);
        $this->assertNotEmpty($subCategory->name);
        $this->assertNotNull($subCategory->category_id);
        $this->assertDatabaseHas('sub_categories', [
            'id' => $subCategory->id
        ]);
    }

    /**
     * Test subcategory factory can create for specific category
     */
    public function test_subcategory_factory_can_create_for_category(): void
    {
        $category = Category::factory()->create(['name' => 'Specific Category']);
        $subCategory = SubCategory::factory()->forCategory($category)->create();

        $this->assertEquals($category->id, $subCategory->category_id);
        $this->assertEquals('Specific Category', $subCategory->category->name);
    }

    /**
     * Test subcategory factory can create apartment subcategory
     */
    public function test_subcategory_factory_can_create_apartment(): void
    {
        $subCategory = SubCategory::factory()->apartment()->create();

        $this->assertEquals('Apartment', $subCategory->name);
    }

    /**
     * Test subcategory factory can create car subcategory
     */
    public function test_subcategory_factory_can_create_car(): void
    {
        $subCategory = SubCategory::factory()->car()->create();

        $this->assertEquals('Car', $subCategory->name);
    }

    /**
     * Test multiple subcategories can belong to same category
     */
    public function test_multiple_subcategories_can_belong_to_same_category(): void
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

        $this->assertEquals($category->id, $subCategory1->category_id);
        $this->assertEquals($category->id, $subCategory2->category_id);
        $this->assertCount(2, $category->subCategories);
    }

    /**
     * Test subcategory can be queried by category
     */
    public function test_subcategory_can_be_queried_by_category(): void
    {
        $category = Category::factory()->create();
        $otherCategory = Category::factory()->create();
        
        $subCategory1 = SubCategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'SubCategory 1'
        ]);
        
        $subCategory2 = SubCategory::factory()->create([
            'category_id' => $otherCategory->id,
            'name' => 'SubCategory 2'
        ]);

        $results = SubCategory::where('category_id', $category->id)->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($subCategory1));
        $this->assertFalse($results->contains($subCategory2));
    }
}

