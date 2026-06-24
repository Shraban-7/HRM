<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\TokenService;
use App\Helpers\HashHelper;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    protected TokenService $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->input('email'))->first();
        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        if (!$this->tokenService->validateResetToken($request->input('token'), $user->id)) {
            return back()->withErrors(['token' => 'Invalid or expired token.']);
        }

        $salt = HashHelper::generateSalt();
        $user->update([
            'password_hash' => HashHelper::hashPassword($request->input('password'), $salt),
            'salt' => $salt,
        ]);

        return redirect()->route('auth.login')
            ->with('success', 'Password reset successfully. Please login.');
    }
}