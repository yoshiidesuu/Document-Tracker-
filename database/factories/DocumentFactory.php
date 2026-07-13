<?php

namespace Database\Factories;

use App\Models\ArtaSetting;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'document_type' => fake()->randomElement(['Memorandum', 'Letter', 'Report', 'Circular', 'Order']),
            'creator_id' => User::factory(),
            'processing_hours' => fake()->numberBetween(1, 48),
            'qr_value' => 'QR-'.strtoupper(fake()->unique()->bothify('??####')),
            'barcode_value' => 'BAR-'.strtoupper(fake()->unique()->bothify('??####')),
            'is_private' => fake()->boolean(10),
            'access_key' => null,
            'arta_setting_id' => ArtaSetting::factory(),
            'arta_category' => fake()->randomElement(['simple', 'complex', 'highly_technical']),
            'notes' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(['pending', 'received', 'in_progress', 'finished', 'terminated', 'reopened']),
            'termination_reason' => null,
        ];
    }

    public function received(): static
    {
        return $this->state(fn () => [
            'status' => 'received',
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => 'in_progress',
        ]);
    }

    public function finished(): static
    {
        return $this->state(fn () => [
            'status' => 'finished',
        ]);
    }

    public function terminated(): static
    {
        return $this->state(fn () => [
            'status' => 'terminated',
            'termination_reason' => fake()->sentence(),
        ]);
    }
}
