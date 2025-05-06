<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller{

    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'password_confirmation' => 'required|same:password',
            ]);
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => bcrypt($validatedData['password']),
            ]);
            $token =    $user->createToken('auth_token')->plainTextToken;
            return response()->json(['user' => UserResource::make($user), 'token' => $token],201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Registration failed', 'error' => $e->getMessage()], 500);
        }
    }
    /**
     * Login a user.
     */
    public function login(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|string|max:255',
                'password' => 'required|string',
            ]);

            if (!Auth::attempt($validatedData)) {
                return response()->json(['message' => 'User not found or wrong password'], 404);
            }
            $user = Auth::user();

            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json(['user' => UserResource::make($user), 'token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Login failed', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Logout a user.
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Logout failed', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the authenticated user.
     */
    public function me(Request $request)
    {
        try {
            return response()->json(['user' => UserResource::make(auth()->user()->load('technician'))]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch user data', 'error' => $e->getMessage()], 500);
        }
    }
}
