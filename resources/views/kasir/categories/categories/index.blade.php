@extends('layouts.kasir')

@section('header', 'Manajemen Kategori')

@section('content')
<div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-gray-800">✨ Daftar Kategori Menu</h3>
        <a href="{{ route('kasir.categories.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl text-sm font-semibold transition">
            + Tambah Kategori
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-gray-100 text-gray-500 text-sm">
                    <th class="py-3 px-2">ID</th>
                    <th class="py-3 px-2">Nama Kategori</th>
                    <th class="py-3 px-2">Dibuat Pada</th>
                    <th class="py-3 px-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                    <td class="py-3 px-2 text-gray-500">#{{ $category->id }}</td>
                    <td class="py-3 px-2 font-semibold text-gray-700">{{ $category->name }}</td>
                    <td class="py-3 px-2 text-sm text-gray-500">{{ $category->created_at->format('d M Y') }}</td>
                    <td class="py-3 px-2">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('kasir.categories.edit', $category->id) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('kasir.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-10 text-center text-gray-400 italic">Belum ada kategori. Klik "+ Tambah" untuk memulai.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
