<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    public function getSubcategoriesByCategory($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $subcategories = $category->subcategories; // Relasi dari model Category

        return response()->json([
            'success' => true,
            'subcategories' => $subcategories
        ]);
    }

    public function getProductCount($id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $productCount = $subcategory->products()->count();

        return response()->json([
            'product_count' => $productCount
        ]);
    }
    
    public function destroy($id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $subcategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subkategori berhasil dihapus'
        ]);
    }
}