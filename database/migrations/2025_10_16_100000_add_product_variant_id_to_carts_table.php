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
        Schema::table('carts', function (Blueprint $table) {
            // Tambahkan kolom product_variant_id tanpa constraint dulu
            $table->unsignedBigInteger('product_variant_id')->nullable()->after('product_id');
            
            // Hapus unique constraint lama
            $table->dropUnique(['user_id', 'product_id']);
            
            // Tambahkan foreign key constraint untuk product_variant_id
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            
            // Tambahkan unique constraint baru
            $table->unique(['user_id', 'product_id', 'product_variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Hapus unique constraint baru
            $table->dropUnique(['user_id', 'product_id', 'product_variant_id']);
            
            // Hapus foreign key constraint
            $table->dropForeign(['product_variant_id']);
            
            // Hapus kolom
            $table->dropColumn('product_variant_id');
            
            // Kembalikan unique constraint lama
            $table->unique(['user_id', 'product_id']);
        });
    }
};