<?php

namespace App\Http\Middleware;

use App\Services\SecurityAuditService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityMonitorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('security.audit.enabled')) {
            return $next($request);
        }

        $startTime = microtime(true);

        $response = $next($request);

        $duration = (microtime(true) - $startTime) * 1000;

        $this->detectSuspiciousActivity($request, $response);
        $this->logIfNeeded($request, $response, $duration);

        return $response;
    }

    private function detectSuspiciousActivity(Request $request, Response $response): void
    {
        if (!config('security.monitoring.detect_session_hijacking')) {
            return;
        }

        $currentFingerprint = $this->getRequestFingerprint($request);
        $sessionFingerprint = session('security.fingerprint');

        if ($sessionFingerprint && $sessionFingerprint !== $currentFingerprint && auth()->check()) {
            app(SecurityAuditService::class)->logSuspiciousActivity(
                'Session fingerprint mismatch - possible session hijacking',
                [
                    'user_id' => auth()->id(),
                    'expected' => $sessionFingerprint,
                    'received' => $currentFingerprint,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]
            );
        }

        session(['security.fingerprint' => $currentFingerprint]);
    }

    private function getRequestFingerprint(Request $request): string
    {
        return sha1($request->ip() . '|' . $request->userAgent());
    }

    private function logIfNeeded(Request $request, Response $response, float $duration): void
    {
        if (!config('security.audit.log_all_requests')) {
            return;
        }

        if (in_array($request->method(), ['GET', 'HEAD']) && $response->isSuccessful()) {
            return;
        }

        if ($response->getStatusCode() >= 400) {
            app(SecurityAuditService::class)->log(
                'http_error',
                "HTTP {$response->getStatusCode()}: {$request->method()} {$request->fullUrl()}",
                [
                    'status' => $response->getStatusCode(),
                    'duration_ms' => round($duration, 2),
                ],
                severity: $response->getStatusCode() >= 500 ? 'warning' : 'info'
            );
        }
    }
}
