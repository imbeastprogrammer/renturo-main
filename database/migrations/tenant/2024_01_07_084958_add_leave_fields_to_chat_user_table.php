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
        Schema::table('chat_user', function (Blueprint $table) {
            $table->boolean('is_active')->default(true); // Indicates if the user is active in the chat
            $table->timestamp('left_at')->nullable();   // Timestamp of when the user left the chat
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_user', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'left_at']);
        });
    }
};
