<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Buat tabel subcategories jika belum ada
        if (!Schema::hasTable('subcategories')) {
            Schema::create('subcategories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedBigInteger('category_id'); // Relasi ke categories.id
                $table->timestamps();
                
                // Foreign key constraint
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            });
        }
        
        // Tambah kolom subcategory_id di tabel products jika belum ada
        if (!Schema::hasColumn('products', 'subcategory_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('subcategory_id')->nullable(); // Tambah kolom baru
                $table->foreign('subcategory_id')->references('id')->on('subcategories')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        // Hapus foreign key dan kolom subcategory_id dari products
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['subcategory_id']);
            $table->dropColumn('subcategory_id');
        });
        
        // Hapus tabel subcategories
        Schema::dropIfExists('subcategories');
    }
};