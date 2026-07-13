<?php

namespace App\Models;

use Database\Factories\ArtaSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtaSetting extends Model
{
    use HasFactory;

    protected static function newFactory(): ArtaSettingFactory
    {
        return ArtaSettingFactory::new();
    }

    protected $table = 'arta_settings';

    protected $fillable = [
        'category',
        'title',
        'days',
        'hours',
        'minutes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'days' => 'integer',
            'hours' => 'integer',
            'minutes' => 'integer',
        ];
    }

    public function getDurationLabelAttribute(): string
    {
        $parts = [];
        if ($this->days) {
            $parts[] = $this->days.' day'.($this->days > 1 ? 's' : '');
        }
        if ($this->hours) {
            $parts[] = $this->hours.' hour'.($this->hours > 1 ? 's' : '');
        }
        if ($this->minutes) {
            $parts[] = $this->minutes.' minute'.($this->minutes > 1 ? 's' : '');
        }

        return $parts ? implode(', ', $parts) : '—';
    }
}
