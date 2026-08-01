<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\TokenService;
use App\Services\Notification\EmailNotificationService;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    protected TokenService $tokenService;

    protected EmailNotificationService $emailService;

    public function __construct(TokenService $tokenService, EmailNotificationService $emailService)
    {
        $this->tokenService = $tokenService;
        $this->emailService = $emailService;
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
            $this->emailService->sendPasswordResetEmail($user, $reset->plain_token);
        }

        return redirect()->route('auth.login')
            ->with('success', 'If the email exists, a reset link has been sent.');
    }
}
