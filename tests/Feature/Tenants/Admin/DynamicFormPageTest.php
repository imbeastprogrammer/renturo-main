<?php

namespace Tests\Feature\Tenants\Admin;

use App\Models\DynamicForm;
use App\Models\DynamicFormPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase\TenantTestCase;

class DynamicFormPageTest extends TenantTestCase
{
    use RefreshDatabase;

    private $form;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'mobile_number' => '1234567890'
        ]);

        // Create test form
        $this->form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => 1
        ]);

        // Authenticate user
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_list_form_pages()
    {
        DynamicFormPage::create([
            'dynamic_form_id' => $this->form->id,
            'user_id' => $this->user->id,
            'title' => 'Test Page 1',
            'sort_no' => 0
        ]);

        DynamicFormPage::create([
            'dynamic_form_id' => $this->form->id,
            'user_id' => $this->user->id,
            'title' => 'Test Page 2',
            'sort_no' => 1
        ]);

        $response = $this->getJson('/form/pages/all');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Dynamic form pages fetched successfully.'
            ])
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function it_can_create_form_page()
    {
        $response = $this->postJson('/form/pages', [
            'dynamic_form_id' => $this->form->id,
            'title' => 'New Test Page'
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Dynamic form page created successfully.'
            ]);

        $this->assertDatabaseHas('dynamic_form_pages', [
            'dynamic_form_id' => $this->form->id,
            'title' => 'New Test Page',
            'sort_no' => 0
        ]);
    }

    /** @test */
    public function it_prevents_duplicate_page_titles_in_same_form()
    {
        DynamicFormPage::create([
            'dynamic_form_id' => $this->form->id,
            'user_id' => $this->user->id,
            'title' => 'Test Page',
            'sort_no' => 0
        ]);

        $response = $this->postJson('/form/pages', [
            'dynamic_form_id' => $this->form->id,
            'title' => 'Test Page'
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'status' => 'error',
                'message' => 'Failed to create form page.'
            ]);
    }

    /** @test */
    public function it_can_update_form_page()
    {
        $page = DynamicFormPage::create([
            'dynamic_form_id' => $this->form->id,
            'user_id' => $this->user->id,
            'title' => 'Test Page',
            'sort_no' => 0
        ]);

        $response = $this->putJson("/form/pages/{$page->id}", [
            'title' => 'Updated Test Page'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Dynamic form page updated successfully.'
            ]);

        $this->assertDatabaseHas('dynamic_form_pages', [
            'id' => $page->id,
            'title' => 'Updated Test Page'
        ]);
    }

    /** @test */
    public function it_can_delete_form_page()
    {
        $page = DynamicFormPage::create([
            'dynamic_form_id' => $this->form->id,
            'user_id' => $this->user->id,
            'title' => 'Test Page',
            'sort_no' => 0
        ]);

        $response = $this->deleteJson("/form/pages/{$page->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Dynamic form page deleted successfully.'
            ]);

        $this->assertSoftDeleted($page);
    }

    /** @test */
    public function it_can_restore_deleted_page()
    {
        $page = DynamicFormPage::create([
            'dynamic_form_id' => $this->form->id,
            'user_id' => $this->user->id,
            'title' => 'Test Page',
            'sort_no' => 0
        ]);

        $page->delete();

        $response = $this->postJson("/form/pages/restore/{$page->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Dynamic form page restored successfully.'
            ]);

        $this->assertNotSoftDeleted($page);
    }

    /** @test */
    public function it_can_reorder_pages()
    {
        $page1 = DynamicFormPage::create([
            'dynamic_form_id' => $this->form->id,
            'user_id' => $this->user->id,
            'title' => 'Test Page 1',
            'sort_no' => 0
        ]);

        $page2 = DynamicFormPage::create([
            'dynamic_form_id' => $this->form->id,
            'user_id' => $this->user->id,
            'title' => 'Test Page 2',
            'sort_no' => 1
        ]);

        $response = $this->postJson('/form/pages/reorder', [
            'form_id' => $this->form->id,
            'pages' => [
                ['id' => $page2->id, 'sort_no' => 0],
                ['id' => $page1->id, 'sort_no' => 1]
            ]
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Form pages reordered successfully.'
            ]);

        $this->assertEquals(0, $page2->fresh()->sort_no);
        $this->assertEquals(1, $page1->fresh()->sort_no);
    }

    /** @test */
    public function it_validates_reorder_page_ownership()
    {
        $page1 = DynamicFormPage::create([
            'dynamic_form_id' => $this->form->id,
            'user_id' => $this->user->id,
            'title' => 'Test Page 1',
            'sort_no' => 0
        ]);

        // Create another form
        $otherForm = DynamicForm::create([
            'name' => 'Other Form',
            'description' => 'Other Description',
            'subcategory_id' => 1
        ]);

        $otherPage = DynamicFormPage::create([
            'dynamic_form_id' => $otherForm->id,
            'user_id' => $this->user->id,
            'title' => 'Other Page',
            'sort_no' => 0
        ]);

        $response = $this->postJson('/form/pages/reorder', [
            'form_id' => $this->form->id,
            'pages' => [
                ['id' => $page1->id, 'sort_no' => 0],
                ['id' => $otherPage->id, 'sort_no' => 1] // Page from different form
            ]
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_maintains_sort_order_after_deletion()
    {
        $page1 = DynamicFormPage::create([
            'dynamic_form_id' => $this->form->id,
            'user_id' => $this->user->id,
            'title' => 'Test Page 1',
            'sort_no' => 0
        ]);

        $page2 = DynamicFormPage::create([
            'dynamic_form_id' => $this->form->id,
            'user_id' => $this->user->id,
            'title' => 'Test Page 2',
            'sort_no' => 1
        ]);

        $page3 = DynamicFormPage::create([
            'dynamic_form_id' => $this->form->id,
            'user_id' => $this->user->id,
            'title' => 'Test Page 3',
            'sort_no' => 2
        ]);

        // Delete middle page
        $this->deleteJson("/form/pages/{$page2->id}");

        // Check that page3's sort_no was decremented
        $this->assertEquals(1, $page3->fresh()->sort_no);
    }
}
