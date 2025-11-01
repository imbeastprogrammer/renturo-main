<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * DynamicFormSubmission Model
 * 
 * Represents a unit/item within a listing.
 * For multi-unit properties, each submission is one bookable unit.
 * For single-unit properties, there's one submission per listing.
 * 
 * All unit-specific details are stored in the JSON 'data' field.
 */
class DynamicFormSubmission extends Model
{
    use HasFactory, SoftDeletes; 

    protected $fillable = [
        'listing_id',
        'dynamic_form_id',
        'user_id',
        'store_id',
        'status',
        'data', // All unit details stored here as JSON
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the parent listing (facility)
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * Get the dynamic form definition
     */
    public function dynamicForm()
    {
        return $this->belongsTo(DynamicForm::class, 'dynamic_form_id');
    }

    /**
     * Get the store/business
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the user who created this submission
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get availability slots for this unit
     */
    public function availability()
    {
        return $this->hasMany(ListingAvailability::class, 'listing_id', 'listing_id')
            ->where('unit_identifier', $this->id);
    }

    /**
     * Get bookings for this unit
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'dynamic_form_submission_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Unit Data Accessors (from JSON)
    |--------------------------------------------------------------------------
    */

    /**
     * Get unit name from JSON data
     */
    public function getUnitName(): ?string
    {
        return $this->data['unit_name'] ?? $this->data['name'] ?? null;
    }

    /**
     * Get unit description from JSON data
     */
    public function getUnitDescription(): ?string
    {
        return $this->data['unit_description'] ?? $this->data['description'] ?? $this->data['about'] ?? null;
    }

    /**
     * Get unit capacity from JSON data
     */
    public function getMaxCapacity(): ?int
    {
        return $this->data['max_capacity'] ?? $this->data['max_players'] ?? $this->data['max_guests'] ?? null;
    }

    /**
     * Get amenities from JSON data
     */
    public function getAmenities(): array
    {
        return $this->data['amenities'] ?? [];
    }

    /*
    |--------------------------------------------------------------------------
    | Pricing Helpers (from JSON)
    |--------------------------------------------------------------------------
    */

    /**
     * Get hourly price from JSON data
     */
    public function getHourlyPrice(): ?float
    {
        return $this->data['price_per_hour'] ?? $this->data['hourly_price'] ?? $this->data['base_hourly_price'] ?? null;
    }

    /**
     * Get daily price from JSON data
     */
    public function getDailyPrice(): ?float
    {
        return $this->data['price_per_day'] ?? $this->data['daily_price'] ?? $this->data['base_daily_price'] ?? null;
    }

    /**
     * Get weekly price from JSON data
     */
    public function getWeeklyPrice(): ?float
    {
        return $this->data['price_per_week'] ?? $this->data['weekly_price'] ?? $this->data['base_weekly_price'] ?? null;
    }

    /**
     * Get monthly price from JSON data
     */
    public function getMonthlyPrice(): ?float
    {
        return $this->data['price_per_month'] ?? $this->data['monthly_price'] ?? $this->data['base_monthly_price'] ?? null;
    }

    /**
     * Get all available prices for this unit
     */
    public function getAllPrices(): array
    {
        return array_filter([
            'hourly' => $this->getHourlyPrice(),
            'daily' => $this->getDailyPrice(),
            'weekly' => $this->getWeeklyPrice(),
            'monthly' => $this->getMonthlyPrice(),
        ]);
    }

    /**
     * Get the lowest price for this unit
     */
    public function getLowestPrice(): ?float
    {
        $prices = $this->getAllPrices();
        return !empty($prices) ? min($prices) : null;
    }

    /**
     * Get security deposit from JSON data
     */
    public function getSecurityDeposit(): ?float
    {
        return $this->data['security_deposit'] ?? null;
    }

    /**
     * Get cleaning fee from JSON data
     */
    public function getCleaningFee(): ?float
    {
        return $this->data['cleaning_fee'] ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | Booking Rules (from JSON)
    |--------------------------------------------------------------------------
    */

    /**
     * Get minimum booking hours from JSON data
     */
    public function getMinBookingHours(): ?int
    {
        return $this->data['min_booking_hours'] ?? $this->data['minimum_booking_hours'] ?? null;
    }

    /**
     * Get maximum booking hours from JSON data
     */
    public function getMaxBookingHours(): ?int
    {
        return $this->data['max_booking_hours'] ?? $this->data['maximum_booking_hours'] ?? null;
    }

    /**
     * Get minimum booking days from JSON data
     */
    public function getMinBookingDays(): ?int
    {
        return $this->data['min_booking_days'] ?? $this->data['minimum_booking_days'] ?? null;
    }

    /**
     * Get maximum booking days from JSON data
     */
    public function getMaxBookingDays(): ?int
    {
        return $this->data['max_booking_days'] ?? $this->data['maximum_booking_days'] ?? null;
    }

    /**
     * Check if instant booking is enabled
     */
    public function hasInstantBooking(): bool
    {
        return (bool) ($this->data['instant_booking'] ?? false);
    }

    /**
     * Get cancellation policy from JSON data
     */
    public function getCancellationPolicy(): ?string
    {
        return $this->data['cancellation_policy'] ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | Status & Availability
    |--------------------------------------------------------------------------
    */

    /**
     * Check if unit is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if unit is available for booking on a specific date
     */
    public function isAvailableOn(string $date): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        return $this->availability()
            ->where('available_date', $date)
            ->where('status', 'available')
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to get only active units
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get units by listing
     */
    public function scopeByListing($query, int $listingId)
    {
        return $query->where('listing_id', $listingId);
    }

    /**
     * Scope to get units by store
     */
    public function scopeByStore($query, int $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    /*
    |--------------------------------------------------------------------------
    | Display Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get a display name for the unit
     */
    public function getDisplayName(): string
    {
        return $this->getUnitName() ?? 'Unit #' . $this->id;
    }

    /**
     * Get formatted unit information for API responses
     */
    public function toApiFormat(): array
    {
        return [
            'id' => $this->id,
            'listing_id' => $this->listing_id,
            'unit_name' => $this->getUnitName(),
            'description' => $this->getUnitDescription(),
            'max_capacity' => $this->getMaxCapacity(),
            'pricing' => [
                'hourly' => $this->getHourlyPrice(),
                'daily' => $this->getDailyPrice(),
                'weekly' => $this->getWeeklyPrice(),
                'monthly' => $this->getMonthlyPrice(),
                'security_deposit' => $this->getSecurityDeposit(),
                'cleaning_fee' => $this->getCleaningFee(),
            ],
            'booking_rules' => [
                'min_booking_hours' => $this->getMinBookingHours(),
                'max_booking_hours' => $this->getMaxBookingHours(),
                'min_booking_days' => $this->getMinBookingDays(),
                'max_booking_days' => $this->getMaxBookingDays(),
                'instant_booking' => $this->hasInstantBooking(),
                'cancellation_policy' => $this->getCancellationPolicy(),
            ],
            'amenities' => $this->getAmenities(),
            'status' => $this->status,
            'additional_data' => $this->data, // Include all custom fields
        ];
    }
}
