<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // 1. Menampilkan daftar produk
    public function index() 
    { 
        $products = Product::with('category')->latest()->get();
        return view('admin.products.index', compact('products')); 
    }

    // 2. Menampilkan form tambah produk
    public function create() 
    { 
        $categories = Category::all();
        return view('admin.products.create', compact('categories')); 
    }

    // 3. Menyimpan produk baru ke database
    public function store(Request $request) 
    { 
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Upload Gambar
        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $image->hashName(),
            'is_available' => true,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan!'); 
    }

    // 4. Menampilkan detail satu produk (Opsional, jika ingin dibuatkan halamannya)
    public function show($id) 
    { 
        $product = Product::findOrFail($id);
        return view('admin.products.show', compact('product')); 
    }

    // 5. Menampilkan form edit produk
    public function edit($id) 
    { 
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories')); 
    }

    // 6. Menyimpan perubahan produk dari form edit
    public function update(Request $request, $id) 
    { 
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Boleh kosong jika tidak ingin ganti gambar
        ]);

        $data = [
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
        ];

        // Jika admin mengupload gambar baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($product->image) {
                Storage::delete('public/products/' . $product->image);
            }
            // Upload gambar baru
            $image = $request->file('image');
            $image->storeAs('public/products', $image->hashName());
            $data['image'] = $image->hashName();
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui!'); 
    }

    // 7. Menghapus produk
    public function destroy($id) 
    { 
        $product = Product::findOrFail($id);
        
        // Hapus file gambar dari storage
        if ($product->image) {
            Storage::delete('public/products/' . $product->image);
        }
        
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus!'); 
    }
}