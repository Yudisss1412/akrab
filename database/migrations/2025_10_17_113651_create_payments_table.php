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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('payment_method'); // e.g., 'bank_transfer', 'e_wallet', 'cod'
            $table->enum('payment_status', ['pending', 'processing', 'success', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->string('transaction_id')->nullable(); // ID transaksi dari payment gateway
            $table->decimal('amount', 15, 2);
            $table->timestamp('paid_at')->nullable();
            $table->json('payment_gateway_response')->nullable(); // Menyimpan response dari payment gateway
            $table->timestamps();
            
            // Index untuk kinerja
            $table->index('order_id');
            $table->index('payment_status');
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
