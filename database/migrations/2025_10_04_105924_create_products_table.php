<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel produk
 *
 * Migration ini membuat tabel 'products' yang menyimpan informasi produk
 * dalam sistem e-commerce AKRAB, termasuk nama, deskripsi, harga, stok,
 * berat, gambar, kategori, penjual, dan status produk.
 */
return new class extends Migration
{
    /**
     * Menjalankan migrasi - membuat tabel produk
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();  // ID unik produk (auto-increment)
            $table->string('name');  // Nama produk
            $table->text('description')->nullable();  // Deskripsi produk (opsional)
            $table->decimal('price', 15, 2);  // Harga produk (maksimal 15 digit, 2 desimal)
            $table->integer('stock')->default(0);  // Stok produk (default 0)
            $table->decimal('weight', 8, 2); // Berat produk dalam gram
            $table->string('image')->nullable(); // Path ke gambar produk
            $table->foreignId('category_id')->nullable(); // ID kategori produk (relasi ke tabel categories)
            $table->foreignId('seller_id'); // ID penjual yang menjual produk (relasi ke tabel sellers)
            $table->enum('status', ['active', 'inactive', 'draft'])->default('draft'); // Status produk
            $table->timestamps(); // Timestamp created_at dan updated_at

            // Tambahkan indeks untuk kinerja
            $table->index('category_id');  // Indeks untuk pencarian berdasarkan kategori
            $table->index('seller_id');    // Indeks untuk pencarian berdasarkan penjual
            $table->index('status');       // Indeks untuk pencarian berdasarkan status
        });
    }

    /**
     * Mengembalikan migrasi - menghapus tabel produk
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
