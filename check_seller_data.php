<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Set up Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check if sellers table exists and get count
if (Schema::hasTable('sellers')) {
    $totalSellers = DB::table('sellers')->count();
    echo "Total sellers in database: " . $totalSellers . "\n";
    
    // Check recent sellers
    $recentSellers = DB::table('sellers')->orderBy('created_at', 'desc')->limit(5)->get();
    echo "Recent sellers:\n";
    foreach ($recentSellers as $seller) {
        // Check what fields are available
        $name = isset($seller->name) ? $seller->name : (isset($seller->store_name) ? $seller->store_name : 'N/A');
        echo "- ID: {$seller->id}, Name: {$name}, Created: {$seller->created_at}\n";
    }
} else {
    echo "Table 'sellers' does not exist\n";
}