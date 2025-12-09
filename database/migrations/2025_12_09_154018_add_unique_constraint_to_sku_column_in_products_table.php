<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pastikan tidak ada nilai SKU duplikat sebelum menambahkan constraint
        // Jika ada, kita update agar menjadi unik
        $duplicates = DB::select("
            SELECT sku, COUNT(*) as count
            FROM products
            WHERE sku IS NOT NULL AND sku != ''
            GROUP BY sku
            HAVING COUNT(*) > 1
        ");

        foreach ($duplicates as $duplicate) {
            // Ambil semua produk dengan SKU duplikat ini
            $products = DB::table('products')
                        ->where('sku', $duplicate->sku)
                        ->get();

            $counter = 1;
            foreach ($products as $product) {
                if ($counter === 1) {
                    // Biarkan yang pertama tetap sama
                    $counter++;
                    continue;
                }

                // Update SKU dengan menambahkan counter
                DB::table('products')
                  ->where('id', $product->id)
                  ->update(['sku' => $duplicate->sku . '-' . $counter]);

                $counter++;
            }
        }

        // Tambahkan unique index ke kolom SKU
        Schema::table('products', function (Blueprint $table) {
            $table->unique('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Hapus unique constraint dari kolom SKU
            $table->dropUnique(['sku']);
        });
    }
};
