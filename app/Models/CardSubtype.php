<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CardSubtype extends Pivot
{
    use HasFactory;

    protected $table = 'card_subtypes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'card_id',
        'subtype_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    public function cards(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public function types(): BelongsTo
    {
        return $this->belongsTo(Subtype::class);
    }
}
