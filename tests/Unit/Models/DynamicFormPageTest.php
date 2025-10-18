<?php

namespace Tests\Unit\Models;

use App\Models\DynamicForm;
use App\Models\DynamicFormPage;
use App\Models\DynamicFormField;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase\TenantTestCase;

class DynamicFormPageTest extends TenantTestCase
{
    use RefreshDatabase;

    private $category;
    private $subcategory;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a category and subcategory for testing
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
    }

    /** @test */
    public function it_can_create_dynamic_form_page()
    {
        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $page = DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => 1,
            'title' => 'Test Page',
            'sort_no' => 0
        ]);

        $this->assertInstanceOf(DynamicFormPage::class, $page);
        $this->assertEquals('Test Page', $page->title);
        $this->assertEquals(0, $page->sort_no);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = (new DynamicFormPage())->getFillable();

        $this->assertEqualsCanonicalizing([
            'user_id',
            'dynamic_form_id',
            'title',
            'sort_no'
        ], $fillable);
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $page = DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => 1,
            'title' => 'Test Page',
            'sort_no' => 0
        ]);

        $page->delete();

        $this->assertSoftDeleted($page);
    }

    /** @test */
    public function it_belongs_to_dynamic_form()
    {
        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $page = DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => 1,
            'title' => 'Test Page',
            'sort_no' => 0
        ]);

        $this->assertInstanceOf(DynamicForm::class, $page->dynamicForm);
        $this->assertEquals($form->id, $page->dynamicForm->id);
    }

    /** @test */
    public function it_belongs_to_user()
    {
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'mobile_number' => '1234567890'
        ]);

        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $page = DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => $user->id,
            'title' => 'Test Page',
            'sort_no' => 0
        ]);

        $this->assertInstanceOf(User::class, $page->user);
        $this->assertEquals($user->id, $page->user->id);
    }

    /** @test */
    public function it_has_many_dynamic_form_fields()
    {
        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $page = DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => 1,
            'title' => 'Test Page',
            'sort_no' => 0
        ]);

        DynamicFormField::create([
            'dynamic_form_page_id' => $page->id,
            'user_id' => 1,
            'input_field_label' => 'Test Field 1',
            'input_field_name' => 'test_field_1',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0
        ]);

        DynamicFormField::create([
            'dynamic_form_page_id' => $page->id,
            'user_id' => 1,
            'input_field_label' => 'Test Field 2',
            'input_field_name' => 'test_field_2',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 1
        ]);

        $this->assertCount(2, $page->dynamicFormFields);
        $this->assertInstanceOf(DynamicFormField::class, $page->dynamicFormFields->first());
    }

    /** @test */
    public function it_orders_fields_by_sort_no()
    {
        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $page = DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => 1,
            'title' => 'Test Page',
            'sort_no' => 0
        ]);

        $field2 = DynamicFormField::create([
            'dynamic_form_page_id' => $page->id,
            'user_id' => 1,
            'input_field_label' => 'Test Field 2',
            'input_field_name' => 'test_field_2',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 1
        ]);

        $field1 = DynamicFormField::create([
            'dynamic_form_page_id' => $page->id,
            'user_id' => 1,
            'input_field_label' => 'Test Field 1',
            'input_field_name' => 'test_field_1',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $fields = $page->dynamicFormFields()->orderBy('sort_no')->get();
        $this->assertEquals($field1->id, $fields->first()->id);
        $this->assertEquals($field2->id, $fields->last()->id);
    }

    /** @test */
    public function it_cascades_soft_deletes_to_fields()
    {
        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $page = DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => 1,
            'title' => 'Test Page',
            'sort_no' => 0
        ]);

        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $page->id,
            'user_id' => 1,
            'input_field_label' => 'Test Field',
            'input_field_name' => 'test_field',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $page->delete();

        $this->assertSoftDeleted($page);
        $this->assertSoftDeleted($field);
    }

    /** @test */
    public function it_can_be_restored_with_fields()
    {
        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $page = DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => 1,
            'title' => 'Test Page',
            'sort_no' => 0
        ]);

        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $page->id,
            'user_id' => 1,
            'input_field_label' => 'Test Field',
            'input_field_name' => 'test_field',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $page->delete();
        $page->restore();

        $this->assertNotSoftDeleted($page);
        $this->assertNotSoftDeleted($field);
    }
}
