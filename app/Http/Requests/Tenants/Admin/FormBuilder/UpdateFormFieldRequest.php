<?php

namespace App\Http\Requests\Tenants\Admin\FormBuilder;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\DynamicFormField;
use Str;
class UpdateFormFieldRequest extends FormRequest
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
            'fields.*.id' => [
                'sometimes',
                'required',
                'exists:dynamic_form_fields,id'
            ], // Assuming each field has an 'id'
            'fields.*.input_field_label' => [
                'required',
                'string',
                'max:255',
                'unique_in_array:fields,input_field_label',
                function ($attribute, $value, $fail) {
                    $fieldIndex = explode('.', $attribute)[1];
                    $fieldId = $this->get('fields')[$fieldIndex]['id'] ?? null;
                    if ($fieldId && DynamicFormField::where('input_field_label', $value)
                        ->where('dynamic_form_page_id', $this->dynamic_form_page_id)
                        ->where('id', '<>', $fieldId)
                        ->exists()) {
                        $fail('The '.$attribute.' has already been taken.');
                    }
                },
            ],
            'fields.*.input_field_name' => [
                'required',
                'string',
                'max:255',
                'unique_in_array:fields,input_field_name',
                function ($attribute, $value, $fail) {
                    $fieldIndex = explode('.', $attribute)[1];
                    $fieldId = $this->get('fields')[$fieldIndex]['id'] ?? null;
                    if ($fieldId && DynamicFormField::where('input_field_name', $value)
                        ->where('dynamic_form_page_id', $this->dynamic_form_page_id)
                        ->where('id', '<>', $fieldId)
                        ->exists()) {
                        $fail('The '.$attribute.' has already been taken.');
                    }
                },
            ],
            'fields.*.input_field_type' => ['required', Rule::in(DynamicFormField::FIELD_TYPES)],
            'fields.*.is_required' => 'required|boolean',
            'fields.*.is_multiple' => 'required|boolean',
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
