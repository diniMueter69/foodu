<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $key = config('app.api_key') ?? env('API_KEY');
        $supplied = $request->header('X-Api-Key') ?: $request->query('api_key');

        abort_unless($key && hash_equals($key, (string)$supplied), 401, 'Unauthorized');

        return $next($request);
    }
}
