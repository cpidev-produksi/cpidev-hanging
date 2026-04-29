<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SupervisorOnly
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) abort(401);

        $slug = $user->role?->slug;

        abort_unless(in_array($slug, ['supervisor'], true), 403, 'Hanya supervisor yang boleh melakukan aksi ini.');
        return $next($request);
    }
}
