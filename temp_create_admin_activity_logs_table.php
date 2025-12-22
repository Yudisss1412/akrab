<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

// Membuat tabel admin_activity_logs sesuai dengan definisi migration
if (!Schema::hasTable('admin_activity_logs')) {
    Schema::create('admin_activity_logs', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->string('activity');
        $table->text('description')->nullable();
        $table->string('status')->default('success');
        $table->string('ip_address')->nullable();
        $table->text('user_agent')->nullable();
        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
    
    echo "Tabel admin_activity_logs berhasil dibuat.\n";
} else {
    echo "Tabel admin_activity_logs sudah ada.\n";
}