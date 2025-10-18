<?php

namespace Tests\Unit\Models;

use App\Models\DynamicForm;
use App\Models\DynamicFormPage;
use App\Models\DynamicFormSubmission;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase\TenantTestCase;

class DynamicFormTest extends TenantTestCase
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
    public function it_can_create_dynamic_form()
    {
        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $this->assertInstanceOf(DynamicForm::class, $form);
        $this->assertEquals('Test Form', $form->name);
        $this->assertEquals('Test Description', $form->description);
        $this->assertEquals($this->subcategory->id, $form->subcategory_id);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = (new DynamicForm())->getFillable();

        $this->assertEqualsCanonicalizing([
            'name',
            'description',
            'subcategory_id'
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

        $form->delete();

        $this->assertSoftDeleted($form);
    }

    /** @test */
    public function it_belongs_to_subcategory()
    {
        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $this->assertInstanceOf(SubCategory::class, $form->subCategory);
        $this->assertEquals($this->subcategory->id, $form->subCategory->id);
    }

    /** @test */
    public function it_has_many_dynamic_form_pages()
    {
        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => 1,
            'title' => 'Test Page 1',
            'sort_no' => 0
        ]);

        DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => 1,
            'title' => 'Test Page 2',
            'sort_no' => 1
        ]);

        $this->assertCount(2, $form->dynamicFormPages);
        $this->assertInstanceOf(DynamicFormPage::class, $form->dynamicFormPages->first());
    }

    /** @test */
    public function it_has_many_dynamic_form_submissions()
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

        DynamicFormSubmission::create([
            'dynamic_form_id' => $form->id,
            'user_id' => $user->id,
            'data' => json_encode(['field1' => 'value1']),
            'status' => 'submitted'
        ]);

        DynamicFormSubmission::create([
            'dynamic_form_id' => $form->id,
            'user_id' => $user->id,
            'data' => json_encode(['field2' => 'value2']),
            'status' => 'submitted'
        ]);

        $this->assertCount(2, $form->dynamicFormSubmissions);
        $this->assertInstanceOf(DynamicFormSubmission::class, $form->dynamicFormSubmissions->first());
    }

    /** @test */
    public function it_cascades_soft_deletes_to_pages_and_submissions()
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

        $submission = DynamicFormSubmission::create([
            'dynamic_form_id' => $form->id,
            'user_id' => $user->id,
            'data' => json_encode(['field1' => 'value1']),
            'status' => 'submitted'
        ]);

        $form->delete();

        $this->assertSoftDeleted($form);
        $this->assertSoftDeleted($page);
        $this->assertSoftDeleted($submission);
    }

    /** @test */
    public function it_can_be_restored_with_pages_and_submissions()
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

        $submission = DynamicFormSubmission::create([
            'dynamic_form_id' => $form->id,
            'user_id' => $user->id,
            'data' => json_encode(['field1' => 'value1']),
            'status' => 'submitted'
        ]);

        $form->delete();
        $form->restore();

        $this->assertNotSoftDeleted($form);
        $this->assertNotSoftDeleted($page);
        $this->assertNotSoftDeleted($submission);
    }

    /** @test */
    public function it_requires_unique_name_within_subcategory()
    {
        DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        DynamicForm::create([
            'name' => 'Test Form', // Same name in same subcategory
            'description' => 'Another Description',
            'subcategory_id' => $this->subcategory->id
        ]);
    }

    /** @test */
    public function it_allows_same_name_in_different_subcategories()
    {
        $anotherSubcategory = SubCategory::create([
            'category_id' => $this->category->id,
            'name' => 'Another Subcategory',
            'description' => 'Another Subcategory Description',
            'is_active' => true
        ]);

        $form1 = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $form2 = DynamicForm::create([
            'name' => 'Test Form', // Same name but different subcategory
            'description' => 'Another Description',
            'subcategory_id' => $anotherSubcategory->id
        ]);

        $this->assertDatabaseHas('dynamic_forms', [
            'id' => $form1->id,
            'name' => 'Test Form',
            'subcategory_id' => $this->subcategory->id
        ]);

        $this->assertDatabaseHas('dynamic_forms', [
            'id' => $form2->id,
            'name' => 'Test Form',
            'subcategory_id' => $anotherSubcategory->id
        ]);
    }

    /** @test */
    public function it_can_get_latest_submission_for_user()
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

        $submission1 = DynamicFormSubmission::create([
            'dynamic_form_id' => $form->id,
            'user_id' => $user->id,
            'data' => json_encode(['field1' => 'value1']),
            'status' => 'submitted'
        ]);

        sleep(1); // Ensure different timestamps

        $submission2 = DynamicFormSubmission::create([
            'dynamic_form_id' => $form->id,
            'user_id' => $user->id,
            'data' => json_encode(['field2' => 'value2']),
            'status' => 'submitted'
        ]);

        $latestSubmission = $form->getLatestSubmissionForUser($user->id);

        $this->assertEquals($submission2->id, $latestSubmission->id);
        $this->assertEquals($submission2->data, $latestSubmission->data);
    }

    /** @test */
    public function it_can_filter_by_subcategory()
    {
        $anotherSubcategory = SubCategory::create([
            'category_id' => $this->category->id,
            'name' => 'Another Subcategory',
            'description' => 'Another Subcategory Description',
            'is_active' => true
        ]);

        $form1 = DynamicForm::create([
            'name' => 'Test Form 1',
            'description' => 'Test Description 1',
            'subcategory_id' => $this->subcategory->id
        ]);

        $form2 = DynamicForm::create([
            'name' => 'Test Form 2',
            'description' => 'Test Description 2',
            'subcategory_id' => $this->subcategory->id
        ]);

        $form3 = DynamicForm::create([
            'name' => 'Test Form 3',
            'description' => 'Test Description 3',
            'subcategory_id' => $anotherSubcategory->id
        ]);

        $forms = DynamicForm::bySubcategory($this->subcategory->id)->get();

        $this->assertCount(2, $forms);
        $this->assertTrue($forms->contains($form1));
        $this->assertTrue($forms->contains($form2));
        $this->assertFalse($forms->contains($form3));
    }

    /** @test */
    public function it_can_search_by_name_or_description()
    {
        $form1 = DynamicForm::create([
            'name' => 'Rental Application Form',
            'description' => 'Form for rental applications',
            'subcategory_id' => $this->subcategory->id
        ]);

        $form2 = DynamicForm::create([
            'name' => 'Property Inspection Form',
            'description' => 'Form for property inspections',
            'subcategory_id' => $this->subcategory->id
        ]);

        $form3 = DynamicForm::create([
            'name' => 'Maintenance Request Form',
            'description' => 'Form for maintenance requests',
            'subcategory_id' => $this->subcategory->id
        ]);

        // Search by name
        $nameResults = DynamicForm::search('Rental')->get();
        $this->assertCount(1, $nameResults);
        $this->assertTrue($nameResults->contains($form1));

        // Search by description
        $descriptionResults = DynamicForm::search('inspection')->get();
        $this->assertCount(1, $descriptionResults);
        $this->assertTrue($descriptionResults->contains($form2));

        // Search with multiple matches
        $multiResults = DynamicForm::search('Form')->get();
        $this->assertCount(3, $multiResults);
        $this->assertTrue($multiResults->contains($form1));
        $this->assertTrue($multiResults->contains($form2));
        $this->assertTrue($multiResults->contains($form3));
    }

    /** @test */
    public function it_orders_pages_by_sort_no()
    {
        $form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $page2 = DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => 1,
            'title' => 'Page 2',
            'sort_no' => 1
        ]);

        $page1 = DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => 1,
            'title' => 'Page 1',
            'sort_no' => 0
        ]);

        $page3 = DynamicFormPage::create([
            'dynamic_form_id' => $form->id,
            'user_id' => 1,
            'title' => 'Page 3',
            'sort_no' => 2
        ]);

        $pages = $form->dynamicFormPages;

        $this->assertEquals('Page 1', $pages[0]->title);
        $this->assertEquals('Page 2', $pages[1]->title);
        $this->assertEquals('Page 3', $pages[2]->title);
    }

    /** @test */
    public function it_orders_submissions_by_latest()
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

        $submission1 = DynamicFormSubmission::create([
            'dynamic_form_id' => $form->id,
            'user_id' => $user->id,
            'data' => json_encode(['field1' => 'value1']),
            'status' => 'submitted'
        ]);

        sleep(1); // Ensure different timestamps

        $submission2 = DynamicFormSubmission::create([
            'dynamic_form_id' => $form->id,
            'user_id' => $user->id,
            'data' => json_encode(['field2' => 'value2']),
            'status' => 'submitted'
        ]);

        sleep(1); // Ensure different timestamps

        $submission3 = DynamicFormSubmission::create([
            'dynamic_form_id' => $form->id,
            'user_id' => $user->id,
            'data' => json_encode(['field3' => 'value3']),
            'status' => 'submitted'
        ]);

        $submissions = $form->dynamicFormSubmissions;

        $this->assertEquals($submission3->id, $submissions[0]->id);
        $this->assertEquals($submission2->id, $submissions[1]->id);
        $this->assertEquals($submission1->id, $submissions[2]->id);
    }
}