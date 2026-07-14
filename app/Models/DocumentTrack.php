<?php

namespace App\Models;

use Database\Factories\DocumentTrackFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTrack extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return DocumentTrackFactory::new();
    }

    protected $fillable = [
        'document_id',
        'user_id',
        'action',
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
