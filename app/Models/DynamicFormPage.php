<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class DynamicFormPage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'sort_no',
        'dynamic_form_id',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array<string>
     */
    protected $with = ['dynamicFormFields'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sort_no' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the form that owns this page.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dynamicForm()
    {
        return $this->belongsTo(DynamicForm::class)->withTrashed();
    }

    /**
     * Get the user that created this page.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the fields associated with this page.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dynamicFormFields()
    {
        return $this->hasMany(DynamicFormField::class)->orderBy('sort_no');
    }

    /**
     * Get the subcategory through the form relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function subCategory()
    {
        return $this->hasOneThrough(
            SubCategory::class,
            DynamicForm::class,
            'id', // Foreign key on dynamic_forms table...
            'id', // Foreign key on sub_categories table...
            'dynamic_form_id', // Local key on dynamic_form_pages table...
            'subcategory_id' // Local key on dynamic_forms table...
        )->withTrashed();
    }

    /**
     * Get the category through the form and subcategory relationships.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function category()
    {
        return $this->hasOneThrough(
            Category::class,
            SubCategory::class,
            'id', // Foreign key on sub_categories table...
            'id', // Foreign key on categories table...
            'dynamic_form_id', // Local key on dynamic_form_pages table...
            'category_id' // Local key on sub_categories table...
        )->withTrashed();
    }

    /**
     * The "booted" method of the model.
     * Set up event listeners for cascading soft deletes and restores
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($page) {
            $page->dynamicFormFields()->delete();
        });

        static::restoring(function ($page) {
            $page->dynamicFormFields()->withTrashed()->restore();
        });
    }

    /**
     * Get the next available sort number for this form.
     *
     * @return int
     */
    public function getNextSortNumber(): int
    {
        $maxSort = static::where('dynamic_form_id', $this->dynamic_form_id)
            ->max('sort_no');
        
        return is_null($maxSort) ? 0 : $maxSort + 1;
    }

    /**
     * Reorder pages after this page is moved or deleted.
     *
     * @param int $oldPosition
     * @param int|null $newPosition
     * @return void
     */
    public function reorderPages(int $oldPosition, ?int $newPosition = null): void
    {
        if ($newPosition === null) {
            // Page was deleted, decrement sort_no for all pages after it
            static::where('dynamic_form_id', $this->dynamic_form_id)
                ->where('sort_no', '>', $oldPosition)
                ->decrement('sort_no');
        } else if ($newPosition > $oldPosition) {
            // Moving down: decrement pages in between
            static::where('dynamic_form_id', $this->dynamic_form_id)
                ->where('id', '!=', $this->id)
                ->where('sort_no', '<=', $newPosition)
                ->where('sort_no', '>', $oldPosition)
                ->decrement('sort_no');
        } else {
            // Moving up: increment pages in between
            static::where('dynamic_form_id', $this->dynamic_form_id)
                ->where('id', '!=', $this->id)
                ->where('sort_no', '>=', $newPosition)
                ->where('sort_no', '<', $oldPosition)
                ->increment('sort_no');
        }
    }
}
