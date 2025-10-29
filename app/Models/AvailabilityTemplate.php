<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AvailabilityTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'listing_id',
        'name',
        'description',
        'days_of_week',
        'specific_dates',
        'date_ranges',
        'start_time',
        'end_time',
        'slot_duration_minutes',
        'base_hourly_price',
        'base_daily_price',
        'peak_hour_multiplier',
        'weekend_multiplier',
        'holiday_multiplier',
        'peak_start_time',
        'peak_end_time',
        'min_duration_hours',
        'max_duration_hours',
        'duration_type',
        'advance_booking_hours',
        'max_advance_booking_days',
        'cancellation_hours',
        'cancellation_fee_percentage',
        'category_rules',
        'booking_rules',
        'is_active',
        'priority',
        'valid_from',
        'valid_until',
        'auto_apply',
        'auto_apply_days_ahead',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'specific_dates' => 'array',
        'date_ranges' => 'array',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'peak_start_time' => 'datetime:H:i',
        'peak_end_time' => 'datetime:H:i',
        'base_hourly_price' => 'decimal:2',
        'base_daily_price' => 'decimal:2',
        'peak_hour_multiplier' => 'decimal:2',
        'weekend_multiplier' => 'decimal:2',
        'holiday_multiplier' => 'decimal:2',
        'cancellation_fee_percentage' => 'decimal:2',
        'category_rules' => 'array',
        'booking_rules' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
        'auto_apply' => 'boolean',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'slot_duration_minutes' => 'integer',
        'min_duration_hours' => 'integer',
        'max_duration_hours' => 'integer',
        'advance_booking_hours' => 'integer',
        'max_advance_booking_days' => 'integer',
        'cancellation_hours' => 'integer',
        'priority' => 'integer',
        'auto_apply_days_ahead' => 'integer',
    ];

    /**
     * Duration type constants
     */
    const DURATION_HOURLY = 'hourly';
    const DURATION_DAILY = 'daily';
    const DURATION_WEEKLY = 'weekly';
    const DURATION_MONTHLY = 'monthly';

    /**
     * Template belongs to a listing
     */
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * Template created by user
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Template updated by user
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Check if template applies to a specific date
     */
    public function appliesTo(Carbon $date): bool
    {
        // Check if template is active
        if (!$this->is_active) {
            return false;
        }

        // Check validity period
        if ($this->valid_from && $date->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $date->gt($this->valid_until)) {
            return false;
        }

        // Check specific dates
        if ($this->specific_dates && in_array($date->toDateString(), $this->specific_dates)) {
            return true;
        }

        // Check date ranges
        if ($this->date_ranges) {
            foreach ($this->date_ranges as $range) {
                $start = Carbon::parse($range['start']);
                $end = Carbon::parse($range['end']);
                if ($date->between($start, $end)) {
                    return true;
                }
            }
        }

        // Check days of week (1 = Monday, 7 = Sunday)
        if ($this->days_of_week && in_array($date->dayOfWeek, $this->days_of_week)) {
            return true;
        }

        return false;
    }

    /**
     * Apply template to a specific date range
     */
    public function applyToDateRange(Carbon $startDate, Carbon $endDate, array $units = null): int
    {
        $current = $startDate->copy();
        $applied = 0;

        while ($current->lte($endDate)) {
            if ($this->appliesTo($current)) {
                $this->applyToDate($current, $units);
                $applied++;
            }
            $current->addDay();
        }

        return $applied;
    }

    /**
     * Apply template to a specific date
     */
    public function applyToDate(Carbon $date, array $units = null): void
    {
        $units = $units ?: $this->getTargetUnits();

        foreach ($units as $unitIdentifier) {
            // Check if availability already exists
            $existing = ListingAvailability::where([
                'listing_id' => $this->listing_id,
                'unit_identifier' => $unitIdentifier,
                'available_date' => $date->toDateString(),
                'start_time' => $this->start_time,
            ])->first();

            if ($existing) {
                // Update existing availability
                $existing->update($this->getAvailabilityData($date, $unitIdentifier));
            } else {
                // Create new availability
                ListingAvailability::create(array_merge(
                    $this->getAvailabilityData($date, $unitIdentifier),
                    [
                        'listing_id' => $this->listing_id,
                        'unit_identifier' => $unitIdentifier,
                        'available_date' => $date->toDateString(),
                        'created_by' => $this->created_by,
                    ]
                ));
            }
        }
    }

    /**
     * Get availability data for a specific date and unit
     */
    protected function getAvailabilityData(Carbon $date, string $unitIdentifier = null): array
    {
        $isWeekend = $date->isWeekend();
        $isPeakHour = $this->isPeakHour($this->start_time);
        $isHoliday = $this->isHoliday($date);

        // Calculate pricing
        $basePrice = $this->base_hourly_price;
        
        if ($isPeakHour) {
            $basePrice *= $this->peak_hour_multiplier;
        }
        
        if ($isWeekend) {
            $basePrice *= $this->weekend_multiplier;
        }
        
        if ($isHoliday) {
            $basePrice *= $this->holiday_multiplier;
        }

        return [
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'slot_duration_minutes' => $this->slot_duration_minutes,
            'hourly_price' => $basePrice,
            'daily_price' => $this->base_daily_price,
            'peak_hour_price' => $isPeakHour ? $basePrice : null,
            'weekend_price' => $isWeekend ? $basePrice : null,
            'holiday_price' => $isHoliday ? $basePrice : null,
            'min_duration_hours' => $this->min_duration_hours,
            'max_duration_hours' => $this->max_duration_hours,
            'duration_type' => $this->duration_type,
            'category_rules' => $this->category_rules,
            'booking_rules' => $this->booking_rules,
            'status' => 'available',
        ];
    }

    /**
     * Check if time is within peak hours
     */
    protected function isPeakHour($time): bool
    {
        if (!$this->peak_start_time || !$this->peak_end_time) {
            return false;
        }

        $timeCarbon = Carbon::parse($time);
        $peakStart = Carbon::parse($this->peak_start_time);
        $peakEnd = Carbon::parse($this->peak_end_time);

        return $timeCarbon->between($peakStart, $peakEnd);
    }

    /**
     * Check if date is a holiday (basic implementation)
     */
    protected function isHoliday(Carbon $date): bool
    {
        // Basic holiday detection - can be enhanced with holiday API
        $holidays = [
            '01-01', // New Year
            '12-25', // Christmas
            '12-31', // New Year's Eve
        ];

        return in_array($date->format('m-d'), $holidays);
    }

    /**
     * Get target units for this template
     */
    protected function getTargetUnits(): array
    {
        if ($this->listing->inventory_type === 'single') {
            return [null]; // Single unit listings don't need unit identifier
        }

        // For multi-unit listings, get all active units
        return $this->listing->units()
            ->where('status', 'active')
            ->pluck('unit_identifier')
            ->toArray();
    }

    /**
     * Generate time slots for this template
     */
    public function generateTimeSlots(): array
    {
        $slots = [];
        $current = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);

        while ($current->lt($end)) {
            $slotEnd = $current->copy()->addMinutes($this->slot_duration_minutes);
            
            if ($slotEnd->lte($end)) {
                $slots[] = [
                    'start' => $current->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                    'duration_minutes' => $this->slot_duration_minutes,
                    'is_peak' => $this->isPeakHour($current->format('H:i')),
                ];
            }
            
            $current = $slotEnd;
        }

        return $slots;
    }

    /**
     * Scope: Active templates only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Templates for specific listing
     */
    public function scopeForListing($query, $listingId)
    {
        return $query->where('listing_id', $listingId);
    }

    /**
     * Scope: Templates by priority (highest first)
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Scope: Templates valid for date
     */
    public function scopeValidForDate($query, Carbon $date)
    {
        return $query->where(function ($q) use ($date) {
            $q->whereNull('valid_from')->orWhere('valid_from', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('valid_until')->orWhere('valid_until', '>=', $date);
        });
    }

    /**
     * Scope: Auto-apply templates
     */
    public function scopeAutoApply($query)
    {
        return $query->where('auto_apply', true);
    }

    /**
     * Auto-apply template to future dates
     */
    public function autoApplyToFutureDates(): int
    {
        if (!$this->auto_apply) {
            return 0;
        }

        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays($this->auto_apply_days_ahead);

        return $this->applyToDateRange($startDate, $endDate);
    }
}