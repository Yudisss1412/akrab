<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('lat', 10, 8)->nullable()->after('address'); // Latitude: -90 to 90, with 8 decimal places
            $table->decimal('lng', 11, 8)->nullable()->after('lat'); // Longitude: -180 to 180, with 8 decimal places
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['lat', 'lng']);
        });
    }
};
