@extends('layouts.admin')

@section('header', 'Edit Pengguna')

@section('content')
<div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">← Kembali ke Daftar</a>
    </div>

    <h2 class="text-2xl font-bold text-gray-800 mb-6">✏️ Edit Pengguna: {{ $user->name }}</h2>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="max-w-2xl">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <!-- Nama -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="{{ $user->name }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ $user->email }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                <select name="role" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('role') border-red-500 @enderror">
                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-6">
                <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                    💾 Simpan Perubahan
                </button>
                <a href="{{ route('admin.users.show', $user->id) }}" class="flex-1 text-center py-3 bg-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-400 transition">
                    ❌ Batal
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
