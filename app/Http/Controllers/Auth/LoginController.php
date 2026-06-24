<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\CustomAuthService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    protected CustomAuthService $authService;

    public function __construct(CustomAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLoginForm()
    {
        if ($this->authService->isAuthenticated()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = $this->authService->login(
            $request->input('identifier'),
            $request->input('password'),
            $request->boolean('remember')
        );

        if (!$user) {
            return back()->withErrors([
                'identifier' => 'Invalid credentials or account is locked.',
            ])->withInput();
        }

        return redirect()->intended(route('dashboard'));
    }
}