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
        // Ganti enum status dengan status yang digunakan di SellerOrderController
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending_payment', 'processing', 'shipping', 'completed', 'cancelled', 'pending', 'confirmed', 'shipped', 'delivered')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan enum status ke status sebelumnya
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled')");
    }
};
