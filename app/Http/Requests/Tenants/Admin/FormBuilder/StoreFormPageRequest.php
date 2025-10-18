<?php

namespace App\Http\Requests\Tenants\Admin\FormBuilder;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UniqueTitleInForm;
use App\Rules\ValidSortOrder;

class StoreFormPageRequest extends FormRequest
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
            'dynamic_form_id' => [
                'required',
                'exists:dynamic_forms,id',
                function ($attribute, $value, $fail) {
                    $form = \App\Models\DynamicForm::find($value);
                    if (!$form || $form->trashed()) {
                        $fail('The selected form is invalid or has been deleted.');
                    }
                }
            ],
            'title' => [
                'required',
                'string',
                'max:255',
                new UniqueTitleInForm($this->dynamic_form_id)
            ],
            'sort_no' => [
                'sometimes',
                'integer',
                new ValidSortOrder($this->dynamic_form_id)
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
            'dynamic_form_id.required' => 'A form must be selected.',
            'dynamic_form_id.exists' => 'The selected form does not exist.',
            'title.required' => 'The page title is required.',
            'title.string' => 'The page title must be text.',
            'title.max' => 'The page title cannot be longer than 255 characters.',
            'title.unique' => 'A page with this title already exists in the selected form.'
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
            'dynamic_form_id' => 'form',
            'title' => 'page title'
        ];
    }
}