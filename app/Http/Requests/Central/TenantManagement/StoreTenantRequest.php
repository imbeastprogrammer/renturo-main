<?php

namespace App\Http\Requests\Central\TenantManagement;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Central\Tenant;
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
            'company' => 'required|string|unique:tenants,company|max:255',
            'status' => 'required|string',
            'plan_type' => 'required|in:demo,starter_plan,professional_plan,enterprise_plan,custom_plan',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'mobile_number' => 'required|string',
            'password' => 'required',
            'role' => 'required|in:ADMIN',
            'created_by' => 'required|exists:users,id',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $password = Str::random(12);

        $this->merge([
            'tenant_id' => Str::lower(Str::random(6)),
            'status' => Tenant::ACTIVE_STATUS,
            'password' => $password,
            'role' => User::ROLE_ADMIN,
            'created_by' => auth()->user()->id,
        ]);
    }
}
