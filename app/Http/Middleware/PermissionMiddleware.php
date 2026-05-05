<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $perm)
    {
        $user = $request->user();
        if (!$user || !$user->role) {
            abort(403, 'Role tidak ditemukan');
        }

        // format: "shfi.view"
        [$group, $key] = array_pad(explode('.', $perm, 2), 2, null);
        if (!$group || !$key) abort(500, 'Permission format salah');

        if (!$user->role->hasPerm($group, $key)) {
            abort(403, 'Anda tidak memiliki izin: '.$perm);
        }

        return $next($request);
    }
}
