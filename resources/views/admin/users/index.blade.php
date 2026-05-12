@extends('layouts.admin')

@section('header', 'Kelola Pengguna')

@section('content')
<div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">👥 Kelola Pengguna</h2>
            <p class="text-gray-600 text-sm">Manage dan monitor semua pengguna aplikasi</p>
        </div>
        <div class="text-right">
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl p-4 text-white">
                <p class="text-sm font-semibold opacity-90">Total Users</p>
                <h3 class="text-3xl font-bold">{{ $users->total() ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
        <form method="GET" class="flex gap-4 flex-wrap">
            @csrf
            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Pengguna</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau email..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                    🔍 Cari
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-400 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b-2 border-gray-200 bg-gray-50">
                    <th class="py-4 px-6 font-semibold text-gray-700">Nama</th>
                    <th class="py-4 px-6 font-semibold text-gray-700">Email</th>
                    <th class="py-4 px-6 font-semibold text-gray-700">Role</th>
                    <th class="py-4 px-6 font-semibold text-gray-700">Status</th>
                    <th class="py-4 px-6 font-semibold text-gray-700">Bergabung</th>
                    <th class="py-4 px-6 font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span class="font-semibold text-gray-800">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-gray-600">{{ $user->email }}</td>
                    <td class="py-4 px-6">
                        @if($user->role === 'admin')
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold">👑 Admin</span>
                        @else
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">👤 User</span>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        @if($user->is_active ?? true)
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">✓ Active</span>
                        @else
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold">✗ Inactive</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-sm text-gray-600">
                        {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm font-semibold hover:bg-blue-200 transition">
                                👁️ Lihat
                            </a>
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="px-3 py-2 bg-yellow-100 text-yellow-700 rounded-lg text-sm font-semibold hover:bg-yellow-200 transition">
                                ✏️ Edit
                            </a>
                            @if($user->id !== auth()->id())
                            <button class="delete-user-btn px-3 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-semibold hover:bg-red-200 transition" data-user-id="{{ $user->id }}">
                                🗑️ Hapus
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-400">
                        <p class="text-lg">📭 Tidak ada pengguna</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="mt-6">
        {{ $users->links() }}
    </div>
    @endif
</div>

<script>
document.querySelectorAll('.delete-user-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const userId = this.getAttribute('data-user-id');

        if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
            fetch(`/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message || 'User berhasil dihapus');
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message);
            });
        }
    });
});
</script>
@endsection
