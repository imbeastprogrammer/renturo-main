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
        Schema::table('dynamic_form_pages', function (Blueprint $table) {
            
            // Add a sub_category_id column as an unsigned big integer
            $table->unsignedBigInteger('sub_category_id')->nullable()->after('title');

            // Foreign key constraint
            $table->foreign('sub_category_id')
                ->references('id')->on('sub_categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dynamic_form_pages', function (Blueprint $table) {
            
            // If you added a foreign key, make sure to drop it first
            $table->dropForeign(['sub_category_id']);
                        
            // Then drop the column
            $table->dropColumn('sub_category_id');
        });
    }
};
