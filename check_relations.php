<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check relationships
try {
    $product = App\Models\Product::with('category', 'seller')->first();
    if ($product && $product->category) {
        echo 'Sample product with category: ' . $product->category->name . PHP_EOL;
    } else {
        echo 'No product with category found' . PHP_EOL;
    }
    
    if ($product && $product->seller) {
        echo 'Sample product with seller: ' . $product->seller->store_name . PHP_EOL;
    } else {
        echo 'No product with seller found' . PHP_EOL;
    }

    $seller = App\Models\Seller::withCount('products')->first();
    if ($seller) {
        echo 'Sample seller with products count: ' . $seller->products_count . PHP_EOL;
    } else {
        echo 'No seller found' . PHP_EOL;
    }

    $category = App\Models\Category::withCount('products')->first();
    if ($category) {
        echo 'Sample category with products count: ' . $category->products_count . PHP_EOL;
    } else {
        echo 'No category found' . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}