@extends('layouts.admin')

@section('header', 'Tambah Kategori')

@section('content')
<div class="max-w-2xl bg-white rounded-2xl shadow-md border border-gray-100 p-8">
    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Kategori</label>
            <input type="text" name="name" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition" placeholder="Contoh: Matcha Latte, Snacks, dll" required>
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        
        <div class="flex gap-3">
            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-xl font-bold transition">Simpan Kategori</button>
            <a href="{{ route('admin.categories.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-6 py-3 rounded-xl font-bold transition">Batal</a>
        </div>
    </form>
</div>
@endsection