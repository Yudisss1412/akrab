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
        Schema::create('violation_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number')->unique(); // Nomor laporan unik
            $table->unsignedBigInteger('reporter_user_id'); // User yang melaporkan
            $table->unsignedBigInteger('violator_user_id'); // User yang dilaporkan (penjual)
            $table->unsignedBigInteger('product_id')->nullable(); // Produk terkait jika ada
            $table->unsignedBigInteger('order_id')->nullable(); // Order terkait jika ada
            $table->enum('violation_type', ['product', 'content', 'scam', 'copyright', 'other', 'spam', 'terms_violation']); // Jenis pelanggaran
            $table->text('description'); // Deskripsi pelanggaran
            $table->text('evidence')->nullable(); // Bukti pelanggaran (dalam bentuk JSON array path file)
            $table->enum('status', ['pending', 'investigating', 'resolved', 'dismissed'])->default('pending'); // Status laporan
            $table->text('admin_notes')->nullable(); // Catatan admin
            $table->unsignedBigInteger('handled_by')->nullable(); // Admin yang menangani
            $table->timestamp('handled_at')->nullable(); // Tanggal ditangani
            $table->enum('resolution', ['warning', 'suspension', 'permanent_ban', 'fine', 'none'])->nullable(); // Resolusi
            $table->decimal('fine_amount', 10, 2)->nullable(); // Jumlah denda jika ada
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            // Foreign keys
            $table->foreign('reporter_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('violator_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('handled_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('violation_reports');
    }
};
