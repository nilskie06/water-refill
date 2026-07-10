<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SessionAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Start session if not started
        if (!$request->hasSession() && $request->hasCookie(session()->getName())) {
            $request->setLaravelSession(session()->driver()->start($request));
        }

        if (\Illuminate\Support\Facades\Auth::guard('web')->check()) {
            $request->setUserResolver(fn () => \Illuminate\Support\Facades\Auth::guard('web')->user());
            return $next($request);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return redirect()->route('login');
    }
}
