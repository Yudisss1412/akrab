<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name'
        ]);

        $category = Category::create([
            'name' => $request->name,
            'status' => 'active'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan',
            'category' => $category
        ]);
    }
    
    public function getProductCount($id)
    {
        $category = Category::findOrFail($id);
        $productCount = $category->products()->count();

        return response()->json([
            'product_count' => $productCount
        ]);
    }
    
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // Cek apakah kategori punya produk
        if ($category->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak bisa dihapus karena masih punya produk'
            ], 422);
        }
        
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus'
        ]);
    }
}