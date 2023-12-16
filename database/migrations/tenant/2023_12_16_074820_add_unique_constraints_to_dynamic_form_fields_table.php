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
        Schema::table('dynamic_form_fields', function (Blueprint $table) {
            // Add unique constraints
            $table->unique(['dynamic_form_page_id', 'input_field_label'], 'dynamic_form_page_label_unique');
            $table->unique(['dynamic_form_page_id', 'input_field_name'], 'dynamic_form_page_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dynamic_form_fields', function (Blueprint $table) {
            // Drop unique constraints
            $table->dropUnique('dynamic_form_page_label_unique');
            $table->dropUnique('dynamic_form_page_name_unique');
        });
    }
};
