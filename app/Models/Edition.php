<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Edition extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'card_id',
        'card_uuid',
        'collector_number',
        'slug',
        'illustrator',
        'flavor',
        'rarity',
        'last_update',
        'set_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'last_update' => 'timestamp',
        'set_id' => 'integer',
    ];

    public function set(): BelongsTo
    {
        return $this->belongsTo(Set::class);
    }

    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }
}
