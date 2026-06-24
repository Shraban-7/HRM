<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CustomAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('user_id')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('auth.login')->with('error', 'Please login first.');
        }

        return $next($request);
    }
}