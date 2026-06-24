<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    protected $fillable = [
        'email', 'username', 'password_hash', 'salt', 'status', 'require_2fa'
    ];

    protected $hidden = ['password_hash', 'salt', 'remember_token'];

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