<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\DocumentTrack;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentTrackFactory extends Factory
{
    protected $model = DocumentTrack::class;

    public function definition(): array
    {
        return [
            'document_id' => Document::factory(),
            'user_id' => User::factory(),
            'received_at' => now(),
            'released_at' => null,
        ];
    }
}
