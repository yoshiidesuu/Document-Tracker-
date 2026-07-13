<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RedirectIfAuthenticated::redirectUsing(fn (Request $request) => route('system.dashboard'));

        RateLimiter::for('api', function (Request $request) {
            $limit = config('security.rate_limiting.api.max_attempts', 120);
            $decay = config('security.rate_limiting.api.decay_minutes', 1);
            return Limit::perMinute($limit)->by($request->user()?->id ?: $request->ip());
        });

        try {
            if (SystemSetting::get('smtp_host')) {
                $encryption = SystemSetting::get('smtp_encryption', 'tls');
                config([
                    'mail.default' => 'smtp',
                    'mail.mailers.smtp.host' => SystemSetting::get('smtp_host'),
                    'mail.mailers.smtp.port' => (int) SystemSetting::get('smtp_port', '587'),
                    'mail.mailers.smtp.encryption' => $encryption === 'none' ? null : $encryption,
                    'mail.mailers.smtp.username' => SystemSetting::get('smtp_username'),
                    'mail.mailers.smtp.password' => SystemSetting::get('smtp_password'),
                    'mail.from.address' => SystemSetting::get('mail_from_address'),
                    'mail.from.name' => SystemSetting::get('mail_from_name', config('app.name')),
                ]);
            }
        } catch (\Throwable $e) {
            // DB may not be ready yet (migrations not run)
        }
    }
}
