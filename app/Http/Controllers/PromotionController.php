<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\ProductPromotion;
use App\Models\Seller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

// ========================================================================
// PROMOTION CONTROLLER - KELOLA PROMOSI & DISKON (SELLER)
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Controller ini menangani manajemen promosi untuk seller
// - Seller bisa buat voucher diskon & diskon produk
// - Fitur standar e-commerce untuk meningkatkan penjualan
//
// FITUR UTAMA:
// 1. Voucher Management - Seller buat voucher diskon (kode promo)
// 2. Product Discount - Diskon langsung untuk produk tertentu
// 3. Promotion Types - Percentage (%) atau Fixed Amount (Rp)
// 4. Time-Based - Start date & end date untuk promosi
// 5. Usage Limit - Batasi penggunaan voucher (quota)
// 6. Min Order Amount - Minimal pembelian untuk pakai voucher
//
// JENIS PROMOSI:
// - Voucher: Kode promo yang bisa dipakai customer (misal: DISKON50)
// - Product Discount: Diskon langsung untuk produk tertentu
// - Free Shipping: Gratis ongkir (akan dikembangkan)
//
// VALIDASI:
// - End date harus setelah start date
// - Discount value harus >= 0
// - Seller hanya bisa manage promosi mereka sendiri
// - Max discount amount untuk percentage discount
// ========================================================================

class PromotionController extends Controller
{
    /**
     * Display the promotions management page.
     * 
     * ==========================================================================
     * FITUR: MANAJEMEN PROMOSI - DASHBOARD PENJUAL
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini tampilkan dashboard promosi untuk seller
     * - Tampil 2 jenis promosi: Voucher & Product Discount
     * - Statistik promosi (active, inactive, expired)
     * 
     * DATA YANG DITAMPILKAN:
     * 1. Vouchers - Voucher codes yang dibuat seller
     * 2. Product Discounts - Diskon untuk produk tertentu
     * 3. Status Statistics - Jumlah promosi per status
     * 
     * VALIDASI:
     * - Hanya seller yang bisa akses
     * - Filter promosi berdasarkan seller_id
     * - Exclude product promotions dari voucher list
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // ========================================
            // STEP 1: CEK AUTHENTICATION
            // ========================================
            // Debug: Check if user is authenticated
            if (!Auth::check()) {
                abort(403, 'Akses ditolak. Anda harus login terlebih dahulu.');
            }

            // ========================================
            // STEP 2: AMBIL SELLER RECORD
            // ========================================
            // Ambil seller berdasarkan user_id yang login
            $seller = Seller::where('user_id', Auth::id())->first();
            \Log::info('Authenticated user ID: ' . Auth::id());
            \Log::info('Seller found: ' . ($seller ? 'Yes, ID: ' . $seller->id : 'No'));

            // ========================================
            // STEP 3: HANDLE JIKA SELLER TIDAK DITEMUKAN
            // ========================================
            // If no seller found, create a helpful error message
            if (!$seller) {
                \Log::warning('No seller found for user ID: ' . Auth::id());
                // Return view with empty data jika tidak ada seller
                $vouchers = collect([]);
                $productDiscounts = collect([]);
                $statusData = ['active' => 0, 'inactive' => 0, 'expired' => 0];
                $productStatusData = ['active' => 0, 'inactive' => 0, 'expired' => 0];

                return view('penjual.manajemen_promosi', compact(
                    'vouchers',
                    'productDiscounts',
                    'statusData',
                    'productStatusData'
                ));
            }

            // ========================================
            // STEP 4: QUERY VOUCHERS (NON-PRODUCT PROMOTIONS)
            // ========================================
            // Get all promotions that are NOT associated with any product (pure vouchers) for this seller
            // Subquery: Exclude promotions yang punya product_promotions
            $vouchers = Promotion::where('seller_id', $seller->id)
                               ->whereNotIn('id', function($query) {
                                   $query->select('promotion_id')
                                         ->from('product_promotions')
                                         ->whereNotNull('promotion_id');
                               })
                               ->orderBy('created_at', 'desc')
                               ->get();

            \Log::info('Fetched ' . $vouchers->count() . ' vouchers for seller: ' . $seller->id);

            // ========================================
            // STEP 5: QUERY PRODUCT DISCOUNTS
            // ========================================
            // Get all product promotions for this seller
            // Ambil semua product IDs milik seller ini dulu
            $sellerProductIds = Product::where('seller_id', $seller->id)->pluck('id');
            \Log::info('Seller product IDs: ' . $sellerProductIds->count() . ' items');
            \Log::info('Seller product IDs list: ' . json_encode($sellerProductIds->toArray()));

            // Get all product promotions associated with seller's products
            // Load relasi product & promotion untuk info lengkap
            $productDiscounts = ProductPromotion::with(['product', 'promotion'])
                               ->whereIn('product_id', $sellerProductIds)
                               ->orderBy('created_at', 'desc')
                               ->get();

            \Log::info('Fetched ' . $productDiscounts->count() . ' product discounts');

            // Log each product discount untuk debugging
            foreach($productDiscounts as $pd) {
                \Log::info('Product discount ID: ' . $pd->id . ', Product ID: ' . $pd->product_id . ', Product: ' . ($pd->product ? $pd->product->name : 'NULL') . ', Promotion: ' . ($pd->promotion ? $pd->promotion->name : 'NULL'));
            }

            // ========================================
            // STEP 6: CALCULATE STATUS STATISTICS
            // ========================================
            // Calculate statistics untuk different promotion statuses (untuk vouchers only)
            $statusCounts = Promotion::where('seller_id', $seller->id)
                                    ->whereNotIn('id', function($query) {
                                        $query->select('promotion_id')
                                              ->from('product_promotions')
                                              ->whereNotNull('promotion_id');
                                    })
                                    ->selectRaw('status, count(*) as count')
                                    ->groupBy('status')
                                    ->pluck('count', 'status');

            // Define all possible statuses dengan default values
            $allStatus = ['active', 'inactive', 'expired'];
            $statusData = [];
            foreach ($allStatus as $status) {
                $statusData[$status] = $statusCounts[$status] ?? 0;
            }

            // ========================================
            // STEP 7: CALCULATE PRODUCT DISCOUNT STATISTICS
            // ========================================
            // Calculate status counts untuk product discounts
            // Join dengan products untuk filter berdasarkan seller
            $productStatusCounts = ProductPromotion::join('products', 'product_promotions.product_id', '=', 'products.id')
            ->whereIn('products.id', $sellerProductIds)  // Use the already calculated seller product IDs
            ->selectRaw('product_promotions.status, count(*) as count')
            ->groupBy('product_promotions.status')
            ->pluck('count', 'status');

            $productStatusData = [];
            foreach ($allStatus as $status) {
                $productStatusData[$status] = $productStatusCounts[$status] ?? 0;
            }

            \Log::info('Final counts - Vouchers: ' . $vouchers->count() . ', Product Discounts: ' . $productDiscounts->count());

            // ========================================
            // STEP 8: RETURN VIEW
            // ========================================
            // Return view manajemen promosi dengan semua data
            return view('penjual.manajemen_promosi', compact(
                'vouchers',
                'productDiscounts',
                'statusData',
                'productStatusData'
            ));
        } catch (\Exception $e) {
            // ========================================
            // STEP 9: HANDLE ERROR
            // ========================================
            // Log the error untuk debugging
            \Log::error('Error in PromotionController@index: ' . $e->getMessage());

            // Return with empty data untuk prevent undefined variable errors
            return view('penjual.manajemen_promosi', [
                'vouchers' => collect([]),
                'productDiscounts' => collect([]),
                'statusData' => ['active' => 0, 'inactive' => 0, 'expired' => 0],
                'productStatusData' => ['active' => 0, 'inactive' => 0, 'expired' => 0]
            ]);
        }
    }

    /**
     * Display the create promotion page for discounts.
     * 
     * ==========================================================================
     * FITUR: FORM CREATE DISKON PRODUK
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini tampilkan form untuk seller buat diskon produk
     * - Seller pilih produk yang mau didiskon
     * - Input discount value, start date, end date
     * 
     * @return \Illuminate\View\View
     */
    public function createDiscount()
    {
        // ========================================
        // STEP 1: AMBIL SELLER RECORD
        // ========================================
        $seller = Seller::where('user_id', Auth::id())->first();

        if (!$seller) {
            \Log::error('Seller not found for user ID: ' . Auth::id() . ' when trying to access create discount page');
            abort(403, 'Seller tidak ditemukan. Silakan cek profil penjual Anda.');
        }

        // ========================================
        // STEP 2: GET ACTIVE PRODUCTS
        // ========================================
        // Get products untuk this seller (hanya yang active)
        $products = Product::where('seller_id', $seller->id)
                          ->where('status', 'active')
                          ->select('id', 'name')
                          ->get();

        // ========================================
        // STEP 3: RETURN VIEW
        // ========================================
        return view('penjual.promosi.create_discount', compact('products'));
    }

    /**
     * Display the create promotion page for vouchers.
     * 
     * ==========================================================================
     * FITUR: FORM CREATE VOUCHER
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini tampilkan form untuk seller buat voucher
     * - Seller input voucher code, discount, validity period
     * 
     * @return \Illuminate\View\View
     */
    public function createVoucher()
    {
        return view('penjual.promosi.create_voucher');
    }

    /**
     * Store a newly created discount promotion in storage.
     * 
     * ==========================================================================
     * FITUR: STORE DISKON PRODUK - SIMPAN KE DATABASE
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini handle submit form diskon produk
     * - Buat 2 records: Promotion (base) + ProductPromotion (product-specific)
     * - Validasi: end_date > start_date, discount >= 0
     * 
     * FLOW:
     * 1. Validasi input form
     * 2. Ambil seller record
     * 3. Buat Promotion record (base promotion)
     * 4. Loop product_ids → Buat ProductPromotion untuk setiap produk
     * 5. Set status berdasarkan tanggal (active/inactive)
     * 
     * VALIDASI:
     * - name: Required, max 255
     * - type: percentage atau fixed_amount
     * - discount_value: Min 0
     * - start_date & end_date: Required, end > start
     * - product_ids: Array of product IDs (minimal 1)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeDiscount(Request $request)
    {
        // ========================================
        // STEP 1: LOG REQUEST DATA
        // ========================================
        // Log data yang diterima untuk debugging
        \Log::info('Store discount request data:', $request->all());

        // ========================================
        // STEP 2: VALIDASI INPUT
        // ========================================
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        // ========================================
        // STEP 3: AMBIL SELLER RECORD
        // ========================================
        $seller = Seller::where('user_id', Auth::id())->first();

        if (!$seller) {
            \Log::error('Seller not found for user ID: ' . Auth::id());
            return redirect()->back()->with('error', 'Seller tidak ditemukan. Silakan cek profil penjual Anda.');
        }

        \Log::info('Creating discount for seller: ' . $seller->id . ' with data: ' . json_encode($request->all()));

        // ========================================
        // STEP 4: BUAT BASE PROMOTION RECORD
        // ========================================
        // Create a base promotion record
        $promotion = Promotion::create([
            'name' => $request->name . ' (Diskon Produk)',
            'type' => $request->type,
            'category' => 'product_discount',  // Explicitly set category untuk product discount
            'code' => strtoupper(substr(uniqid(), -8)), // Generate unique code untuk tracking
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'seller_id' => $seller->id,
            'status' => Carbon::now()->between($request->start_date, $request->end_date) ? 'active' : 'inactive',
        ]);

        \Log::info('Base promotion created successfully with ID: ' . $promotion->id);

        // ========================================
        // STEP 5: BUAT PRODUCT PROMOTION RECORDS
        // ========================================
        // Create product promotion records untuk each selected product
        \Log::info('Creating product promotions for discount. Total products: ' . count($request->product_ids));

        foreach ($request->product_ids as $productId) {
            \Log::info('Processing product ID: ' . $productId);

            // ========================================
            // STEP 5A: VALIDASI PRODUCT OWNERSHIP
            // ========================================
            // Verify the product belongs to the seller
            $product = Product::where('id', $productId)->where('seller_id', $seller->id)->first();

            if ($product) {
                // ========================================
                // STEP 5B: BUAT PRODUCT PROMOTION
                // ========================================
                $productPromotion = ProductPromotion::create([
                    'product_id' => $productId,
                    'promotion_id' => $promotion->id,
                    'discount_value' => $request->discount_value,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'status' => Carbon::now()->between($request->start_date, $request->end_date) ? 'active' : 'inactive',
                ]);

                \Log::info('Created ProductPromotion: ' . $productPromotion->id . ' for product: ' . $productId . ' and promotion: ' . $promotion->id);
            } else {
                \Log::warning('Product ID ' . $productId . ' does not belong to seller ID ' . $seller->id);
            }
        }

        // ========================================
        // STEP 6: RETURN DENGAN SUCCESS MESSAGE
        // ========================================
        return redirect()->route('penjual.promosi')->with('success', 'Diskon produk berhasil dibuat.');
    }

    /**
     * Store a newly created voucher in storage.
     * 
     * ==========================================================================
     * FITUR: STORE VOUCHER - SIMPAN KE DATABASE
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini handle submit form voucher
     * - Voucher adalah kode promo yang bisa dipakai customer
     * - Berbeda dengan product discount yang langsung apply ke produk
     * 
     * FLOW:
     * 1. Validasi input form
     * 2. Ambil seller record
     * 3. Buat Promotion record dengan category = 'voucher'
     * 4. Set status berdasarkan tanggal
     * 
     * VALIDASI:
     * - code: Required, unique di tabel promotions
     * - type: percentage, fixed_amount, atau free_shipping
     * - min_order_amount: Minimal pembelian untuk pakai voucher
     * - max_discount_amount: Max discount untuk percentage type
     * - usage_limit: Batas penggunaan voucher (0 = unlimited)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeVoucher(Request $request)
    {
        // ========================================
        // STEP 1: VALIDASI INPUT
        // ========================================
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed_amount,free_shipping',
            'code' => 'required|string|unique:promotions,code',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
        ]);

        // ========================================
        // STEP 2: AMBIL SELLER RECORD
        // ========================================
        $seller = Seller::where('user_id', Auth::id())->first();

        if (!$seller) {
            \Log::error('Seller not found for user ID: ' . Auth::id());
            return redirect()->back()->with('error', 'Seller tidak ditemukan. Silakan cek profil penjual Anda.');
        }

        \Log::info('Creating voucher for seller: ' . $seller->id . ' with data: ' . json_encode($request->all()));

        // ========================================
        // STEP 3: BUAT VOUCHER RECORD
        // ========================================
        $promotion = Promotion::create([
            'name' => $request->name,
            'type' => $request->type,
            'category' => 'voucher',  // Explicitly set category untuk voucher
            'code' => $request->code,
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'max_discount_amount' => $request->max_discount_amount,
            'usage_limit' => $request->usage_limit ?? 0,
            'seller_id' => $seller->id,
            'status' => Carbon::now()->between($request->start_date, $request->end_date) ? 'active' : 'inactive',
        ]);

        \Log::info('Voucher created successfully with ID: ' . $promotion->id);

        // ========================================
        // STEP 4: RETURN DENGAN SUCCESS MESSAGE
        // ========================================
        return redirect()->route('penjual.promosi')->with('success', 'Voucher berhasil dibuat.');
    }
    
    /**
     * Display the edit promotion page.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $seller = Seller::where('user_id', Auth::id())->first();
        
        if (!$seller) {
            \Log::error('Seller not found for user ID: ' . Auth::id() . ' when trying to edit promotion ID: ' . $id);
            abort(403, 'Seller tidak ditemukan. Silakan cek profil penjual Anda.');
        }
        
        // First, try to find as a general promotion (voucher)
        $promotion = Promotion::where('id', $id)
                            ->where('seller_id', $seller->id)
                            ->first();
        
        if ($promotion) {
            // This is a voucher promotion
            return view('penjual.promosi.edit_voucher', compact('promotion'));
        }
        
        // If not found as a general promotion, try as a product promotion
        $productPromotion = ProductPromotion::where('id', $id)
                                          ->whereHas('product', function($query) use ($seller) {
                                              $query->where('seller_id', $seller->id);
                                          })
                                          ->with('product', 'promotion')
                                          ->first();
        
        if (!$productPromotion) {
            abort(404, 'Promosi tidak ditemukan atau Anda tidak memiliki akses ke promosi ini.');
        }
        
        // Format data to match expected structure
        $promotionData = [
            'id' => $productPromotion->id,
            'name' => $productPromotion->product->name . ' - Diskon',
            'type' => 'diskon',
            'discount_type' => $productPromotion->promotion->type ?? 'percentage',
            'discount_value' => $productPromotion->discount_value,
            'start_date' => Carbon::parse($productPromotion->start_date)->format('Y-m-d\TH:i'),
            'end_date' => Carbon::parse($productPromotion->end_date)->format('Y-m-d\TH:i'),
            'min_purchase' => 0,
            'quota' => 0,
            'used_quota' => 0,
            'products' => [$productPromotion->product->id], // Single product
            'status' => $productPromotion->status,
            'product' => $productPromotion->product
        ];
        
        return view('penjual.promosi.edit_discount', compact('promotionData'));
    }
    
    /**
     * Update the specified discount promotion in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateDiscount(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $seller = Seller::where('user_id', Auth::id())->first();
        
        if (!$seller) {
            \Log::error('Seller not found for user ID: ' . Auth::id() . ' when trying to update product promotion ID: ' . $id);
            return redirect()->back()->with('error', 'Seller tidak ditemukan. Silakan cek profil penjual Anda.');
        }
        
        $productPromotion = ProductPromotion::where('id', $id)
                                          ->whereHas('product', function($query) use ($seller) {
                                              $query->where('seller_id', $seller->id);
                                          })
                                          ->first();
        
        if (!$productPromotion) {
            \Log::error('Product promotion not found or does not belong to seller: ' . $seller->id . ' for product promotion ID: ' . $id);
            abort(404, 'Promosi produk tidak ditemukan atau Anda tidak memiliki akses ke promosi ini.');
        }

        $productPromotion->update([
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => Carbon::now()->between($request->start_date, $request->end_date) ? 'active' : 'inactive',
        ]);

        // Also update the base promotion if it exists
        if ($productPromotion->promotion) {
            $productPromotion->promotion->update([
                'name' => $request->name,
                'type' => $request->type,
                'category' => 'product_discount',  // This is a product discount
                'discount_value' => $request->discount_value,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => Carbon::now()->between($request->start_date, $request->end_date) ? 'active' : 'inactive',
            ]);
        }

        return redirect()->route('penjual.promosi')->with('success', 'Diskon produk berhasil diperbarui.');
    }
    
    /**
     * Update the specified voucher in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateVoucher(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed_amount,free_shipping',
            'code' => 'required|string|unique:promotions,code,' . $id,
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
        ]);

        $seller = Seller::where('user_id', Auth::id())->first();
        
        if (!$seller) {
            \Log::error('Seller not found for user ID: ' . Auth::id() . ' when trying to update voucher ID: ' . $id);
            return redirect()->back()->with('error', 'Seller tidak ditemukan. Silakan cek profil penjual Anda.');
        }
        
        $promotion = Promotion::where('id', $id)
                            ->where('seller_id', $seller->id)
                            ->first();
        
        if (!$promotion) {
            \Log::error('Promotion not found or does not belong to seller: ' . $seller->id . ' for promotion ID: ' . $id);
            abort(404, 'Promosi tidak ditemukan atau Anda tidak memiliki akses ke promosi ini.');
        }

        $promotion->update([
            'name' => $request->name,
            'type' => $request->type,
            'category' => 'voucher',  // This is a voucher
            'code' => $request->code,
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'max_discount_amount' => $request->max_discount_amount,
            'usage_limit' => $request->usage_limit ?? 0,
            'status' => Carbon::now()->between($request->start_date, $request->end_date) ? 'active' : 'inactive',
        ]);

        return redirect()->route('penjual.promosi')->with('success', 'Voucher berhasil diperbarui.');
    }
    
    /**
     * Nonaktifkan a promotion.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function nonaktifkan($id)
    {
        $seller = Seller::where('user_id', Auth::id())->first();
        
        if (!$seller) {
            \Log::error('Seller not found for user ID: ' . Auth::id() . ' when trying to deactivate promotion ID: ' . $id);
            return redirect()->back()->with('error', 'Seller tidak ditemukan. Silakan cek profil penjual Anda.');
        }
        
        // Check if it's a general promotion (voucher)
        $promotion = Promotion::where('id', $id)
                            ->where('seller_id', $seller->id)
                            ->first();
        
        if ($promotion) {
            // This is a voucher promotion
            // Check if promotion can be deactivated (not expired)
            $now = Carbon::now();
            $endDate = Carbon::parse($promotion->end_date);

            if ($endDate < $now && $promotion->status !== 'active' && $promotion->status !== 'inactive') {
                return redirect()->route('penjual.promosi')->with('error', 'Promosi sudah berakhir dan tidak bisa dinonaktifkan.');
            }

            $promotion->update(['status' => 'inactive']);

            return redirect()->route('penjual.promosi')->with('success', 'Voucher berhasil dinonaktifkan.');
        }
        
        // If not found as a general promotion, try as a product promotion
        $productPromotion = ProductPromotion::where('id', $id)
                                          ->whereHas('product', function($query) use ($seller) {
                                              $query->where('seller_id', $seller->id);
                                          })
                                          ->first();
        
        if ($productPromotion) {
            // Check if product promotion can be deactivated (not expired)
            $now = Carbon::now();
            $endDate = Carbon::parse($productPromotion->end_date);

            if ($endDate < $now && $productPromotion->status !== 'active' && $productPromotion->status !== 'inactive') {
                return redirect()->route('penjual.promosi')->with('error', 'Diskon produk sudah berakhir dan tidak bisa dinonaktifkan.');
            }

            $productPromotion->update(['status' => 'inactive']);

            return redirect()->route('penjual.promosi')->with('success', 'Diskon produk berhasil dinonaktifkan.');
        }
        
        return redirect()->route('penjual.promosi')->with('error', 'Promosi tidak ditemukan.');
    }
    
    /**
     * Hapus a promotion permanently.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $seller = Seller::where('user_id', Auth::id())->first();
        
        if (!$seller) {
            \Log::error('Seller not found for user ID: ' . Auth::id() . ' when trying to delete promotion ID: ' . $id);
            return redirect()->back()->with('error', 'Seller tidak ditemukan. Silakan cek profil penjual Anda.');
        }
        
        // Check if it's a general promotion (voucher)
        $promotion = Promotion::where('id', $id)
                            ->where('seller_id', $seller->id)
                            ->first();
        
        if ($promotion) {
            // This is a voucher promotion
            $promotion->delete();

            return redirect()->route('penjual.promosi')->with('success', 'Voucher berhasil dihapus secara permanen.');
        }
        
        // If not found as a general promotion, try as a product promotion
        $productPromotion = ProductPromotion::where('id', $id)
                                          ->whereHas('product', function($query) use ($seller) {
                                              $query->where('seller_id', $seller->id);
                                          })
                                          ->first();
        
        if ($productPromotion) {
            $productPromotion->delete();

            return redirect()->route('penjual.promosi')->with('success', 'Diskon produk berhasil dihapus secara permanen.');
        }
        
        return redirect()->route('penjual.promosi')->with('error', 'Promosi tidak ditemukan.');
    }
}