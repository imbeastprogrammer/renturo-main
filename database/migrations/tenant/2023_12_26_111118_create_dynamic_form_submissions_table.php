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
        Schema::create('dynamic_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dynamic_form_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('data'); // Stores the submission data.
            $table->timestamps();
            $table->softDeletes(); // This adds the 'deleted_at' column for soft deletes

            $table->foreign('dynamic_form_id')->references('id')->on('dynamic_forms')->onDelete('cascade');

            // Optionally add a foreign key for user_id if you have a users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dynamic_form_submissions');
    }
};
