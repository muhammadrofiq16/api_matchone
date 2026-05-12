<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
        }

        $users = $query->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|in:admin,customer,user',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'] ?? 'customer',
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat');
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'nullable|in:admin,user',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate');
    }

    public function destroy(int $id)
    {
        try {
            // Validate ID is numeric
            if (!is_numeric($id)) {
                return response()->json(['message' => 'Invalid user ID'], 400);
            }

            $user = User::find($id);

            if (!$user) {
                return response()->json(['message' => 'User tidak ditemukan'], 404);
            }

            $authId = auth()->user()->id ?? null;

            // Prevent deleting own account
            if (!$authId) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            if ((int)$user->id === (int)$authId) {
                return response()->json(['message' => 'Tidak bisa menghapus user sendiri'], 403);
            }

            $user->delete();

            return response()->json(['message' => 'User berhasil dihapus', 'deleted_id' => $user->id], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}

