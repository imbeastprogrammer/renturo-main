<?php

namespace Tests\Unit\Models;

use App\Models\DynamicForm;
use App\Models\DynamicFormField;
use App\Models\DynamicFormPage;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase\TenantTestCase;

class DynamicFormFieldTest extends TenantTestCase
{
    use RefreshDatabase;

    private $user;
    private $category;
    private $subcategory;
    private $form;
    private $page;

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

        // Create category and subcategory
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

        // Create form and page
        $this->form = DynamicForm::create([
            'name' => 'Test Form',
            'description' => 'Test Description',
            'subcategory_id' => $this->subcategory->id
        ]);

        $this->page = DynamicFormPage::create([
            'dynamic_form_id' => $this->form->id,
            'user_id' => $this->user->id,
            'title' => 'Test Page',
            'sort_no' => 0
        ]);
    }

    /** @test */
    public function it_can_create_field()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Field',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $this->assertInstanceOf(DynamicFormField::class, $field);
        $this->assertEquals('Test Field', $field->input_field_label);
        $this->assertEquals('test_field', $field->input_field_name);
        $this->assertEquals('text', $field->input_field_type);
        $this->assertTrue($field->is_required);
        $this->assertEquals(0, $field->sort_no);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = (new DynamicFormField())->getFillable();

        $this->assertEqualsCanonicalizing([
            'user_id',
            'dynamic_form_page_id',
            'input_field_label',
            'input_field_name',
            'input_field_type',
            'is_required',
            'sort_no',
            'data',
            'value'
        ], $fillable);
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Field',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $field->delete();

        $this->assertSoftDeleted($field);
    }

    /** @test */
    public function it_belongs_to_page()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Field',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $this->assertInstanceOf(DynamicFormPage::class, $field->dynamicFormPage);
        $this->assertEquals($this->page->id, $field->dynamicFormPage->id);
    }

    /** @test */
    public function it_belongs_to_user()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Field',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $this->assertInstanceOf(User::class, $field->user);
        $this->assertEquals($this->user->id, $field->user->id);
    }

    /** @test */
    public function it_has_one_form_through_page()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Field',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $this->assertInstanceOf(DynamicForm::class, $field->dynamicForm);
        $this->assertEquals($this->form->id, $field->dynamicForm->id);
    }

    /** @test */
    public function it_generates_field_name_from_label()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Field Label With Spaces!',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $this->assertEquals('test_field_label_with_spaces', $field->input_field_name);
    }

    /** @test */
    public function it_initializes_default_data_for_select_field()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Select',
            'input_field_type' => 'select',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $this->assertIsArray($field->data);
        $this->assertArrayHasKey('options', $field->data);
        $this->assertEmpty($field->data['options']);
    }

    /** @test */
    public function it_initializes_default_data_for_number_field()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Number',
            'input_field_type' => 'number',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $this->assertIsArray($field->data);
        $this->assertArrayHasKey('min', $field->data);
        $this->assertArrayHasKey('max', $field->data);
        $this->assertArrayHasKey('step', $field->data);
        $this->assertEquals(1, $field->data['step']);
    }

    /** @test */
    public function it_initializes_default_data_for_file_field()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test File',
            'input_field_type' => 'file',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $this->assertIsArray($field->data);
        $this->assertArrayHasKey('accept', $field->data);
        $this->assertArrayHasKey('maxSize', $field->data);
        $this->assertEquals('*/*', $field->data['accept']);
        $this->assertEquals(5 * 1024 * 1024, $field->data['maxSize']); // 5MB
    }

    /** @test */
    public function it_can_check_field_type_capabilities()
    {
        $selectField = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Select',
            'input_field_type' => 'select',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $textField = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Text',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 1
        ]);

        $fileField = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test File',
            'input_field_type' => 'file',
            'is_required' => true,
            'sort_no' => 2
        ]);

        $this->assertTrue($selectField->canHaveOptions());
        $this->assertFalse($textField->canHaveOptions());
        $this->assertFalse($fileField->canHaveOptions());

        $this->assertFalse($selectField->canHaveValidation());
        $this->assertTrue($textField->canHaveValidation());
        $this->assertFalse($fileField->canHaveValidation());

        $this->assertFalse($selectField->canHaveFileRestrictions());
        $this->assertFalse($textField->canHaveFileRestrictions());
        $this->assertTrue($fileField->canHaveFileRestrictions());
    }

    /** @test */
    public function it_generates_validation_rules()
    {
        $textField = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Text',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0,
            'data' => ['pattern' => '^[A-Z]+$']
        ]);

        $rules = $textField->getValidationRules();

        $this->assertContains('required', $rules);
        $this->assertContains('string', $rules);
        $this->assertContains('regex:/^[A-Z]+$/', $rules);
    }

    /** @test */
    public function it_formats_values_correctly()
    {
        $numberField = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Number',
            'input_field_type' => 'number',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $dateField = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Date',
            'input_field_type' => 'date',
            'is_required' => true,
            'sort_no' => 1
        ]);

        $checklistField = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Checklist',
            'input_field_type' => 'checklist',
            'is_required' => true,
            'sort_no' => 2
        ]);

        $this->assertEquals(42.5, $numberField->formatValue('42.5'));
        $this->assertEquals(date('Y-m-d'), $dateField->formatValue(date('Y-m-d')));
        $this->assertEquals(['a', 'b'], $checklistField->formatValue(['a', 'b']));
    }

    /** @test */
    public function it_provides_default_values()
    {
        $numberField = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Number',
            'input_field_type' => 'number',
            'is_required' => true,
            'sort_no' => 0,
            'data' => ['min' => 10]
        ]);

        $selectField = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Select',
            'input_field_type' => 'select',
            'is_required' => true,
            'sort_no' => 1,
            'data' => ['options' => ['a', 'b', 'c']]
        ]);

        $checkboxField = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Checkbox',
            'input_field_type' => 'checkbox',
            'is_required' => true,
            'sort_no' => 2
        ]);

        $this->assertEquals(10, $numberField->getDefaultValue());
        $this->assertEquals('a', $selectField->getDefaultValue());
        $this->assertFalse($checkboxField->getDefaultValue());
    }

    /** @test */
    public function it_initializes_default_data_for_textarea_field()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Textarea',
            'input_field_type' => 'textarea',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $this->assertIsArray($field->data);
        $this->assertArrayHasKey('rows', $field->data);
        $this->assertArrayHasKey('cols', $field->data);
        $this->assertArrayHasKey('pattern', $field->data);
        $this->assertArrayHasKey('placeholder', $field->data);
        $this->assertEquals(3, $field->data['rows']);
        $this->assertEquals(40, $field->data['cols']);
        $this->assertNull($field->data['pattern']);
        $this->assertNull($field->data['placeholder']);
    }

    /** @test */
    public function it_initializes_default_data_for_rating_field()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Rating',
            'input_field_type' => 'rating',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $this->assertIsArray($field->data);
        $this->assertArrayHasKey('max', $field->data);
        $this->assertEquals(5, $field->data['max']);
    }

    /** @test */
    public function it_validates_email_field()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Email',
            'input_field_type' => 'email',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $rules = $field->getValidationRules();
        $messages = $field->getValidationMessages();

        $this->assertContains('required', $rules);
        $this->assertContains('email', $rules);
        $this->assertArrayHasKey('email', $messages);
    }

    /** @test */
    public function it_validates_date_and_time_fields()
    {
        $dateField = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Date',
            'input_field_type' => 'date',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $timeField = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Time',
            'input_field_type' => 'time',
            'is_required' => true,
            'sort_no' => 1
        ]);

        $dateRules = $dateField->getValidationRules();
        $timeRules = $timeField->getValidationRules();

        $this->assertContains('date', $dateRules);
        $this->assertContains('date_format:H:i', $timeRules);
    }

    /** @test */
    public function it_validates_file_field_restrictions()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test File',
            'input_field_type' => 'file',
            'is_required' => true,
            'sort_no' => 0,
            'data' => [
                'accept' => '.pdf,.doc,.docx',
                'maxSize' => 2 * 1024 * 1024 // 2MB
            ]
        ]);

        $rules = $field->getValidationRules();
        $messages = $field->getValidationMessages();

        $this->assertContains('file', $rules);
        $this->assertContains('mimes:pdf,doc,docx', $rules);
        $this->assertContains('max:2048', $rules); // 2MB in KB
        $this->assertArrayHasKey('file', $messages);
        $this->assertArrayHasKey('mimes', $messages);
        $this->assertArrayHasKey('max', $messages);
    }

    /** @test */
    public function it_validates_checklist_and_multiselect_fields()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Checklist',
            'input_field_type' => 'checklist',
            'is_required' => true,
            'sort_no' => 0,
            'data' => [
                'options' => ['a', 'b', 'c']
            ]
        ]);

        $rules = $field->getValidationRules();
        $messages = $field->getValidationMessages();

        $this->assertContains('array', $rules);
        $this->assertContains('in:a,b,c', $rules);
        $this->assertArrayHasKey('array', $messages);
        $this->assertArrayHasKey('in', $messages);
    }

    /** @test */
    public function it_formats_color_field_value()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Color',
            'input_field_type' => 'color',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $this->assertEquals('#ff0000', $field->formatValue('#FF0000'));
        $this->assertEquals('#000000', $field->getDefaultValue());
    }

    /** @test */
    public function it_handles_null_data_gracefully()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Field',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0,
            'data' => null
        ]);

        $this->assertIsArray($field->data);
        $this->assertArrayHasKey('pattern', $field->data);
        $this->assertArrayHasKey('placeholder', $field->data);
        $this->assertNull($field->data['pattern']);
        $this->assertNull($field->data['placeholder']);
    }

    /** @test */
    public function it_handles_special_characters_in_field_name_generation()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Field @#$%^&*()',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $this->assertEquals('test_field', $field->input_field_name);
    }

    /** @test */
    public function it_handles_unicode_characters_in_field_name_generation()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Field 测试字段',
            'input_field_type' => 'text',
            'is_required' => true,
            'sort_no' => 0
        ]);

        $this->assertEquals('test_field', $field->input_field_name);
    }

    /** @test */
    public function it_validates_nested_array_values()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Matrix',
            'input_field_type' => 'matrix',
            'is_required' => true,
            'sort_no' => 0,
            'data' => [
                'rows' => ['r1', 'r2'],
                'columns' => ['c1', 'c2']
            ]
        ]);

        $rules = $field->getValidationRules();
        $messages = $field->getValidationMessages();

        $this->assertContains('array', $rules);
        $this->assertContains('*.*.in:r1,r2', $rules);
        $this->assertContains('*.*.in:c1,c2', $rules);
        $this->assertArrayHasKey('array', $messages);
    }

    /** @test */
    public function it_handles_conditional_validation_rules()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Field',
            'input_field_type' => 'text',
            'is_required' => false,
            'sort_no' => 0,
            'data' => [
                'depends_on' => 'other_field',
                'show_if' => 'equals',
                'value' => 'yes'
            ]
        ]);

        $rules = $field->getValidationRules();
        $this->assertContains('required_if:other_field,yes', $rules);
    }

    /** @test */
    public function it_handles_json_serialization()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Test Field',
            'input_field_type' => 'select',
            'is_required' => true,
            'sort_no' => 0,
            'data' => ['options' => ['a', 'b', 'c']]
        ]);

        $json = $field->toJson();
        $array = $field->toArray();

        $this->assertJson($json);
        $this->assertIsArray($array);
        $this->assertArrayHasKey('data', $array);
        $this->assertEquals(['a', 'b', 'c'], $array['data']['options']);
    }

    /** @test */
    public function it_validates_phone_number_field()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Phone Number',
            'input_field_type' => 'phone',
            'is_required' => true,
            'sort_no' => 0,
            'data' => [
                'country_code' => '+1',
                'format' => '###-###-####'
            ]
        ]);

        $rules = $field->getValidationRules();
        $messages = $field->getValidationMessages();

        $this->assertContains('regex:/^\+?1?\d{10}$/', $rules);
        $this->assertArrayHasKey('regex', $messages);
    }

    /** @test */
    public function it_validates_currency_field()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Price',
            'input_field_type' => 'currency',
            'is_required' => true,
            'sort_no' => 0,
            'data' => [
                'currency' => 'USD',
                'min' => 0,
                'max' => 1000000
            ]
        ]);

        $rules = $field->getValidationRules();
        $messages = $field->getValidationMessages();

        $this->assertContains('numeric', $rules);
        $this->assertContains('min:0', $rules);
        $this->assertContains('max:1000000', $rules);
        $this->assertArrayHasKey('numeric', $messages);
    }

    /** @test */
    public function it_validates_repeater_field()
    {
        $field = DynamicFormField::create([
            'dynamic_form_page_id' => $this->page->id,
            'user_id' => $this->user->id,
            'input_field_label' => 'Repeater',
            'input_field_type' => 'repeater',
            'is_required' => true,
            'sort_no' => 0,
            'data' => [
                'min_items' => 1,
                'max_items' => 5,
                'fields' => [
                    [
                        'label' => 'Sub Field',
                        'type' => 'text',
                        'required' => true
                    ]
                ]
            ]
        ]);

        $rules = $field->getValidationRules();
        $messages = $field->getValidationMessages();

        $this->assertContains('array', $rules);
        $this->assertContains('min:1', $rules);
        $this->assertContains('max:5', $rules);
        $this->assertContains('*.sub_field.required', $rules);
        $this->assertArrayHasKey('array', $messages);
    }
}
