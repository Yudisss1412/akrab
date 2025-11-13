<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

    public function show($id)
    {
        $subcategory = Subcategory::findOrFail($id);

        return response()->json([
            'success' => true,
            'subcategory' => $subcategory
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

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subcategories,name',
            'category_id' => 'required|exists:categories,id'
        ]);

        $subcategory = Subcategory::create([
            'name' => $request->name,
            'category_id' => $request->category_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subkategori berhasil ditambahkan',
            'subcategory' => $subcategory
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subcategories,name,' . $id
        ]);

        $subcategory = Subcategory::findOrFail($id);
        $subcategory->name = $request->name;
        $subcategory->save();

        return response()->json([
            'success' => true,
            'message' => 'Subkategori berhasil diperbarui',
            'subcategory' => $subcategory
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