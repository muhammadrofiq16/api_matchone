@extends('layouts.admin')

@section('header', 'Tambah Pengguna Baru')

@section('content')
<div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">← Kembali ke Daftar</a>
    </div>

    <h2 class="text-2xl font-bold text-gray-800 mb-6">➕ Tambah Pengguna Baru</h2>

    <form action="{{ route('admin.users.store') }}" method="POST" class="max-w-2xl">
        @csrf

        <div class="space-y-6">
            <!-- Nama -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                <p class="text-gray-500 text-sm mt-1">Minimal 8 karakter</p>
                @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('password_confirmation')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                <select name="role" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('role') border-red-500 @enderror">
                    <option value="">-- Pilih Role --</option>
                    <option value="user" selected>User</option>
                    <option value="admin">Admin</option>
                </select>
                @error('role')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-6">
                <button type="submit" class="flex-1 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">
                    ➕ Tambah Pengguna
                </button>
                <a href="{{ route('admin.users.index') }}" class="flex-1 text-center py-3 bg-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-400 transition">
                    ❌ Batal
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
