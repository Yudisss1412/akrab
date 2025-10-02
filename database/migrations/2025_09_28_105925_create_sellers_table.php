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
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('store_name');
            $table->string('owner_name');
            $table->string('email');
            $table->enum('status', ['aktif', 'ditangguhkan', 'menunggu_verifikasi', 'baru'])->default('baru');
            $table->date('join_date')->nullable();
            $table->integer('active_products_count')->default(0);
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
