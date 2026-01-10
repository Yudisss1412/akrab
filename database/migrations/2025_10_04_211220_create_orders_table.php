<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel pesanan
 *
 * Migration ini membuat tabel 'orders' yang menyimpan informasi pesanan
 * dalam sistem e-commerce AKRAB, termasuk nomor pesanan, pengguna yang memesan,
 * status pesanan, jumlah pembayaran, biaya pengiriman, asuransi, diskon,
 * waktu pembayaran, catatan, kurir pengiriman, dan nomor pelacakan.
 */
return new class extends Migration
{
    /**
     * Menjalankan migrasi - membuat tabel pesanan
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();  // ID unik pesanan (auto-increment)
            $table->string('order_number')->unique();  // Nomor unik pesanan
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // ID pengguna yang membuat pesanan (relasi ke tabel users)
            $table->enum('status', ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'])->default('pending');  // Status pesanan
            $table->decimal('sub_total', 15, 2);  // Jumlah subtotal pesanan
            $table->decimal('shipping_cost', 15, 2);  // Biaya pengiriman
            $table->decimal('insurance_cost', 15, 2)->default(0);  // Biaya asuransi (default 0)
            $table->decimal('discount', 15, 2)->default(0);  // Diskon pesanan (default 0)
            $table->decimal('total_amount', 15, 2);  // Total jumlah pembayaran
            $table->timestamp('paid_at')->nullable();  // Waktu pembayaran dilakukan (opsional)
            $table->text('notes')->nullable();  // Catatan tambahan dari pembeli (opsional)
            $table->string('shipping_courier')->nullable();  // Kurir pengiriman (opsional)
            $table->string('tracking_number')->nullable();  // Nomor pelacakan pengiriman (opsional)
            $table->timestamps();  // Timestamp created_at dan updated_at
        });
    }

    /**
     * Mengembalikan migrasi - menghapus tabel pesanan
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
