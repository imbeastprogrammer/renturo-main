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
        Schema::create('dynamic_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('dynamic_form_page_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('input_field_label');
            $table->string('input_field_name');
            $table->string('input_field_type');
            $table->boolean('is_required');
            $table->boolean('is_multiple');
            $table->unsignedInteger('sort_no');
            $table->json('data');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dynamic_form_fields');
    }
};
