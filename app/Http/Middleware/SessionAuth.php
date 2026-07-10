<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class SessionAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in via session
        if (!session()->has('auth_user_id')) {
            return redirect()->route('login');
        }

        // Load fresh user from database
        $user = User::find(session('auth_user_id'));
        if (!$user) {
            session()->forget('auth_user_id');
            return redirect()->route('login');
        }

        // Bind user to request
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
