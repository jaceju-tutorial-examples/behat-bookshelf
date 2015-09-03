<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CheckoutHistory extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'returned',
    ];

    public function scopeOfUser(Builder $query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeNotReturned(Builder $query)
    {
        return $query->where('returned', false);
    }

    public function scopeNotReturnedByUser(Builder $query, $userId)
    {
        return $query->ofUser($userId)->notReturned();
    }
}
