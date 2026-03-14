<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }

    public function getFilamentName(): string
    {
        return $this->employee?->fullname ?? $this->email;
    }

    public function hasActiveSession(): bool
    {
        return DB::table('sessions')
            ->where('user_id', $this->id)
            ->exists();
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'user_id');
    }
}
