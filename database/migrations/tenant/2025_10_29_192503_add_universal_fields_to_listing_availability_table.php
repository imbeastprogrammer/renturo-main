<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('listing_availability', function (Blueprint $table) {
            // Check if columns exist before adding them
            if (!Schema::hasColumn('listing_availability', 'unit_identifier')) {
                $table->string('unit_identifier')->nullable()->after('listing_id');
            }
            
            if (!Schema::hasColumn('listing_availability', 'available_units')) {
                $table->integer('available_units')->default(1)->after('unit_identifier');
            }
            
            if (!Schema::hasColumn('listing_availability', 'peak_hour_price')) {
                $table->decimal('peak_hour_price', 10, 2)->nullable()->after('end_time');
            }
            
            if (!Schema::hasColumn('listing_availability', 'weekend_price')) {
                $table->decimal('weekend_price', 10, 2)->nullable()->after('peak_hour_price');
            }
            
            if (!Schema::hasColumn('listing_availability', 'holiday_price')) {
                $table->decimal('holiday_price', 10, 2)->nullable()->after('weekend_price');
            }
            
            if (!Schema::hasColumn('listing_availability', 'min_duration_hours')) {
                $table->integer('min_duration_hours')->default(1)->after('holiday_price');
            }
            
            if (!Schema::hasColumn('listing_availability', 'max_duration_hours')) {
                $table->integer('max_duration_hours')->nullable()->after('min_duration_hours');
            }
            
            if (!Schema::hasColumn('listing_availability', 'duration_type')) {
                $table->enum('duration_type', ['hourly', 'daily', 'weekly', 'monthly'])->default('hourly')->after('max_duration_hours');
            }
            
            if (!Schema::hasColumn('listing_availability', 'slot_duration_minutes')) {
                $table->integer('slot_duration_minutes')->default(60)->after('duration_type');
            }
            
            if (!Schema::hasColumn('listing_availability', 'recurrence_type')) {
                $table->enum('recurrence_type', [
                    'none', 'daily', 'weekly', 'monthly', 'yearly'
                ])->default('none')->after('slot_duration_minutes');
            }
            
            if (!Schema::hasColumn('listing_availability', 'recurrence_pattern')) {
                $table->json('recurrence_pattern')->nullable()->after('recurrence_type');
            }
            
            if (!Schema::hasColumn('listing_availability', 'recurrence_end_date')) {
                $table->date('recurrence_end_date')->nullable()->after('recurrence_pattern');
            }
            
            if (!Schema::hasColumn('listing_availability', 'category_rules')) {
                $table->json('category_rules')->nullable()->after('recurrence_end_date');
            }
            
            if (!Schema::hasColumn('listing_availability', 'booking_rules')) {
                $table->json('booking_rules')->nullable()->after('category_rules');
            }
            
            if (!Schema::hasColumn('listing_availability', 'metadata')) {
                $table->json('metadata')->nullable()->after('notes');
            }
            
            if (!Schema::hasColumn('listing_availability', 'status')) {
                $table->enum('status', [
                    'available', 'blocked', 'booked', 'maintenance', 'cleaning', 'reserved', 'cancelled'
                ])->default('available')->after('metadata');
            }
            
            if (!Schema::hasColumn('listing_availability', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->after('status');
            }
            
            if (!Schema::hasColumn('listing_availability', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->after('created_by');
            }
        });
        
        // Update existing records to use new status column based on is_available (if status column was added)
        if (Schema::hasColumn('listing_availability', 'status') && Schema::hasColumn('listing_availability', 'is_available')) {
            DB::statement("UPDATE listing_availability SET status = CASE WHEN is_available = 1 THEN 'available' ELSE 'blocked' END");
        }
        
        // Add indexes for performance (check if they don't exist)
        Schema::table('listing_availability', function (Blueprint $table) {
            // Check if indexes exist before creating them
            $indexes = DB::select("SHOW INDEX FROM listing_availability");
            $indexNames = collect($indexes)->pluck('Key_name')->toArray();
            
            if (!in_array('listing_unit_date_idx', $indexNames)) {
                $table->index(['listing_id', 'unit_identifier', 'available_date'], 'listing_unit_date_idx');
            }
            
            if (!in_array('date_status_idx', $indexNames)) {
                $table->index(['available_date', 'status'], 'date_status_idx');
            }
            
            if (!in_array('recurrence_idx', $indexNames)) {
                $table->index(['recurrence_type', 'recurrence_end_date'], 'recurrence_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listing_availability', function (Blueprint $table) {
            // Drop indexes if they exist
            $indexes = DB::select("SHOW INDEX FROM listing_availability");
            $indexNames = collect($indexes)->pluck('Key_name')->toArray();
            
            if (in_array('listing_unit_date_idx', $indexNames)) {
                $table->dropIndex('listing_unit_date_idx');
            }
            
            if (in_array('date_status_idx', $indexNames)) {
                $table->dropIndex('date_status_idx');
            }
            
            if (in_array('recurrence_idx', $indexNames)) {
                $table->dropIndex('recurrence_idx');
            }
            
            // Drop columns if they exist
            $columns = Schema::getColumnListing('listing_availability');
            
            $columnsToRemove = [
                'unit_identifier', 'available_units', 'peak_hour_price', 'weekend_price', 
                'holiday_price', 'min_duration_hours', 'max_duration_hours', 'duration_type',
                'slot_duration_minutes', 'recurrence_type', 'recurrence_pattern', 
                'recurrence_end_date', 'category_rules', 'booking_rules', 'metadata',
                'status', 'created_by', 'updated_by'
            ];
            
            foreach ($columnsToRemove as $column) {
                if (in_array($column, $columns)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};