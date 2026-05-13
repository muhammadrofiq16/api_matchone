<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'phone'    => 'nullable|string|max:20',
            ]);

            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone'    => $validated['phone'] ?? null,
                'points'   => 0,
                'role'     => 'customer',
            ]);

            return response()->json([
                'message' => 'User registered successfully',
                'user'    => $user,
                'token'   => $user->createToken('api-token')->plainTextToken,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    /**
     * Login dengan email + password
     */
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email'    => 'required|string|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'message' => 'Invalid credentials',
                ], 401);
            }

            return response()->json([
                'message' => 'Login successful',
                'user'    => $user,
                'token'   => $user->createToken('api-token')->plainTextToken,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    /**
     * Login dengan Google Token (dari Android)
     *
     * Android kirim: POST /api/auth/google
     * Body: { "google_token": "eyJhbGci..." }
     *
     * Flow:
     * 1. Verifikasi Google token ke Google API
     * 2. Ambil data user dari Google
     * 3. Buat atau cari user di database
     * 4. Return Sanctum token untuk dipakai request selanjutnya
     */
    public function googleLogin(Request $request)
    {
        try {
            $request->validate([
                'google_token' => 'required|string',
            ]);

            $googleToken = $request->google_token;

            // Verifikasi token ke Google tokeninfo endpoint
            $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $googleToken,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'message' => 'Google token tidak valid',
                ], 401);
            }

            $googleUser = $response->json();

            // Pastikan token memang untuk app ini (aud = client ID)
            // Uncomment dan isi CLIENT_ID sesuai project kamu di Google Console
            // if ($googleUser['aud'] !== env('GOOGLE_CLIENT_ID')) {
            //     return response()->json(['message' => 'Token bukan untuk aplikasi ini'], 401);
            // }

            // Cari atau buat user berdasarkan email Google
            $user = User::firstOrCreate(
                ['email' => $googleUser['email']],
                [
                    'name'              => $googleUser['name'] ?? 'Google User',
                    'password'          => Hash::make(str()->random(32)), // password acak, user tidak perlu tahu
                    'google_id'         => $googleUser['sub'],
                    'avatar'            => $googleUser['picture'] ?? null,
                    'email_verified_at' => now(), // Google sudah verifikasi email
                    'points'            => 0,
                    'role'              => 'customer',
                ]
            );

            // Update google_id jika user sudah ada tapi belum punya google_id
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser['sub'],
                    'avatar'    => $googleUser['picture'] ?? $user->avatar,
                ]);
            }

            // Hapus token lama (opsional, agar tidak numpuk)
            $user->tokens()->where('name', 'google-token')->delete();

            return response()->json([
                'message' => 'Login dengan Google berhasil',
                'user'    => $user,
                'token'   => $user->createToken('google-token')->plainTextToken, // ← Sanctum token, simpan ini di Android
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ], 200);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'     => 'sometimes|required|string|max:255',
                'phone'    => 'sometimes|nullable|string|max:20',
                'password' => 'sometimes|required|string|min:8|confirmed',
            ]);

            $user = $request->user();

            if (isset($validated['name']))     $user->name     = $validated['name'];
            if (isset($validated['phone']))    $user->phone    = $validated['phone'];
            if (isset($validated['password'])) $user->password = Hash::make($validated['password']);

            $user->save();

            return response()->json([
                'message' => 'Profile updated successfully',
                'user'    => $user,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        }
    }
}