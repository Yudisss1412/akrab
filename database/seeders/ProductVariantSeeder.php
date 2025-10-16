<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductVariant;
use App\Models\Product;

class ProductVariantSeeder extends Seeder
{
    use WithoutModelEvents;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure products exist before creating variants
        $products = Product::all();
        if ($products->count() === 0) {
            $this->command->info('No products found, calling ProductSeeder to create products...');
            $this->call(ProductSeeder::class);
            $products = Product::all();
        }

        // Prepare variants data
        $variantsData = [
            // For product 0: Kaos Polos Premium
            [
                'product_id' => $products[0]->id ?? null,
                'name' => 'S (Small)',
                'additional_price' => 0,
                'stock' => 30,
            ],
            [
                'product_id' => $products[0]->id ?? null,
                'name' => 'M (Medium)',
                'additional_price' => 0,
                'stock' => 35,
            ],
            [
                'product_id' => $products[0]->id ?? null,
                'name' => 'L (Large)',
                'additional_price' => 0,
                'stock' => 35,
            ],
            
            // For product 1: Celana Jeans Premium
            [
                'product_id' => $products[1]->id ?? null,
                'name' => '30 inch',
                'additional_price' => 0,
                'stock' => 15,
            ],
            [
                'product_id' => $products[1]->id ?? null,
                'name' => '32 inch',
                'additional_price' => 0,
                'stock' => 20,
            ],
            [
                'product_id' => $products[1]->id ?? null,
                'name' => '34 inch',
                'additional_price' => 0,
                'stock' => 15,
            ],
            
            // For product 2: Topi Baseball Trendy
            [
                'product_id' => $products[2]->id ?? null,
                'name' => 'Red',
                'additional_price' => 0,
                'stock' => 25,
            ],
            [
                'product_id' => $products[2]->id ?? null,
                'name' => 'Black',
                'additional_price' => 0,
                'stock' => 25,
            ],
            [
                'product_id' => $products[2]->id ?? null,
                'name' => 'Blue',
                'additional_price' => 0,
                'stock' => 25,
            ],
            
            // For product 3: Sepatu Sneakers Casual
            [
                'product_id' => $products[3]->id ?? null,
                'name' => 'Size 40',
                'additional_price' => 0,
                'stock' => 10,
            ],
            [
                'product_id' => $products[3]->id ?? null,
                'name' => 'Size 41',
                'additional_price' => 0,
                'stock' => 10,
            ],
            [
                'product_id' => $products[3]->id ?? null,
                'name' => 'Size 42',
                'additional_price' => 0,
                'stock' => 10,
            ],
        ];

        foreach ($variantsData as $variantData) {
            if ($variantData['product_id']) {
                ProductVariant::create($variantData);
            }
        }
    }
}