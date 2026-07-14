<?php

namespace App\Models;

use Database\Factories\DocumentTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    protected static function newFactory(): DocumentTypeFactory
    {
        return DocumentTypeFactory::new();
    }

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'document_type_id');
    }
}
