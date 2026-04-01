<?php
require __DIR__.'/vendor/autoload.php';
$app = (require __DIR__.'/bootstrap/app.php')->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

echo "=== Testing Category Filter ===\n\n";

$products = Product::whereHas('seller')
    ->whereHas('category', function($q) {
        $q->whereRaw('LOWER(name) = ?', ['kuliner']);
    })
    ->get();

echo "Found: " . $products->count() . " products\n\n";
foreach($products as $p) {
    echo "- " . $p->name . " (Category: " . $p->category->name . ")\n";
}
