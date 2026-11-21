<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->validate([
            'search' => ['sometimes', 'string', 'max:255'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
        ]);

        $query = Book::query()->with('category');

        if (! empty($filters['search'])) {
            $query->where('name', 'like', '%'.$filters['search'].'%');
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        return BookResource::collection($query->get());
    }

    public function show(Book $book): BookResource
    {
        return new BookResource($book->load('category'));
    }

    public function store(StoreBookRequest $request): JsonResponse
    {
        return (new BookResource(Book::create($request->validated())))
            ->response()->setStatusCode(201);
    }

    public function update(UpdateBookRequest $request, Book $book): BookResource
    {
        $book->update($request->validated());

        return new BookResource($book->refresh());
    }

    public function destroy(Book $book): JsonResponse
    {
        if ($book->borrowings()->exists()) {
            return response()->json([
                'message' => 'Cannot delete a book that has borrowing records.',
            ], 409);
        }

        $book->delete();

        return response()->json([], 204);
    }
}
