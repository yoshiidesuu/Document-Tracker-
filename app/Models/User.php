<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasSecurityFeatures;

#[Fillable([
    'name',
    'email',
    'password',
    'password_changed_at',
    'email_hash',
    'last_login_at',
    'last_login_ip',
    'mfa_secret',
    'mfa_enabled',
    'terms_accepted_at',
    'privacy_accepted_at',
    'profile_picture',
    'id_number',
    'firstname',
    'middlename',
    'lastname',
    'department_id',
    'office_id',
    'age',
    'gender',
    'bday',
    'ip',
    'geolocation',
    'status',
    'locked',
    'banned',
    'login_count',
])]
#[Hidden([
    'password',
    'remember_token',
    'mfa_secret',
    'email_hash',
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasSecurityFeatures;

    protected array $encryptable = ['email'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_changed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'mfa_enabled' => 'boolean',
            'mfa_recovery_codes_generated_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
            'privacy_accepted_at' => 'datetime',
            'login_count' => 'integer',
            'locked' => 'boolean',
            'banned' => 'boolean',
            'bday' => 'date',
            'age' => 'integer',
            'geolocation' => 'json',
        ];
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                if (!empty($attributes['firstname']) || !empty($attributes['lastname'])) {
                    $parts = array_filter([
                        $attributes['firstname'] ?? null,
                        $attributes['middlename'] ?? null,
                        $attributes['lastname'] ?? null,
                    ]);
                    return !empty($parts) ? implode(' ', $parts) : ($value ?? '');
                }
                return $value ?? '';
            },
        );
    }

    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->firstname,
            $this->middlename,
            $this->lastname,
        ]);
        return !empty($parts) ? implode(' ', $parts) : ($this->attributes['name'] ?? '');
    }

    public function getProfilePictureUrlAttribute(): ?string
    {
        if (!$this->profile_picture) return null;
        if (str_starts_with($this->profile_picture, 'http')) return $this->profile_picture;
        $filename = basename($this->profile_picture);
        return route('file.profile', ['filename' => $filename]);
    }

    public function getInitialsAttribute(): string
    {
        $initials = '';
        if ($this->firstname) $initials .= strtoupper($this->firstname[0]);
        if ($this->lastname) $initials .= strtoupper($this->lastname[0]);
        return $initials ?: strtoupper(substr($this->attributes['name'] ?? 'U', 0, 2));
    }

    public function isBanned(): bool
    {
        return (bool) $this->banned;
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->banned && !$this->locked;
    }

    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function office(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : func_get_args();
        return $this->roles()->whereIn('slug', $roles)->exists();
    }

    public function hasPermission(string $permission): bool
    {
        if ($permission === 'permissions.manage' && $this->hasRole('admin')) {
            return true;
        }
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) return true;
        }
        return false;
    }
}
