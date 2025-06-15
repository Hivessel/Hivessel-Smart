<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

class GlobalRateLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->user()?->id ?: $request->ip();

        if (RateLimiter::tooManyAttempts('global:' . $key, 200)) {
            return response()->json([
                'message' => 'Too many requests.',
            ], 429)->header('Retry-After', RateLimiter::availableIn('global:' . $key));
        }

        RateLimiter::hit('global:' . $key, 60); // 60 seconds decay

        return $next($request);
    }
}
