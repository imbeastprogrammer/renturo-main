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
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            
            // Owner/Creator Information
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Category & Type
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories')->onDelete('restrict');
            $table->string('listing_type')->default('sports'); // sports, accommodation, transport, etc.
            
            // Dynamic Form Link (for sport-specific or category-specific details)
            $table->foreignId('dynamic_form_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('dynamic_form_submission_id')->nullable()->constrained()->onDelete('set null');
            
            // Core Listing Information
            $table->string('title');
            $table->text('description');
            $table->string('slug')->unique();
            
            // Location
            $table->string('address');
            $table->string('city');
            $table->string('province');
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            
            // Pricing
            $table->decimal('price_per_hour', 10, 2)->nullable();
            $table->decimal('price_per_day', 10, 2)->nullable();
            $table->decimal('price_per_week', 10, 2)->nullable();
            $table->decimal('price_per_month', 10, 2)->nullable();
            $table->string('currency', 3)->default('PHP');
            
            // Capacity & Basic Amenities
            $table->integer('max_capacity')->nullable();
            $table->json('amenities')->nullable(); // ["parking", "restroom", "wifi", "lockers"]
            
            // Status & Visibility
            $table->enum('status', ['draft', 'active', 'inactive', 'suspended', 'archived'])->default('draft');
            $table->enum('visibility', ['public', 'private', 'unlisted'])->default('public');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            
            // Booking Settings
            $table->boolean('instant_booking')->default(false);
            $table->integer('minimum_booking_hours')->default(1);
            $table->integer('maximum_booking_hours')->nullable();
            $table->integer('advance_booking_days')->default(30); // How far in advance can users book
            $table->integer('cancellation_hours')->default(24); // Hours before booking to allow cancellation
            
            // Statistics
            $table->integer('views_count')->default(0);
            $table->integer('bookings_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('reviews_count')->default(0);
            
            // SEO & Metadata
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('meta_keywords')->nullable();
            
            // Timestamps
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['category_id', 'sub_category_id']);
            $table->index(['status', 'visibility']);
            $table->index(['city', 'province']);
            $table->index(['latitude', 'longitude']);
            $table->index('is_featured');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listings');
    }
};
