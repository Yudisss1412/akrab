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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2);
            $table->integer('stock')->default(0);
            $table->decimal('weight', 8, 2); // in grams
            $table->string('image')->nullable(); // path to image
            $table->foreignId('category_id')->nullable();
            $table->foreignId('seller_id');
            $table->enum('status', ['active', 'inactive', 'draft'])->default('draft');
            $table->timestamps();
            
            // Tambahkan indeks untuk kinerja
            $table->index('category_id');
            $table->index('seller_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
