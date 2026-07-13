<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'sender_id' => User::factory(),
            'receiver_id' => User::factory(),
            'subject' => fake()->sentence(4),
            'message' => fake()->paragraph(3),
            'is_read' => fake()->boolean(50),
            'read_at' => fake()->boolean(50) ? now()->subMinutes(rand(1, 1440)) : null,
            'created_at' => now(),
        ];
    }
}
