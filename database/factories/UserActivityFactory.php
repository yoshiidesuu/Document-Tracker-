<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserActivityFactory extends Factory
{
    protected $model = UserActivity::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => fake()->randomElement(['login', 'logout', 'created', 'updated', 'deleted', 'viewed', 'exported', 'password_changed', 'profile_updated']),
            'model_type' => fake()->randomElement(['App\Models\User', 'App\Models\Document', 'App\Models\Department', 'App\Models\Office', 'App\Models\Role']),
            'model_id' => fake()->numberBetween(1, 100),
            'description' => fake()->sentence(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'details' => null,
            'created_at' => now(),
        ];
    }
}
