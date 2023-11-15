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
            'input_field_label' => 'required|regex:/^[A-Za-z\s]+$/|max:255',
            'input_field_name' => 'required',
            'input_field_type' => ['required', Rule::in(DynamicFormField::FIELD_TYPES)],
            'is_required' => 'sometimes|required|boolean',
            'is_multiple' => 'sometimes|required|boolean',
            'data' => 'sometimes|required|array'
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
            'input_field_name' => Str::lower(Str::snake($this->input_field_label)),
        ]);
    }
}
