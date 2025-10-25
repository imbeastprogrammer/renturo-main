<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'media';

    protected $fillable = [
        'mediable_type',
        'mediable_id',
        'user_id',
        'media_type',
        'category',
        'file_name',
        'original_name',
        's3_key',
        's3_bucket',
        's3_url',
        'cdn_url',
        'thumbnail_url',
        'file_size',
        'mime_type',
        'extension',
        'width',
        'height',
        'duration',
        'sort_order',
        'is_primary',
        'is_processed',
        'metadata',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_processed' => 'boolean',
        'metadata' => 'array',
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'duration' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $appends = ['url', 'human_file_size'];

    /**
     * Media types constants
     */
    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';
    const TYPE_DOCUMENT = 'document';

    /**
     * Category constants
     */
    const CATEGORY_PROFILE = 'profile';
    const CATEGORY_COVER = 'cover';
    const CATEGORY_POST = 'post';
    const CATEGORY_STORY = 'story';
    const CATEGORY_COMMENT = 'comment';
    const CATEGORY_LISTING = 'listing';
    const CATEGORY_LOGO = 'logo';
    const CATEGORY_BANNER = 'banner';
    const CATEGORY_DOCUMENT = 'document';
    const CATEGORY_ATTACHMENT = 'attachment';
    const CATEGORY_OTHER = 'other';

    /**
     * Polymorphic relationship - media belongs to any entity
     */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Media belongs to a user (uploader)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the URL accessor (prefer CDN if available)
     */
    public function getUrlAttribute(): string
    {
        return $this->cdn_url ?? $this->s3_url;
    }

    /**
     * Get human-readable file size
     */
    public function getHumanFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if media is an image
     */
    public function isImage(): bool
    {
        return $this->media_type === self::TYPE_IMAGE;
    }

    /**
     * Check if media is a video
     */
    public function isVideo(): bool
    {
        return $this->media_type === self::TYPE_VIDEO;
    }

    /**
     * Check if media is a document
     */
    public function isDocument(): bool
    {
        return $this->media_type === self::TYPE_DOCUMENT;
    }

    /**
     * Set this media as primary for its entity
     */
    public function setAsPrimary(): bool
    {
        // Unset all other primary media for this entity
        static::where('mediable_type', $this->mediable_type)
            ->where('mediable_id', $this->mediable_id)
            ->where('category', $this->category)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);

        // Set this one as primary
        $this->is_primary = true;
        return $this->save();
    }

    /**
     * Delete media from S3 when model is deleted
     */
    protected static function booted()
    {
        static::deleted(function ($media) {
            // Delete from S3
            if ($media->s3_key) {
                Storage::disk('s3')->delete($media->s3_key);
            }
            
            // Delete thumbnail if exists
            if ($media->thumbnail_url) {
                $thumbnailKey = str_replace($media->s3_url, '', $media->thumbnail_url);
                if ($thumbnailKey) {
                    Storage::disk('s3')->delete($thumbnailKey);
                }
            }
        });
    }

    /**
     * Scope: Get images only
     */
    public function scopeImages($query)
    {
        return $query->where('media_type', self::TYPE_IMAGE);
    }

    /**
     * Scope: Get videos only
     */
    public function scopeVideos($query)
    {
        return $query->where('media_type', self::TYPE_VIDEO);
    }

    /**
     * Scope: Get by category
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Get primary media
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope: Order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }
}
