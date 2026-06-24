<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\CustomAuthService;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    protected CustomAuthService $authService;

    public function __construct(CustomAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function logout(Request $request)
    {
        $user = $this->authService->getCurrentUser();
        if ($user) {
            $this->authService->logout($user);
        }

        return redirect()->route('auth.login')
            ->with('success', 'You have been logged out successfully.');
    }
}