<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Media Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for media uploads, processing, and storage
    |
    */

    'storage' => [
        'disk' => env('MEDIA_DISK', 's3'),
        'path' => env('MEDIA_PATH', 'media'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Processing
    |--------------------------------------------------------------------------
    |
    | Image resize dimensions and quality settings
    |
    */

    'images' => [
        'sizes' => [
            'original' => [
                'max_width' => 1920,
                'max_height' => 1920,
                'quality' => 90,
            ],
            'medium' => [
                'max_width' => 800,
                'max_height' => 800,
                'quality' => 85,
            ],
            'thumbnail' => [
                'max_width' => 300,
                'max_height' => 300,
                'quality' => 80,
            ],
        ],
        'max_file_size' => 10485760, // 10MB in bytes
        'allowed_mimes' => ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'],
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Video Settings
    |--------------------------------------------------------------------------
    |
    | Video upload limits (no processing for MVP)
    |
    */

    'videos' => [
        'max_file_size' => 52428800, // 50MB in bytes
        'allowed_mimes' => ['video/mp4', 'video/quicktime', 'video/x-msvideo'],
        'allowed_extensions' => ['mp4', 'mov', 'avi'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Document Settings
    |--------------------------------------------------------------------------
    |
    | Document upload settings
    |
    */

    'documents' => [
        'max_file_size' => 10485760, // 10MB in bytes
        'allowed_mimes' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ],
        'allowed_extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Categories
    |--------------------------------------------------------------------------
    |
    | Available media categories
    |
    */

    'categories' => [
        'profile' => 'User profile photo',
        'cover' => 'Cover photo',
        'post' => 'Social media post',
        'story' => 'Story content',
        'comment' => 'Comment attachment',
        'listing' => 'Listing photo/video',
        'logo' => 'Business/Store logo',
        'banner' => 'Banner image',
        'document' => 'Document file',
        'attachment' => 'General attachment',
        'other' => 'Other',
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Types
    |--------------------------------------------------------------------------
    |
    | Media type definitions
    |
    */

    'types' => [
        'image' => 'Image',
        'video' => 'Video',
        'document' => 'Document',
    ],

    /*
    |--------------------------------------------------------------------------
    | Entity Types
    |--------------------------------------------------------------------------
    |
    | Allowed polymorphic entity types
    |
    */

    'entity_types' => [
        'User' => 'App\Models\User',
        'Listing' => 'App\Models\Listing',
        'Store' => 'App\Models\Store',
        'Post' => 'App\Models\Post',
        'DynamicFormSubmission' => 'App\Models\DynamicFormSubmission',
        'Comment' => 'App\Models\Comment',
    ],

    /*
    |--------------------------------------------------------------------------
    | S3 Path Structure
    |--------------------------------------------------------------------------
    |
    | S3 path structure: {entity}/{id}/{category}/{filename}
    | Example: user/1/profile/uuid.jpg
    |
    */

    'path_structure' => '{entity}/{id}/{category}',

    /*
    |--------------------------------------------------------------------------
    | CDN Settings
    |--------------------------------------------------------------------------
    |
    | CDN URL for media delivery (optional, for future use)
    |
    */

    'cdn' => [
        'enabled' => env('MEDIA_CDN_ENABLED', false),
        'url' => env('MEDIA_CDN_URL', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Processing Driver
    |--------------------------------------------------------------------------
    |
    | Image processing driver (gd or imagick)
    |
    */

    'driver' => env('IMAGE_DRIVER', 'gd'),

    /*
    |--------------------------------------------------------------------------
    | Auto-delete Settings
    |--------------------------------------------------------------------------
    |
    | Automatically delete old S3 files when replacing media
    |
    */

    'auto_delete_old' => true,

];

