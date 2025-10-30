<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('listing_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            
            // Unit identification
            $table->string('unit_identifier'); // "Room 101", "Toyota Vios ABC-123", "Court A"
            $table->string('unit_name'); // "Deluxe Ocean View", "Economy Car", "Premium Indoor Court"
            $table->text('unit_description')->nullable(); // Detailed description
            
            // Unit specifications
            $table->json('unit_features')->nullable(); // Specific amenities, specs, equipment
            $table->json('unit_specifications')->nullable(); // Size, capacity, technical specs
            
            // Pricing modifiers
            $table->decimal('price_modifier', 5, 2)->default(1.00); // 1.2 = 20% more expensive than base
            $table->decimal('base_hourly_price', 10, 2)->nullable(); // Override listing price
            $table->decimal('base_daily_price', 10, 2)->nullable(); // Override listing price
            
            // Unit status and availability
            $table->enum('status', [
                'active',       // Available for booking
                'maintenance',  // Under maintenance
                'cleaning',     // Being cleaned
                'retired',      // No longer available
                'reserved'      // Reserved for special use
            ])->default('active');
            
            // Unit-specific rules
            $table->json('unit_rules')->nullable(); // Specific booking rules for this unit
            $table->integer('max_occupancy')->nullable(); // Max people allowed
            $table->integer('min_booking_hours')->nullable(); // Minimum booking duration
            $table->integer('max_booking_hours')->nullable(); // Maximum booking duration
            
            // Physical details (for different property types)
            $table->decimal('size_sqm', 8, 2)->nullable(); // Size in square meters
            $table->string('floor_level')->nullable(); // "Ground Floor", "2nd Floor"
            $table->string('location_details')->nullable(); // "Building A", "East Wing"
            
            // Media and images
            $table->string('primary_image')->nullable(); // Main unit photo
            $table->json('image_gallery')->nullable(); // Additional photos
            
            // Tracking and metadata
            $table->json('metadata')->nullable(); // Flexible additional data
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->unique(['listing_id', 'unit_identifier'], 'listing_unit_unique');
            $table->index(['listing_id', 'status'], 'listing_status_idx');
            $table->index(['status', 'created_at'], 'status_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_units');
    }
};