<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('security.network.enforce_https')) {
            return $next($request);
        }

        if (! $request->isSecure() && ! $this->isSafeEnvironment()) {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }

    private function isSafeEnvironment(): bool
    {
        return app()->environment('local', 'testing');
    }
}
