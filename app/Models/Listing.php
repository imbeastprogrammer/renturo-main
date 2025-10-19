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
