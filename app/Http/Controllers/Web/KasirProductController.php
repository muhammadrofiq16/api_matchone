<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class KasirProductController extends Controller
{
    public function index() 
    { 
        $products = Product::with('category')->latest()->get();
        return view('kasir.products.index', compact('products')); 
    }

    public function create() 
    { 
        $categories = Category::all();
        return view('kasir.products.create', compact('categories')); 
    }

    public function store(Request $request) 
    { 
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // Menggunakan Mesin Inti Cloudinary (Terbukti Sukses!)
            $cloudinary = new \Cloudinary\Cloudinary(env('CLOUDINARY_URL'));
            
            $upload = $cloudinary->uploadApi()->upload($request->file('image')->getRealPath(), [
                'folder' => 'products'
            ]);
            $imageUrl = $upload['secure_url'];

            Product::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'image' => $imageUrl,
                'is_available' => true,
            ]);

            return redirect()->route('kasir.products.index')->with('success', 'Menu Matcha berhasil ditambahkan!'); 

        } catch (\Exception $e) {
            Log::error('Upload Gagal: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Eror Server saat menyimpan data!');
        }
    }

    public function edit($id) 
    { 
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('kasir.products.edit', compact('product', 'categories')); 
    }

    public function update(Request $request, $id) 
    { 
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['category_id', 'name', 'description', 'price']);

        if ($request->hasFile('image')) {
            try {
                $cloudinary = new \Cloudinary\Cloudinary(env('CLOUDINARY_URL'));

                if ($product->image) {
                    $this->deleteFromCloudinary($product->image, $cloudinary);
                }
                
                $upload = $cloudinary->uploadApi()->upload($request->file('image')->getRealPath(), [
                    'folder' => 'products'
                ]);
                $data['image'] = $upload['secure_url'];
            } catch (\Exception $e) {
                Log::error('Update Gagal: ' . $e->getMessage());
                return back()->withInput()->with('error', 'Gagal update gambar ke Cloud!');
            }
        }

        $product->update($data);
        return redirect()->route('kasir.products.index')->with('success', 'Produk berhasil diperbarui!'); 
    }

    public function destroy($id) 
    { 
        $product = Product::findOrFail($id);
        
        if ($product->image) {
            $cloudinary = new \Cloudinary\Cloudinary(env('CLOUDINARY_URL'));
            $this->deleteFromCloudinary($product->image, $cloudinary);
        }
        
        $product->delete();
        return redirect()->route('kasir.products.index')->with('success', 'Produk berhasil dihapus!'); 
    }

    private function deleteFromCloudinary($imageUrl, $cloudinary)
    {
        try {
            $parts = explode('/', $imageUrl);
            $lastPart = end($parts);
            $filename = pathinfo($lastPart, PATHINFO_FILENAME);
            $publicId = 'products/' . $filename;
            
            $cloudinary->uploadApi()->destroy($publicId);
        } catch (\Exception $e) {
            Log::error('Hapus Gagal: ' . $e->getMessage());
        }
    }
}