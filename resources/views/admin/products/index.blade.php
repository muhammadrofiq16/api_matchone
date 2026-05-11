@extends('layouts.admin')

@section('header', 'Manajemen Produk')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-700">Daftar Menu Matcha</h3>
        <a href="{{ route('admin.products.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
            + Tambah Produk
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b bg-gray-50 text-gray-600 text-sm uppercase">
                    <th class="py-3 px-4">Gambar</th>
                    <th class="py-3 px-4">Nama Produk</th>
                    <th class="py-3 px-4">Kategori</th>
                    <th class="py-3 px-4">Harga</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600">
                @forelse($products as $product)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="py-3 px-4">
                        @if($product->image)
                            <img src="{{ $product->image }}" class="w-16 h-16 object-cover rounded-lg shadow-sm">
                        @else
                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-400">No Image</div>
                        @endif
                    </td>
                    <td class="py-3 px-4 font-medium text-gray-800">{{ $product->name }}</td>
                    <td class="py-3 px-4">
                        <span class="bg-blue-100 text-blue-600 px-2 py-1 rounded-md text-xs uppercase font-bold">
                            {{ $product->category->name ?? 'Tanpa Kategori' }}
                        </span>
                    </td>
                    <td class="py-3 px-4 font-semibold text-green-600">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex justify-center gap-3">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="text-blue-500 hover:text-blue-700">Edit</a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-gray-400 italic">Belum ada produk matcha yang ditambahkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection