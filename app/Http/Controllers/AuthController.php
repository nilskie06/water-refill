<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'in:admin,staff',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'staff',
        ]);

        \Illuminate\Support\Facades\Auth::login($user);

        if ($request->expectsJson()) {
            $token = $user->createToken('auth-token')->plainTextToken;
            return response()->json(['user' => $user, 'token' => $token], 201);
        }

        return redirect()->route('dashboard')->with('success', 'Account created!');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (!\Illuminate\Support\Facades\Auth::attempt($credentials)) {
            if ($request->expectsJson()) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }
            return back()->withErrors(['email' => 'The provided credentials are incorrect.'])->withInput($request->only('email'));
        }

        if ($request->expectsJson()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            $token = $user->createToken('auth-token')->plainTextToken;
            return response()->json(['user' => $user, 'token' => $token]);
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
