<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Exception;

class MediaService
{
    /**
     * Image size configurations (max dimensions)
     */
    const SIZE_ORIGINAL = 1920;
    const SIZE_MEDIUM = 800;
    const SIZE_THUMBNAIL = 300;

    /**
     * Max file sizes (bytes)
     */
    const MAX_IMAGE_SIZE = 10485760; // 10MB
    const MAX_VIDEO_SIZE = 52428800; // 50MB
    const MAX_DOCUMENT_SIZE = 10485760; // 10MB

    /**
     * Allowed MIME types
     */
    const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    const ALLOWED_VIDEO_TYPES = ['video/mp4', 'video/quicktime', 'video/x-msvideo'];
    const ALLOWED_DOCUMENT_TYPES = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

    /**
     * Upload media (images, videos, documents)
     *
     * @param UploadedFile $file
     * @param string $mediableType - Entity type (User, Listing, Store, Post)
     * @param int $mediableId - Entity ID
     * @param string $category - Media category (profile, listing, post, etc.)
     * @param int $userId - Uploader ID
     * @return Media
     */
    public function upload(
        UploadedFile $file,
        string $mediableType,
        int $mediableId,
        string $category,
        int $userId
    ): Media {
        // Validate file
        $this->validateFile($file);

        // Determine media type
        $mediaType = $this->determineMediaType($file->getMimeType());

        // Process based on type
        if ($mediaType === Media::TYPE_IMAGE) {
            return $this->uploadImage($file, $mediableType, $mediableId, $category, $userId);
        } elseif ($mediaType === Media::TYPE_VIDEO) {
            return $this->uploadVideo($file, $mediableType, $mediableId, $category, $userId);
        } else {
            return $this->uploadDocument($file, $mediableType, $mediableId, $category, $userId);
        }
    }

    /**
     * Upload and process image
     */
    protected function uploadImage(
        UploadedFile $file,
        string $mediableType,
        int $mediableId,
        string $category,
        int $userId
    ): Media {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        
        // Generate unique filename
        $fileName = $this->generateFileName($extension);
        
        // Get S3 path based on entity type and category
        $s3Path = $this->getS3Path($mediableType, $mediableId, $category);
        
        // Load image with Intervention
        $image = Image::make($file);
        $width = $image->width();
        $height = $image->height();
        
        // Upload original (resized to max)
        $originalImage = clone $image;
        if ($width > self::SIZE_ORIGINAL || $height > self::SIZE_ORIGINAL) {
            $originalImage->resize(self::SIZE_ORIGINAL, self::SIZE_ORIGINAL, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
        
        $originalKey = "{$s3Path}/{$fileName}";
        Storage::disk('s3')->put($originalKey, (string) $originalImage->encode());
        $originalUrl = Storage::disk('s3')->url($originalKey);
        
        // Create thumbnail
        $thumbnailImage = clone $image;
        $thumbnailImage->resize(self::SIZE_THUMBNAIL, self::SIZE_THUMBNAIL, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        
        $thumbnailKey = "{$s3Path}/thumb_{$fileName}";
        Storage::disk('s3')->put($thumbnailKey, (string) $thumbnailImage->encode());
        $thumbnailUrl = Storage::disk('s3')->url($thumbnailKey);
        
        // Create media record
        $media = Media::create([
            'mediable_type' => $mediableType,
            'mediable_id' => $mediableId,
            'user_id' => $userId,
            'media_type' => Media::TYPE_IMAGE,
            'category' => $category,
            'file_name' => $fileName,
            'original_name' => $originalName,
            's3_key' => $originalKey,
            's3_bucket' => config('filesystems.disks.s3.bucket'),
            's3_url' => $originalUrl,
            'thumbnail_url' => $thumbnailUrl,
            'file_size' => $file->getSize(),
            'mime_type' => $mimeType,
            'extension' => $extension,
            'width' => $originalImage->width(),
            'height' => $originalImage->height(),
            'is_processed' => true,
        ]);
        
        return $media;
    }

    /**
     * Upload video (no processing)
     */
    protected function uploadVideo(
        UploadedFile $file,
        string $mediableType,
        int $mediableId,
        string $category,
        int $userId
    ): Media {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        
        // Generate unique filename
        $fileName = $this->generateFileName($extension);
        
        // Get S3 path
        $s3Path = $this->getS3Path($mediableType, $mediableId, $category);
        $s3Key = "{$s3Path}/{$fileName}";
        
        // Upload to S3
        Storage::disk('s3')->putFileAs($s3Path, $file, $fileName);
        $s3Url = Storage::disk('s3')->url($s3Key);
        
        // Create media record
        $media = Media::create([
            'mediable_type' => $mediableType,
            'mediable_id' => $mediableId,
            'user_id' => $userId,
            'media_type' => Media::TYPE_VIDEO,
            'category' => $category,
            'file_name' => $fileName,
            'original_name' => $originalName,
            's3_key' => $s3Key,
            's3_bucket' => config('filesystems.disks.s3.bucket'),
            's3_url' => $s3Url,
            'file_size' => $file->getSize(),
            'mime_type' => $mimeType,
            'extension' => $extension,
            'is_processed' => true,
        ]);
        
        return $media;
    }

    /**
     * Upload document (PDF, Word, etc.)
     */
    protected function uploadDocument(
        UploadedFile $file,
        string $mediableType,
        int $mediableId,
        string $category,
        int $userId
    ): Media {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        
        // Generate unique filename
        $fileName = $this->generateFileName($extension);
        
        // Get S3 path
        $s3Path = $this->getS3Path($mediableType, $mediableId, $category);
        $s3Key = "{$s3Path}/{$fileName}";
        
        // Upload to S3
        Storage::disk('s3')->putFileAs($s3Path, $file, $fileName);
        $s3Url = Storage::disk('s3')->url($s3Key);
        
        // Create media record
        $media = Media::create([
            'mediable_type' => $mediableType,
            'mediable_id' => $mediableId,
            'user_id' => $userId,
            'media_type' => Media::TYPE_DOCUMENT,
            'category' => $category,
            'file_name' => $fileName,
            'original_name' => $originalName,
            's3_key' => $s3Key,
            's3_bucket' => config('filesystems.disks.s3.bucket'),
            's3_url' => $s3Url,
            'file_size' => $file->getSize(),
            'mime_type' => $mimeType,
            'extension' => $extension,
            'is_processed' => true,
        ]);
        
        return $media;
    }

    /**
     * Delete media and its files from S3
     */
    public function delete(Media $media): bool
    {
        try {
            // Delete from S3
            if ($media->s3_key) {
                Storage::disk('s3')->delete($media->s3_key);
            }
            
            // Delete thumbnail if exists
            if ($media->thumbnail_url) {
                $thumbnailKey = str_replace(
                    Storage::disk('s3')->url(''),
                    '',
                    $media->thumbnail_url
                );
                Storage::disk('s3')->delete($thumbnailKey);
            }
            
            // Delete database record
            return $media->delete();
        } catch (Exception $e) {
            throw new Exception("Failed to delete media: " . $e->getMessage());
        }
    }

    /**
     * Generate unique filename
     */
    protected function generateFileName(string $extension): string
    {
        return Str::uuid() . '.' . $extension;
    }

    /**
     * Get S3 path based on entity and category
     */
    protected function getS3Path(string $mediableType, int $mediableId, string $category): string
    {
        $type = strtolower(class_basename($mediableType));
        return "{$type}/{$mediableId}/{$category}";
    }

    /**
     * Determine media type from MIME type
     */
    protected function determineMediaType(string $mimeType): string
    {
        if (in_array($mimeType, self::ALLOWED_IMAGE_TYPES)) {
            return Media::TYPE_IMAGE;
        } elseif (in_array($mimeType, self::ALLOWED_VIDEO_TYPES)) {
            return Media::TYPE_VIDEO;
        } else {
            return Media::TYPE_DOCUMENT;
        }
    }

    /**
     * Validate uploaded file
     */
    protected function validateFile(UploadedFile $file): void
    {
        $mimeType = $file->getMimeType();
        $size = $file->getSize();
        
        // Check if file type is allowed
        $allAllowedTypes = array_merge(
            self::ALLOWED_IMAGE_TYPES,
            self::ALLOWED_VIDEO_TYPES,
            self::ALLOWED_DOCUMENT_TYPES
        );
        
        if (!in_array($mimeType, $allAllowedTypes)) {
            throw new Exception("File type {$mimeType} is not allowed.");
        }
        
        // Check file size based on type
        if (in_array($mimeType, self::ALLOWED_IMAGE_TYPES) && $size > self::MAX_IMAGE_SIZE) {
            throw new Exception("Image file size exceeds maximum allowed size of " . (self::MAX_IMAGE_SIZE / 1048576) . "MB.");
        }
        
        if (in_array($mimeType, self::ALLOWED_VIDEO_TYPES) && $size > self::MAX_VIDEO_SIZE) {
            throw new Exception("Video file size exceeds maximum allowed size of " . (self::MAX_VIDEO_SIZE / 1048576) . "MB.");
        }
        
        if (in_array($mimeType, self::ALLOWED_DOCUMENT_TYPES) && $size > self::MAX_DOCUMENT_SIZE) {
            throw new Exception("Document file size exceeds maximum allowed size of " . (self::MAX_DOCUMENT_SIZE / 1048576) . "MB.");
        }
    }
}

