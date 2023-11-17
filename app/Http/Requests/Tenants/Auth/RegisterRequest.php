<?php

namespace App\Http\Requests\Tenants\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use App\Models\User;

class RegisterRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:255',
            'role' => 'required|in:OWNER,USER',
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'password' => 'required',
            'username' => 'required|string|min:6',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'role.in' => 'The role field is invalid. Please choose (OWNER, USER)',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $password = Str::random(16);

        $this->merge([
            'password' => $password,
        ]);
    }
}
