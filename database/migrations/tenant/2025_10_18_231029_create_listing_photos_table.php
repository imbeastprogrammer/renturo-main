<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            
            // Photo Information
            $table->string('photo_url'); // Full URL to the photo
            $table->string('thumbnail_url')->nullable(); // Optimized thumbnail
            $table->string('original_filename')->nullable();
            
            // Photo Details
            $table->string('caption')->nullable();
            $table->string('alt_text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false); // Main listing photo
            
            // Storage Information
            $table->string('storage_path')->nullable(); // Path in storage system
            $table->string('storage_disk')->default('public'); // Which disk (public, s3, etc.)
            $table->integer('file_size')->nullable(); // Size in bytes
            $table->string('mime_type')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['listing_id', 'is_primary']);
            $table->index(['listing_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listing_photos');
    }
};
