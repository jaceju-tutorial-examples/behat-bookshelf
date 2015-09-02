<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'name',
        'available',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checkoutHistories()
    {
        return $this->hasMany(CheckoutHistory::class);
    }
}
