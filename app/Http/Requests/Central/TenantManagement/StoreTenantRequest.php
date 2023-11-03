<?php

namespace App\Http\Requests\Central\TenantManagement;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

use Str;

class StoreTenantRequest extends FormRequest
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
            'tenant_id' => 'required|max:6',
            'name' => 'required|string|unique:tenants,name|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'mobile_no' => 'required',
            'password' => 'required',
            'role' => 'required|in:ADMIN',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'tenant_id' => Str::lower(Str::random(6)),
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
        ]);
    }
}
