<?php

namespace App\Http\Requests\Tenants\Admin\FormBuilder;

use App\Models\DynamicForm;
use App\Rules\ValidFieldName;
use App\Rules\ValidFieldType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormPagesAndFieldsRequest extends FormRequest
{
    /**
     * The form being updated.
     *
     * @var \App\Models\DynamicForm|null
     */
    protected $form;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->form = DynamicForm::find($this->route('id'));

        return auth()->check() && 
               auth()->user()->can('edit-forms') && 
               $this->form !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('dynamic_forms')->where(function ($query) {
                    return $query->where('subcategory_id', $this->form->subcategory_id);
                })->ignore($this->form->id)
            ],
            'description' => [
                'required',
                'string',
                'min:10',
                'max:1000'
            ],
            'dynamic_form_pages' => [
                'required',
                'array',
                'min:1'
            ],
            'dynamic_form_pages.*.id' => [
                'sometimes',
                'integer',
                Rule::exists('dynamic_form_pages', 'id')->where(function ($query) {
                    return $query->where('dynamic_form_id', $this->form->id);
                })
            ],
            'dynamic_form_pages.*.title' => [
                'required',
                'string',
                'min:3',
                'max:255',
                function ($attribute, $value, $fail) {
                    $pageIndex = explode('.', $attribute)[1];
                    $pageId = $this->input("dynamic_form_pages.{$pageIndex}.id");

                    // Check for duplicate titles in the request
                    $titles = collect($this->input('dynamic_form_pages'))->pluck('title');
                    if ($titles->filter(fn($title) => $title === $value)->count() > 1) {
                        $fail('Duplicate page title: ' . $value);
                    }

                    // Check for existing titles in other pages
                    $query = $this->form->dynamicFormPages()
                        ->where('title', $value);

                    if ($pageId) {
                        $query->where('id', '!=', $pageId);
                    }

                    if ($query->exists()) {
                        $fail('A page with this title already exists in this form.');
                    }
                }
            ],
            'dynamic_form_pages.*.sort_no' => [
                'sometimes',
                'integer',
                'min:0'
            ],
            'dynamic_form_pages.*.dynamic_form_fields' => [
                'required',
                'array'
            ],
            'dynamic_form_pages.*.dynamic_form_fields.*.id' => [
                'sometimes',
                'integer',
                Rule::exists('dynamic_form_fields', 'id')->where(function ($query) {
                    return $query->whereIn('dynamic_form_page_id', $this->form->dynamicFormPages->pluck('id'));
                })
            ],
            'dynamic_form_pages.*.dynamic_form_fields.*.input_field_label' => [
                'required',
                'string',
                'min:3',
                'max:255'
            ],
            'dynamic_form_pages.*.dynamic_form_fields.*.input_field_name' => [
                'sometimes',
                'string',
                new ValidFieldName()
            ],
            'dynamic_form_pages.*.dynamic_form_fields.*.input_field_type' => [
                'required',
                'string',
                new ValidFieldType()
            ],
            'dynamic_form_pages.*.dynamic_form_fields.*.is_required' => [
                'required',
                'boolean'
            ],
            'dynamic_form_pages.*.dynamic_form_fields.*.sort_no' => [
                'sometimes',
                'integer',
                'min:0'
            ],
            'dynamic_form_pages.*.dynamic_form_fields.*.data' => [
                'sometimes',
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    if ($value === null) {
                        return;
                    }

                    $fieldIndex = explode('.', $attribute)[3];
                    $pageIndex = explode('.', $attribute)[1];
                    $fieldType = $this->input("dynamic_form_pages.{$pageIndex}.dynamic_form_fields.{$fieldIndex}.input_field_type");

                    switch ($fieldType) {
                        case 'select':
                        case 'radio':
                        case 'checkbox':
                            if (!isset($value['options']) || !is_array($value['options'])) {
                                $fail("The {$fieldType} field requires an 'options' array.");
                            }
                            break;

                        case 'number':
                            if (isset($value['min']) && !is_numeric($value['min'])) {
                                $fail("The 'min' value must be numeric.");
                            }
                            if (isset($value['max']) && !is_numeric($value['max'])) {
                                $fail("The 'max' value must be numeric.");
                            }
                            if (isset($value['step']) && !is_numeric($value['step'])) {
                                $fail("The 'step' value must be numeric.");
                            }
                            break;

                        case 'file':
                            if (!isset($value['accept'])) {
                                $fail("The file field requires an 'accept' attribute.");
                            }
                            if (isset($value['maxSize']) && !is_numeric($value['maxSize'])) {
                                $fail("The 'maxSize' value must be numeric.");
                            }
                            break;

                        case 'text':
                        case 'textarea':
                            if (isset($value['pattern']) && @preg_match($value['pattern'], '') === false) {
                                $fail("The 'pattern' value must be a valid regular expression.");
                            }
                            if (isset($value['rows']) && !is_numeric($value['rows'])) {
                                $fail("The 'rows' value must be numeric.");
                            }
                            if (isset($value['cols']) && !is_numeric($value['cols'])) {
                                $fail("The 'cols' value must be numeric.");
                            }
                            break;
                    }
                }
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'name.required' => 'A form name is required.',
            'name.string' => 'The form name must be a string.',
            'name.min' => 'The form name must be at least :min characters.',
            'name.max' => 'The form name cannot be longer than :max characters.',
            'name.unique' => 'A form with this name already exists in this subcategory.',
            'description.required' => 'A form description is required.',
            'description.string' => 'The form description must be a string.',
            'description.min' => 'The form description must be at least :min characters.',
            'description.max' => 'The form description cannot be longer than :max characters.',
            'dynamic_form_pages.required' => 'At least one page is required.',
            'dynamic_form_pages.array' => 'The pages must be provided as an array.',
            'dynamic_form_pages.min' => 'At least one page is required.',
            'dynamic_form_pages.*.title.required' => 'Each page must have a title.',
            'dynamic_form_pages.*.title.string' => 'The page title must be a string.',
            'dynamic_form_pages.*.title.min' => 'The page title must be at least :min characters.',
            'dynamic_form_pages.*.title.max' => 'The page title cannot be longer than :max characters.',
            'dynamic_form_pages.*.sort_no.integer' => 'The page sort order must be an integer.',
            'dynamic_form_pages.*.sort_no.min' => 'The page sort order cannot be negative.',
            'dynamic_form_pages.*.dynamic_form_fields.required' => 'Each page must have at least one field.',
            'dynamic_form_pages.*.dynamic_form_fields.array' => 'The fields must be provided as an array.',
            'dynamic_form_pages.*.dynamic_form_fields.*.input_field_label.required' => 'Each field must have a label.',
            'dynamic_form_pages.*.dynamic_form_fields.*.input_field_label.string' => 'The field label must be a string.',
            'dynamic_form_pages.*.dynamic_form_fields.*.input_field_label.min' => 'The field label must be at least :min characters.',
            'dynamic_form_pages.*.dynamic_form_fields.*.input_field_label.max' => 'The field label cannot be longer than :max characters.',
            'dynamic_form_pages.*.dynamic_form_fields.*.input_field_type.required' => 'Each field must have a type.',
            'dynamic_form_pages.*.dynamic_form_fields.*.input_field_type.string' => 'The field type must be a string.',
            'dynamic_form_pages.*.dynamic_form_fields.*.is_required.required' => 'Each field must specify whether it is required.',
            'dynamic_form_pages.*.dynamic_form_fields.*.is_required.boolean' => 'The required field must be a boolean.',
            'dynamic_form_pages.*.dynamic_form_fields.*.sort_no.integer' => 'The field sort order must be an integer.',
            'dynamic_form_pages.*.dynamic_form_fields.*.sort_no.min' => 'The field sort order cannot be negative.',
            'dynamic_form_pages.*.dynamic_form_fields.*.data.array' => 'The field data must be an array.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'name' => 'form name',
            'description' => 'form description',
            'dynamic_form_pages' => 'pages',
            'dynamic_form_pages.*.title' => 'page title',
            'dynamic_form_pages.*.sort_no' => 'page sort order',
            'dynamic_form_pages.*.dynamic_form_fields' => 'fields',
            'dynamic_form_pages.*.dynamic_form_fields.*.input_field_label' => 'field label',
            'dynamic_form_pages.*.dynamic_form_fields.*.input_field_name' => 'field name',
            'dynamic_form_pages.*.dynamic_form_fields.*.input_field_type' => 'field type',
            'dynamic_form_pages.*.dynamic_form_fields.*.is_required' => 'required field',
            'dynamic_form_pages.*.dynamic_form_fields.*.sort_no' => 'field sort order',
            'dynamic_form_pages.*.dynamic_form_fields.*.data' => 'field data'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'name' => trim($this->name),
            'description' => trim($this->description)
        ]);

        if ($this->has('dynamic_form_pages')) {
            $pages = $this->input('dynamic_form_pages');
            foreach ($pages as $pageIndex => $page) {
                if (isset($page['title'])) {
                    $pages[$pageIndex]['title'] = trim($page['title']);
                }

                if (isset($page['dynamic_form_fields'])) {
                    foreach ($page['dynamic_form_fields'] as $fieldIndex => $field) {
                        if (isset($field['input_field_label'])) {
                            $pages[$pageIndex]['dynamic_form_fields'][$fieldIndex]['input_field_label'] = trim($field['input_field_label']);
                        }

                        if (isset($field['input_field_label']) && !isset($field['input_field_name'])) {
                            // Generate field name from label
                            $fieldName = strtolower(trim($field['input_field_label']));
                            $fieldName = preg_replace('/\s+/', '_', $fieldName); // Replace spaces with underscores
                            $fieldName = preg_replace('/[^a-z0-9_]/', '', $fieldName); // Remove special characters
                            $pages[$pageIndex]['dynamic_form_fields'][$fieldIndex]['input_field_name'] = $fieldName;
                        }
                    }
                }
            }
            $this->merge(['dynamic_form_pages' => $pages]);
        }
    }
}
