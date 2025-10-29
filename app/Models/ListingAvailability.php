<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Universal ListingAvailability Model
 * 
 * Represents availability slots for any type of rental property:
 * - Basketball courts (hourly slots)
 * - Hotel rooms (daily slots with check-in/out)
 * - Car rentals (daily/weekly slots)
 * - Event venues (event-based slots)
 * - Vacation rentals (nightly stays)
 */
class ListingAvailability extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'listing_availability';

    /**
     * Status constants
     */
    const STATUS_AVAILABLE = 'available';
    const STATUS_BLOCKED = 'blocked';
    const STATUS_BOOKED = 'booked';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_CLEANING = 'cleaning';
    const STATUS_RESERVED = 'reserved';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Duration type constants
     */
    const DURATION_HOURLY = 'hourly';
    const DURATION_DAILY = 'daily';
    const DURATION_WEEKLY = 'weekly';
    const DURATION_MONTHLY = 'monthly';

    /**
     * Recurrence type constants
     */
    const RECURRENCE_NONE = 'none';
    const RECURRENCE_DAILY = 'daily';
    const RECURRENCE_WEEKLY = 'weekly';
    const RECURRENCE_MONTHLY = 'monthly';
    const RECURRENCE_YEARLY = 'yearly';

    protected $fillable = [
        'listing_id',
        'unit_identifier',
        'available_units',
        'available_date',
        'start_time',
        'end_time',
        'peak_hour_price',
        'weekend_price',
        'holiday_price',
        'min_duration_hours',
        'max_duration_hours',
        'duration_type',
        'slot_duration_minutes',
        'recurrence_type',
        'recurrence_pattern',
        'recurrence_end_date',
        'category_rules',
        'booking_rules',
        'notes',
        'metadata',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'available_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'recurrence_end_date' => 'date',
        'recurrence_pattern' => 'array',
        'category_rules' => 'array',
        'booking_rules' => 'array',
        'metadata' => 'array',
        'peak_hour_price' => 'decimal:2',
        'weekend_price' => 'decimal:2',
        'holiday_price' => 'decimal:2',
        'available_units' => 'integer',
        'min_duration_hours' => 'integer',
        'max_duration_hours' => 'integer',
        'slot_duration_minutes' => 'integer',
    ];

    /**
     * Relationships
     */
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(ListingUnit::class, 'unit_identifier', 'unit_identifier')
            ->where('listing_id', $this->listing_id);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scopes
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeBooked($query)
    {
        return $query->where('status', self::STATUS_BOOKED);
    }

    public function scopeBlocked($query)
    {
        return $query->whereIn('status', [self::STATUS_BLOCKED, self::STATUS_MAINTENANCE, self::STATUS_CLEANING]);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('available_date', $date);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('available_date', [$startDate, $endDate]);
    }

    public function scopeForUnit($query, $unitIdentifier)
    {
        return $query->where('unit_identifier', $unitIdentifier);
    }

    public function scopeForTimeRange($query, $startTime, $endTime)
    {
        return $query->where('start_time', '>=', $startTime)
                    ->where('end_time', '<=', $endTime);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('available_date')
                    ->orderBy('start_time')
                    ->orderBy('unit_identifier');
    }

    /**
     * Helper Methods
     */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function isBooked(): bool
    {
        return $this->status === self::STATUS_BOOKED;
    }

    public function isBlocked(): bool
    {
        return in_array($this->status, [self::STATUS_BLOCKED, self::STATUS_MAINTENANCE, self::STATUS_CLEANING]);
    }

    public function getTimeRangeAttribute(): string
    {
        if (!$this->start_time || !$this->end_time) {
            return 'All day';
        }

        $start = Carbon::parse($this->start_time)->format('g:i A');
        $end = Carbon::parse($this->end_time)->format('g:i A');

        return "{$start} - {$end}";
    }

    public function getEffectivePriceAttribute(): ?float
    {
        $date = Carbon::parse($this->available_date);
        $time = Carbon::parse($this->start_time);

        // Check for holiday pricing
        if ($this->holiday_price && $this->isHoliday($date)) {
            return $this->holiday_price;
        }

        // Check for peak hour pricing (takes precedence over weekend)
        if ($this->peak_hour_price && $this->isPeakHour($time)) {
            return $this->peak_hour_price;
        }

        // Check for weekend pricing
        if ($this->weekend_price && $date->isWeekend()) {
            return $this->weekend_price;
        }

        // Fall back to listing base price
        if ($this->duration_type === self::DURATION_DAILY) {
            return $this->listing->base_daily_price;
        }

        return $this->listing->base_hourly_price;
    }

    public function getDurationInMinutesAttribute(): int
    {
        if (!$this->start_time || !$this->end_time) {
            return 0;
        }

        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);

        return $end->diffInMinutes($start);
    }

    public function generateTimeSlots(): array
    {
        if (!$this->start_time || !$this->end_time) {
            return [];
        }

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
                    'price' => $this->effective_price,
                    'available' => $this->isAvailable(),
                    'unit' => $this->unit_identifier,
                ];
            }
            
            $current = $slotEnd;
        }

        return $slots;
    }

    public function checkAvailabilityConflict($startTime, $endTime, $date = null): bool
    {
        $date = $date ?: $this->available_date;

        // Check if this slot conflicts with the requested time
        $requestStart = Carbon::parse($startTime);
        $requestEnd = Carbon::parse($endTime);
        $slotStart = Carbon::parse($this->start_time);
        $slotEnd = Carbon::parse($this->end_time);

        // Check for time overlap
        return $requestStart->lt($slotEnd) && $requestEnd->gt($slotStart);
    }

    public function markAsBooked(): bool
    {
        $this->status = self::STATUS_BOOKED;
        return $this->save();
    }

    public function markAsAvailable(): bool
    {
        $this->status = self::STATUS_AVAILABLE;
        return $this->save();
    }

    public function block($reason = null): bool
    {
        $this->status = self::STATUS_BLOCKED;
        if ($reason) {
            $this->notes = $reason;
        }
        return $this->save();
    }

    protected function isPeakHour(Carbon $time): bool
    {
        // Basic peak hour detection (6 PM - 10 PM)
        $hour = $time->hour;
        return $hour >= 18 && $hour < 22;
    }

    protected function isHoliday(Carbon $date): bool
    {
        // Basic holiday detection
        $holidays = [
            '01-01', // New Year
            '12-25', // Christmas
            '12-31', // New Year's Eve
        ];

        return in_array($date->format('m-d'), $holidays);
    }

    /**
     * Category-specific formatting
     */
    public function formatForCategory(): array
    {
        $categoryName = strtolower($this->listing->category->name ?? '');
        $subCategoryName = strtolower($this->listing->subCategory->name ?? '');

        // Check category and subcategory names for sports-related keywords
        if (str_contains($categoryName, 'sports') || str_contains($subCategoryName, 'basketball') || str_contains($subCategoryName, 'court')) {
            return $this->formatForSports();
        }

        // Check for hotel/accommodation keywords
        if (str_contains($categoryName, 'hotel') || str_contains($subCategoryName, 'room') || str_contains($categoryName, 'accommodation')) {
            return $this->formatForHotel();
        }

        // Check for transportation keywords
        if (str_contains($categoryName, 'transport') || str_contains($subCategoryName, 'car') || str_contains($subCategoryName, 'rental')) {
            return $this->formatForTransport();
        }

        // Check for event keywords
        if (str_contains($categoryName, 'event') || str_contains($subCategoryName, 'venue') || str_contains($categoryName, 'venue')) {
            return $this->formatForEvents();
        }

        return $this->formatGeneric();
    }

    protected function formatForSports(): array
    {
        return [
            'type' => 'sports',
            'date' => $this->available_date->format('Y-m-d'),
            'time_slots' => $this->generateTimeSlots(),
            'court' => $this->unit_identifier,
            'hourly_rate' => $this->effective_price,
            'available' => $this->isAvailable(),
        ];
    }

    protected function formatForHotel(): array
    {
        return [
            'type' => 'hotel',
            'date' => $this->available_date->format('Y-m-d'),
            'room' => $this->unit_identifier,
            'check_in' => $this->category_rules['check_in_time'] ?? '15:00',
            'check_out' => $this->category_rules['check_out_time'] ?? '11:00',
            'nightly_rate' => $this->effective_price,
            'available' => $this->isAvailable(),
        ];
    }

    protected function formatForTransport(): array
    {
        return [
            'type' => 'transport',
            'date' => $this->available_date->format('Y-m-d'),
            'vehicle' => $this->unit_identifier,
            'daily_rate' => $this->effective_price,
            'min_rental_days' => $this->min_duration_hours / 24,
            'available' => $this->isAvailable(),
        ];
    }

    protected function formatForEvents(): array
    {
        return [
            'type' => 'event',
            'date' => $this->available_date->format('Y-m-d'),
            'venue' => $this->unit_identifier,
            'time_range' => $this->time_range,
            'event_rate' => $this->effective_price,
            'setup_time' => $this->category_rules['setup_hours'] ?? 2,
            'available' => $this->isAvailable(),
        ];
    }

    protected function formatGeneric(): array
    {
        return [
            'type' => 'general',
            'date' => $this->available_date->format('Y-m-d'),
            'time_range' => $this->time_range,
            'unit' => $this->unit_identifier,
            'price' => $this->effective_price,
            'duration_type' => $this->duration_type,
            'available' => $this->isAvailable(),
        ];
    }
}
