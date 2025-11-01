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
            
            // Relations
            $table->unsignedBigInteger('listing_id')->nullable(); // Parent listing (FK added later)
            $table->unsignedBigInteger('dynamic_form_id')->nullable(); // Form template used
            $table->unsignedBigInteger('user_id')->nullable(); // Owner who submitted
            $table->unsignedBigInteger('store_id')->nullable(); // Related store/business
            
            // Unit data stored as flexible JSON
            $table->json('data'); // All unit-specific details: name, pricing, capacity, amenities, etc.
            
            // Unit status
            $table->enum('status', ['draft', 'active', 'inactive', 'maintenance', 'retired'])->default('active');
            
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys (listing_id FK will be added after listings table is created)
            $table->foreign('dynamic_form_id')->references('id')->on('dynamic_forms')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            
            // Indexes
            $table->index('listing_id');
            $table->index('status');
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
