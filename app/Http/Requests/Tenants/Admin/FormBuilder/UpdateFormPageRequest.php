<?php

namespace App\Http\Requests\Tenants\Admin\FormBuilder;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormPageRequest extends FormRequest
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
        $page = \App\Models\DynamicFormPage::findOrFail($this->route('id'));

        return [
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('dynamic_form_pages')
                    ->where('dynamic_form_id', $page->dynamic_form_id)
                    ->whereNull('deleted_at')
                    ->ignore($page->id)
            ],
            'sort_no' => [
                'sometimes',
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($page) {
                    $maxSort = \App\Models\DynamicFormPage::where('dynamic_form_id', $page->dynamic_form_id)
                        ->where('id', '!=', $page->id)
                        ->max('sort_no');
                    
                    if ($value > ($maxSort + 1)) {
                        $fail('The sort order cannot be greater than ' . ($maxSort + 1));
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
            'title.required' => 'The page title is required.',
            'title.string' => 'The page title must be text.',
            'title.max' => 'The page title cannot be longer than 255 characters.',
            'title.unique' => 'A page with this title already exists in this form.',
            'sort_no.required' => 'The sort order is required.',
            'sort_no.integer' => 'The sort order must be a number.',
            'sort_no.min' => 'The sort order cannot be negative.'
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
            'title' => 'page title',
            'sort_no' => 'sort order'
        ];
    }
}