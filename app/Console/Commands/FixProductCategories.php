<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;

class FixProductCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fix-categories {--product-name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix products without valid categories or subcategories. Use --product-name to fix specific product.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $productName = $this->option('product-name');

        $this->info('Memulai proses perbaikan kategori dan subkategori produk...');

        // Cek apakah kolom subcategory_id ada di tabel produk
        $hasSubcategoryIdColumn = Schema::hasColumn('products', 'subcategory_id');

        // Ambil semua kategori dan subkategori yang tersedia
        $categories = Category::all();
        $subcategories = Subcategory::all();

        if ($categories->isEmpty()) {
            $this->error('Tidak ada kategori yang ditemukan di database.');
            return;
        }

        if ($productName) {
            // Perbaiki produk spesifik
            $products = Product::where('name', 'like', "%{$productName}%")->get();
            $this->info("Mencari produk dengan nama yang mengandung '{$productName}'. Ditemukan {$products->count()} produk.");

            if ($products->isEmpty()) {
                $this->info("Tidak ada produk ditemukan dengan nama mengandung '{$productName}'.");
                return;
            }
        } else {
            // Ambil produk-produk yang tidak memiliki kategori valid
            $products = Product::whereDoesntHave('category')
                              ->orWhereNull('category_id')
                              ->get();

            $this->info("Menemukan {$products->count()} produk tanpa kategori valid.");
        }

        $updatedCount = 0;
        foreach ($products as $product) {
            // Pilih kategori secara acak
            $randomCategory = $categories->random();

            // Pilih subkategori secara acak
            $randomSubcategory = null;
            if ($hasSubcategoryIdColumn) {
                // Cek apakah kolom subcategory_id ada, maka gunakan pendekatan relasi
                $categorySubcategories = $subcategories->where('category_id', $randomCategory->id);

                $product->category_id = $randomCategory->id;

                if ($categorySubcategories->isNotEmpty()) {
                    $randomSubcategory = $categorySubcategories->random();
                    $product->subcategory_id = $randomSubcategory->id;
                } else {
                    $product->subcategory_id = null;
                }
            } else {
                // Jika kolom subcategory_id tidak ada, gunakan pendekatan field subcategory
                $product->category_id = $randomCategory->id;

                // Pilih subkategori acak dari semua subkategori
                if ($subcategories->isNotEmpty()) {
                    $randomSubcategory = $subcategories->random();
                    $product->subcategory = $randomSubcategory->name;
                }
            }

            $product->save();
            $updatedCount++;

            $this->info("Produk ID {$product->id} ({$product->name}) diperbarui dengan kategori: {$randomCategory->name}" .
                        ($randomSubcategory ? " dan subkategori: " . $randomSubcategory->name : ""));
        }

        // Jika tidak memperbaiki produk spesifik, lanjutkan dengan produk lain
        if (!$productName) {
            // Sekarang periksa produk-produk dengan kategori valid tapi subkategori tidak valid
            if ($hasSubcategoryIdColumn) {
                $productsWithoutSubcategory = Product::whereHas('category') // Pastikan kategori valid
                                                     ->where(function($query) {
                                                         $query->whereDoesntHave('subcategory')
                                                               ->orWhereNull('subcategory_id');
                                                     })
                                                     ->get();

                $this->info("Menemukan {$productsWithoutSubcategory->count()} produk dengan kategori valid tetapi subkategori tidak valid (relasi).");

                foreach ($productsWithoutSubcategory as $product) {
                    // Cari subkategori yang sesuai dengan kategori produk ini
                    $categorySubcategories = $subcategories->where('category_id', $product->category_id);

                    if ($categorySubcategories->isNotEmpty()) {
                        $randomSubcategory = $categorySubcategories->random();
                        $product->subcategory_id = $randomSubcategory->id;
                        $product->save();
                        $updatedCount++;

                        $this->info("Produk ID {$product->id} ({$product->name}) diperbarui dengan subkategori: {$randomSubcategory->name}");
                    }
                }

                // Juga cari produk-produk yang memiliki field 'subcategory' bernilai 'Umum'
                $productsWithUmumSubcategory = Product::whereHas('category')
                                                      ->where('subcategory', 'Umum')
                                                      ->get();

                $this->info("Menemukan {$productsWithUmumSubcategory->count()} produk dengan kategori valid tapi subkategori bernilai 'Umum' (field string).");

                foreach ($productsWithUmumSubcategory as $product) {
                    // Cari subkategori yang sesuai dengan kategori produk ini
                    $categorySubcategories = $subcategories->where('category_id', $product->category_id);

                    if ($categorySubcategories->isNotEmpty()) {
                        $randomSubcategory = $categorySubcategories->random();
                        $product->subcategory = $randomSubcategory->name;
                        $product->save();
                        $updatedCount++;

                        $this->info("Produk ID {$product->id} ({$product->name}) diperbarui field subcategory menjadi: {$randomSubcategory->name}");
                    }
                }
            } else {
                // Jika kolom subcategory_id tidak ada, periksa field subcategory
                $productsWithoutSubcategory = Product::whereNotNull('category_id')
                                                     ->where(function($query) {
                                                         $query->whereNull('subcategory')
                                                               ->orWhere('subcategory', '')
                                                               ->orWhere('subcategory', 'Umum');
                                                     })
                                                     ->get();

                $this->info("Menemukan {$productsWithoutSubcategory->count()} produk dengan kategori valid tetapi subkategori tidak valid (field subcategory).");

                foreach ($productsWithoutSubcategory as $product) {
                    // Cari subkategori yang sesuai dengan kategori produk ini
                    $categorySubcategories = $subcategories->where('category_id', $product->category_id);

                    if ($categorySubcategories->isNotEmpty()) {
                        $randomSubcategory = $categorySubcategories->random();
                        $product->subcategory = $randomSubcategory->name;
                        $product->save();
                        $updatedCount++;

                        $this->info("Produk ID {$product->id} ({$product->name}) diperbarui dengan subkategori: {$randomSubcategory->name}");
                    }
                }
            }
        }

        // Sekarang mari kita perbaiki juga produk-produk yang memiliki nama toko 'Toko Tes'
        $this->info("Memeriksa produk-produk dengan nama toko yang perlu diperbaiki...");

        // Ambil semua seller yang valid dari database
        $sellers = \App\Models\Seller::all();
        if ($sellers->isNotEmpty()) {
            $productsWithTestShop = Product::whereHas('seller', function($q) {
                $q->where('store_name', 'like', '%Toko Tes%');
            })->orWhereHas('seller', function($q) {
                $q->where('store_name', 'Toko Tes');
            })->get();

            $this->info("Menemukan {$productsWithTestShop->count()} produk dengan seller yang mungkin bernama 'Toko Tes' atau serupa.");

            foreach ($productsWithTestShop as $product) {
                // Ambil seller acak yang valid dari database
                $randomSeller = $sellers->random();
                $product->seller_id = $randomSeller->id;
                $product->save();

                $updatedCount++;
                $this->info("Produk ID {$product->id} ({$product->name}) diperbarui seller menjadi: {$randomSeller->store_name}");
            }
        } else {
            $this->info("Tidak ditemukan seller di database.");
        }

        $this->info("Proses selesai. {$updatedCount} produk telah diperbarui.");
    }
}