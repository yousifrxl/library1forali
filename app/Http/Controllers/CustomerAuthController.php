<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'phone_number' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $customer = Customer::where('phone_number', $credentials['phone_number'])->first();

        if (! $customer || ! Hash::check($credentials['password'], $customer->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'token' => $customer->createToken('customer_token')->plainTextToken,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['customer' => $request->user('customer')]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user('customer')->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
