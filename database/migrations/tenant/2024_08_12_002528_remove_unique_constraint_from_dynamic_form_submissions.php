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
            $table->dropUnique('user_form_unique');  // This is the name of the unique index
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
            $table->unique(['user_id', 'dynamic_form_id'], 'user_form_unique');     
        });
    }
};
