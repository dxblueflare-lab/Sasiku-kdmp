<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Cek apakah user memiliki salah satu dari role yang diizinkan
        foreach ($roles as $role) {
            if ($user->role === $role) {
                return $next($request);
            }
        }

        // Jika tidak memiliki role yang sesuai, redirect ke halaman unauthorized
        abort(403, 'Unauthorized access');
    }
}