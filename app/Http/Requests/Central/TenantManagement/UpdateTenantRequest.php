<?php

namespace App\Http\Requests\Central\TenantManagement;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Central\Tenant;
use App\Models\User;

use Str;

class UpdateTenantRequest extends FormRequest
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
            'status' => 'required|string',
            'plan_type' => 'required|in:demo,starter_plan,professional_plan,enterprise_plan,custom_plan',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'mobile_number' => 'required|string',
            'updated_by' => 'required|exists:users,id',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'updated_by' => auth()->user()->id,
        ]);
    }
}
