<?php

namespace App\Http\Requests\Tenants\StoreManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CreateStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('stores')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
            'url' => 'max:100|unique:stores,url',
            'logo' => 'nullable|string'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        #TODO: Add uploaded file validation for logo
        // Generate a random url for the store
        $url = rand(10000000000000, 99999999999999);

        $this->merge([
            'url' => $url
        ]);
    }
}
