<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Passenger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'type' => 'required|in:user,passenger',
        ]);

        if ($validated['type'] === 'user') {
            $account = User::where('email', $validated['email'])->first();
        } else {
            $account = Passenger::where('email', $validated['email'])->first();
        }

        if (! $account || ! Hash::check($validated['password'], $account->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $account->tokens()->delete();

        $token = $account->createToken($validated['type'] . '-api-token')->plainTextToken;

        $expirationMinutes = config('sanctum.expiration');

        $expiresAt = $expirationMinutes
            ? now()->addMinutes($expirationMinutes)
            : null;

        $data = [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt,
            'type' => $validated['type'],
        ];

        if ($validated['type'] === 'user') {
            $data['user'] = $account;
            $data['roles'] = $account->getRoleNames();
        } else {
            $data['passenger'] = $account;
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ]);
    }

    public function me(Request $request)
    {
        $account = $request->user();

        if ($account instanceof User) {
            return response()->json([
                'success' => true,
                'data' => [
                    'type' => 'user',
                    'user' => $account,
                    'roles' => $account->getRoleNames(),
                ],
            ]);
        }

        if ($account instanceof Passenger) {
            $account->load('flights');

            return response()->json([
                'success' => true,
                'data' => [
                    'type' => 'passenger',
                    'passenger' => $account,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid account type',
        ], 403);
    }
}