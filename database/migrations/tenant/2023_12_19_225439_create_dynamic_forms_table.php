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
        Schema::create('dynamic_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            // Set up the foreign key without onDelete('cascade') to prevent a cascade delete of the form
            $table->unsignedBigInteger('subcategory_id');
            $table->foreign('subcategory_id')->references('id')->on('sub_categories');
            $table->timestamps();
            $table->softDeletes(); // This adds the 'deleted_at' column for soft deletes

            // Unique constraint for 'name' with 'subcategory_id'
            $table->unique(['name', 'subcategory_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dynamic_forms');
    }
};
