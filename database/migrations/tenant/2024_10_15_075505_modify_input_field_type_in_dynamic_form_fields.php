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
            // Drop the old column
            $table->dropColumn('input_field_type');
        });

        Schema::table('dynamic_form_fields', function (Blueprint $table) {
            // Add new string column
            $table->string('input_field_type')->after('input_field_name');
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
            // Drop the string column
            $table->dropColumn('input_field_type');
        });

        Schema::table('dynamic_form_fields', function (Blueprint $table) {
            // Add back the enum column
            $table->enum('input_field_type', [
                'heading', 'body', 'text', 'textarea', 'number', 'email',
                'date', 'time', 'select', 'checkbox', 'radio', 'checklist',
                'attachment', 'rating', 'password', 'multiselect', 'file',
                'hidden', 'color', 'url'
            ])->after('input_field_name');
        });
    }
};
