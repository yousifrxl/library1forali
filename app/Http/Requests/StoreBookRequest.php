<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'author' => ['required', 'string'],
            'ISBN' => ['required', 'string', Rule::unique('books', 'ISBN')],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
        ];
    }
}
