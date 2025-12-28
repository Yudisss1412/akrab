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
        Schema::create('marketing_campaigns_tracking', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_name');
            $table->string('campaign_type')->nullable(); // email, social_media, banner, etc.
            $table->text('description')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->decimal('budget', 15, 2)->default(0);
            $table->integer('impressions')->default(0); // jumlah tayang
            $table->integer('clicks')->default(0); // jumlah klik
            $table->integer('conversions')->default(0); // jumlah konversi
            $table->decimal('revenue_generated', 15, 2)->default(0); // pendapatan yang dihasilkan
            $table->decimal('roi', 8, 2)->default(0); // return on investment
            $table->string('status')->default('active'); // active, completed, paused, cancelled
            $table->json('target_audience')->nullable(); // informasi target audiens
            $table->json('metrics')->nullable(); // metrik tambahan dalam format JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_campaigns_tracking');
    }
};
