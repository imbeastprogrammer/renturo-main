<?php

namespace App\Http\Requests\Tenants\StoreManagement;

use App\Models\Store;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateStoreRequest extends FormRequest
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

        $storeId = $this->route('store'); // Retrieve the store ID from the route

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('stores')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })->ignore($storeId)
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
    }
}
