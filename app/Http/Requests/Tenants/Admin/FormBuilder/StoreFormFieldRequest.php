<?php

namespace App\Http\Requests\Tenants\Admin\FormBuilder;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidFieldName;
use App\Rules\ValidFieldType;
use App\Rules\ValidSortOrder;

class StoreFormFieldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'dynamic_form_page_id' => [
                'required',
                'exists:dynamic_form_pages,id',
                function ($attribute, $value, $fail) {
                    $page = \App\Models\DynamicFormPage::find($value);
                    if (!$page || $page->trashed()) {
                        $fail('The selected page is invalid or has been deleted.');
                    }
                }
            ],
            'input_field_label' => [
                'required',
                'string',
                'max:255'
            ],
            'input_field_name' => [
                'required',
                'string',
                new ValidFieldName,
                function ($attribute, $value, $fail) {
                    // Check if field name is unique within the form
                    $page = \App\Models\DynamicFormPage::find($this->dynamic_form_page_id);
                    if ($page) {
                        $exists = \App\Models\DynamicFormField::whereHas('dynamicFormPage', function ($query) use ($page) {
                            $query->where('dynamic_form_id', $page->dynamic_form_id);
                        })->where('input_field_name', $value)->exists();

                        if ($exists) {
                            $fail('This field name is already used in the form.');
                        }
                    }
                }
            ],
            'input_field_type' => [
                'required',
                'string',
                new ValidFieldType($this->input('data'))
            ],
            'is_required' => [
                'required',
                'boolean'
            ],
            'sort_no' => [
                'sometimes',
                'integer',
                new ValidSortOrder($this->dynamic_form_page_id)
            ],
            'data' => [
                'sometimes',
                'array'
            ],
            'data.options' => [
                'required_if:input_field_type,select,radio,checkbox',
                'array',
                'min:1'
            ],
            'data.options.*' => [
                'required',
                'string',
                'distinct',
                'max:255'
            ],
            'data.min' => [
                'required_if:input_field_type,range',
                'numeric'
            ],
            'data.max' => [
                'required_if:input_field_type,range',
                'numeric',
                'gt:data.min'
            ],
            'data.step' => [
                'required_if:input_field_type,range',
                'numeric',
                'gt:0'
            ],
            'data.accept' => [
                'required_if:input_field_type,file',
                'string'
            ],
            'data.max_size' => [
                'required_if:input_field_type,file,image',
                'numeric',
                'gt:0'
            ],
            'data.dimensions' => [
                'required_if:input_field_type,image',
                'array'
            ],
            'data.dimensions.min_width' => [
                'required_with:data.dimensions',
                'integer',
                'gt:0'
            ],
            'data.dimensions.max_width' => [
                'required_with:data.dimensions',
                'integer',
                'gte:data.dimensions.min_width'
            ],
            'data.dimensions.min_height' => [
                'required_with:data.dimensions',
                'integer',
                'gt:0'
            ],
            'data.dimensions.max_height' => [
                'required_with:data.dimensions',
                'integer',
                'gte:data.dimensions.min_height'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'dynamic_form_page_id.required' => 'A page must be selected.',
            'dynamic_form_page_id.exists' => 'The selected page does not exist.',
            'input_field_label.required' => 'The field label is required.',
            'input_field_label.string' => 'The field label must be text.',
            'input_field_label.max' => 'The field label cannot be longer than 255 characters.',
            'input_field_name.required' => 'The field name is required.',
            'input_field_name.string' => 'The field name must be text.',
            'input_field_type.required' => 'The field type is required.',
            'input_field_type.string' => 'The field type must be text.',
            'is_required.required' => 'Please specify if the field is required.',
            'is_required.boolean' => 'The required field must be true or false.',
            'sort_no.integer' => 'The sort order must be a number.',
            'data.array' => 'Additional field data must be an array.',
            'data.options.required_if' => 'Options are required for this field type.',
            'data.options.array' => 'Options must be a list.',
            'data.options.min' => 'At least one option is required.',
            'data.options.*.required' => 'Option value is required.',
            'data.options.*.string' => 'Option value must be text.',
            'data.options.*.distinct' => 'Options must be unique.',
            'data.options.*.max' => 'Option value cannot be longer than 255 characters.',
            'data.min.required_if' => 'Minimum value is required for range fields.',
            'data.min.numeric' => 'Minimum value must be a number.',
            'data.max.required_if' => 'Maximum value is required for range fields.',
            'data.max.numeric' => 'Maximum value must be a number.',
            'data.max.gt' => 'Maximum value must be greater than minimum value.',
            'data.step.required_if' => 'Step value is required for range fields.',
            'data.step.numeric' => 'Step value must be a number.',
            'data.step.gt' => 'Step value must be greater than 0.',
            'data.accept.required_if' => 'File type restrictions are required for file fields.',
            'data.accept.string' => 'File type restrictions must be text.',
            'data.max_size.required_if' => 'Maximum file size is required for file/image fields.',
            'data.max_size.numeric' => 'Maximum file size must be a number.',
            'data.max_size.gt' => 'Maximum file size must be greater than 0.',
            'data.dimensions.required_if' => 'Image dimensions are required for image fields.',
            'data.dimensions.array' => 'Image dimensions must be specified as width and height.',
            'data.dimensions.min_width.required_with' => 'Minimum width is required for image fields.',
            'data.dimensions.min_width.integer' => 'Minimum width must be a number.',
            'data.dimensions.min_width.gt' => 'Minimum width must be greater than 0.',
            'data.dimensions.max_width.required_with' => 'Maximum width is required for image fields.',
            'data.dimensions.max_width.integer' => 'Maximum width must be a number.',
            'data.dimensions.max_width.gte' => 'Maximum width must be greater than or equal to minimum width.',
            'data.dimensions.min_height.required_with' => 'Minimum height is required for image fields.',
            'data.dimensions.min_height.integer' => 'Minimum height must be a number.',
            'data.dimensions.min_height.gt' => 'Minimum height must be greater than 0.',
            'data.dimensions.max_height.required_with' => 'Maximum height is required for image fields.',
            'data.dimensions.max_height.integer' => 'Maximum height must be a number.',
            'data.dimensions.max_height.gte' => 'Maximum height must be greater than or equal to minimum height.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'dynamic_form_page_id' => 'page',
            'input_field_label' => 'field label',
            'input_field_name' => 'field name',
            'input_field_type' => 'field type',
            'is_required' => 'required field',
            'sort_no' => 'sort order',
            'data' => 'field data',
            'data.options' => 'options',
            'data.min' => 'minimum value',
            'data.max' => 'maximum value',
            'data.step' => 'step value',
            'data.accept' => 'accepted file types',
            'data.max_size' => 'maximum file size',
            'data.dimensions' => 'image dimensions',
            'data.dimensions.min_width' => 'minimum width',
            'data.dimensions.max_width' => 'maximum width',
            'data.dimensions.min_height' => 'minimum height',
            'data.dimensions.max_height' => 'maximum height'
        ];
    }
}