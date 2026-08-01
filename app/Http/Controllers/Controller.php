<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Resolve the currently authenticated user.
     */
    protected function authenticatedUser(): ?User
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

    /**
     * Resolve the employee profile bound to the authenticated user.
     */
    protected function authenticatedEmployee(): ?Employee
    {
        $user = $this->authenticatedUser();

        if (! $user) {
            return null;
        }

        return Employee::where('user_id', $user->id)->first();
    }
}
