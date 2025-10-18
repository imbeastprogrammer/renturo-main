<?php

namespace App\Http\Requests\Tenants\Admin\FormBuilder;

use App\Models\DynamicForm;
use App\Models\SubCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormRequest extends FormRequest
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
        $this->form = DynamicForm::find($this->route('form'));

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
        $dynamicFormId = $this->route('form');

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('dynamic_forms')->where(function ($query) {
                    return $query->where('subcategory_id', $this->subcategory_id);
                })->ignore($dynamicFormId)
            ],
            'description' => [
                'required',
                'string',
                'min:10',
                'max:1000'
            ],
            'subcategory_id' => [
                'required',
                'integer',
                Rule::exists('sub_categories', 'id')->where(function ($query) {
                    $query->where('is_active', true)
                        ->whereNull('deleted_at');
                }),
                function ($attribute, $value, $fail) {
                    $subcategory = SubCategory::with(['category' => function ($query) {
                        $query->withTrashed();
                    }])->find($value);

                    if (!$subcategory || !$subcategory->category || $subcategory->category->trashed()) {
                        $fail('The selected subcategory\'s parent category does not exist or has been deleted.');
                    }
                },
                function ($attribute, $value, $fail) {
                    // Check if the form has submissions and is trying to change subcategory
                    if ($this->form->dynamicFormSubmissions()->exists() && 
                        $value !== $this->form->subcategory_id) {
                        $fail('Cannot change subcategory: form has existing submissions.');
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
            'name.unique' => 'A form with this name already exists in the selected subcategory.',
            'description.required' => 'A form description is required.',
            'description.string' => 'The form description must be a string.',
            'description.min' => 'The form description must be at least :min characters.',
            'description.max' => 'The form description cannot be longer than :max characters.',
            'subcategory_id.required' => 'A subcategory must be selected.',
            'subcategory_id.integer' => 'Invalid subcategory ID.',
            'subcategory_id.exists' => 'The selected subcategory does not exist or is inactive.'
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
            'subcategory_id' => 'subcategory'
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
    }
}
