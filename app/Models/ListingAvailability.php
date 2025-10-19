<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

/**
 * ListingAvailability Model
 * 
 * Represents availability windows for a listing.
 * Supports recurring (e.g., every Monday 8AM-5PM) and specific date availability.
 * 
 * @property int $id
 * @property int $listing_id
 * @property string $availability_type
 * @property int|null $day_of_week
 * @property string|null $start_time
 * @property string|null $end_time
 * @property string|null $available_date
 * @property string|null $start_date
 * @property string|null $end_date
 * @property float|null $price_override
 * @property bool $is_available
 * @property string|null $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class ListingAvailability extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'listing_availability';

    /**
     * Availability Types
     */
    public const TYPE_RECURRING = 'recurring';
    public const TYPE_SPECIFIC_DATE = 'specific_date';
    public const TYPE_DATE_RANGE = 'date_range';
    public const TYPE_BLOCKED = 'blocked';

    /**
     * Days of Week
     */
    public const SUNDAY = 0;
    public const MONDAY = 1;
    public const TUESDAY = 2;
    public const WEDNESDAY = 3;
    public const THURSDAY = 4;
    public const FRIDAY = 5;
    public const SATURDAY = 6;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'listing_id',
        'availability_type',
        'day_of_week',
        'start_time',
        'end_time',
        'available_date',
        'start_date',
        'end_date',
        'price_override',
        'is_available',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'day_of_week' => 'integer',
        'price_override' => 'decimal:2',
        'is_available' => 'boolean',
        'available_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the listing that owns the availability.
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to get only available slots.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope to get blocked slots.
     */
    public function scopeBlocked($query)
    {
        return $query->where('availability_type', self::TYPE_BLOCKED);
    }

    /**
     * Scope to get recurring availability.
     */
    public function scopeRecurring($query)
    {
        return $query->where('availability_type', self::TYPE_RECURRING);
    }

    /**
     * Scope to get availability for a specific day of week.
     */
    public function scopeForDayOfWeek($query, int $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    /**
     * Scope to get availability for a specific date.
     */
    public function scopeForDate($query, Carbon $date)
    {
        return $query->where(function ($q) use ($date) {
            // Specific date match
            $q->where(function ($q2) use ($date) {
                $q2->where('availability_type', self::TYPE_SPECIFIC_DATE)
                    ->whereDate('available_date', $date->format('Y-m-d'));
            })
            // Date range match
            ->orWhere(function ($q2) use ($date) {
                $q2->where('availability_type', self::TYPE_DATE_RANGE)
                    ->whereDate('start_date', '<=', $date->format('Y-m-d'))
                    ->whereDate('end_date', '>=', $date->format('Y-m-d'));
            })
            // Recurring match (by day of week)
            ->orWhere(function ($q2) use ($date) {
                $q2->where('availability_type', self::TYPE_RECURRING)
                    ->where('day_of_week', $date->dayOfWeek);
            });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get the day name from day_of_week.
     */
    public function getDayName(): ?string
    {
        if ($this->day_of_week === null) {
            return null;
        }

        $days = [
            self::SUNDAY => 'Sunday',
            self::MONDAY => 'Monday',
            self::TUESDAY => 'Tuesday',
            self::WEDNESDAY => 'Wednesday',
            self::THURSDAY => 'Thursday',
            self::FRIDAY => 'Friday',
            self::SATURDAY => 'Saturday',
        ];

        return $days[$this->day_of_week] ?? null;
    }

    /**
     * Get the time range as a formatted string.
     */
    public function getTimeRange(): ?string
    {
        if (!$this->start_time || !$this->end_time) {
            return null;
        }

        $start = Carbon::parse($this->start_time)->format('g:i A');
        $end = Carbon::parse($this->end_time)->format('g:i A');

        return "{$start} - {$end}";
    }

    /**
     * Get the date range as a formatted string.
     */
    public function getDateRange(): ?string
    {
        if ($this->availability_type === self::TYPE_SPECIFIC_DATE && $this->available_date) {
            return $this->available_date->format('M d, Y');
        }

        if ($this->availability_type === self::TYPE_DATE_RANGE && $this->start_date && $this->end_date) {
            return $this->start_date->format('M d, Y') . ' - ' . $this->end_date->format('M d, Y');
        }

        return null;
    }

    /**
     * Get a human-readable description of the availability.
     */
    public function getDescription(): string
    {
        $description = '';

        if ($this->availability_type === self::TYPE_RECURRING) {
            $description = 'Every ' . $this->getDayName();
            if ($timeRange = $this->getTimeRange()) {
                $description .= ' ' . $timeRange;
            }
        } elseif ($this->availability_type === self::TYPE_SPECIFIC_DATE) {
            $description = $this->getDateRange();
            if ($timeRange = $this->getTimeRange()) {
                $description .= ' ' . $timeRange;
            }
        } elseif ($this->availability_type === self::TYPE_DATE_RANGE) {
            $description = $this->getDateRange();
        } elseif ($this->availability_type === self::TYPE_BLOCKED) {
            $description = 'Blocked: ' . ($this->getDateRange() ?? 'Unavailable');
        }

        if (!$this->is_available) {
            $description = 'Unavailable - ' . $description;
        }

        if ($this->notes) {
            $description .= ' (' . $this->notes . ')';
        }

        return $description;
    }

    /**
     * Check if this availability is for a recurring schedule.
     */
    public function isRecurring(): bool
    {
        return $this->availability_type === self::TYPE_RECURRING;
    }

    /**
     * Check if this availability is blocked.
     */
    public function isBlocked(): bool
    {
        return $this->availability_type === self::TYPE_BLOCKED || !$this->is_available;
    }

    /**
     * Check if the availability applies to a given date.
     */
    public function appliesToDate(Carbon $date): bool
    {
        if ($this->availability_type === self::TYPE_SPECIFIC_DATE) {
            return $this->available_date && $this->available_date->isSameDay($date);
        }

        if ($this->availability_type === self::TYPE_DATE_RANGE) {
            return $this->start_date && $this->end_date 
                && $date->between($this->start_date, $this->end_date);
        }

        if ($this->availability_type === self::TYPE_RECURRING) {
            return $this->day_of_week === $date->dayOfWeek;
        }

        return false;
    }

    /**
     * Get the price (override if set, otherwise from listing).
     */
    public function getPrice(string $priceType = 'price_per_hour'): ?float
    {
        if ($this->price_override) {
            return $this->price_override;
        }

        return $this->listing ? $this->listing->$priceType : null;
    }

    /**
     * Check if this slot overlaps with another availability slot.
     */
    public function overlapsWith(ListingAvailability $other): bool
    {
        // For recurring availability, check if they're on the same day
        if ($this->isRecurring() && $other->isRecurring()) {
            if ($this->day_of_week !== $other->day_of_week) {
                return false;
            }
            
            // Check time overlap
            return $this->start_time < $other->end_time && $this->end_time > $other->start_time;
        }

        // For date-based availability, check date overlap
        if ($this->available_date && $other->available_date) {
            if (!$this->available_date->isSameDay($other->available_date)) {
                return false;
            }
            
            // Check time overlap if times are set
            if ($this->start_time && $this->end_time && $other->start_time && $other->end_time) {
                return $this->start_time < $other->end_time && $this->end_time > $other->start_time;
            }
            
            return true;
        }

        // For date ranges
        if ($this->start_date && $this->end_date && $other->start_date && $other->end_date) {
            return $this->start_date <= $other->end_date && $this->end_date >= $other->start_date;
        }

        return false;
    }
}
