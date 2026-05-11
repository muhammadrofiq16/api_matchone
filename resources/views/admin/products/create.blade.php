@extends('layouts.admin')

@section('header', 'Tambah Menu Baru')

@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('admin.products.index') }}" class="inline-flex items-center text-sm text-slate-500 hover:text-emerald-600 mb-4 transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Daftar Produk
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8">
            <div class="mb-8 border-b border-slate-100 pb-6">
                <h3 class="text-xl font-bold text-slate-800">Formulir Produk Matcha</h3>
                <p class="text-sm text-slate-500">Silakan isi detail produk untuk katalog Matchone kamu.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl">
                    <ul class="list-disc list-inside text-xs text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-6">
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Produk</label>
                        <input type="text" name="name" value="{{ old('name') }}" 
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:bg-white outline-none transition-all" 
                            placeholder="Contoh: Matcha Caramel Latte" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Kategori</label>
                            <select name="category_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Harga (Rp)</label>
                            <input type="number" name="price" value="{{ old('price') }}"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all" 
                                placeholder="25000" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Foto Produk</label>
                        <div class="mt-2 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                            <input type="file" name="image" 
                                class="block w-full text-sm text-slate-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-xs file:font-bold
                                file:bg-emerald-50 file:text-emerald-700
                                hover:file:bg-emerald-100 transition-all cursor-pointer" required>
                            <p class="text-[10px] text-slate-400 mt-3 px-1 italic italic">Format: JPG, PNG (Maks. 2MB)</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi Produk</label>
                        <textarea name="description" rows="4" 
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all" 
                            placeholder="Jelaskan rasa unik menu matcha ini...">{{ old('description') }}</textarea>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                        <button type="submit" 
                            class="w-full md:w-auto bg-emerald-500 text-white px-12 py-3 rounded-xl font-bold hover:bg-emerald-600 transition shadow-lg shadow-emerald-200 active:scale-95">
                            Simpan Menu Ke Cloud
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection