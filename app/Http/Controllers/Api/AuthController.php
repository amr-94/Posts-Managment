<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user' // default role
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(
            [
                'status' => true,
                'message' => 'User registered successfully',
                'data' => $user,
                'token' => $token,
            ],
            201,
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Invalid credentials',
                ],
                401,
            );
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(
            [
                'status' => true,
                'message' => 'Login successful',
                'data' => $user,
                'token' => $token,
            ],
            200,
        );
    }

    public function logout(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json(
            [
                'status' => true,
                'message' => 'Logged out successfully',
            ],
            200,
        );
    }

    public function profile(): JsonResponse
    {
        return response()->json(
            [
                'status' => true,
                'message' => 'User profile',
                'data' => Auth::user(),
            ],
            200,
        );
    }
}
