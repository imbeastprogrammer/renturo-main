<?php

namespace App\Http\Requests\Tenants\Admin\FormBuilder;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReorderFormPagesRequest extends FormRequest
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
            'form_id' => [
                'required',
                'exists:dynamic_forms,id',
                function ($attribute, $value, $fail) {
                    $form = \App\Models\DynamicForm::find($value);
                    if (!$form || $form->trashed()) {
                        $fail('The selected form is invalid or has been deleted.');
                    }
                }
            ],
            'pages' => 'required|array|min:1',
            'pages.*.id' => [
                'required',
                'exists:dynamic_form_pages,id',
                Rule::exists('dynamic_form_pages', 'id')
                    ->where('dynamic_form_id', $this->form_id)
            ],
            'pages.*.sort_no' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    if ($value !== (int)$index) {
                        $fail('The sort order must match the array index.');
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
    public function messages(): array
    {
        return [
            'form_id.required' => 'A form must be selected.',
            'form_id.exists' => 'The selected form does not exist.',
            'pages.required' => 'At least one page must be provided.',
            'pages.array' => 'The pages must be provided as an array.',
            'pages.min' => 'At least one page must be provided.',
            'pages.*.id.required' => 'Each page must have an ID.',
            'pages.*.id.exists' => 'One or more pages do not exist.',
            'pages.*.sort_no.required' => 'Each page must have a sort order.',
            'pages.*.sort_no.integer' => 'Sort order must be a number.',
            'pages.*.sort_no.min' => 'Sort order cannot be negative.'
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
            'form_id' => 'form',
            'pages' => 'pages',
            'pages.*.id' => 'page ID',
            'pages.*.sort_no' => 'sort order'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (is_array($this->pages)) {
            // Sort pages by sort_no to ensure they're in order
            $pages = collect($this->pages)->sortBy('sort_no')->values();
            
            // Reindex sort_no to ensure they're sequential
            $pages = $pages->map(function ($page, $index) {
                $page['sort_no'] = $index;
                return $page;
            })->all();

            $this->merge(['pages' => $pages]);
        }
    }
}
