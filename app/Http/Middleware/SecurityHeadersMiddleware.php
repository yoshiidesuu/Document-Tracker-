<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = Str::random(40);
        View::share('cspNonce', $nonce);
        Vite::useCspNonce($nonce);

        $response = $next($request);

        $config = config('security.headers');

        $response->headers->set('X-Frame-Options', $config['x-frame-options']);
        $response->headers->set('X-Content-Type-Options', $config['x-content-type-options']);
        $response->headers->set('X-XSS-Protection', $config['x-xss-protection']);
        $response->headers->set('Referrer-Policy', $config['referrer-policy']);
        $response->headers->set('Permissions-Policy', $config['permissions-policy']);
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        if ($config['content-security-policy']) {
            $csp = $config['content-security-policy'];
            $csp = preg_replace('/script-src\s+[^;]+/', '$0 ' . "'nonce-{$nonce}'", $csp);
            $response->headers->set('Content-Security-Policy', $csp);
        }

        if ($config['hsts']['enabled'] && $request->isSecure()) {
            $hsts = "max-age={$config['hsts']['max-age']}";
            if ($config['hsts']['include-sub-domains']) $hsts .= '; includeSubDomains';
            if ($config['hsts']['preload']) $hsts .= '; preload';
            $response->headers->set('Strict-Transport-Security', $hsts);
        }

        $response->headers->remove('Server');
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
