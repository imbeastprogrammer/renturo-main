<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class DynamicFormField extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Valid field types.
     *
     * @var array<string>
     */
    const FIELD_TYPES = [
        'heading',     // Section heading
        'body',        // Section description
        'text',        // Single line text input
        'textarea',    // Multi-line text input
        'number',      // Numeric input
        'email',       // Email input
        'date',        // Date picker
        'time',        // Time picker
        'select',      // Single select dropdown
        'checkbox',    // Single checkbox
        'radio',       // Radio button group
        'checklist',   // Multiple checkboxes
        'attachment',  // File upload with preview
        'rating',      // Star/numeric rating
        'password',    // Password input
        'multiselect', // Multiple select dropdown
        'file',        // File upload
        'hidden',      // Hidden input
        'color',       // Color picker
        'url',         // URL input
        'phone',       // Phone number input
        'currency',    // Currency input
        'matrix',      // Matrix/grid input
        'repeater',    // Repeatable field group
    ];

    /**
     * Field types that can have options.
     *
     * @var array<string>
     */
    const OPTION_FIELD_TYPES = [
        'select',
        'radio',
        'checklist',
        'multiselect'
    ];

    /**
     * Field types that can have validation rules.
     *
     * @var array<string>
     */
    const VALIDATABLE_FIELD_TYPES = [
        'text',
        'textarea',
        'number',
        'email',
        'date',
        'time',
        'password',
        'url'
    ];

    /**
     * Field types that can have file restrictions.
     *
     * @var array<string>
     */
    const FILE_FIELD_TYPES = [
        'file',
        'attachment'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'dynamic_form_page_id',
        'input_field_label',
        'input_field_name',
        'input_field_type',
        'is_required',
        'sort_no',
        'data',
        'value'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_required' => 'boolean',
        'sort_no' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the data attribute.
     *
     * @param mixed $value
     * @return array
     */
    public function getDataAttribute($value)
    {
        if ($value === null) {
            return [];
        }
        return is_array($value) ? $value : json_decode($value, true) ?? [];
    }

    /**
     * Set the data attribute.
     *
     * @param mixed $value
     * @return void
     */
    public function setDataAttribute($value)
    {
        $this->attributes['data'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * The relationships that should always be loaded.
     *
     * @var array<string>
     */
    protected $with = ['user'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($field) {
            // Generate field name from label if not provided
            if (empty($field->input_field_name)) {
                $fieldName = strtolower(trim($field->input_field_label));
                $fieldName = preg_replace('/\s+/', '_', $fieldName); // Replace spaces with underscores
                $fieldName = preg_replace('/[^a-z0-9_]/', '', $fieldName); // Remove special characters
                $fieldName = rtrim($fieldName, '_'); // Remove trailing underscores
                $field->input_field_name = $fieldName;
            }

            // Initialize data array
            $data = $field->data ?? [];

            // Set default data based on field type
            switch ($field->input_field_type) {
                case 'select':
                case 'radio':
                case 'checklist':
                case 'multiselect':
                    $data['options'] = $data['options'] ?? [];
                    break;

                case 'number':
                case 'currency':
                    $data['min'] = $data['min'] ?? null;
                    $data['max'] = $data['max'] ?? null;
                    $data['step'] = $data['step'] ?? 1;
                    break;

                case 'file':
                case 'attachment':
                    $data['accept'] = $data['accept'] ?? '*/*';
                    $data['maxSize'] = $data['maxSize'] ?? (5 * 1024 * 1024); // 5MB default
                    break;

                case 'text':
                case 'textarea':
                    $data['pattern'] = $data['pattern'] ?? null;
                    $data['placeholder'] = $data['placeholder'] ?? null;
                    if ($field->input_field_type === 'textarea') {
                        $data['rows'] = $data['rows'] ?? 3;
                        $data['cols'] = $data['cols'] ?? 40;
                    }
                    break;

                case 'rating':
                    $data['max'] = $data['max'] ?? 5;
                    break;

                case 'matrix':
                    $data['rows'] = $data['rows'] ?? [];
                    $data['columns'] = $data['columns'] ?? [];
                    break;

                case 'repeater':
                    $data['min_items'] = $data['min_items'] ?? 1;
                    $data['max_items'] = $data['max_items'] ?? 10;
                    $data['fields'] = $data['fields'] ?? [];
                    break;

                case 'phone':
                    $data['country_code'] = $data['country_code'] ?? '+1';
                    $data['format'] = $data['format'] ?? '###-###-####';
                    break;
            }

            $field->data = $data;
        });
    }

    /**
     * Get the user that created this field.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the page that owns this field.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dynamicFormPage(): BelongsTo
    {
        return $this->belongsTo(DynamicFormPage::class, 'dynamic_form_page_id')->withTrashed();
    }

    /**
     * Get the form through the page relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function dynamicForm(): HasOneThrough
    {
        return $this->hasOneThrough(
            DynamicForm::class,
            DynamicFormPage::class,
            'id', // Foreign key on dynamic_form_pages table...
            'id', // Foreign key on dynamic_forms table...
            'dynamic_form_page_id', // Local key on dynamic_form_fields table...
            'dynamic_form_id' // Local key on dynamic_form_pages table...
        )->withTrashed();
    }

    /**
     * Check if this field type can have options.
     *
     * @return bool
     */
    public function canHaveOptions(): bool
    {
        return in_array($this->input_field_type, self::OPTION_FIELD_TYPES);
    }

    /**
     * Check if this field type can have validation rules.
     *
     * @return bool
     */
    public function canHaveValidation(): bool
    {
        return in_array($this->input_field_type, self::VALIDATABLE_FIELD_TYPES);
    }

    /**
     * Check if this field type can have file restrictions.
     *
     * @return bool
     */
    public function canHaveFileRestrictions(): bool
    {
        return in_array($this->input_field_type, self::FILE_FIELD_TYPES);
    }

    /**
     * Get the validation rules for this field.
     *
     * @return array<string, mixed>
     */
    public function getValidationRules(): array
    {
        $rules = [];

        // Add required rule if field is required
        if ($this->is_required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        // Add conditional validation if field has dependencies
        if (isset($this->data['depends_on']) && isset($this->data['show_if']) && isset($this->data['value'])) {
            $rules[] = "required_if:{$this->data['depends_on']},{$this->data['value']}";
        }

        // Add type-specific rules
        switch ($this->input_field_type) {
            case 'text':
            case 'textarea':
                $rules[] = 'string';
                if (isset($this->data['pattern'])) {
                    $rules[] = 'regex:/' . $this->data['pattern'] . '/';
                }
                break;

            case 'number':
            case 'currency':
                $rules[] = 'numeric';
                if (isset($this->data['min'])) {
                    $rules[] = 'min:' . $this->data['min'];
                }
                if (isset($this->data['max'])) {
                    $rules[] = 'max:' . $this->data['max'];
                }
                break;

            case 'email':
                $rules[] = 'email';
                break;

            case 'date':
                $rules[] = 'date';
                break;

            case 'time':
                $rules[] = 'date_format:H:i';
                break;

            case 'select':
            case 'radio':
                if (isset($this->data['options'])) {
                    $rules[] = 'in:' . implode(',', $this->data['options']);
                }
                break;

            case 'checklist':
            case 'multiselect':
            case 'matrix':
            case 'repeater':
                $rules[] = 'array';
                if ($this->input_field_type === 'matrix') {
                    if (isset($this->data['rows']) && isset($this->data['columns'])) {
                        $rules[] = '*.*.in:' . implode(',', $this->data['rows']);
                        $rules[] = '*.*.in:' . implode(',', $this->data['columns']);
                    }
                } elseif ($this->input_field_type === 'repeater') {
                    if (isset($this->data['min_items'])) {
                        $rules[] = 'min:' . $this->data['min_items'];
                    }
                    if (isset($this->data['max_items'])) {
                        $rules[] = 'max:' . $this->data['max_items'];
                    }
                    if (isset($this->data['fields'])) {
                        foreach ($this->data['fields'] as $field) {
                            if ($field['required'] ?? false) {
                                $rules[] = '*.' . str_replace(' ', '_', strtolower($field['label'])) . '.required';
                            }
                        }
                    }
                } elseif (isset($this->data['options'])) {
                    $rules[] = 'in:' . implode(',', $this->data['options']);
                }
                break;

            case 'file':
            case 'attachment':
                $rules[] = 'file';
                if (isset($this->data['accept'])) {
                    $rules[] = 'mimes:' . str_replace(['.', ','], ['', ','], $this->data['accept']);
                }
                if (isset($this->data['maxSize'])) {
                    $rules[] = 'max:' . ($this->data['maxSize'] / 1024); // Convert to KB
                }
                break;

            case 'url':
                $rules[] = 'url';
                break;

            case 'color':
                $rules[] = 'regex:/^#[0-9a-f]{6}$/i';
                break;

            case 'phone':
                $rules[] = 'regex:/^\+?1?\d{10}$/';
                break;
        }

        return $rules;
    }

    /**
     * Get the validation messages for this field.
     *
     * @return array<string, string>
     */
    public function getValidationMessages(): array
    {
        $messages = [];
        $fieldName = $this->input_field_label;

        $messages['required'] = "The {$fieldName} field is required.";
        $messages['nullable'] = "The {$fieldName} field is optional.";

        // Add conditional validation messages
        if (isset($this->data['depends_on']) && isset($this->data['show_if']) && isset($this->data['value'])) {
            $messages['required_if'] = "The {$fieldName} field is required when {$this->data['depends_on']} is {$this->data['value']}.";
        }

        switch ($this->input_field_type) {
            case 'text':
            case 'textarea':
                $messages['string'] = "The {$fieldName} must be text.";
                $messages['regex'] = "The {$fieldName} format is invalid.";
                break;

            case 'number':
            case 'currency':
                $messages['numeric'] = "The {$fieldName} must be a number.";
                $messages['min'] = "The {$fieldName} must be at least :min.";
                $messages['max'] = "The {$fieldName} must not be greater than :max.";
                break;

            case 'email':
                $messages['email'] = "The {$fieldName} must be a valid email address.";
                break;

            case 'date':
                $messages['date'] = "The {$fieldName} must be a valid date.";
                break;

            case 'time':
                $messages['date_format'] = "The {$fieldName} must be in HH:MM format.";
                break;

            case 'select':
            case 'radio':
                $messages['in'] = "The selected {$fieldName} is invalid.";
                break;

            case 'checklist':
            case 'multiselect':
            case 'matrix':
            case 'repeater':
                $messages['array'] = "The {$fieldName} must be an array.";
                $messages['in'] = "The selected {$fieldName} is invalid.";
                if ($this->input_field_type === 'matrix') {
                    $messages['*.*.in'] = "The selected value is invalid.";
                } elseif ($this->input_field_type === 'repeater') {
                    $messages['min'] = "The {$fieldName} must have at least :min items.";
                    $messages['max'] = "The {$fieldName} must not have more than :max items.";
                    if (isset($this->data['fields'])) {
                        foreach ($this->data['fields'] as $field) {
                            if ($field['required'] ?? false) {
                                $fieldKey = str_replace(' ', '_', strtolower($field['label']));
                                $messages["*.{$fieldKey}.required"] = "The {$field['label']} field is required for each item.";
                            }
                        }
                    }
                }
                break;

            case 'file':
            case 'attachment':
                $messages['file'] = "The {$fieldName} must be a file.";
                $messages['mimes'] = "The {$fieldName} must be a file of type: :values.";
                $messages['max'] = "The {$fieldName} must not be greater than :max kilobytes.";
                break;

            case 'url':
                $messages['url'] = "The {$fieldName} must be a valid URL.";
                break;

            case 'color':
                $messages['regex'] = "The {$fieldName} must be a valid hex color code.";
                break;

            case 'phone':
                $messages['regex'] = "The {$fieldName} must be a valid phone number.";
                break;
        }

        return $messages;
    }

    /**
     * Format a value according to the field type.
     *
     * @param mixed $value
     * @return mixed
     */
    public function formatValue($value)
    {
        if ($value === null) {
            return null;
        }

        switch ($this->input_field_type) {
            case 'number':
                return is_numeric($value) ? (float)$value : null;

            case 'date':
                return date('Y-m-d', strtotime($value));

            case 'time':
                return date('H:i', strtotime($value));

            case 'checklist':
            case 'multiselect':
                return is_array($value) ? array_values($value) : [$value];

            case 'checkbox':
                return (bool)$value;

            case 'color':
                return strtolower($value);

            default:
                return (string)$value;
        }
    }

    /**
     * Get the default value for this field type.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        switch ($this->input_field_type) {
            case 'number':
                return isset($this->data['min']) ? $this->data['min'] : 0;

            case 'date':
                return date('Y-m-d');

            case 'time':
                return date('H:i');

            case 'select':
            case 'radio':
                return isset($this->data['options'][0]) ? $this->data['options'][0] : null;

            case 'checklist':
            case 'multiselect':
                return [];

            case 'checkbox':
                return false;

            case 'color':
                return '#000000';

            default:
                return '';
        }
    }
}
