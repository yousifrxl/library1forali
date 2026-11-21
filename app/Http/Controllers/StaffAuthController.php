<?php

namespace App\Http\Controllers;

use App\Http\Resources\StaffResource;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $staff = Staff::where('email', $credentials['email'])->first();

        if (! $staff || ! Hash::check($credentials['password'], $staff->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'token' => $staff->createToken('auth_token')->plainTextToken,
        ]);
    }

    public function me(Request $request): StaffResource
    {
        return new StaffResource($request->user('staff'));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user('staff')->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
