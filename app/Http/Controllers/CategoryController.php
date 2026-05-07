<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Get all categories
     */
    public function index()
    {
        $categories = Category::with('products')->get();
        
        return response()->json([
            'message' => 'Categories retrieved successfully',
            'data' => $categories
        ], 200);
    }

    /**
     * Get a single category with its products
     */
    public function show($id)
    {
        $category = Category::with('products')->find($id);
        
        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Category retrieved successfully',
            'data' => $category
        ], 200);
    }

    /**
     * Create a new category (Admin only)
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories',
            ]);

            $category = Category::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
            ]);

            return response()->json([
                'message' => 'Category created successfully',
                'data' => $category
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Update a category (Admin only)
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Category::find($id);
            
            if (!$category) {
                return response()->json([
                    'message' => 'Category not found'
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $id,
            ]);

            if (isset($validated['name'])) {
                $category->name = $validated['name'];
                $category->slug = Str::slug($validated['name']);
            }

            $category->save();

            return response()->json([
                'message' => 'Category updated successfully',
                'data' => $category
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Delete a category (Admin only)
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        
        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ], 200);
    }
}
