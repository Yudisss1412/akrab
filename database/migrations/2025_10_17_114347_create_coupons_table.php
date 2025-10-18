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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Kode kupon
            $table->enum('discount_type', ['percentage', 'fixed_amount']); // Jenis diskon
            $table->decimal('discount_value', 10, 2); // Nilai diskon
            $table->decimal('min_order_amount', 15, 2)->default(0); // Minimum pembelian
            $table->decimal('max_discount_amount', 15, 2)->nullable(); // Maksimum diskon (jika jenisnya persentase)
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable(); // Diubah menjadi nullable untuk menghindari error
            $table->integer('usage_limit')->default(0); // Batas penggunaan 0 = unlimited
            $table->integer('used_count')->default(0); // Jumlah penggunaan saat ini
            $table->enum('status', ['active', 'inactive', 'expired'])->default('inactive');
            $table->timestamps();
            
            // Index untuk kinerja
            $table->index('code');
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
        Schema::dropIfExists('coupons');
    }
};
