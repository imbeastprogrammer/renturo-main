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
        Schema::create('listing_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            
            // Availability Type
            $table->enum('availability_type', ['recurring', 'specific_date', 'date_range', 'blocked'])->default('recurring');
            
            // For Recurring Availability (e.g., Every Monday 8AM-5PM)
            $table->integer('day_of_week')->nullable(); // 0=Sunday, 1=Monday, ..., 6=Saturday
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            
            // For Specific Date or Date Range
            $table->date('available_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            
            // Pricing Override (optional, if different from listing default)
            $table->decimal('price_override', 10, 2)->nullable();
            
            // Status
            $table->boolean('is_available')->default(true);
            $table->string('notes')->nullable(); // e.g., "Maintenance day", "Holiday hours"
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for efficient availability queries
            $table->index(['listing_id', 'day_of_week', 'is_available'], 'listing_avail_listing_day_available_idx');
            $table->index(['listing_id', 'available_date', 'is_available'], 'listing_avail_listing_date_available_idx');
            $table->index(['listing_id', 'start_date', 'end_date'], 'listing_avail_listing_date_range_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listing_availability');
    }
};
