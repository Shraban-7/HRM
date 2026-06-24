<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Helpers\HashHelper;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Request;

class CustomAuthService
{
    public function login(string $identifier, string $password, bool $remember = false): ?User
    {
        $user = User::where('email', $identifier)
            ->orWhere('username', $identifier)
            ->first();

        if (!$user) {
            return null;
        }

        if ($user->status !== 'active') {
            return null;
        }

        if (!HashHelper::verifyPassword($password, $user->salt, $user->password_hash)) {
            $this->incrementLoginAttempts($user);
            return null;
        }

        $this->clearLoginAttempts($user);
        $this->createUserSession($user, $remember);

        return $user;
    }

    public function logout(User $user): void
    {
        Session::forget('user_id');
        Session::forget('user_roles');
        Session::forget('user_permissions');
        Session::regenerate(true);
    }

    public function validateCredentials(User $user, string $password): bool
    {
        return HashHelper::verifyPassword($password, $user->salt, $user->password_hash);
    }

    public function lockAccountIfNeeded(User $user): void
    {
        $attempts = (int)Session::get('login_attempts_' . $user->id, 0);
        if ($attempts >= 5) {
            $user->update(['status' => 'locked']);
        }
    }

    public function getCurrentUser(): ?User
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return null;
        }
        return User::with('roles.permissions')->find($userId);
    }

    public function isAuthenticated(): bool
    {
        return Session::has('user_id');
    }

    private function createUserSession(User $user, bool $remember): void
    {
        Session::put('user_id', $user->id);
        Session::put('user_name', $user->username);

        $roles = $user->roles()->with('permissions')->get();
        Session::put('user_roles', $roles->pluck('slug')->toArray());

        $permissions = $roles->flatMap(function ($role) {
            return $role->permissions->pluck('slug');
        })->unique()->values()->toArray();
        Session::put('user_permissions', $permissions);

        if ($remember) {
            Session::setExpiration(60 * 24 * 30); // 30 days
        }

        Session::regenerate(true);
    }

    private function incrementLoginAttempts(User $user): void
    {
        $key = 'login_attempts_' . $user->id;
        $attempts = (int)Session::get($key, 0) + 1;
        Session::put($key, $attempts);

        if ($attempts >= 5) {
            $user->update(['status' => 'locked']);
        }
    }

    private function clearLoginAttempts(User $user): void
    {
        Session::forget('login_attempts_' . $user->id);
    }
}