<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Listing Model
 * 
 * Represents a rentable property/venue listing in the Renturo platform.
 * Supports multiple listing types: sports venues, accommodations, transportation, etc.
 * 
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property int|null $sub_category_id
 * @property string $listing_type
 * @property int|null $dynamic_form_id
 * @property int|null $dynamic_form_submission_id
 * @property string $title
 * @property string $description
 * @property string $slug
 * @property string $address
 * @property string $city
 * @property string $province
 * @property string|null $postal_code
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $price_per_hour
 * @property float|null $price_per_day
 * @property float|null $price_per_week
 * @property float|null $price_per_month
 * @property string $currency
 * @property int|null $max_capacity
 * @property array|null $amenities
 * @property string $status
 * @property string $visibility
 * @property bool $is_featured
 * @property bool $is_verified
 * @property bool $instant_booking
 * @property int $minimum_booking_hours
 * @property int|null $maximum_booking_hours
 * @property int $advance_booking_days
 * @property int $cancellation_hours
 * @property int $views_count
 * @property int $bookings_count
 * @property float $average_rating
 * @property int $reviews_count
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property array|null $meta_keywords
 * @property \Carbon\Carbon|null $published_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class Listing extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'listings';

    /**
     * Listing Types
     */
    public const TYPE_SPORTS = 'sports';
    public const TYPE_ACCOMMODATION = 'accommodation';
    public const TYPE_TRANSPORT = 'transport';
    public const TYPE_EVENT_SPACE = 'event_space';
    public const TYPE_EQUIPMENT = 'equipment';
    public const TYPE_OTHER = 'other';

    /**
     * Listing Status
     */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_ARCHIVED = 'archived';

    /**
     * Visibility Options
     */
    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_PRIVATE = 'private';
    public const VISIBILITY_UNLISTED = 'unlisted';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'sub_category_id',
        'listing_type',
        'dynamic_form_id',
        'dynamic_form_submission_id',
        'title',
        'description',
        'slug',
        'address',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
        'price_per_hour',
        'price_per_day',
        'price_per_week',
        'price_per_month',
        'currency',
        'max_capacity',
        'amenities',
        'status',
        'visibility',
        'is_featured',
        'is_verified',
        'instant_booking',
        'minimum_booking_hours',
        'maximum_booking_hours',
        'advance_booking_days',
        'cancellation_hours',
        'views_count',
        'bookings_count',
        'average_rating',
        'reviews_count',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amenities' => 'array',
        'meta_keywords' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'price_per_hour' => 'decimal:2',
        'price_per_day' => 'decimal:2',
        'price_per_week' => 'decimal:2',
        'price_per_month' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'instant_booking' => 'boolean',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array<string>
     */
    protected $with = ['photos'];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from title
        static::creating(function ($listing) {
            if (empty($listing->slug)) {
                $listing->slug = Str::slug($listing->title);
                
                // Ensure slug uniqueness
                $originalSlug = $listing->slug;
                $count = 1;
                while (static::where('slug', $listing->slug)->exists()) {
                    $listing->slug = $originalSlug . '-' . $count++;
                }
            }
        });

        // Update published_at when status changes to active
        static::updating(function ($listing) {
            if ($listing->isDirty('status') && $listing->status === self::STATUS_ACTIVE && !$listing->published_at) {
                $listing->published_at = now();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the owner/creator of the listing.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for user() - owner of the listing.
     */
    public function owner()
    {
        return $this->user();
    }

    /**
     * Get the category of the listing.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the sub-category of the listing.
     */
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    /**
     * Get the dynamic form associated with the listing.
     */
    public function dynamicForm()
    {
        return $this->belongsTo(DynamicForm::class);
    }

    /**
     * Get the dynamic form submission with sport-specific details.
     */
    public function dynamicFormSubmission()
    {
        return $this->belongsTo(DynamicFormSubmission::class);
    }

    /**
     * Get all photos for the listing.
     */
    public function photos()
    {
        return $this->hasMany(ListingPhoto::class)->orderBy('sort_order');
    }

    /**
     * Get the primary photo for the listing.
     */
    public function primaryPhoto()
    {
        return $this->hasOne(ListingPhoto::class)->where('is_primary', true);
    }

    /**
     * Get availability slots for the listing.
     */
    public function availability()
    {
        return $this->hasMany(ListingAvailability::class);
    }

    /**
     * Get all bookings for this listing
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Listing has many units (for multi-unit properties)
     */
    public function units()
    {
        return $this->hasMany(ListingUnit::class);
    }

    /**
     * Listing has many availability templates
     */
    public function availabilityTemplates()
    {
        return $this->hasMany(AvailabilityTemplate::class);
    }

    /**
     * Get active units only
     */
    public function activeUnits()
    {
        return $this->units()->where('status', 'active');
    }

    /**
     * Get active availability templates
     */
    public function activeTemplates()
    {
        return $this->availabilityTemplates()->where('is_active', true);
    }

    /**
     * Get recurring availability (e.g., every Monday 8AM-5PM).
     */
    public function recurringAvailability()
    {
        return $this->hasMany(ListingAvailability::class)
            ->where('availability_type', 'recurring')
            ->where('is_available', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time');
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to get only active listings.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope to get only published listings.
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope to get only public listings.
     */
    public function scopePublic($query)
    {
        return $query->where('visibility', self::VISIBILITY_PUBLIC);
    }

    /**
     * Scope to get featured listings.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get verified listings.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to filter by listing type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('listing_type', $type);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to filter by sub-category.
     */
    public function scopeBySubCategory($query, $subCategoryId)
    {
        return $query->where('sub_category_id', $subCategoryId);
    }

    /**
     * Scope to filter by owner/user.
     */
    public function scopeByOwner($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to search listings by keyword (title, description, address, city).
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%")
                ->orWhere('address', 'like', "%{$keyword}%")
                ->orWhere('city', 'like', "%{$keyword}%")
                ->orWhere('province', 'like', "%{$keyword}%");
        });
    }

    /**
     * Scope to filter by location (city or province).
     */
    public function scopeByLocation($query, $city = null, $province = null)
    {
        if ($city) {
            $query->where('city', 'like', "%{$city}%");
        }
        if ($province) {
            $query->where('province', 'like', "%{$province}%");
        }
        return $query;
    }

    /**
     * Scope to filter by price range.
     */
    public function scopeByPriceRange($query, $minPrice = null, $maxPrice = null, $priceType = 'price_per_hour')
    {
        if ($minPrice) {
            $query->where($priceType, '>=', $minPrice);
        }
        if ($maxPrice) {
            $query->where($priceType, '<=', $maxPrice);
        }
        return $query;
    }

    /**
     * Scope to find listings within a radius (in kilometers).
     * Uses the Haversine formula for distance calculation.
     */
    public function scopeWithinRadius($query, $latitude, $longitude, $radiusKm)
    {
        return $query->selectRaw("
                *,
                (6371 * acos(cos(radians(?)) 
                * cos(radians(latitude)) 
                * cos(radians(longitude) - radians(?)) 
                + sin(radians(?)) 
                * sin(radians(latitude)))) AS distance
            ", [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance');
    }

    /**
     * Scope to filter listings available for specific date range
     * ONLY shows listings if ALL requested dates/times are available
     */
    public function scopeAvailableForDateRange($query, $checkInDate, $checkOutDate, $checkInTime = null, $checkOutTime = null)
    {
        return $query->where(function ($q) use ($checkInDate, $checkOutDate, $checkInTime, $checkOutTime) {
            // First, check if listing has NO conflicting bookings
            $q->whereDoesntHave('bookings', function ($bookingQuery) use ($checkInDate, $checkOutDate, $checkInTime, $checkOutTime) {
                $bookingQuery->whereIn('status', ['pending', 'confirmed', 'paid', 'checked_in', 'in_progress'])
                    ->where(function ($dateQuery) use ($checkInDate, $checkOutDate, $checkInTime, $checkOutTime) {
                        if ($checkInTime && $checkOutTime) {
                            // Hourly booking - check for time overlap
                            $dateQuery->where(function ($overlapQuery) use ($checkInDate, $checkOutDate, $checkInTime, $checkOutTime) {
                                $overlapQuery->whereBetween('check_in_date', [$checkInDate, $checkOutDate])
                                    ->orWhereBetween('check_out_date', [$checkInDate, $checkOutDate])
                                    ->orWhere(function ($encompassQuery) use ($checkInDate, $checkOutDate) {
                                        $encompassQuery->where('check_in_date', '<=', $checkInDate)
                                            ->where('check_out_date', '>=', $checkOutDate);
                                    });
                            });
                        } else {
                            // Daily booking - check for date overlap
                            $dateQuery->where(function ($overlapQuery) use ($checkInDate, $checkOutDate) {
                                $overlapQuery->whereBetween('check_in_date', [$checkInDate, $checkOutDate])
                                    ->orWhereBetween('check_out_date', [$checkInDate, $checkOutDate])
                                    ->orWhere(function ($encompassQuery) use ($checkInDate, $checkOutDate) {
                                        $encompassQuery->where('check_in_date', '<=', $checkInDate)
                                            ->where('check_out_date', '>=', $checkOutDate);
                                    });
                            });
                        }
                    });
            });
            
            // Second, verify availability slots exist for the requested dates
            $q->whereHas('availability', function ($availQuery) use ($checkInDate, $checkOutDate, $checkInTime, $checkOutTime) {
                $availQuery->whereBetween('available_date', [$checkInDate, $checkOutDate])
                    ->where('status', 'available');
                
                // If time-based booking, ensure the time slots match
                if ($checkInTime && $checkOutTime) {
                    $availQuery->where('start_time', '<=', $checkInTime)
                        ->where('end_time', '>=', $checkOutTime);
                }
            });
        });
    }

    /**
     * Scope to filter listings that have any future availability
     */
    public function scopeHasAvailability($query)
    {
        return $query->whereHas('availability', function ($q) {
            $q->where('available_date', '>=', now()->toDateString())
              ->where('status', 'available');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Universal Inventory Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if listing is single unit (basketball court, entire house)
     */
    public function isSingleUnit(): bool
    {
        return $this->inventory_type === 'single';
    }

    /**
     * Check if listing has multiple units (hotel rooms, car fleet)
     */
    public function isMultiUnit(): bool
    {
        return $this->inventory_type === 'multiple';
    }

    /**
     * Check if listing is shared resource (conference room)
     */
    public function isSharedResource(): bool
    {
        return $this->inventory_type === 'shared';
    }

    /**
     * Get availability for date range (universal method)
     */
    public function getAvailabilityForDateRange($startDate, $endDate, $unitIdentifier = null)
    {
        $query = $this->availability()
            ->whereBetween('available_date', [$startDate, $endDate])
            ->orderBy('available_date')
            ->orderBy('start_time');

        if ($unitIdentifier) {
            $query->where('unit_identifier', $unitIdentifier);
        }

        return $query->get()->groupBy('available_date');
    }

    /**
     * Check if listing has availability for specific date and time
     */
    public function hasAvailabilityAt($date, $startTime = null, $endTime = null, $unitIdentifier = null): bool
    {
        $query = $this->availability()
            ->where('available_date', $date)
            ->where('status', 'available');

        if ($unitIdentifier) {
            $query->where('unit_identifier', $unitIdentifier);
        }

        if ($startTime && $endTime) {
            $query->where('start_time', '<=', $startTime)
                  ->where('end_time', '>=', $endTime);
        }

        return $query->exists();
    }

    /**
     * Get available units for a specific date
     */
    public function getAvailableUnits($date)
    {
        if ($this->isSingleUnit()) {
            return $this->hasAvailabilityAt($date) ? [null] : [];
        }

        return $this->availability()
            ->where('available_date', $date)
            ->where('status', 'available')
            ->pluck('unit_identifier')
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Create availability from template
     */
    public function applyTemplate(AvailabilityTemplate $template, $startDate, $endDate)
    {
        return $template->applyToDateRange(
            \Carbon\Carbon::parse($startDate),
            \Carbon\Carbon::parse($endDate)
        );
    }

    /**
     * Get effective pricing for date/time
     */
    public function getEffectivePrice($date, $time = null, $durationType = 'hourly')
    {
        $carbonDate = \Carbon\Carbon::parse($date);
        $carbonTime = $time ? \Carbon\Carbon::parse($time) : null;

        // Check for specific availability pricing first
        $availability = $this->availability()
            ->where('available_date', $date)
            ->when($time, function ($query) use ($time) {
                $query->where('start_time', '<=', $time)
                      ->where('end_time', '>', $time);
            })
            ->first();

        if ($availability) {
            return $availability->effective_price;
        }

        // Fall back to base pricing
        $basePrice = match($durationType) {
            'daily' => $this->base_daily_price,
            'weekly' => $this->base_weekly_price,
            'monthly' => $this->base_monthly_price,
            default => $this->base_hourly_price,
        };

        // Apply modifiers
        if ($carbonDate->isWeekend()) {
            $basePrice *= 1.2; // 20% weekend surcharge
        }

        if ($carbonTime && $carbonTime->hour >= 18 && $carbonTime->hour < 22) {
            $basePrice *= 1.3; // 30% peak hour surcharge
        }

        return $basePrice;
    }

    /**
     * Get occupancy rate for date range
     */
    public function getOccupancyRate($startDate, $endDate): float
    {
        $totalSlots = $this->availability()
            ->whereBetween('available_date', [$startDate, $endDate])
            ->count();

        if ($totalSlots === 0) {
            return 0;
        }

        $bookedSlots = $this->availability()
            ->whereBetween('available_date', [$startDate, $endDate])
            ->where('status', 'booked')
            ->count();

        return ($bookedSlots / $totalSlots) * 100;
    }

    /**
     * Get revenue potential for date range
     */
    public function getRevenuePotential($startDate, $endDate): array
    {
        $availability = $this->availability()
            ->whereBetween('available_date', [$startDate, $endDate])
            ->get();

        $potential = 0;
        $earned = 0;

        foreach ($availability as $slot) {
            $slotRevenue = $slot->effective_price * ($slot->duration_in_minutes / 60);
            $potential += $slotRevenue;

            if ($slot->status === 'booked') {
                $earned += $slotRevenue;
            }
        }

        return [
            'potential' => $potential,
            'earned' => $earned,
            'lost' => $potential - $earned,
            'percentage' => $potential > 0 ? ($earned / $potential) * 100 : 0,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if the listing is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if the listing is published.
     */
    public function isPublished(): bool
    {
        return $this->isActive() && $this->published_at && $this->published_at->isPast();
    }

    /**
     * Check if the listing is publicly visible.
     */
    public function isPublic(): bool
    {
        return $this->visibility === self::VISIBILITY_PUBLIC;
    }

    /**
     * Check if the listing is featured.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Check if the listing is verified.
     */
    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    /**
     * Check if the listing allows instant booking.
     */
    public function allowsInstantBooking(): bool
    {
        return $this->instant_booking;
    }

    /**
     * Get the primary photo URL or a default placeholder.
     */
    public function getPrimaryPhotoUrl(): string
    {
        if ($this->primaryPhoto) {
            return $this->primaryPhoto->photo_url;
        }

        // Return a default placeholder
        return asset('images/placeholder-listing.jpg');
    }

    /**
     * Get all photo URLs as an array.
     */
    public function getPhotoUrls(): array
    {
        return $this->photos->pluck('photo_url')->toArray();
    }

    /**
     * Get the lowest price available.
     */
    public function getLowestPrice(): ?float
    {
        $prices = array_filter([
            $this->price_per_hour,
            $this->price_per_day,
            $this->price_per_week,
            $this->price_per_month,
        ]);

        return !empty($prices) ? min($prices) : null;
    }

    /**
     * Get a formatted price string.
     */
    public function getFormattedPrice(string $priceType = 'price_per_hour'): string
    {
        $price = $this->$priceType;
        
        if (!$price) {
            return 'Contact for pricing';
        }

        $formatted = number_format($price, 2);
        $period = str_replace('price_per_', '', $priceType);

        return "{$this->currency} {$formatted}/{$period}";
    }

    /**
     * Get the full address as a string.
     */
    public function getFullAddress(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->province,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Increment the views count.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Increment the bookings count.
     */
    public function incrementBookings(): void
    {
        $this->increment('bookings_count');
    }

    /**
     * Update the average rating.
     */
    public function updateAverageRating(float $newRating): void
    {
        $totalRatings = $this->reviews_count + 1;
        $totalScore = ($this->average_rating * $this->reviews_count) + $newRating;
        
        $this->update([
            'average_rating' => $totalScore / $totalRatings,
            'reviews_count' => $totalRatings,
        ]);
    }

    /**
     * Check if the listing has a specific amenity.
     */
    public function hasAmenity(string $amenity): bool
    {
        return in_array($amenity, $this->amenities ?? []);
    }

    /**
     * Add an amenity to the listing.
     */
    public function addAmenity(string $amenity): void
    {
        $amenities = $this->amenities ?? [];
        
        if (!in_array($amenity, $amenities)) {
            $amenities[] = $amenity;
            $this->update(['amenities' => $amenities]);
        }
    }

    /**
     * Remove an amenity from the listing.
     */
    public function removeAmenity(string $amenity): void
    {
        $amenities = $this->amenities ?? [];
        
        $amenities = array_filter($amenities, fn($a) => $a !== $amenity);
        
        $this->update(['amenities' => array_values($amenities)]);
    }

    /**
     * Publish the listing.
     */
    public function publish(): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'published_at' => $this->published_at ?? now(),
        ]);
    }

    /**
     * Unpublish/deactivate the listing.
     */
    public function unpublish(): void
    {
        $this->update(['status' => self::STATUS_INACTIVE]);
    }

    /**
     * Archive the listing.
     */
    public function archive(): void
    {
        $this->update(['status' => self::STATUS_ARCHIVED]);
    }

    /**
     * Get availability status for specific date range
     * Returns: 'available', 'partially_available', 'fully_booked', 'no_slots'
     */
    public function getAvailabilityStatus($checkInDate = null, $checkOutDate = null): array
    {
        // Default to next 30 days if no dates provided
        $startDate = $checkInDate ?? now()->toDateString();
        $endDate = $checkOutDate ?? now()->addDays(30)->toDateString();

        $totalSlots = $this->availability()
            ->whereBetween('available_date', [$startDate, $endDate])
            ->count();

        if ($totalSlots === 0) {
            return [
                'status' => 'no_slots',
                'label' => 'No availability data',
                'available_slots' => 0,
                'total_slots' => 0,
                'percentage' => 0,
            ];
        }

        $availableSlots = $this->availability()
            ->whereBetween('available_date', [$startDate, $endDate])
            ->where('status', 'available')
            ->count();

        $percentage = round(($availableSlots / $totalSlots) * 100);

        if ($availableSlots === 0) {
            $status = 'fully_booked';
            $label = 'Fully Booked';
        } elseif ($availableSlots === $totalSlots) {
            $status = 'available';
            $label = 'Available';
        } else {
            $status = 'partially_available';
            $label = 'Limited Availability';
        }

        return [
            'status' => $status,
            'label' => $label,
            'available_slots' => $availableSlots,
            'total_slots' => $totalSlots,
            'percentage' => $percentage,
        ];
    }

    /**
     * Check if listing is available for a specific date/time
     */
    public function isAvailableFor($checkInDate, $checkOutDate, $checkInTime = null, $checkOutTime = null): bool
    {
        $query = $this->availability()
            ->whereBetween('available_date', [$checkInDate, $checkOutDate])
            ->where('status', 'available');

        if ($checkInTime && $checkOutTime) {
            $query->where('start_time', '<=', $checkInTime)
                  ->where('end_time', '>=', $checkOutTime);
        }

        // For date range booking, ensure all dates have availability
        $requiredDays = \Carbon\Carbon::parse($checkInDate)->diffInDays(\Carbon\Carbon::parse($checkOutDate)) + 1;
        $availableDays = $query->distinct('available_date')->count();

        return $availableDays >= $requiredDays;
    }

    /**
     * Feature the listing.
     */
    public function feature(): void
    {
        $this->update(['is_featured' => true]);
    }

    /**
     * Unfeature the listing.
     */
    public function unfeature(): void
    {
        $this->update(['is_featured' => false]);
    }

    /**
     * Verify the listing.
     */
    public function verify(): void
    {
        $this->update(['is_verified' => true]);
    }

    /**
     * Unverify the listing.
     */
    public function unverify(): void
    {
        $this->update(['is_verified' => false]);
    }
}
