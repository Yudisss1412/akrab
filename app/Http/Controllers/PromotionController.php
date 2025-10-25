<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\ProductPromotion;
use App\Models\Seller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PromotionController extends Controller
{
    /**
     * Display the promotions management page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // Debug: Check if user is authenticated
            if (!Auth::check()) {
                abort(403, 'Akses ditolak. Anda harus login terlebih dahulu.');
            }
            
            $seller = Seller::where('user_id', Auth::id())->first();
            \Log::info('Authenticated user ID: ' . Auth::id());
            \Log::info('Seller found: ' . ($seller ? 'Yes, ID: ' . $seller->id : 'No'));
            
            // If no seller found, create a helpful error message
            if (!$seller) {
                \Log::warning('No seller found for user ID: ' . Auth::id());
                // Return view with empty data if no seller
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
            
            // Get all promotions that are NOT associated with any product (pure vouchers) for this seller
            $vouchers = Promotion::where('seller_id', $seller->id)
                               ->whereNotIn('id', function($query) {
                                   $query->select('promotion_id')
                                         ->from('product_promotions')
                                         ->whereNotNull('promotion_id');
                               })
                               ->orderBy('created_at', 'desc')
                               ->get();
            
            \Log::info('Fetched ' . $vouchers->count() . ' vouchers for seller: ' . $seller->id);

            // Get all product promotions for this seller - get seller's product IDs first
            $sellerProductIds = Product::where('seller_id', $seller->id)->pluck('id');
            \Log::info('Seller product IDs: ' . $sellerProductIds->count() . ' items');
            \Log::info('Seller product IDs list: ' . json_encode($sellerProductIds->toArray()));
            
            // Get all product promotions associated with seller's products
            $productDiscounts = ProductPromotion::with(['product', 'promotion'])
                               ->whereIn('product_id', $sellerProductIds)
                               ->orderBy('created_at', 'desc')
                               ->get();
            
            \Log::info('Fetched ' . $productDiscounts->count() . ' product discounts');
            
            // Log each product discount to see what's being fetched
            foreach($productDiscounts as $pd) {
                \Log::info('Product discount ID: ' . $pd->id . ', Product ID: ' . $pd->product_id . ', Product: ' . ($pd->product ? $pd->product->name : 'NULL') . ', Promotion: ' . ($pd->promotion ? $pd->promotion->name : 'NULL'));
            }

            // Calculate statistics for different promotion statuses (for vouchers only)
            $statusCounts = Promotion::where('seller_id', $seller->id)
                                    ->whereNotIn('id', function($query) {
                                        $query->select('promotion_id')
                                              ->from('product_promotions')
                                              ->whereNotNull('promotion_id');
                                    })
                                    ->selectRaw('status, count(*) as count')
                                    ->groupBy('status')
                                    ->pluck('count', 'status');

            // Define all possible statuses with default values
            $allStatus = ['active', 'inactive', 'expired'];
            $statusData = [];
            foreach ($allStatus as $status) {
                $statusData[$status] = $statusCounts[$status] ?? 0;
            }

            // Calculate status counts for product discounts
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
            
            return view('penjual.manajemen_promosi', compact(
                'vouchers', 
                'productDiscounts', 
                'statusData', 
                'productStatusData'
            ));
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error in PromotionController@index: ' . $e->getMessage());
            
            // Return with empty data to prevent undefined variable errors
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
     * @return \Illuminate\View\View
     */
    public function createDiscount()
    {
        $seller = Seller::where('user_id', Auth::id())->first();
        
        if (!$seller) {
            \Log::error('Seller not found for user ID: ' . Auth::id() . ' when trying to access create discount page');
            abort(403, 'Seller tidak ditemukan. Silakan cek profil penjual Anda.');
        }
        
        // Get products for this seller
        $products = Product::where('seller_id', $seller->id)
                          ->where('status', 'active')
                          ->select('id', 'name')
                          ->get();

        return view('penjual.promosi.create_discount', compact('products'));
    }
    
    /**
     * Display the create promotion page for vouchers.
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeDiscount(Request $request)
    {
        // Log data yang diterima untuk debugging
        \Log::info('Store discount request data:', $request->all());
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $seller = Seller::where('user_id', Auth::id())->first();
        
        if (!$seller) {
            \Log::error('Seller not found for user ID: ' . Auth::id());
            return redirect()->back()->with('error', 'Seller tidak ditemukan. Silakan cek profil penjual Anda.');
        }
        
        \Log::info('Creating discount for seller: ' . $seller->id . ' with data: ' . json_encode($request->all()));

        // Create a base promotion record
        $promotion = Promotion::create([
            'name' => $request->name . ' (Diskon Produk)',
            'type' => $request->type,
            'category' => 'product_discount',  // Explicitly set category for product discount
            'code' => strtoupper(substr(uniqid(), -8)), // Generate unique code for tracking
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'seller_id' => $seller->id,
            'status' => Carbon::now()->between($request->start_date, $request->end_date) ? 'active' : 'inactive',
        ]);
        
        \Log::info('Base promotion created successfully with ID: ' . $promotion->id);

        // Create product promotion records for each selected product
        \Log::info('Creating product promotions for discount. Total products: ' . count($request->product_ids));
        
        foreach ($request->product_ids as $productId) {
            \Log::info('Processing product ID: ' . $productId);
            
            // Verify the product belongs to the seller
            $product = Product::where('id', $productId)->where('seller_id', $seller->id)->first();
            
            if ($product) {
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

        return redirect()->route('penjual.promosi')->with('success', 'Diskon produk berhasil dibuat.');
    }
    
    /**
     * Store a newly created voucher in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeVoucher(Request $request)
    {
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

        $seller = Seller::where('user_id', Auth::id())->first();
        
        if (!$seller) {
            \Log::error('Seller not found for user ID: ' . Auth::id());
            return redirect()->back()->with('error', 'Seller tidak ditemukan. Silakan cek profil penjual Anda.');
        }
        
        \Log::info('Creating voucher for seller: ' . $seller->id . ' with data: ' . json_encode($request->all()));

        $promotion = Promotion::create([
            'name' => $request->name,
            'type' => $request->type,
            'category' => 'voucher',  // Explicitly set category for voucher
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