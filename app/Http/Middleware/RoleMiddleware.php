<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Memeriksa role pengguna sebelum mengakses halaman.
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {

        // Memastikan pengguna sudah login
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Memeriksa kesesuaian role pengguna
        if (!in_array($request->user()->role, $roles)) {
            abort(403, 'Kamu tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
