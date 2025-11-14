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
        // Hapus kolom subcategory_id dengan raw query untuk menghindari masalah foreign key
        Schema::table('products', function ($table) {
            DB::statement('ALTER TABLE products DROP FOREIGN KEY IF EXISTS products_subcategory_id_foreign');
        });

        Schema::table('products', function (Blueprint $table) {
            // Hapus kolom subcategory_id
            if (Schema::hasColumn('products', 'subcategory_id')) {
                $table->dropColumn('subcategory_id');
            }

            // Tambahkan kolom subcategory string jika belum ada
            if (!Schema::hasColumn('products', 'subcategory')) {
                $table->string('subcategory')->nullable()->after('category_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Hapus kolom string subcategory
            if (Schema::hasColumn('products', 'subcategory')) {
                $table->dropColumn('subcategory');
            }

            // Kembalikan kolom subcategory_id
            if (!Schema::hasColumn('products', 'subcategory_id')) {
                $table->unsignedBigInteger('subcategory_id')->nullable();
            }
        });
    }
};