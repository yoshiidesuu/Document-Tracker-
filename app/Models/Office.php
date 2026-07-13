<?php

namespace App\Models;

use Database\Factories\OfficeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    protected static function newFactory(): OfficeFactory
    {
        return OfficeFactory::new();
    }

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'department_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
