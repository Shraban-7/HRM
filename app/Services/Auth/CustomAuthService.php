<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class CustomAuthService
{
    public function login(string $identifier, string $password, bool $remember = false): ?User
    {
        $user = User::where('email', $identifier)
            ->orWhere('username', $identifier)
            ->first();

        if (! $user) {
            return null;
        }

        if ($user->status !== 'active') {
            return null;
        }

        if (! $this->validateCredentials($user, $password)) {
            $this->incrementLoginAttempts($user);

            return null;
        }

        $this->clearLoginAttempts($user);
        Auth::login($user, $remember);
        $this->createUserSession($user, $remember);

        return $user;
    }

    public function logout(User $user): void
    {
        Auth::logout();
        Session::forget('user_id');
        Session::forget('user_roles');
        Session::forget('user_permissions');
        Session::regenerate(true);
    }

    public function validateCredentials(User $user, string $password): bool
    {
        // 1. Primary verification using Laravel Hash facade
        try {
            if (Hash::check($password, $user->password)) {
                return true;
            }
        } catch (\RuntimeException $e) {
            // Non-bcrypt legacy hash encountered, fall through to legacy handler
        }

        // 2. Legacy fallback: check salted SHA-256
        $salt = $user->getAttribute('salt');
        if (! empty($salt) && hash('sha256', $salt . $password) === $user->password) {
            $user->password = Hash::make($password);
            if (Schema::hasColumn('users', 'salt')) {
                DB::table('users')->where('id', $user->id)->update(['salt' => null]);
            }
            $user->save();

            return true;
        }

        // 3. Legacy fallback: check unsalted SHA-256
        if (hash('sha256', $password) === $user->password) {
            $user->password = Hash::make($password);
            $user->save();

            return true;
        }

        return false;
    }

    public function lockAccountIfNeeded(User $user): void
    {
        $attempts = (int) Session::get('login_attempts_'.$user->id, 0);
        if ($attempts >= 5) {
            $user->update(['status' => 'locked']);
        }
    }

    public function getCurrentUser(): ?User
    {
        $user = Auth::user();

        if ($user) {
            return $user->load('roles.permissions');
        }

        $userId = Session::get('user_id');
        if (! $userId) {
            return null;
        }

        return User::with('roles.permissions')->find($userId);
    }

    public function isAuthenticated(): bool
    {
        return Auth::check() || Session::has('user_id');
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
        $key = 'login_attempts_'.$user->id;
        $attempts = (int) Session::get($key, 0) + 1;
        Session::put($key, $attempts);

        if ($attempts >= 5) {
            $user->update(['status' => 'locked']);
        }
    }

    private function clearLoginAttempts(User $user): void
    {
        Session::forget('login_attempts_'.$user->id);
    }
}
