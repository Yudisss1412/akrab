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
        Schema::create('product_promotions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('promotion_id');
            $table->decimal('discount_value', 10, 2)->nullable(); // Override nilai diskon jika berbeda dari promosi utama
            $table->timestamp('start_date'); // Tanggal mulai diskon produk (bisa berbeda dari tanggal promosi utama)
            $table->timestamp('end_date')->nullable(); // Tanggal selesai diskon produk
            $table->enum('status', ['active', 'inactive', 'expired'])->default('inactive');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('cascade');
            
            // Indexes untuk kinerja
            $table->index('product_id');
            $table->index('promotion_id');
            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_promotions');
    }
};
