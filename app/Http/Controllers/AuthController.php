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

        // Store user ID in session
        session(['auth_user_id' => $user->id]);

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

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            if ($request->expectsJson()) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }
            return back()->withErrors(['email' => 'The provided credentials are incorrect.'])->withInput($request->only('email'));
        }

        // Store user ID in session
        session(['auth_user_id' => $user->id]);

        if ($request->expectsJson()) {
            $token = $user->createToken('auth-token')->plainTextToken;
            return response()->json(['user' => $user, 'token' => $token]);
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        session()->forget('auth_user_id');
        
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }
        
        return redirect()->route('login');
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
