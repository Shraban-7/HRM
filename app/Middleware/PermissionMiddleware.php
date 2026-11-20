<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permissionSlug)
    {
        $permissions = Session::get('user_permissions');

        if ($permissions === null && $request->user()) {
            $user = $request->user();
            if ($user->hasRole('super_admin') || $user->hasPermission($permissionSlug)) {
                return $next($request);
            }
        } elseif (is_array($permissions)) {
            if (in_array($permissionSlug, $permissions)) {
                return $next($request);
            }
            if ($request->user() && $request->user()->hasRole('super_admin')) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Forbidden: insufficient permission.'], 403);
        }

        return redirect()->route('dashboard')
            ->with('error', 'You do not have the required permission.');
    }
}
