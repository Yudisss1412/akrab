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
        // Drop the existing foreign key constraint
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->dropForeign(['seller_id']);
        });

        // Recreate the foreign key constraint to reference the sellers table
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key constraint
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->dropForeign(['seller_id']);
        });

        // Recreate the old foreign key constraint to reference the users table
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
