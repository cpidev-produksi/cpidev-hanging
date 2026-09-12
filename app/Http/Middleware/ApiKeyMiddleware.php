<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $key   = $request->header('X-API-KEY');
        $valid = config('services.esp32.api_key');

        if (!$key || !$valid || !hash_equals($valid, $key)) {
            abort(401, 'Invalid API key.');
        }

        return $next($request);
    }
}
