<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roleSlug)
    {
        $roles = Session::get('user_roles');

        if ($roles === null && $request->user()) {
            $user = $request->user();
            if ($user->hasRole($roleSlug) || $user->hasRole('super_admin')) {
                return $next($request);
            }
        } elseif (is_array($roles)) {
            if (in_array($roleSlug, $roles) || in_array('super_admin', $roles)) {
                return $next($request);
            }
            if ($request->user() && ($request->user()->hasRole($roleSlug) || $request->user()->hasRole('super_admin'))) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Forbidden: insufficient role.'], 403);
        }

        return redirect()->route('dashboard')
            ->with('error', 'You do not have permission to access this page.');
    }
}
