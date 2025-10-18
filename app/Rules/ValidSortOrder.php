<?php

namespace App\Rules;

use App\Models\DynamicFormPage;
use Illuminate\Contracts\Validation\Rule;

class ValidSortOrder implements Rule
{
    private $formId;
    private $excludePageId;
    private $failureReason;

    /**
     * Create a new rule instance.
     *
     * @param int $formId
     * @param int|null $excludePageId
     * @return void
     */
    public function __construct($formId, $excludePageId = null)
    {
        $this->formId = $formId;
        $this->excludePageId = $excludePageId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Sort order must be non-negative
        if ($value < 0) {
            $this->failureReason = 'Sort order must be non-negative.';
            return false;
        }

        // Get the count of pages in this form
        $query = DynamicFormPage::where('dynamic_form_id', $this->formId);
        if ($this->excludePageId) {
            $query->where('id', '!=', $this->excludePageId);
        }
        $pageCount = $query->count();

        // Sort order must not exceed the number of pages
        if ($value > $pageCount) {
            $this->failureReason = 'Sort order cannot exceed the number of pages.';
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->failureReason ?? 'The sort order is invalid.';
    }
}
