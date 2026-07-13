<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockIpsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $blocked = array_filter(config('security.network.blocked_ips', []));
        $whitelist = array_filter(config('security.network.whitelist_ips', []));

        if (! empty($blocked) && in_array($request->ip(), $blocked)) {
            abort(403, 'Access denied.');
        }

        if (! empty($whitelist) && ! in_array($request->ip(), $whitelist)) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}
