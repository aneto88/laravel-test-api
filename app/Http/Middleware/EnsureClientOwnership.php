<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $authenticatedUserId = auth()->id();
        $requestedClientId = $request->route('client');

        if ($authenticatedUserId != $requestedClientId) {
            return response()->json([
                'error' => 'Unauthorized.'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
