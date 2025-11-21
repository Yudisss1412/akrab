<?php
require_once '../vendor/autoload.php';

// Create a simple script to check product categories
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $results = DB::select('
        SELECT c.name as category_name, COUNT(p.id) as product_count 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        GROUP BY c.name
        ORDER BY product_count DESC
    ');
    
    echo "Jumlah produk per kategori:\n";
    foreach ($results as $row) {
        echo $row->category_name . ': ' . $row->product_count . " produk\n";
    }
    
    if (empty($results)) {
        echo "Tidak ada produk ditemukan di database.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Clean up the temporary file
unlink(__FILE__);