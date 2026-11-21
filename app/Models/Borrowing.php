<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Borrowing extends Model
{
    protected $fillable = [
        'customer_id',
        'book_id',
        'borrowing_date',
        'return_date',
        'is_returned',
    ];

    protected $casts = [
        'borrowing_date' => 'date',
        'return_date' => 'date',
        'is_returned' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
