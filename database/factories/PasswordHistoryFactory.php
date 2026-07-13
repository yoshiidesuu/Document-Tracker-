<?php

namespace Database\Factories;

use App\Models\PasswordHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PasswordHistoryFactory extends Factory
{
    protected $model = PasswordHistory::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'password' => bcrypt(fake()->password(12)),
            'created_at' => now()->subDays(rand(1, 365)),
        ];
    }
}
