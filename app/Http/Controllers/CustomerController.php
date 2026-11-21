<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('customers', 'email')],
            'phone_number' => ['required', 'string', 'max:30', Rule::unique('customers', 'phone_number')],
            'password' => ['required', 'string', 'min:8'],
        ]);
        $data['password'] = Hash::make($data['password']);
        $customer = Customer::create($data);
        $token = $customer->createToken('login')->plainTextToken;

        return response()->json([
            'customer' => $customer,
            'token' => $token,
        ], 201);
    }

    public function index()
    {
        return CustomerResource::collection(Customer::all());
    }

    public function show(Customer $customer): CustomerResource
    {
        return new CustomerResource($customer->load('borrowings'));
    }
}
