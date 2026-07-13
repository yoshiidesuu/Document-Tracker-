<?php

namespace Database\Factories;

use App\Models\Office;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfficeFactory extends Factory
{
    protected $model = Office::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company() . ' Office',
            'description' => fake()->paragraph(),
            'is_active' => fake()->boolean(90),
        ];
    }
}