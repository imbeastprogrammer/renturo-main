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
        Schema::table('dynamic_form_submissions', function (Blueprint $table) {
            // Add a unique constraint on user_id and dynamic_form_id
            $table->unique(['user_id', 'dynamic_form_id'], 'user_form_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dynamic_form_submissions', function (Blueprint $table) {
            // Remove the unique constraint if the migration is rolled back
            $table->dropUnique('user_form_unique');
        });
    }
};
