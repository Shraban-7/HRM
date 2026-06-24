<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\HashHelper;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username|min:3',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $salt = HashHelper::generateSalt();
        $user = User::create([
            'email' => $request->input('email'),
            'username' => $request->input('username'),
            'password_hash' => HashHelper::hashPassword($request->input('password'), $salt),
            'salt' => $salt,
            'status' => 'active',
        ]);

        return redirect()->route('auth.login')
            ->with('success', 'Account created successfully. Please login.');
    }
}