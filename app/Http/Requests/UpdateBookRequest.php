<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $book = $this->route('book');
        $bookId = $book instanceof \App\Models\Book ? $book->id : $book;

        return [
            'name' => ['sometimes', 'required', 'string'],
            'author' => ['sometimes', 'required', 'string'],
            'ISBN' => ['sometimes', 'required', 'string', Rule::unique('books', 'ISBN')->ignore($bookId)],
            'category_id' => ['sometimes', 'required', 'integer', Rule::exists('categories', 'id')],
        ];
    }
}
