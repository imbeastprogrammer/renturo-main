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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            
            // Booking reference
            $table->string('booking_number')->unique(); // e.g., BK-2025-001234
            $table->string('booking_type')->default('rental'); // rental, reservation, booking
            
            // Related entities
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->foreignId('listing_unit_id')->nullable()->constrained()->onDelete('cascade'); // For multi-unit properties
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Renter/Guest
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade'); // Property owner
            
            // Booking dates and times
            $table->date('booking_date'); // Date when booking was made
            $table->date('check_in_date'); // Start date
            $table->date('check_out_date'); // End date
            $table->time('check_in_time')->nullable(); // For hourly bookings
            $table->time('check_out_time')->nullable(); // For hourly bookings
            $table->integer('duration_hours')->nullable(); // For hourly rentals (sports, meetings)
            $table->integer('duration_days')->nullable(); // For daily rentals (hotels, cars)
            $table->enum('duration_type', ['hourly', 'daily', 'weekly', 'monthly'])->default('daily');
            
            // Pricing details
            $table->decimal('base_price', 10, 2); // Base price per unit
            $table->decimal('subtotal', 10, 2); // Base price * duration
            $table->decimal('service_fee', 10, 2)->default(0); // Platform fee
            $table->decimal('cleaning_fee', 10, 2)->default(0); // Cleaning fee (hotels, vacation rentals)
            $table->decimal('security_deposit', 10, 2)->default(0); // Refundable deposit
            $table->decimal('tax_amount', 10, 2)->default(0); // Tax
            $table->decimal('discount_amount', 10, 2)->default(0); // Discounts applied
            $table->decimal('total_price', 10, 2); // Final total amount
            $table->string('currency', 3)->default('PHP');
            
            // Guest/Renter information
            $table->integer('number_of_guests')->default(1); // For hotels, venues
            $table->integer('number_of_players')->nullable(); // For sports venues
            $table->integer('number_of_vehicles')->nullable(); // For parking
            $table->json('guest_details')->nullable(); // Additional guest info
            
            // Booking status and workflow
            $table->enum('status', [
                'pending',       // Awaiting confirmation
                'confirmed',     // Confirmed by owner
                'paid',          // Payment completed
                'checked_in',    // Guest checked in
                'in_progress',   // Booking in progress
                'completed',     // Booking completed
                'cancelled',     // Cancelled by user or owner
                'rejected',      // Rejected by owner
                'expired',       // Expired without confirmation
                'refunded'       // Payment refunded
            ])->default('pending');
            
            $table->enum('payment_status', [
                'pending',
                'partial',       // Deposit paid
                'paid',
                'refunded',
                'failed'
            ])->default('pending');
            
            // Cancellation details
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users');
            $table->text('cancellation_reason')->nullable();
            $table->decimal('cancellation_fee', 10, 2)->default(0);
            $table->decimal('refund_amount', 10, 2)->default(0);
            
            // Special requests and notes
            $table->text('special_requests')->nullable(); // Guest requests
            $table->text('owner_notes')->nullable(); // Private owner notes
            $table->text('internal_notes')->nullable(); // Admin/system notes
            
            // Confirmation and check-in
            $table->string('confirmation_code')->nullable()->unique(); // For self check-in
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            
            // Review and rating
            $table->boolean('review_submitted')->default(false);
            $table->timestamp('review_submitted_at')->nullable();
            
            // Automatic booking settings
            $table->boolean('auto_confirmed')->default(false); // If instant booking
            $table->boolean('requires_approval')->default(true);
            
            // Communication
            $table->timestamp('last_message_at')->nullable();
            $table->integer('unread_messages_count')->default(0);
            
            // Category-specific data
            $table->json('booking_metadata')->nullable(); // Flexible data per category
            
            // Payment tracking
            $table->string('payment_method')->nullable(); // card, cash, bank_transfer, wallet
            $table->string('payment_transaction_id')->nullable();
            $table->timestamp('payment_completed_at')->nullable();
            
            // Source tracking
            $table->string('booking_source')->default('mobile_app'); // mobile_app, web, admin
            $table->string('platform')->nullable(); // ios, android, web
            
            // Timestamps and soft deletes
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['listing_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['owner_id', 'status']);
            $table->index(['booking_number']);
            $table->index(['check_in_date', 'check_out_date']);
            $table->index(['status', 'payment_status']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
