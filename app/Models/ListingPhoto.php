<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ListingPhoto Model
 * 
 * Represents photos/images for a listing.
 * 
 * @property int $id
 * @property int $listing_id
 * @property string $photo_url
 * @property string|null $thumbnail_url
 * @property string|null $original_filename
 * @property string|null $caption
 * @property string|null $alt_text
 * @property int $sort_order
 * @property bool $is_primary
 * @property string|null $storage_path
 * @property string $storage_disk
 * @property int|null $file_size
 * @property string|null $mime_type
 * @property int|null $width
 * @property int|null $height
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class ListingPhoto extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'listing_photos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'listing_id',
        'photo_url',
        'thumbnail_url',
        'original_filename',
        'caption',
        'alt_text',
        'sort_order',
        'is_primary',
        'storage_path',
        'storage_disk',
        'file_size',
        'mime_type',
        'width',
        'height',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // When setting a photo as primary, unset other primary photos for the same listing
        static::saving(function ($photo) {
            if ($photo->is_primary && $photo->listing_id) {
                static::where('listing_id', $photo->listing_id)
                    ->where('id', '!=', $photo->id ?? 0)
                    ->update(['is_primary' => false]);
            }
        });

        // Auto-set sort_order to the next available number if not provided
        static::creating(function ($photo) {
            if (!isset($photo->sort_order) || $photo->sort_order === 0) {
                $maxSortOrder = static::where('listing_id', $photo->listing_id)
                    ->max('sort_order');
                $photo->sort_order = ($maxSortOrder ?? 0) + 1;
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the listing that owns the photo.
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if this is the primary photo.
     */
    public function isPrimary(): bool
    {
        return $this->is_primary;
    }

    /**
     * Set this photo as primary.
     */
    public function setPrimary(): void
    {
        $this->update(['is_primary' => true]);
    }

    /**
     * Get the photo URL or thumbnail URL.
     */
    public function getUrl(bool $thumbnail = false): string
    {
        if ($thumbnail && $this->thumbnail_url) {
            return $this->thumbnail_url;
        }

        return $this->photo_url;
    }

    /**
     * Get the file size in a human-readable format.
     */
    public function getHumanFileSize(): ?string
    {
        if (!$this->file_size) {
            return null;
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the image dimensions as a string.
     */
    public function getDimensions(): ?string
    {
        if (!$this->width || !$this->height) {
            return null;
        }

        return "{$this->width}x{$this->height}";
    }

    /**
     * Check if the photo is an image (based on mime type).
     */
    public function isImage(): bool
    {
        if (!$this->mime_type) {
            return false;
        }

        return str_starts_with($this->mime_type, 'image/');
    }
}
