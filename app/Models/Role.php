<?php

namespace App\Models;

use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'permissions', 'is_system'];

    protected static function newFactory(): RoleFactory
    {
        return RoleFactory::new();
    }

    protected function casts(): array
    {
        return [
            'permissions' => 'json',
            'is_system' => 'boolean',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function rolePermissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }

    public function permissions(): HasMany
    {
        return $this->rolePermissions();
    }

    public function hasPermission(string $permission): bool
    {
        // Check both JSON array and database permissions
        $jsonPermissions = $this->permissions ?? [];
        if (in_array($permission, $jsonPermissions)) {
            return true;
        }

        // Also check database permissions
        return $this->rolePermissions()->where('name', $permission)->exists();
    }
}
