<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This table handles ALL media uploads for Renturo:
     * - Social: User avatars, posts, stories, comments
     * - Rental: Listing photos/videos, store logos
     * - Forms: Document attachments
     * 
     * Uses polymorphic relationships to link to any entity
     */
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            
            // Polymorphic relationship - can belong to User, Listing, Store, Post, etc.
            $table->string('mediable_type')->index();
            $table->unsignedBigInteger('mediable_id')->index();
            
            // Who uploaded it
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Media classification
            $table->enum('media_type', ['image', 'video', 'document'])->default('image');
            $table->enum('category', [
                'profile',      // User avatar
                'cover',        // User cover photo
                'post',         // Social media post
                'story',        // Story (24hr content)
                'comment',      // Comment attachment
                'listing',      // Property photo/video
                'logo',         // Store/business logo
                'banner',       // Store banner
                'document',     // PDF, contracts, etc.
                'attachment',   // Form attachments
                'other'
            ])->default('other');
            
            // File information
            $table->string('file_name');                    // Stored filename
            $table->string('original_name');                // Original uploaded filename
            $table->string('s3_key')->unique();            // S3 object key
            $table->string('s3_bucket');                   // S3 bucket name
            $table->string('s3_url', 500);                 // Full S3 URL
            $table->string('cdn_url', 500)->nullable();    // CloudFront CDN URL
            $table->string('thumbnail_url', 500)->nullable(); // Thumbnail URL
            
            // File metadata
            $table->unsignedBigInteger('file_size')->default(0);  // Bytes
            $table->string('mime_type', 100);
            $table->string('extension', 10);
            
            // Image/Video specific
            $table->unsignedInteger('width')->nullable();    // Image/video width
            $table->unsignedInteger('height')->nullable();   // Image/video height
            $table->unsignedInteger('duration')->nullable(); // Video duration in seconds
            
            // Ordering and flags
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);  // Primary photo/avatar
            $table->boolean('is_processed')->default(true); // For async processing
            
            // Additional metadata (JSON)
            $table->json('metadata')->nullable();  // Alt text, captions, exif data, etc.
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['mediable_type', 'mediable_id']);
            $table->index(['user_id', 'media_type']);
            $table->index(['category', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
