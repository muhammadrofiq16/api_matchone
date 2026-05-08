<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Check if user has admin role
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized - Admin access required',
                'current_role' => $request->user()->role
            ], 403);
        }

        return $next($request);
    }
}
