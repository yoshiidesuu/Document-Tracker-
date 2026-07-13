<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $fillable = [
        'user_id',
        'activity',
        'description',
        'metadata',
        'ip_address',
        'geolocation',
        'old_data',
        'new_data',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'geolocation' => 'array',
            'old_data' => 'array',
            'new_data' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActivity($query, string $activity)
    {
        return $query->where('activity', $activity);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
