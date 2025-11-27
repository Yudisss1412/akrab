<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the enum values for status column to include waiting_payment_verification
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'waiting_payment_verification', 'paid', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original status enum values without waiting_payment_verification
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'paid', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded')");
    }
};
