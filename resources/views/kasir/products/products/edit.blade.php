@extends('layouts.kasir')

@section('header', 'Edit Menu: ' . $product->name)

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow p-8 mx-auto">
    <form action="{{ route('kasir.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Nama Produk</label>
                <input type="text" name="name" value="{{ $product->name }}" class="w-full border-gray-300 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 outline-none" required>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Kategori</label>
                <select name="category_id" class="w-full border-gray-300 border rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-green-500" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Harga (Rp)</label>
                    <input type="number" name="price" value="{{ $product->price }}" class="w-full border-gray-300 border rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-green-500" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Ganti Gambar (Opsional)</label>
                    <input type="file" name="image" class="w-full text-sm text-gray-500">
                </div>
            </div>

            @if($product->image)
            <div class="mt-2">
                <p class="text-xs text-gray-400 mb-1">Gambar saat ini:</p>
                <img src="{{ asset('storage/products/'.$product->image) }}" class="w-24 h-24 object-cover rounded-lg border">
            </div>
            @endif

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Deskripsi Produk</label>
                <textarea name="description" rows="4" class="w-full border-gray-300 border rounded-lg px-4 py-2 outline-none focus:ring-2 focus:ring-green-500">{{ $product->description }}</textarea>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-lg font-bold hover:bg-blue-700 transition shadow-md">Update Menu</button>
                <a href="{{ route('kasir.products.index') }}" class="text-gray-500 hover:text-gray-700 font-medium">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection
