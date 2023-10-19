<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // Validation
        $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|min:5',
        ]);

        // Create a new user
        $user = new User;
        $user->username = $request->input('username');
        $user->password = Hash::make($request->input('password'));

        $user->save();

        return response()->json(['message' => 'User registered successfully']);
    }

    public function login(Request $request)
    {
        // Validation
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('username', 'password'))) {
            // Authentication successful
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['access_token' => $token, 'user' => $user]);
        } else {
            // Authentication failed
            throw ValidationException::withMessages([
                'username' => 'Invalid credentials',
            ]);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}


