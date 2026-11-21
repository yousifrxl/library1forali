<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'ISBN' => $this->ISBN,
            'author' => $this->author,
            'category_id' => $this->category_id,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'is_available' => ! $this->borrowings()
                ->wherePivot('is_returned', false)
                ->exists(),
            'borrowing_date' => $this->whenPivotLoaded('borrowings', fn () => $this->pivot->borrowing_date),
            'return_date' => $this->whenPivotLoaded('borrowings', fn () => $this->pivot->return_date),
            'is_returned' => $this->whenPivotLoaded('borrowings', fn () => (bool) $this->pivot->is_returned),
        ];
    }
}
