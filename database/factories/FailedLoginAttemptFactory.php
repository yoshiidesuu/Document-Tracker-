<?php

namespace Database\Factories;

use App\Models\FailedLoginAttempt;
use Illuminate\Database\Eloquent\Factories\Factory;

class FailedLoginAttemptFactory extends Factory
{
    protected $model = FailedLoginAttempt::class;

    public function definition(): array
    {
        return [
            'identifier' => fake()->email(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'attempts' => fake()->numberBetween(1, 10),
            'last_attempt_at' => now(),
            'locked_until' => fake()->boolean(20) ? now()->addMinutes(rand(15, 60)) : null,
        ];
    }
}