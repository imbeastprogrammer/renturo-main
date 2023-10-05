<?php

namespace App\Http\Requests\Tenants\Admin\UserManagement;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class StoreUserRequest extends FormRequest
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
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'mobile_no' => 'required',
            'role' => 'required|in:ADMIN,OWNER,USER',
        ];
    }
}
