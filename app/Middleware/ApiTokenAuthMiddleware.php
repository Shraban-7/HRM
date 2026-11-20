<?php

namespace App\Middleware;

use App\Models\PersonalAccessToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization');

        if (! $header || ! str_starts_with($header, 'Bearer ')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated: Bearer token is missing.',
            ], 401);
        }

        $plainText = substr($header, 7);
        $token = PersonalAccessToken::findToken($plainText);

        if (! $token || ! $token->user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated: Token is invalid or expired.',
            ], 401);
        }

        Auth::setUser($token->user);
        $request->setUserResolver(fn () => $token->user);

        return $next($request);
    }
}
