<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $firstname = fake()->firstName();
        $lastname = fake()->lastName();

        return [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'name' => "{$firstname} {$lastname}",
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'password_changed_at' => now(),
            'remember_token' => Str::random(10),
            'status' => 'active',
            'locked' => false,
            'banned' => false,
            'age' => fake()->numberBetween(18, 80),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'bday' => fake()->date('Y-m-d', '2005-01-01'),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function banned(): static
    {
        return $this->state(fn (array $attributes) => [
            'banned' => true,
            'status' => 'banned',
        ]);
    }

    public function locked(): static
    {
        return $this->state(fn (array $attributes) => [
            'locked' => true,
            'status' => 'locked',
        ]);
    }
}
