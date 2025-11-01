<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvailabilityException extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'dynamic_form_submission_id',
        'date',
        'start_time',
        'end_time',
        'type',
        'reason',
        'notes',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the unit (dynamic form submission) this exception belongs to
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(DynamicFormSubmission::class, 'dynamic_form_submission_id');
    }

    /**
     * Get the user who created this exception
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this exception
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Check if exception blocks a specific time
     */
    public function blocksTime(string $time): bool
    {
        // If no time specified, blocks all day
        if (!$this->start_time || !$this->end_time) {
            return true;
        }

        return $time >= $this->start_time && $time < $this->end_time;
    }

    /**
     * Scope: Active exceptions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: For specific date
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    /**
     * Scope: For specific unit
     */
    public function scopeForUnit($query, $unitId)
    {
        return $query->where('dynamic_form_submission_id', $unitId);
    }
}

