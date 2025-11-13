<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:categories,name'
            ]);

            $category = Category::create([
                'name' => $request->name,
                'slug' => \Str::slug($request->name),
                'status' => 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan',
                'category' => $category
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            \Log::error('Category store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding category: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProductCount($id)
    {
        try {
            $category = Category::findOrFail($id);
            $productCount = $category->products()->count();

            return response()->json([
                'product_count' => $productCount
            ]);
        } catch (Exception $e) {
            \Log::error('Category getProductCount error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching product count'
            ], 404);
        }
    }

    public function destroy($id)
    {
        try {
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
        } catch (Exception $e) {
            \Log::error('Category destroy error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting category'
            ], 404);
        }
    }
}