<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ListingUnit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'listing_id',
        'unit_identifier',
        'unit_name',
        'unit_description',
        'unit_features',
        'unit_specifications',
        'price_modifier',
        'base_hourly_price',
        'base_daily_price',
        'status',
        'unit_rules',
        'max_occupancy',
        'min_booking_hours',
        'max_booking_hours',
        'size_sqm',
        'floor_level',
        'location_details',
        'primary_image',
        'image_gallery',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'unit_features' => 'array',
        'unit_specifications' => 'array',
        'unit_rules' => 'array',
        'image_gallery' => 'array',
        'metadata' => 'array',
        'price_modifier' => 'decimal:2',
        'base_hourly_price' => 'decimal:2',
        'base_daily_price' => 'decimal:2',
        'size_sqm' => 'decimal:2',
        'max_occupancy' => 'integer',
        'min_booking_hours' => 'integer',
        'max_booking_hours' => 'integer',
    ];

    /**
     * Status constants
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_CLEANING = 'cleaning';
    const STATUS_RETIRED = 'retired';
    const STATUS_RESERVED = 'reserved';

    /**
     * Unit belongs to a listing
     */
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * Unit has many availability slots
     */
    public function availability(): HasMany
    {
        return $this->hasMany(ListingAvailability::class, 'unit_identifier', 'unit_identifier')
            ->where('listing_id', $this->listing_id);
    }

    /**
     * Unit has media (photos, videos)
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Unit created by user
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Unit updated by user
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get effective hourly price (unit price or listing price with modifier)
     */
    public function getEffectiveHourlyPriceAttribute(): ?float
    {
        if ($this->base_hourly_price) {
            return $this->base_hourly_price;
        }

        if ($this->listing->base_hourly_price) {
            return $this->listing->base_hourly_price * $this->price_modifier;
        }

        return null;
    }

    /**
     * Get effective daily price (unit price or listing price with modifier)
     */
    public function getEffectiveDailyPriceAttribute(): ?float
    {
        if ($this->base_daily_price) {
            return $this->base_daily_price;
        }

        if ($this->listing->base_daily_price) {
            return $this->listing->base_daily_price * $this->price_modifier;
        }

        return null;
    }

    /**
     * Check if unit is available for booking
     */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if unit is under maintenance
     */
    public function isUnderMaintenance(): bool
    {
        return in_array($this->status, [self::STATUS_MAINTENANCE, self::STATUS_CLEANING]);
    }

    /**
     * Get unit display name (identifier + name)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->unit_identifier . ' - ' . $this->unit_name;
    }

    /**
     * Get primary image URL
     */
    public function getPrimaryImageUrlAttribute(): ?string
    {
        if ($this->primary_image) {
            return $this->primary_image;
        }

        $primaryMedia = $this->media()->where('category', 'unit')->where('is_primary', true)->first();
        return $primaryMedia?->s3_url;
    }

    /**
     * Get all image URLs
     */
    public function getAllImagesAttribute(): array
    {
        $images = [];

        // Add primary image
        if ($this->primary_image_url) {
            $images[] = $this->primary_image_url;
        }

        // Add gallery images
        if ($this->image_gallery) {
            $images = array_merge($images, $this->image_gallery);
        }

        // Add media images
        $mediaImages = $this->media()
            ->where('category', 'unit')
            ->where('media_type', 'image')
            ->pluck('s3_url')
            ->toArray();

        $images = array_merge($images, $mediaImages);

        return array_unique($images);
    }

    /**
     * Scope: Active units only
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope: Available for booking (active and not under maintenance)
     */
    public function scopeBookable($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope: Units for specific listing
     */
    public function scopeForListing($query, $listingId)
    {
        return $query->where('listing_id', $listingId);
    }

    /**
     * Scope: Units with specific features
     */
    public function scopeWithFeatures($query, array $features)
    {
        return $query->where(function ($q) use ($features) {
            foreach ($features as $feature) {
                $q->orWhereJsonContains('unit_features', $feature);
            }
        });
    }

    /**
     * Scope: Units within price range
     */
    public function scopePriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice) {
            $query->where(function ($q) use ($minPrice) {
                $q->where('base_hourly_price', '>=', $minPrice)
                  ->orWhereHas('listing', function ($listingQuery) use ($minPrice) {
                      $listingQuery->whereRaw('base_hourly_price * ? >= ?', [$this->price_modifier, $minPrice]);
                  });
            });
        }

        if ($maxPrice) {
            $query->where(function ($q) use ($maxPrice) {
                $q->where('base_hourly_price', '<=', $maxPrice)
                  ->orWhereHas('listing', function ($listingQuery) use ($maxPrice) {
                      $listingQuery->whereRaw('base_hourly_price * ? <= ?', [$this->price_modifier, $maxPrice]);
                  });
            });
        }

        return $query;
    }

    /**
     * Scope: Units by capacity
     */
    public function scopeByCapacity($query, $minCapacity = null, $maxCapacity = null)
    {
        if ($minCapacity) {
            $query->where('max_occupancy', '>=', $minCapacity);
        }

        if ($maxCapacity) {
            $query->where('max_occupancy', '<=', $maxCapacity);
        }

        return $query;
    }

    /**
     * Create availability slots for this unit
     */
    public function createAvailability(array $data): ListingAvailability
    {
        return ListingAvailability::create(array_merge($data, [
            'listing_id' => $this->listing_id,
            'unit_identifier' => $this->unit_identifier,
        ]));
    }

    /**
     * Get availability for date range
     */
    public function getAvailabilityForDateRange($startDate, $endDate)
    {
        return $this->availability()
            ->whereBetween('available_date', [$startDate, $endDate])
            ->orderBy('available_date')
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Check if unit is available for specific date and time
     */
    public function isAvailableAt($date, $startTime, $endTime): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        // Check if there's an available slot that covers the requested time
        $availableSlot = $this->availability()
            ->where('available_date', $date)
            ->where('status', 'available')
            ->where('start_time', '<=', $startTime)
            ->where('end_time', '>=', $endTime)
            ->exists();

        return $availableSlot;
    }
}