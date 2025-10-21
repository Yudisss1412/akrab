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
        Schema::table('products', function (Blueprint $table) {
            // Kolom untuk detail produk
            $table->json('specifications')->nullable()->after('description'); // Spesifikasi produk dalam format JSON
            $table->text('material')->nullable()->after('specifications'); // Bahan produk
            $table->string('size')->nullable()->after('material'); // Ukuran produk
            $table->string('color')->nullable()->after('size'); // Warna produk
            $table->string('brand')->nullable()->after('color'); // Merek produk
            $table->json('features')->nullable()->after('brand'); // Fitur-fitur produk
            $table->json('additional_images')->nullable()->after('features'); // Gambar tambahan produk
            $table->integer('min_order')->default(1)->after('additional_images'); // Jumlah minimum pemesanan
            $table->integer('ready_stock')->default(0)->after('min_order'); // Stok yang siap dikirim
            $table->string('origin')->nullable()->after('ready_stock'); // Asal produk
            $table->string('warranty')->nullable()->after('origin'); // Garansi produk
            $table->integer('view_count')->default(0)->after('warranty'); // Jumlah dilihat
            $table->decimal('discount_price', 15, 2)->nullable()->after('view_count'); // Harga setelah diskon
            $table->date('discount_start_date')->nullable()->after('discount_price'); // Tanggal mulai diskon
            $table->date('discount_end_date')->nullable()->after('discount_start_date'); // Tanggal akhir diskon
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'specifications',
                'material',
                'size',
                'color',
                'brand',
                'features',
                'additional_images',
                'min_order',
                'ready_stock',
                'origin',
                'warranty',
                'view_count',
                'discount_price',
                'discount_start_date',
                'discount_end_date'
            ]);
        });
    }
};
