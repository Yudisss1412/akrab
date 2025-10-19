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
        Schema::table('product_images', function (Blueprint $table) {
            // Hapus field lama jika ada
            if (Schema::hasColumn('product_images', 'path')) {
                $table->renameColumn('path', 'image_path');
            }
            
            // Tambahkan field yang diperlukan jika belum ada
            if (!Schema::hasColumn('product_images', 'alt_text')) {
                $table->string('alt_text')->nullable()->after('image_path');
            }
            
            // Hapus field yang tidak diperlukan
            if (Schema::hasColumn('product_images', 'is_primary')) {
                $table->dropColumn('is_primary');
            }
            
            if (Schema::hasColumn('product_images', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            // Kembalikan field lama jika ada
            if (Schema::hasColumn('product_images', 'image_path')) {
                $table->renameColumn('image_path', 'path');
            }
            
            // Hapus field yang ditambahkan
            if (Schema::hasColumn('product_images', 'alt_text')) {
                $table->dropColumn('alt_text');
            }
        });
    }
};
