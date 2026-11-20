<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'email', 'username', 'password', 'status', 'require_2fa',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles()->where('slug', $slug)->exists();
    }

    public function hasPermission(string $slug): bool
    {
        return $this->roles()->whereHas('permissions', function ($q) use ($slug) {
            $q->where('slug', $slug);
        })->exists();
    }

    public function hasAnyPermission(array $slugs): bool
    {
        return $this->roles()->whereHas('permissions', function ($q) use ($slugs) {
            $q->whereIn('slug', $slugs);
        })->exists();
    }
}
