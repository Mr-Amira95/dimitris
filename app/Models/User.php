<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'username',
        'full_name',
        'email',
        'password',
        'role_id',
        'is_active',
        'password_setup_token',
        'password_setup_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'password_setup_token',
    ];

    protected function casts(): array
    {
        return [
            'password'                 => 'hashed',
            'is_active'                => 'boolean',
            'password_setup_expires_at' => 'datetime',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin(): bool
    {
        return $this->role?->slug === 'admin';
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->role?->hasPermission($permission) ?? false;
    }

    public function hasPasswordSetup(): bool
    {
        return $this->password !== null;
    }
}
