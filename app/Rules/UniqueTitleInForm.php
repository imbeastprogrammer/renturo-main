<?php

namespace App\Rules;

use App\Models\DynamicFormPage;
use Illuminate\Contracts\Validation\Rule;

class UniqueTitleInForm implements Rule
{
    private $formId;
    private $excludePageId;

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
        $query = DynamicFormPage::where('dynamic_form_id', $this->formId)
            ->where('title', $value);

        if ($this->excludePageId) {
            $query->where('id', '!=', $this->excludePageId);
        }

        return !$query->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'A page with this title already exists in this form.';
    }
}
