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
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        
        // Get all promotions (vouchers) for this seller
        $vouchers = Promotion::where('seller_id', $seller->id)
                           ->orderBy('created_at', 'desc')
                           ->get();

        // Get all product promotions for this seller
        $productDiscounts = ProductPromotion::with(['product', 'promotion'])
                           ->whereHas('product', function($query) use ($seller) {
                               $query->where('seller_id', $seller->id);
                           })
                           ->orderBy('created_at', 'desc')
                           ->get();

        // Calculate statistics for different promotion statuses
        $statusCounts = Promotion::where('seller_id', $seller->id)
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
        $productStatusCounts = ProductPromotion::whereHas('product', function($query) use ($seller) {
            $query->where('seller_id', $seller->id);
        })
        ->selectRaw('status, count(*) as count')
        ->groupBy('status')
        ->pluck('count', 'status');

        $productStatusData = [];
        foreach ($allStatus as $status) {
            $productStatusData[$status] = $productStatusCounts[$status] ?? 0;
        }

        return view('penjual.manajemen_promosi', compact(
            'vouchers', 
            'productDiscounts', 
            'statusData', 
            'productStatusData'
        ));
    }
    
    /**
     * Display the create promotion page for discounts.
     *
     * @return \Illuminate\View\View
     */
    public function createDiscount()
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        
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
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $seller = Seller::where('user_id', Auth::id())->firstOrFail();

        // Create a base promotion record
        $promotion = Promotion::create([
            'name' => $request->name . ' (Diskon Produk)',
            'type' => $request->type,
            'code' => strtoupper(substr(uniqid(), -8)), // Generate unique code for tracking
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'seller_id' => $seller->id,
            'status' => Carbon::now()->between($request->start_date, $request->end_date) ? 'active' : 'inactive',
        ]);

        // Create product promotion records for each selected product
        foreach ($request->product_ids as $productId) {
            ProductPromotion::create([
                'product_id' => $productId,
                'promotion_id' => $promotion->id,
                'discount_value' => $request->discount_value,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => Carbon::now()->between($request->start_date, $request->end_date) ? 'active' : 'inactive',
            ]);
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

        $seller = Seller::where('user_id', Auth::id())->firstOrFail();

        Promotion::create([
            'name' => $request->name,
            'type' => $request->type,
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
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        
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
                                          ->firstOrFail();
        
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

        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        $productPromotion = ProductPromotion::where('id', $id)
                                          ->whereHas('product', function($query) use ($seller) {
                                              $query->where('seller_id', $seller->id);
                                          })
                                          ->firstOrFail();

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

        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        $promotion = Promotion::where('id', $id)
                            ->where('seller_id', $seller->id)
                            ->firstOrFail();

        $promotion->update([
            'name' => $request->name,
            'type' => $request->type,
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
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        
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
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        
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