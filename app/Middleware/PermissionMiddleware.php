<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permissionSlug)
    {
        $permissions = Session::get('user_permissions', []);

        if (!in_array($permissionSlug, $permissions)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden: insufficient permission.'], 403);
            }
            return redirect()->route('dashboard')
                ->with('error', 'You do not have the required permission.');
        }

        return $next($request);
    }
}