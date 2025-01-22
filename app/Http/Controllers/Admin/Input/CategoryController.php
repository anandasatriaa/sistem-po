<?php

namespace App\Http\Controllers\Admin\Input;

use App\Http\Controllers\Controller;
use App\Models\Category\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.category.index', compact('categories'));
    }

    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'namacategory' => 'required|string|max:255',
        ]);

        try {
            // Generate a new category code
            $lastCategory = Category::orderBy('id', 'desc')->first();
            $newCode = $lastCategory
                ? 'CAT' . ((int) substr($lastCategory->kode, 3) + 1)
                : 'CAT1';

            // Create a new category
            Category::create([
                'kode' => $newCode,
                'nama' => $request->namacategory,
            ]);

            // Return success response
            return response()->json(['success' => true, 'message' => 'Category berhasil ditambahkan'], 201);
        } catch (\Exception $e) {
            // Handle any errors and return failure response with message
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan category: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $kode)
    {
        // Validate the data
        $validated = $request->validate([
            'namacategoryupdate' => 'required|string|max:255',
        ]);

        try {
            // Find the category by its 'kode' instead of 'id'
            $category = Category::where('kode', $kode)->firstOrFail();

            // Update the category's name
            $category->nama = $request->namacategoryupdate;
            $category->save();

            // Return success response
            return response()->json(['success' => true, 'message' => 'Category berhasil diupdate'], 201);
        } catch (\Exception $e) {
            // Handle any errors and return failure response with message
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengupdate category: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Find the category by its ID and delete it
            $category = Category::findOrFail($id);
            $category->delete();

            // Return success response
            return response()->json(['success' => true, 'message' => 'Category berhasil dihapus'], 201);
        } catch (\Exception $e) {
            // Handle any errors and return failure response
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus category: ' . $e->getMessage()], 500);
        }
    }

    public function lastCategoryId()
    {
        $lastCategory = Category::orderBy('id', 'desc')->first();
        $newCode = $lastCategory
            ? 'CAT' . ((int) substr($lastCategory->kode, 3) + 1)
            : 'CAT1';

        return response()->json(['kode' => $newCode]);
    }

}
