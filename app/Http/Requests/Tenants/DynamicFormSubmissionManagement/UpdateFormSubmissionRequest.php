<?php

namespace App\Http\Requests\Tenants\DynamicFormSubmissionManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\DynamicFormField;

class UpdateFormSubmissionRequest extends FormRequest
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
        $formId = $this->input('dynamic_form_id');
        $rules = [
            'dynamic_form_id' => 'required|exists:dynamic_forms,id',
            'dynamic_form_pages' => 'required|array',
            // This rule ensures that each page ID is within the form's pages
            'dynamic_form_pages.*.dynamic_form_page_id' => [
                'required',
                Rule::exists('dynamic_form_pages', 'id')->where('dynamic_form_id', $formId),
            ],
        ];

        // Fetch all field IDs for each page in the form to ensure that the field IDs are valid
        $validFieldIds = DynamicFormField::whereIn('dynamic_form_page_id', function ($query) use ($formId) {
            $query->select('id')
                ->from('dynamic_form_pages')
                ->where('dynamic_form_id', $formId);
        })->pluck('id')->toArray();

        foreach ($this->input('dynamic_form_pages', []) as $pageIndex => $page) {
            $pageId = $page['dynamic_form_page_id'] ?? null;
            $fieldIds = DynamicFormField::where('dynamic_form_page_id', $pageId)->pluck('id')->toArray();
            $requiredFieldIds = DynamicFormField::where('dynamic_form_page_id', $pageId)->where('is_required', true)->pluck('id')->toArray();

            foreach ($page['dynamic_form_fields'] ?? [] as $fieldIndex => $field) {
                $fieldId = $field['field_id'] ?? null;
                $fieldRuleKey = "dynamic_form_pages.{$pageIndex}.dynamic_form_fields.{$fieldIndex}.field_id";
                $valueRuleKey = "dynamic_form_pages.{$pageIndex}.dynamic_form_fields.{$fieldIndex}.value";
                
                // Check if the field ID is within the valid IDs for the page
                $rules[$fieldRuleKey] = [
                    'required',
                    Rule::in($validFieldIds),
                ];

                // If the field is required, check that it has a value
                if (in_array($fieldId, $requiredFieldIds)) {
                    $rules[$valueRuleKey] = 'required';
                } else {
                    $rules[$valueRuleKey] = 'nullable';
                }
            }
        }

        return $rules;
    }

    public function messages()
    {
        $messages = [];
        foreach ($this->input('dynamic_form_pages', []) as $pageIndex => $page) {
            foreach ($page['dynamic_form_fields'] ?? [] as $fieldIndex => $field) {
                $fieldId = $field['field_id'] ?? null;
                $fieldRuleKey = "dynamic_form_pages.{$pageIndex}.dynamic_form_fields.{$fieldIndex}.field_id";
                $valueRuleKey = "dynamic_form_pages.{$pageIndex}.dynamic_form_fields.{$fieldIndex}.value";
                
                // Custom message for field ID validation
                $messages["{$fieldRuleKey}.required"] = "Field ID at page index {$pageIndex}, field index {$fieldIndex} is required.";
                $messages["{$fieldRuleKey}.in"] = "Field ID {$fieldId} at page index {$pageIndex}, field index {$fieldIndex} is invalid.";

                // Custom message for required value validation
                $messages["{$valueRuleKey}.required"] = "Value for field ID {$fieldId} at page index {$pageIndex}, field index {$fieldIndex} is required.";
            }
        }

        return $messages;
    }
}
