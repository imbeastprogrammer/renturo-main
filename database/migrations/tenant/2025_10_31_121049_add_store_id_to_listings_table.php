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
        Schema::table('listings', function (Blueprint $table) {
            // Add store_id after user_id
            $table->foreignId('store_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');
            
            // Add index for performance
            $table->index('store_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('listings', function (Blueprint $table) {
            // Drop foreign key and index
            $table->dropForeign(['store_id']);
            $table->dropIndex(['store_id']);
            $table->dropColumn('store_id');
        });
    }
};
