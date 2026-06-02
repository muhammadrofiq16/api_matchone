<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KasirMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        if ($request->user()->role !== 'kasir') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthorized - Kasir access required',
                    'current_role' => $request->user()->role
                ], 403);
            }
            return redirect()->route('login')->with('error', 'Akses ditolak. Anda bukan kasir.');
        }

        return $next($request);
    }
}
