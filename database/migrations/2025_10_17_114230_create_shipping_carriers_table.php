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
        Schema::create('shipping_carriers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama jasa pengiriman
            $table->string('code')->unique(); // Kode jasa pengiriman
            $table->text('description')->nullable(); // Deskripsi
            $table->string('logo')->nullable(); // Path ke logo
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            // Index untuk kinerja
            $table->index('code');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_carriers');
    }
};
