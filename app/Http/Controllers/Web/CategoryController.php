<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str; // Penting untuk slug otomatis
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar semua kategori.
     */
    public function index(): View
    { 
        $categories = Category::latest()->get(); 
        return view('admin.categories.index', compact('categories')); 
    }

    /**
     * Menampilkan form tambah kategori.
     */
    public function create(): View
    { 
        return view('admin.categories.create'); 
    }

    /**
     * Menyimpan kategori baru ke database.
     */
    public function store(Request $request): RedirectResponse
    { 
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name'
        ], [
            'name.unique' => 'Nama kategori ini sudah ada, gunakan nama lain.'
        ]);

        // Gabungan: Ditambahkan logika Str::slug supaya tidak error "field slug doesn't have default value"
        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) 
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambah!'); 
    }

    /**
     * Menampilkan form edit kategori.
     */
    public function edit(string $id): View
    { 
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category')); 
    }

    /**
     * Memperbarui data kategori.
     */
    public function update(Request $request, string $id): RedirectResponse
    { 
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id
        ]);

        // Gabungan: Update nama sekaligus update slug-nya
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diubah!'); 
    }

    /**
     * Menghapus kategori.
     */
    public function destroy(string $id): RedirectResponse
    { 
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return redirect()->route('admin.categories.index')
                ->with('success', 'Kategori berhasil dihapus!'); 
        } catch (\Exception $e) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Gagal menghapus! Kategori mungkin masih digunakan oleh produk.');
        }
    }
}