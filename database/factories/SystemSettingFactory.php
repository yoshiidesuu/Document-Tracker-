<?php

namespace Database\Factories;

use App\Models\SystemSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SystemSettingFactory extends Factory
{
    protected $model = SystemSetting::class;

    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(2),
            'value' => fake()->sentence(),
            'type' => fake()->randomElement(['string', 'integer', 'boolean', 'json']),
            'group' => fake()->randomElement(['general', 'security', 'email', 'appearance', 'document']),
            'description' => fake()->sentence(),
            'is_public' => fake()->boolean(30),
        ];
    }
}
