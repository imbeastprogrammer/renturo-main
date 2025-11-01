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
        Schema::create('availability_exceptions', function (Blueprint $table) {
            $table->id();
            
            // Link to specific unit
            $table->foreignId('dynamic_form_submission_id')->constrained()->onDelete('cascade');
            
            // Date/time configuration
            $table->date('date'); // Specific date blocked
            $table->time('start_time')->nullable(); // null = all day
            $table->time('end_time')->nullable(); // null = all day
            
            // Exception type
            $table->enum('type', ['blocked', 'maintenance', 'holiday', 'private_event', 'other'])->default('blocked');
            $table->string('reason')->nullable(); // "Floor polishing", "Private tournament", etc.
            $table->text('notes')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            
            // Tracking
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['dynamic_form_submission_id', 'date'], 'unit_date_idx');
            $table->index(['date', 'is_active'], 'date_active_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availability_exceptions');
    }
};

