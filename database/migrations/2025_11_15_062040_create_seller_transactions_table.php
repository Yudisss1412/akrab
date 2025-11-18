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
        Schema::create('seller_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('sellers')->onDelete('cascade');
            $table->string('transaction_type'); // 'sale', 'withdrawal', 'refund', 'commission', 'fee', 'adjustment'
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('reference_type')->nullable(); // 'order', 'withdrawal', 'payment', etc.
            $table->unsignedBigInteger('reference_id')->nullable(); // ID of the related record
            $table->text('description')->nullable();
            $table->timestamp('transaction_date')->nullable();
            $table->enum('status', ['completed', 'pending', 'failed', 'reversed'])->default('completed');
            $table->json('metadata')->nullable(); // Additional transaction data
            $table->timestamps();

            // Indexes for performance
            $table->index('seller_id');
            $table->index('transaction_type');
            $table->index('status');
            $table->index('transaction_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_transactions');
    }
};
