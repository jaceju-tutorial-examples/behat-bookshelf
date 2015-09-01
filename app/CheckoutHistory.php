<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CheckoutHistory extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'returned',
    ];
}
