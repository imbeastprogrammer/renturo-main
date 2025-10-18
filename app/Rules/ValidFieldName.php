<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidFieldName implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     * Field names should:
     * - Start with a letter
     * - Contain only letters, numbers, and underscores
     * - Be between 3 and 50 characters
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Check if value starts with a letter
        if (!preg_match('/^[a-zA-Z]/', $value)) {
            return false;
        }

        // Check if value contains only allowed characters
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $value)) {
            return false;
        }

        // Check length
        $length = strlen($value);
        return $length >= 3 && $length <= 50;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The field name must start with a letter, contain only letters, numbers, and underscores, and be between 3-50 characters.';
    }
}
