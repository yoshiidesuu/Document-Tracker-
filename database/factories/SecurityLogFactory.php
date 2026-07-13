<?php

namespace Database\Factories;

use App\Models\SecurityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SecurityLogFactory extends Factory
{
    protected $model = SecurityLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event' => fake()->randomElement(['login_success', 'login_failed', 'logout', 'password_changed', 'password_reset', 'mfa_enabled', 'mfa_disabled', 'session_expired', 'session_revoked', 'ip_changed', 'suspicious_activity', 'brute_force_detected', 'account_locked', 'account_unlocked', 'account_banned', 'account_unbanned']),
            'description' => fake()->sentence(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'severity' => fake()->randomElement(['info', 'warning', 'critical']),
            'metadata' => null,
            'created_at' => now(),
        ];
    }
}