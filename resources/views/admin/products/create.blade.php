@extends('layouts.admin')

@section('header', 'Tambah Menu Baru')

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow p-8 mx-auto">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Nama Produk</label>
                <input type="text" name="name" class="w-full border-gray-300 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 outline-none" placeholder="Contoh: Matcha Latte Signature" required>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Kategori</label>
                <select name="category_id" class="w-full border-gray-300 border rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-green-500" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Harga (Rp)</label>
                    <input type="number" name="price" class="w-full border-gray-300 border rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-green-500" placeholder="25000" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Gambar Produk</label>
                    <input type="file" name="image" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100" required>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Deskripsi Produk</label>
                <textarea name="description" rows="4" class="w-full border-gray-300 border rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-green-500" placeholder="Jelaskan keunikan rasa produk ini..."></textarea>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="bg-green-600 text-white px-8 py-2 rounded-lg font-bold hover:bg-green-700 transition shadow-md">Simpan Menu</button>
                <a href="{{ route('admin.products.index') }}" class="text-gray-500 hover:text-gray-700 font-medium">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection