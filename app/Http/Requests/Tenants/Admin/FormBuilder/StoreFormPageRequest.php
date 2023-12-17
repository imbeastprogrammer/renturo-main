<?php

namespace App\Http\Requests\Tenants\Admin\FormBuilder;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormPageRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'sub_category_id' => 'required|exists:sub_categories,id',
        ];
    }
}
