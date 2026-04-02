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

        // Supervisor selalu boleh lewat (anggap supervisor / superadmin)
        if (in_array($slug, ['supervisor', 'superadmin'], true)) {
            return $next($request);
        }

        if (!$slug || !in_array($slug, $allowedSlugs, true)) {
            abort(403, 'Tidak memiliki hak akses.');
        }

        return $next($request);
    }
}
