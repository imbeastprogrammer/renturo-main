<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class DynamicForm extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'subcategory_id',
        'user_id'
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array<string>
     */
    protected $with = ['subCategory'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * The "booted" method of the model.
     * Set up event listeners for cascading soft deletes
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($form) {
            $form->dynamicFormPages()->delete();
            $form->dynamicFormSubmissions()->delete();
        });

        static::restoring(function ($form) {
            $form->dynamicFormPages()->withTrashed()->restore();
            $form->dynamicFormSubmissions()->withTrashed()->restore();
        });
    }

    /**
     * Get the pages associated with this form.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dynamicFormPages()
    {
        return $this->hasMany(DynamicFormPage::class)->orderBy('sort_no');
    }

    /**
     * Get the subcategory that this form belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id')->withTrashed();
    }

    /**
     * Get the category through the subcategory relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function category()
    {
        return $this->hasOneThrough(
            Category::class,
            SubCategory::class,
            'id', // Foreign key on subcategories table...
            'id', // Foreign key on categories table...
            'subcategory_id', // Local key on dynamic_forms table...
            'category_id' // Local key on subcategories table...
        )->withTrashed();
    }


    /**
     * Get the submissions associated with this form.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dynamicFormSubmissions()
    {
        return $this->hasMany(DynamicFormSubmission::class)->latest();
    }

    /**
     * Get the latest submission for a specific user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getLatestSubmissionForUser($userId)
    {
        return $this->dynamicFormSubmissions()
            ->where('user_id', $userId)
            ->latest()
            ->first();
    }

    /**
     * Scope a query to include forms for a specific subcategory.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $subcategoryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBySubcategory($query, $subcategoryId)
    {
        return $query->where('subcategory_id', $subcategoryId);
    }

    /**
     * Scope a query to search forms by name or description.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Get the user that created this form.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all fields across all pages.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllFields()
    {
        return DynamicFormField::whereIn(
            'dynamic_form_page_id',
            $this->dynamicFormPages()->pluck('id')
        )->orderBy('dynamic_form_page_id')
          ->orderBy('sort_no')
          ->get();
    }

    /**
     * Get all required fields across all pages.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRequiredFields()
    {
        return DynamicFormField::whereIn(
            'dynamic_form_page_id',
            $this->dynamicFormPages()->pluck('id')
        )->where('is_required', true)
          ->orderBy('dynamic_form_page_id')
          ->orderBy('sort_no')
          ->get();
    }

    /**
     * Get all fields of a specific type across all pages.
     *
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFieldsByType($type)
    {
        return DynamicFormField::whereIn(
            'dynamic_form_page_id',
            $this->dynamicFormPages()->pluck('id')
        )->where('input_field_type', $type)
          ->orderBy('dynamic_form_page_id')
          ->orderBy('sort_no')
          ->get();
    }

    /**
     * Get all submissions for a specific user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSubmissionsForUser($userId)
    {
        return $this->dynamicFormSubmissions()
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    /**
     * Get all submissions with a specific status.
     *
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSubmissionsByStatus($status)
    {
        return $this->dynamicFormSubmissions()
            ->where('status', $status)
            ->latest()
            ->get();
    }

    /**
     * Get the total number of submissions.
     *
     * @return int
     */
    public function getSubmissionsCount()
    {
        return $this->dynamicFormSubmissions()->count();
    }

    /**
     * Get the total number of fields across all pages.
     *
     * @return int
     */
    public function getFieldsCount()
    {
        return DynamicFormField::whereIn(
            'dynamic_form_page_id',
            $this->dynamicFormPages()->pluck('id')
        )->count();
    }

    /**
     * Get the total number of required fields across all pages.
     *
     * @return int
     */
    public function getRequiredFieldsCount()
    {
        return DynamicFormField::whereIn(
            'dynamic_form_page_id',
            $this->dynamicFormPages()->pluck('id')
        )->where('is_required', true)
          ->count();
    }

    /**
     * Check if a user has any submissions for this form.
     *
     * @param int $userId
     * @return bool
     */
    public function hasSubmissionsFromUser($userId)
    {
        return $this->dynamicFormSubmissions()
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Check if a user has a specific submission status for this form.
     *
     * @param int $userId
     * @param string $status
     * @return bool
     */
    public function hasSubmissionStatus($userId, $status)
    {
        return $this->dynamicFormSubmissions()
            ->where('user_id', $userId)
            ->where('status', $status)
            ->exists();
    }

    /**
     * Get the latest submission status for a user.
     *
     * @param int $userId
     * @return string|null
     */
    public function getLatestSubmissionStatus($userId)
    {
        $submission = $this->getLatestSubmissionForUser($userId);
        return $submission ? $submission->status : null;
    }

    /**
     * Get the submission data for a specific field from a user's latest submission.
     *
     * @param int $userId
     * @param string $fieldName
     * @return mixed|null
     */
    public function getLatestSubmissionFieldData($userId, $fieldName)
    {
        $submission = $this->getLatestSubmissionForUser($userId);
        if (!$submission) {
            return null;
        }

        $data = json_decode($submission->data, true);
        return $data[$fieldName] ?? null;
    }
}
