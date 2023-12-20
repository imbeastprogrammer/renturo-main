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

            // Drop the old foreign key and column
            $table->dropForeign(['sub_category_id']);
            $table->dropColumn('sub_category_id');

            // Add the new dynamic_form_id column
            $table->unsignedBigInteger('dynamic_form_id')->nullable()->after('id');

            // Add the new foreign key constraint
            $table->foreign('dynamic_form_id')
                  ->references('id')->on('dynamic_forms')
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
            
            // Drop the new foreign key and column
            $table->dropForeign(['dynamic_form_id']);
            $table->dropColumn('dynamic_form_id');

            // Add back the original sub_category_id column and foreign key
            $table->unsignedBigInteger('sub_category_id')->nullable()->after('id');
            $table->foreign('sub_category_id')
                  ->references('id')->on('sub_categories')
                  ->onDelete('cascade');
        });
    }
};
