<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Get all products with optional filtering
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by availability
        if ($request->has('is_available')) {
            $query->where('is_available', $request->boolean('is_available'));
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $products = $query->get();

        return response()->json([
            'message' => 'Products retrieved successfully',
            'data' => $products
        ], 200);
    }

    /**
     * Get a single product
     */
    public function show($id)
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Product retrieved successfully',
            'data' => $product
        ], 200);
    }

    /**
     * Create a new product (Admin only)
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'image' => 'nullable|string',
                'is_available' => 'boolean',
            ]);

            $product = Product::create([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'image' => $validated['image'] ?? null,
                'is_available' => $validated['is_available'] ?? true,
            ]);

            return response()->json([
                'message' => 'Product created successfully',
                'data' => $product->load('category')
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Update a product (Admin only)
     */
    public function update(Request $request, $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found'
                ], 404);
            }

            $validated = $request->validate([
                'category_id' => 'sometimes|required|exists:categories,id',
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|required|numeric|min:0',
                'image' => 'nullable|string',
                'is_available' => 'boolean',
            ]);

            $product->update($validated);

            return response()->json([
                'message' => 'Product updated successfully',
                'data' => $product->load('category')
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Delete a product (Admin only)
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ], 200);
    }

    /**
     * Toggle product availability
     */
    public function toggleAvailability($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        $product->update([
            'is_available' => !$product->is_available
        ]);

        return response()->json([
            'message' => 'Product availability updated',
            'data' => $product->load('category')
        ], 200);
    }
}
