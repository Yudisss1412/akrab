<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /**
     * Display the promotions management page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('penjual.manajemen_promosi');
    }
    
    /**
     * Display the create promotion page for discounts.
     *
     * @return \Illuminate\View\View
     */
    public function createDiscount()
    {
        // This could be a specific form for creating discount promotions
        return view('penjual.promosi.create_discount');
    }
    
    /**
     * Display the create promotion page for vouchers.
     *
     * @return \Illuminate\View\View
     */
    public function createVoucher()
    {
        // This could be a specific form for creating voucher promotions
        return view('penjual.promosi.create_voucher');
    }
    
    /**
     * Display the edit promotion page.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // For now we'll mock the promotion data
        // In a real application, you would fetch the actual promotion from database
        $promotion = [
            'id' => $id,
            'name' => 'Promosi Contoh',
            'type' => 'diskon', // or 'voucher'
            'discount_type' => 'percentage', // or 'fixed'
            'discount_value' => 10,
            'start_date' => '2024-01-01T00:00',
            'end_date' => '2024-12-31T23:59',
            'min_purchase' => 0,
            'quota' => 100,
            'used_quota' => 10,
            'products' => [], // Array of product IDs for discount promotions
            'status' => 'berlangsung' // 'berlangsung', 'akan_datang', 'berakhir', atau 'nonaktif'
        ];
        
        // Determine the view based on promotion type
        if ($promotion['type'] === 'voucher') {
            return view('penjual.promosi.edit_voucher', compact('promotion'));
        } else {
            return view('penjual.promosi.edit_discount', compact('promotion'));
        }
    }
    
    /**
     * Nonaktifkan a promotion.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function nonaktifkan($id)
    {
        // In a real application, you would fetch the promotion from database
        // and update its status to 'nonaktif'
        
        // For now we'll mock the behavior
        // In this mock, we'll just redirect back with a success message
        // In a real application, you would use Eloquent to update the promotion status
        
        // Example of what would happen in a real application:
        // $promotion = Promotion::findOrFail($id);
        // 
        // // Check if promotion can be nonaktifkan (not expired)
        // $now = now();
        // $endDate = \Carbon\Carbon::parse($promotion->end_date);
        // 
        // if ($endDate < $now && $promotion->status !== 'berlangsung' && $promotion->status !== 'akan_datang') {
        //     return redirect()->route('penjual.promosi')->with('error', 'Promosi sudah berakhir dan tidak bisa dinonaktifkan.');
        // }
        // 
        // $promotion->update(['status' => 'nonaktif']);
        
        return redirect()->route('penjual.promosi')->with('success', 'Promosi berhasil dinonaktifkan.');
    }
    
    /**
     * Hapus a promotion permanently.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // In a real application, you would fetch the promotion from database
        // and delete it permanently
        
        // For now we'll mock the behavior
        // In a real application, you would use Eloquent to delete the promotion
        
        // Example of what would happen in a real application:
        // $promotion = Promotion::findOrFail($id);
        // $promotion->delete();
        
        return redirect()->route('penjual.promosi')->with('success', 'Promosi berhasil dihapus secara permanen.');
    }
}