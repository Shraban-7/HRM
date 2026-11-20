<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PersonalAccessToken extends Model
{
    protected $fillable = ['user_id', 'name', 'token', 'abilities', 'last_used_at', 'expires_at'];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'abilities' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function createToken(User $user, string $name, array $abilities = ['*']): string
    {
        $plainTextToken = Str::random(40);
        $hash = hash('sha256', $plainTextToken);

        static::create([
            'user_id' => $user->id,
            'name' => $name,
            'token' => $hash,
            'abilities' => $abilities,
            'expires_at' => now()->addDays(30),
        ]);

        return $plainTextToken;
    }

    public static function findToken(string $plainTextToken): ?self
    {
        $hash = hash('sha256', $plainTextToken);

        $token = static::where('token', $hash)->first();

        if (! $token) {
            return null;
        }

        if ($token->expires_at && $token->expires_at->isPast()) {
            return null;
        }

        $token->update(['last_used_at' => now()]);

        return $token;
    }
}
