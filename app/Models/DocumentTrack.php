<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentTrack extends Model
{
    protected $fillable = [
        'document_id',
        'user_id',
        'received_at',
        'released_at',
    ];

    protected function casts(): array
    {
        return [
            'received_at' => 'datetime',
            'released_at' => 'datetime',
        ];
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
