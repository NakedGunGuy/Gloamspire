<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Cart extends Model
{
    protected $fillable = ['listing_id', 'user_id', 'amount'];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function card()
    {
        return $this->HasManyThrough(Card::class, Edition::class, Listing::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
