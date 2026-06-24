<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\TokenService;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    protected TokenService $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->input('email'))->first();

        if ($user) {
            $reset = $this->tokenService->createResetToken($user);
            // In production, send email with $reset->plain_token
        }

        return redirect()->route('auth.login')
            ->with('success', 'If the email exists, a reset link has been sent.');
    }
}