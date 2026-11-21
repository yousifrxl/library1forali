<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection(Category::with('books')->get());
    }

    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category->load('books'));
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        return (new CategoryResource(Category::create($request->validated())))
            ->response()->setStatusCode(201);
    }

    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource
    {
        $category->update($request->validated());

        return new CategoryResource($category->refresh());
    }

    public function destroy(Category $category): JsonResponse
    {
        try {
            $category->delete();
        } catch (QueryException) {
            return response()->json(['message' => 'Cannot delete a category that still has books.'], 409);
        }

        return response()->json([], 204);
    }
}
