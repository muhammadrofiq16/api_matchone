@extends('layouts.admin')

@section('header', 'Detail Pengguna')

@section('content')
<div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">← Kembali ke Daftar</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1">
            <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl p-8 text-center text-white">
                <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                <p class="text-blue-100 text-sm mt-1">{{ $user->email }}</p>
            </div>

            <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h3 class="font-semibold text-gray-800 mb-3">📊 Informasi</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase">Role</p>
                        <p class="text-lg font-bold text-gray-800">
                            @if($user->role === 'admin')
                                👑 Admin
                            @else
                                👤 User
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase">Bergabung</p>
                        <p class="text-lg font-bold text-gray-800">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase">Update Terakhir</p>
                        <p class="text-lg font-bold text-gray-800">{{ $user->updated_at ? $user->updated_at->format('d M Y H:i') : '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:col-span-2">
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">📋 Detail Akun</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" value="{{ $user->name }}" disabled class="w-full px-4 py-2 bg-gray-200 border border-gray-300 rounded-lg text-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" value="{{ $user->email }}" disabled class="w-full px-4 py-2 bg-gray-200 border border-gray-300 rounded-lg text-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <input type="text" value="{{ ucfirst($user->role) }}" disabled class="w-full px-4 py-2 bg-gray-200 border border-gray-300 rounded-lg text-gray-700">
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200 flex gap-3">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="flex-1 text-center py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                        ✏️ Edit
                    </a>
                    @if($user->id !== auth()->id())
                    <button class="delete-user-btn flex-1 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition" data-user-id="{{ $user->id }}">
                        🗑️ Hapus
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
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
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                alert(data.message || 'User berhasil dihapus');
                window.location.href = '{{ route("admin.users.index") }}';
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
