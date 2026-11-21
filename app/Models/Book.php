<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    protected $fillable = ['name', 'author', 'ISBN', 'category_id'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function borrowings(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'borrowings')
            ->withPivot(['borrowing_date', 'return_date', 'is_returned'])
            ->withTimestamps();
    }
}
