<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\DynamicFormAvailability;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dynamic_form_availabilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dynamic_form_submission_id');
            $table->unsignedBigInteger('user_id');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('type', DynamicFormAvailability::AVAILABILITY_TYPES);
            $table->integer('minimum_duration')->default(0);
            $table->string('status');
            $table->json('recurring')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign key constraints
            $table->foreign('dynamic_form_submission_id')->references('id')->on('dynamic_form_submissions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dynamic_form_availabilities');
    }
};
