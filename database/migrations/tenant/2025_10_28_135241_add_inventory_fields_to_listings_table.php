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
        Schema::table('listings', function (Blueprint $table) {
            // Inventory management fields
            $table->enum('inventory_type', [
                'single',      // One unit (basketball court, entire house, single car)
                'multiple',    // Multiple units (hotel rooms, fleet cars, multiple courts)
                'shared'       // Shared resource (conference room, common areas)
            ])->default('single')->after('status');
            
            $table->integer('total_units')->default(1)->after('inventory_type'); // How many units available
            $table->json('unit_details')->nullable()->after('total_units'); // Unit identifiers, names, etc.
            
            // Booking configuration
            $table->enum('booking_type', [
                'instant',     // Instant booking (no owner approval)
                'request',     // Request booking (owner approval required)
                'inquiry'      // Inquiry only (no direct booking)
            ])->default('request')->after('unit_details');
            
            $table->integer('min_booking_duration')->default(1)->after('booking_type'); // Minimum hours/days
            $table->integer('max_booking_duration')->nullable()->after('min_booking_duration'); // Maximum hours/days
            $table->enum('duration_unit', ['hours', 'days', 'weeks', 'months'])->default('hours')->after('max_booking_duration');
            
            // Advance booking rules
            $table->integer('min_advance_hours')->default(1)->after('duration_unit'); // Min hours in advance
            $table->integer('max_advance_days')->nullable()->after('min_advance_hours'); // Max days in advance
            
            // Pricing strategy
            $table->enum('pricing_model', [
                'fixed',       // Fixed pricing
                'dynamic',     // Dynamic pricing based on demand
                'tiered',      // Tiered pricing (bulk discounts)
                'seasonal'     // Seasonal pricing
            ])->default('fixed')->after('max_advance_days');
            
            // Default pricing (can be overridden by availability or units)
            $table->decimal('base_hourly_price', 10, 2)->nullable()->after('pricing_model');
            $table->decimal('base_daily_price', 10, 2)->nullable()->after('base_hourly_price');
            $table->decimal('base_weekly_price', 10, 2)->nullable()->after('base_daily_price');
            $table->decimal('base_monthly_price', 10, 2)->nullable()->after('base_weekly_price');
            
            // Service fees and deposits
            $table->decimal('service_fee_percentage', 5, 2)->default(5.00)->after('base_monthly_price'); // Platform fee %
            $table->decimal('security_deposit', 10, 2)->nullable()->after('service_fee_percentage'); // Security deposit
            $table->decimal('cleaning_fee', 10, 2)->nullable()->after('security_deposit'); // Cleaning fee
            
            // Cancellation policy
            $table->enum('cancellation_policy', [
                'flexible',    // Free cancellation 24h before
                'moderate',    // Free cancellation 5 days before
                'strict',      // Free cancellation 14 days before
                'super_strict', // No refund after booking
                'custom'       // Custom policy
            ])->default('moderate')->after('cleaning_fee');
            
            $table->json('cancellation_rules')->nullable()->after('cancellation_policy'); // Custom cancellation rules
            
            // Check-in/Check-out (for hotels, vacation rentals)
            $table->time('default_checkin_time')->nullable()->after('cancellation_rules'); // e.g., 15:00
            $table->time('default_checkout_time')->nullable()->after('default_checkin_time'); // e.g., 11:00
            
            // Capacity and restrictions
            $table->integer('max_guests')->nullable()->after('default_checkout_time'); // Maximum occupancy
            $table->integer('min_age_requirement')->nullable()->after('max_guests'); // Minimum age
            $table->json('house_rules')->nullable()->after('min_age_requirement'); // Property rules
            
            // Operational settings
            $table->boolean('auto_accept_bookings')->default(false)->after('house_rules'); // Auto-accept if instant booking
            $table->integer('response_time_hours')->default(24)->after('auto_accept_bookings'); // Owner response time
            $table->boolean('allow_same_day_booking')->default(true)->after('response_time_hours');
            
            // Category-specific settings
            $table->json('category_settings')->nullable()->after('allow_same_day_booking'); // Category-specific configurations
            
            // Business hours (default operating hours)
            $table->time('business_hours_start')->nullable()->after('category_settings'); // e.g., 06:00
            $table->time('business_hours_end')->nullable()->after('business_hours_start'); // e.g., 23:00
            $table->json('business_days')->nullable()->after('business_hours_end'); // [1,2,3,4,5,6,7] for days of week
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn([
                'inventory_type',
                'total_units',
                'unit_details',
                'booking_type',
                'min_booking_duration',
                'max_booking_duration',
                'duration_unit',
                'min_advance_hours',
                'max_advance_days',
                'pricing_model',
                'base_hourly_price',
                'base_daily_price',
                'base_weekly_price',
                'base_monthly_price',
                'service_fee_percentage',
                'security_deposit',
                'cleaning_fee',
                'cancellation_policy',
                'cancellation_rules',
                'default_checkin_time',
                'default_checkout_time',
                'max_guests',
                'min_age_requirement',
                'house_rules',
                'auto_accept_bookings',
                'response_time_hours',
                'allow_same_day_booking',
                'category_settings',
                'business_hours_start',
                'business_hours_end',
                'business_days'
            ]);
        });
    }
};