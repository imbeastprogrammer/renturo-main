<?php

namespace App\Http\Requests\Tenants\DynamicFormAvailabilityManagement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDynamicFormAvailabilityRequest extends FormRequest
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
            'dynamic_form_submission_id' => 'required|integer|exists:dynamic_form_submissions,id',
            'user_id' => 'required|integer|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'availability_type' => ['required', 'string', Rule::in(['hourly', 'daily'])],
            'status' => ['required', 'string', Rule::in(['open', 'closed'])],
            'recurring' => 'nullable|array', 
            'start_time' => [
                'nullable',
                'date_format:H:i',
                Rule::requiredIf($this->input('availability_type') === 'hourly'),
            ],
            'end_time' => [
                'nullable',
                'date_format:H:i',
                Rule::requiredIf($this->input('availability_type') === 'hourly'),
            ],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->sometimes('end_time', 'after:start_time', function ($input) {
            return $input->availability_type === 'hourly' && $input->start_time;
        });
    }
}
