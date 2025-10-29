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
        Schema::create('availability_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            
            // Template identification
            $table->string('name'); // "Weekday Schedule", "Weekend Schedule", "Holiday Hours"
            $table->text('description')->nullable(); // Template description
            
            // Template pattern
            $table->json('days_of_week')->nullable(); // [1,2,3,4,5] for Mon-Fri, [6,7] for weekends
            $table->json('specific_dates')->nullable(); // ["2025-12-25", "2025-01-01"] for holidays
            $table->json('date_ranges')->nullable(); // [{"start": "2025-12-01", "end": "2025-12-31"}]
            
            // Time configuration
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('slot_duration_minutes')->default(60);
            
            // Pricing configuration
            $table->decimal('base_hourly_price', 10, 2);
            $table->decimal('base_daily_price', 10, 2)->nullable();
            $table->decimal('peak_hour_multiplier', 3, 2)->default(1.00); // 1.5 = 50% more
            $table->decimal('weekend_multiplier', 3, 2)->default(1.00);
            $table->decimal('holiday_multiplier', 3, 2)->default(1.00);
            
            // Peak hours definition
            $table->time('peak_start_time')->nullable(); // e.g., 18:00 (6 PM)
            $table->time('peak_end_time')->nullable();   // e.g., 22:00 (10 PM)
            
            // Booking rules
            $table->integer('min_duration_hours')->default(1);
            $table->integer('max_duration_hours')->nullable();
            $table->enum('duration_type', ['hourly', 'daily', 'weekly', 'monthly'])->default('hourly');
            $table->integer('advance_booking_hours')->default(1); // Min hours in advance
            $table->integer('max_advance_booking_days')->nullable(); // Max days in advance
            
            // Cancellation policy
            $table->integer('cancellation_hours')->default(24); // Hours before for free cancellation
            $table->decimal('cancellation_fee_percentage', 5, 2)->default(0.00); // 0-100%
            
            // Category-specific rules
            $table->json('category_rules')->nullable(); // Check-in times, cleaning fees, etc.
            $table->json('booking_rules')->nullable(); // Additional booking constraints
            
            // Template status and application
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(1); // Higher priority templates override lower ones
            $table->date('valid_from')->nullable(); // Template valid from date
            $table->date('valid_until')->nullable(); // Template valid until date
            
            // Auto-application settings
            $table->boolean('auto_apply')->default(false); // Automatically apply to new dates
            $table->integer('auto_apply_days_ahead')->default(30); // How many days ahead to auto-apply
            
            // Tracking
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['listing_id', 'is_active'], 'listing_active_idx');
            $table->index(['is_active', 'priority'], 'active_priority_idx');
            $table->index(['valid_from', 'valid_until'], 'validity_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availability_templates');
    }
};