<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'roles',
        'email',
        'password',
        'is_active',
        'last_activity',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'roles' => 'array',
        ];
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles ?? []);
    }

    public function hasAnyRole(array $roles): bool
    {
        return !empty(array_intersect($roles, $this->roles ?? []));
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array('admin', $this->roles ?? []);
    }

    public function getFilamentName(): string
    {
        return $this->employee?->fullname ?? $this->email;
    }

    public function hasActiveSession(): bool
    {
        if (!in_array('admin', $this->roles ?? [])) {
            return false;
        }

        return $this->last_activity &&
            now()->diffInMinutes($this->last_activity) < 5;
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'user_id');
    }
}
