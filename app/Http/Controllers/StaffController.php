<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffStoreRequest;
use App\Http\Requests\StaffUpdateRequest;
use App\Http\Resources\StaffResource;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StaffController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return StaffResource::collection(Staff::all());
    }

    public function store(StaffStoreRequest $request): JsonResponse
    {
        $staff = Staff::create($request->validated());

        return (new StaffResource($staff))->response()->setStatusCode(201);
    }

    public function show($id): StaffResource
    {
        $staff = Staff::findOrFail($id);

        return new StaffResource($staff);
    }

    public function update($id, StaffUpdateRequest $request): StaffResource
    {
        $staff = Staff::findOrFail($id);
        $staff->update($request->validated());

        return new StaffResource($staff->refresh());
    }

    public function destroy($id): JsonResponse
    {
        $staff = Staff::findOrFail($id);
        $staff->delete();

        return response()->json(['message' => 'Staff deleted successfully']);
    }
}
