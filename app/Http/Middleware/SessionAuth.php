<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class SessionAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Check session first
        if (session()->has('auth_user_id')) {
            $user = User::find(session('auth_user_id'));
            if ($user) {
                $request->setUserResolver(fn () => $user);
                return $next($request);
            }
        }

        // Fallback: check Bearer token (for API clients)
        $token = $request->bearerToken();
        if ($token) {
            $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $user = $accessToken->tokenable;
                if ($user) {
                    $request->setUserResolver(fn () => $user);
                    return $next($request);
                }
            }
        }

        // Not authenticated
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return redirect()->route('login');
    }
}
