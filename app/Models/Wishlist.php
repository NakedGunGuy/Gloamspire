<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'edition_id', 'card_count', 'is_foil'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function edition()
    {
        return $this->belongsTo(Edition::class, 'edition_id');
    }

    public function card()
    {
        return $this->HasManyThrough(Card::class, Edition::class);
    }
}

