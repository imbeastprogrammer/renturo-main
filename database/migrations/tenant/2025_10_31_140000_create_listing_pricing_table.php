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
        Schema::create('listing_pricing', function (Blueprint $table) {
            $table->id();
            
            // Link to parent listing (one-to-one)
            $table->foreignId('listing_id')->unique()->constrained()->onDelete('cascade');
            
            // Currency
            $table->string('currency', 3)->default('PHP');
            
            // Price Range (calculated from units for multi-unit, direct for single-unit)
            $table->decimal('price_min', 10, 2)->nullable();
            $table->decimal('price_max', 10, 2)->nullable();
            
            // Pricing Model
            $table->enum('pricing_model', ['fixed', 'dynamic', 'tiered', 'seasonal'])->default('fixed');
            
            // Default Base Pricing (for single unit or fallback)
            $table->decimal('base_hourly_price', 10, 2)->nullable();
            $table->decimal('base_daily_price', 10, 2)->nullable();
            $table->decimal('base_weekly_price', 10, 2)->nullable();
            $table->decimal('base_monthly_price', 10, 2)->nullable();
            
            // Platform Fees
            $table->decimal('service_fee_percentage', 5, 2)->default(5.00);
            $table->decimal('platform_fee_percentage', 5, 2)->default(0.00);
            
            // Default Charges (can be overridden at unit level)
            $table->decimal('security_deposit', 10, 2)->nullable();
            $table->decimal('cleaning_fee', 10, 2)->nullable();
            
            // Discounts
            $table->decimal('weekly_discount_percentage', 5, 2)->nullable();
            $table->decimal('monthly_discount_percentage', 5, 2)->nullable();
            
            // Pricing Rules
            $table->decimal('min_price', 10, 2)->nullable();
            $table->decimal('max_price', 10, 2)->nullable();
            $table->decimal('price_per_guest', 10, 2)->nullable();
            
            // Tax Settings
            $table->decimal('tax_percentage', 5, 2)->default(12.00);
            $table->boolean('tax_included')->default(false);
            
            // Effective Period
            $table->timestamp('effective_from')->nullable();
            $table->timestamp('effective_until')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['price_min', 'price_max']);
            $table->index('currency');
            $table->index('listing_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_pricing');
    }
};

