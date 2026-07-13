<?php

namespace App\Traits;

use App\Models\FailedLoginAttempt;
use App\Models\PasswordHistory;
use App\Models\SecurityLog;
use App\Models\UserActivity;
use App\Rules\StrongPassword;
use App\Services\EncryptionService;
use App\Services\SecurityAuditService;
use Illuminate\Support\Facades\Hash;

trait HasSecurityFeatures
{
    public static function bootHasSecurityFeatures(): void
    {
        static::creating(function ($model) {
            if (! $model->password_changed_at && $model->password) {
                $model->password_changed_at = now();
            }
            if (config('security.encryption.encrypt_pii') && $model->isEncryptable('email') && $model->email) {
                $model->email_hash = app(EncryptionService::class)->hashEmail($model->email);
            }
            $model->status ??= 'active';
            $model->locked ??= false;
            $model->banned ??= false;
        });

        static::updated(function ($model) {
            if ($model->wasChanged('password')) {
                $oldHash = $model->getOriginal('password');
                if ($oldHash) {
                    PasswordHistory::create([
                        'user_id' => $model->id,
                        'password_hash' => $oldHash,
                    ]);
                }

                app(SecurityAuditService::class)->logPasswordChange($model->id);

                $historyCount = config('security.password.history_count', 5);
                $toKeep = PasswordHistory::where('user_id', $model->id)
                    ->orderBy('created_at', 'desc')
                    ->take($historyCount)
                    ->pluck('id');

                PasswordHistory::where('user_id', $model->id)
                    ->whereNotIn('id', $toKeep)
                    ->delete();
            }
        });
    }

    public function isEncryptable(string $field): bool
    {
        return in_array($field, $this->encryptable ?? []);
    }

    public function isPasswordExpired(): bool
    {
        $maxAgeDays = config('security.password.max_age_days', 90);
        if ($maxAgeDays <= 0) {
            return false;
        }
        if (! $this->password_changed_at) {
            return true;
        }

        return $this->password_changed_at->addDays($maxAgeDays)->isPast();
    }

    public function passwordExpiresInDays(): ?int
    {
        $maxAgeDays = config('security.password.max_age_days', 90);
        if (! $this->password_changed_at || $maxAgeDays <= 0) {
            return null;
        }
        $expiresAt = $this->password_changed_at->addDays($maxAgeDays);
        if ($expiresAt->isPast()) {
            return 0;
        }

        return now()->diffInDays($expiresAt, false);
    }

    public function isAccountLocked(): bool
    {
        if ($this->banned) {
            return true;
        }

        return (bool) $this->locked;
    }

    public function hasExceededMaxLoginAttempts(): bool
    {
        $maxAttempts = config('security.auth.max_login_attempts', 5);
        $lockoutMinutes = config('security.auth.lockout_time_minutes', 15);
        $attempts = FailedLoginAttempt::where('email', $this->email)
            ->where('created_at', '>=', now()->subMinutes($lockoutMinutes))
            ->count();

        return $attempts >= $maxAttempts;
    }

    public function incrementLoginAttempts(): void
    {
        FailedLoginAttempt::create([
            'email' => $this->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        if ($this->hasExceededMaxLoginAttempts()) {
            $this->forceFill(['locked' => true])->save();
            app(SecurityAuditService::class)->logAccountLockout($this->email);
        }
    }

    public function clearLoginAttempts(): void
    {
        FailedLoginAttempt::where('email', $this->email)->delete();
        if ($this->locked) {
            $this->forceFill(['locked' => false])->save();
        }
    }

    public function lock(): void
    {
        $this->forceFill(['locked' => true])->save();
        app(SecurityAuditService::class)->logAccountLockout($this->email);
    }

    public function unlock(): void
    {
        $this->forceFill(['locked' => false])->save();
        FailedLoginAttempt::where('email', $this->email)->delete();
    }

    public function ban(): void
    {
        $this->forceFill(['banned' => true, 'status' => 'banned'])->save();
        app(SecurityAuditService::class)->log('user_banned', "User banned: {$this->email}", null, $this->id);
    }

    public function unban(): void
    {
        $this->forceFill(['banned' => false, 'status' => 'active'])->save();
        app(SecurityAuditService::class)->log('user_unbanned', "User unbanned: {$this->email}", null, $this->id);
    }

    public function getPasswordValidationRules(): array
    {
        $rules = ['required', 'string', new StrongPassword];
        $historyCount = config('security.password.history_count', 5);
        if ($historyCount > 0) {
            $rules[] = function ($attribute, $value, $fail) use ($historyCount) {
                $recentHashes = $this->passwordHistories()
                    ->orderBy('created_at', 'desc')
                    ->take($historyCount)
                    ->pluck('password_hash');
                foreach ($recentHashes as $oldHash) {
                    if (Hash::check($value, $oldHash)) {
                        $fail('You cannot reuse a recent password.');

                        return;
                    }
                }
            };
        }

        return $rules;
    }

    public function securityLogs()
    {
        return $this->hasMany(SecurityLog::class);
    }

    public function userActivities()
    {
        return $this->hasMany(UserActivity::class);
    }

    public function failedLoginAttempts()
    {
        return $this->hasMany(FailedLoginAttempt::class, 'email', 'email');
    }

    public function passwordHistories()
    {
        return $this->hasMany(PasswordHistory::class);
    }
}
