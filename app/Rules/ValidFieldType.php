<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidFieldType implements Rule
{
    private const ALLOWED_TYPES = [
        'text',
        'textarea',
        'number',
        'email',
        'tel',
        'url',
        'date',
        'time',
        'datetime-local',
        'select',
        'radio',
        'checkbox',
        'file',
        'image',
        'password',
        'color',
        'range'
    ];

    private const TYPE_DATA_REQUIREMENTS = [
        'select' => ['options'],
        'radio' => ['options'],
        'checkbox' => ['options'],
        'range' => ['min', 'max', 'step'],
        'file' => ['accept', 'max_size'],
        'image' => ['max_size', 'dimensions']
    ];

    private $data;
    private $failureReason;

    /**
     * Create a new rule instance.
     *
     * @param array|null $data Additional field data
     * @return void
     */
    public function __construct($data = null)
    {
        $this->data = $data;
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
        // Check if type is allowed
        if (!in_array($value, self::ALLOWED_TYPES)) {
            $this->failureReason = "Invalid field type. Allowed types are: " . implode(', ', self::ALLOWED_TYPES);
            return false;
        }

        // Check if required data is present for the type
        if (isset(self::TYPE_DATA_REQUIREMENTS[$value])) {
            if (!$this->data) {
                $this->failureReason = "Additional data is required for field type '{$value}'.";
                return false;
            }

            foreach (self::TYPE_DATA_REQUIREMENTS[$value] as $requirement) {
                if (!isset($this->data[$requirement])) {
                    $this->failureReason = "Field type '{$value}' requires '{$requirement}' in data.";
                    return false;
                }
            }

            // Validate specific data requirements
            switch ($value) {
                case 'select':
                case 'radio':
                case 'checkbox':
                    if (!is_array($this->data['options']) || empty($this->data['options'])) {
                        $this->failureReason = "Options must be a non-empty array.";
                        return false;
                    }
                    break;

                case 'range':
                    if (!is_numeric($this->data['min']) || !is_numeric($this->data['max']) || !is_numeric($this->data['step'])) {
                        $this->failureReason = "Min, max, and step must be numeric values.";
                        return false;
                    }
                    if ($this->data['min'] >= $this->data['max']) {
                        $this->failureReason = "Min value must be less than max value.";
                        return false;
                    }
                    if ($this->data['step'] <= 0) {
                        $this->failureReason = "Step must be greater than 0.";
                        return false;
                    }
                    break;

                case 'file':
                case 'image':
                    if (!is_numeric($this->data['max_size']) || $this->data['max_size'] <= 0) {
                        $this->failureReason = "Max size must be a positive number.";
                        return false;
                    }
                    if ($value === 'image' && !is_array($this->data['dimensions'])) {
                        $this->failureReason = "Dimensions must be specified for image fields.";
                        return false;
                    }
                    break;
            }
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
        return $this->failureReason ?? 'The field type is invalid.';
    }

    /**
     * Get the list of allowed field types.
     *
     * @return array
     */
    public static function getAllowedTypes()
    {
        return self::ALLOWED_TYPES;
    }

    /**
     * Get the data requirements for a specific field type.
     *
     * @param string $type
     * @return array|null
     */
    public static function getDataRequirements($type)
    {
        return self::TYPE_DATA_REQUIREMENTS[$type] ?? null;
    }
}
