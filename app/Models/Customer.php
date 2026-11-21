<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = ['name', 'email', 'phone_number', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['password' => 'hashed'];

    public function borrowings(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'borrowings')
            ->withPivot(['borrowing_date', 'return_date', 'is_returned'])
            ->withTimestamps();
    }
}
