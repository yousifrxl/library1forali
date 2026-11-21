<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class BorrowingsController extends Controller
{
    public function borrowings(Request $request): JsonResponse
    {
        return response()->json(
            $request->user('customer')->borrowings()->with('category')->get(),
        );
    }

    public function borrow(Request $request): JsonResponse
    {
        $data = $request->validate([
            'book_id' => ['required', 'integer', Rule::exists('books', 'id')],
            'borrowing_date' => ['required', 'date', 'after_or_equal:today'],
            'return_date' => ['required', 'date', 'after:borrowing_date'],
        ]);

        $customer = $request->user('customer');
        $book = Book::findOrFail($data['book_id']);

        $openBorrowings = $customer->borrowings()->wherePivot('is_returned', false);
        if ($openBorrowings->count() >= 3) {
            return response()->json(['message' => 'A customer may have at most three open borrowings.'], 422);
        }

        $overlap = $book->borrowings()
            ->wherePivot('is_returned', false)
            ->wherePivot('borrowing_date', '<=', $data['return_date'])
            ->wherePivot('return_date', '>=', $data['borrowing_date'])
            ->exists();

        if ($overlap) {
            return response()->json(['message' => 'This book is already borrowed for some of those dates.'], 422);
        }

        $customer->borrowings()->attach($book->id, [
            'borrowing_date' => $data['borrowing_date'],
            'return_date' => $data['return_date'],
            'is_returned' => false,
        ]);

        return response()->json(['message' => 'Borrowing created successfully.']);
    }

    public function delete(Request $request, int $id): JsonResponse
    {
        $borrowing = Borrowing::query()
            ->where('customer_id', $request->user('customer')->id)
            ->where('book_id', $id)
            ->where('is_returned', false)
            ->orderBy('id')
            ->first();

        if (! $borrowing) {
            return response()->json(['message' => 'Open borrowing not found.'], 404);
        }

        $borrowing->delete();

        return response()->json(['message' => 'Borrowing removed.']);
    }

    public function markAsReturned(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer', Rule::exists('customers', 'id')],
            'book_id' => ['required', 'integer', Rule::exists('books', 'id')],
        ]);

        $customer = Customer::findOrFail($data['customer_id']);
        $borrowing = Borrowing::query()
            ->where('customer_id', $customer->id)
            ->where('book_id', $data['book_id'])
            ->where('is_returned', false)
            ->orderBy('id')
            ->first();

        if (! $borrowing) {
            return response()->json(['message' => 'This customer has no open borrowing for this book.'], 404);
        }

        $borrowing->update(['is_returned' => true]);

        return response()->json([
            'message' => 'Book marked as returned.',
            'customer_id' => $customer->id,
            'book_id' => $borrowing->book_id,
            'borrowing_date' => $borrowing->borrowing_date->toDateString(),
        ]);
    }

    public function all(): JsonResponse
    {
        return response()->json(Borrowing::with(['customer', 'book'])->get());
    }

    public function overdue(): JsonResponse
    {
        return response()->json(
            Borrowing::with(['customer', 'book'])
                ->where('is_returned', false)
                ->whereDate('return_date', '<', Carbon::today())
                ->get(),
        );
    }
}
