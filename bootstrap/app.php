<?php

use App\Http\Middleware\BlockIpsMiddleware;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\FileUploadValidationMiddleware;
use App\Http\Middleware\ForceHttpsMiddleware;
use App\Http\Middleware\InputSanitizeMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Http\Middleware\SecurityMonitorMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeadersMiddleware::class);
        $middleware->append(SecurityMonitorMiddleware::class);
        $middleware->append(BlockIpsMiddleware::class);
        $middleware->append(InputSanitizeMiddleware::class);
        $middleware->append(ForceHttpsMiddleware::class);

        $middleware->alias([
            'security.headers' => SecurityHeadersMiddleware::class,
            'security.sanitize' => InputSanitizeMiddleware::class,
            'security.monitor' => SecurityMonitorMiddleware::class,
            'security.https' => ForceHttpsMiddleware::class,
            'security.uploads' => FileUploadValidationMiddleware::class,
            'role' => CheckRole::class,
        ]);

        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => 'Resource not found.'], 404);
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() === 429) {
                return response()->json([
                    'message' => 'Too many requests. Please slow down.',
                    'retry_after' => $e->getHeaders()['Retry-After'] ?? null,
                ], 429);
            }
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $message = app()->isLocal()
                    ? $e->getMessage()
                    : 'An internal server error occurred.';

                return response()->json(['message' => $message], 500);
            }
        });

        $exceptions->throttle(function (Request $request, Throwable $e) {
            if ($request->is('api/*')) {
                return ThrottleRequests::with(
                    config('security.rate_limiting.api.max_attempts', 120),
                    config('security.rate_limiting.api.decay_minutes', 1)
                );
            }
        });
    })->create();
