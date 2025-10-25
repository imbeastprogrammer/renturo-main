<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add avatar and cover_photo columns for backward compatibility
     * and quick access. Full media history stored in `media` table.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('mobile_number');
            $table->string('cover_photo')->nullable()->after('avatar');
            $table->text('bio')->nullable()->after('cover_photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'cover_photo', 'bio']);
        });
    }
};
