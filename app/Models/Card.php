<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Card extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'element',
        'name',
        'slug',
        'effect',
        'effect_raw',
        'flavor',
        'cost_memory',
        'cost_reserve',
        'level',
        'power',
        'life',
        'durability',
        'speed',
        'last_update',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'last_update' => 'timestamp',
    ];

    public function types(): belongsToMany
    {
        return $this->belongsToMany(Type::class, 'card_types')->using(CardType::class);
    }

    public function classes(): belongsToMany
    {
        return $this->belongsToMany(Classe::class, 'card_classes', 'card_id', 'class_id')->using(CardClass::class);
    }

    public function subtypes(): belongsToMany
    {
        return $this->belongsToMany(Subtype::class, 'card_subtypes')->using(CardSubtype::class);
    }

    public function editions(): HasMany
    {
        return $this->hasMany(Edition::class, 'card_id');
    }

    public function listings(): HasManyThrough
    {
        return $this->HasManyThrough(Edition::class, Listing::class);
    }

}
