<?php

namespace Database\Factories;

use App\Models\ArtaSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArtaSettingFactory extends Factory
{
    protected $model = ArtaSetting::class;

    public function definition(): array
    {
        $category = fake()->randomElement(['simple', 'complex', 'highly_technical']);
        $days = fake()->numberBetween(1, 20);
        $title = fake()->unique()->words(2, true).' '.ucfirst($category).' Setting';

        return [
            'category' => $category,
            'title' => $title,
            'days' => $days,
            'hours' => null,
            'minutes' => null,
            'is_active' => fake()->boolean(90),
        ];
    }
}
