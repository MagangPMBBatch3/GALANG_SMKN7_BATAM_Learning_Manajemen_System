<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $role
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check()) {
            // Return JSON for AJAX/API requests, redirect for browser page requests
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['message' => 'Unauthenticated. Please login.'], 401);
            }
            return redirect('/login');
        }

        $user = Auth::user();

        // Check if user has the required role
        if (!$user->hasRole($role)) {
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['message' => 'Unauthorized. Required role: ' . $role], 403);
            }
            abort(403, 'Unauthorized. You do not have the required role: ' . $role);
        }

        return $next($request);
    }
}
