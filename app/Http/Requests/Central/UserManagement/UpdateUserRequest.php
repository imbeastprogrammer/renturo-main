<?php

namespace App\Http\Requests\Central\UserManagement;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        #TODO: check why the mobile number and email are not included in update?
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'updated_by' => 'required|exists:users,id',
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
            'updated_by' => auth()->user()->id,
        ]);
    }
}
