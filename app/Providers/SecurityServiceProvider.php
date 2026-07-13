<?php

namespace App\Providers;

use App\Http\Middleware\ForceHttpsMiddleware;
use App\Http\Middleware\InputSanitizeMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Http\Middleware\SecurityMonitorMiddleware;
use App\Services\EncryptionService;
use App\Services\SecurityAuditService;
use Illuminate\Support\ServiceProvider;

class SecurityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EncryptionService::class, fn () => new EncryptionService);
        $this->app->singleton(SecurityAuditService::class, fn () => new SecurityAuditService);
    }

    public function boot(): void
    {
        $this->configureLogging();

        $this->configureCors();

        if (config('security.network.enforce_https')) {
            $this->app->make('url')->forceScheme('https');
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\SecurityAuditCleanup::class,
            ]);
        }
    }

    private function configureLogging(): void
    {
        $channel = config('security.audit.channel', 'security');
        config([
            "logging.channels.{$channel}" => [
                'driver' => 'daily',
                'path' => storage_path("logs/{$channel}.log"),
                'level' => 'debug',
                'days' => config('security.audit.retention_days', 365),
                'replace_placeholders' => true,
            ],
        ]);
    }

    private function configureCors(): void
    {
        if (!config()->has('cors')) return;
        config([
            'cors.paths' => ['api/*'],
            'cors.allowed_methods' => config('security.cors.allowed_methods'),
            'cors.allowed_origins' => config('security.cors.allowed_origins'),
            'cors.supports_credentials' => config('security.cors.allow_credentials'),
            'cors.max_age' => config('security.cors.max_age'),
        ]);
    }
}
