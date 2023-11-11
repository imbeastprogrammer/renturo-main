<?php

namespace App\Http\Requests\Central\UserManagement;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Central\User;

class UpdateUserProfileRequest extends FormRequest
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
            'email' => 'required|string|email|max:255|unique:users,email,'. auth()->user()->id,
            'mobile_number' => 'required|string|unique:users,mobile_number,' . auth()->user()->id,
        ];
    }
}
