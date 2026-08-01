<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

if (! function_exists('auth_effective_user')) {
    function auth_effective_user(): ?User
    {
        $user = Auth::user();

        if ($user) {
            return $user;
        }

        $userId = session('user_id');

        if (! $userId) {
            return null;
        }

        return User::find($userId);
    }
}

if (! function_exists('auth_effective_permissions')) {
    function auth_effective_permissions(): array
    {
        $sessionPermissions = (array) session('user_permissions', []);
        $user = auth_effective_user();

        if (! $user) {
            return $sessionPermissions;
        }

        $dbPermissions = $user->roles()->with('permissions')->get()
            ->flatMap(fn ($role) => $role->permissions->pluck('slug'))
            ->unique()
            ->values()
            ->toArray();

        return array_values(array_unique(array_merge($sessionPermissions, $dbPermissions)));
    }
}

if (! function_exists('auth_has_permission')) {
    function auth_has_permission(string $slug): bool
    {
        return in_array($slug, auth_effective_permissions(), true);
    }
}

if (! function_exists('auth_has_role')) {
    function auth_has_role(string $slug): bool
    {
        $user = auth_effective_user();

        if (! $user) {
            return in_array($slug, (array) session('user_roles', []), true);
        }

        $sessionRoles = (array) session('user_roles', []);

        return in_array($slug, $sessionRoles, true) || $user->hasRole($slug);
    }
}
