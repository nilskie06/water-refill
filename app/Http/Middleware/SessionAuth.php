<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SessionAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Ensure session is started
        if (!$request->hasSession() && $request->cookies->has(session()->getName())) {
            $request->setLaravelSession(
                \Illuminate\Support\Facades\Session::driver()->start($request)
            );
        }

        // Check if user is authenticated via web guard
        $user = \Illuminate\Support\Facades\Auth::guard('web')->user();
        
        if ($user) {
            $request->setUserResolver(fn () => $user);
            return $next($request);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return redirect()->route('login');
    }
}
