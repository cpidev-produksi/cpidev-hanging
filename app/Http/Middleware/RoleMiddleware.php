<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$allowedSlugs)
    {
        $user = $request->user();
        if (!$user) abort(401);

        $slug = $user->role?->slug;

        // Jika route tidak memberi role, izin default
        if (empty($allowedSlugs)) {
            return $next($request);
        }

        // Hanya izinkan jika slug sesuai allowedSlugs
        if (!$slug || !in_array($slug, $allowedSlugs, true)) {
            abort(403, 'Tidak memiliki hak akses.');
        }

        return $next($request);
    }
}
