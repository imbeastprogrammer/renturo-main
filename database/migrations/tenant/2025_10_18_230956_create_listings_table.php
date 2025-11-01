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
            $table->foreignId('store_id')->nullable()->constrained()->onDelete('cascade');
            
            // Category & Type
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories')->onDelete('restrict');
            $table->string('listing_type')->default('sports'); // sports, accommodation, transport, etc.
            
            // Dynamic Form Link (for sport-specific or category-specific details)
            $table->foreignId('dynamic_form_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('dynamic_form_submission_id')->nullable()->constrained()->onDelete('set null');
            
            // Core Listing Information (Parent/Facility Level)
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
            
            // Inventory Management
            $table->enum('inventory_type', ['single', 'multiple'])->default('single'); // single unit or multiple units
            $table->integer('total_units')->default(1); // Total number of bookable units
            
            // Status & Visibility
            $table->enum('status', ['draft', 'active', 'inactive', 'suspended', 'archived'])->default('draft');
            $table->enum('visibility', ['public', 'private', 'unlisted'])->default('public');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            
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
            $table->index('inventory_type');
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
