<?php

namespace App\Http\Requests\Tenants\Admin\FormBuilder;

use App\Models\DynamicFormField;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Str;

class StoreFormFieldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'dynamic_form_page_id' => 'required|exists:dynamic_form_pages,id',
            'fields' => 'required|array',
            'fields.*.input_field_label' => [
                'required',
                'string',
                'max:255',
                'unique_in_array:fields,input_field_label',
                Rule::unique('dynamic_form_fields', 'input_field_label')
                    ->where('dynamic_form_page_id', $this->dynamic_form_page_id)
            ],
            'fields.*.input_field_name' => [
                'required',
                'string',
                'max:255',
                'unique_in_array:fields,input_field_name',
                Rule::unique('dynamic_form_fields', 'input_field_name')
                    ->where('dynamic_form_page_id', $this->dynamic_form_page_id)
            ],
            'fields.*.input_field_type' => ['required', Rule::in(DynamicFormField::FIELD_TYPES)],
            'fields.*.is_required' => 'required|boolean',
            'fields.*.data' => 'sometimes|required|array',
            // 'data.*.label' => 'required|string|max:255', // Validate each label in the data array
            // 'data.*.value' => 'required' // Validate each value in the data array
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->has('fields') && is_array($this->fields)) {
            $fieldsWithNames = array_map(function ($field) {
                if (!isset($field['input_field_name']) || empty($field['input_field_name'])) {
                    $field['input_field_name'] = isset($field['input_field_label']) ? Str::lower(Str::snake($field['input_field_label'])) : null;
                }
                return $field;
            }, $this->fields);
    
            $this->merge(['fields' => $fieldsWithNames]);
        }
    }
}
