<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roleSlug)
    {
        $roles = Session::get('user_roles', []);

        if (!in_array($roleSlug, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden: insufficient role.'], 403);
            }
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}