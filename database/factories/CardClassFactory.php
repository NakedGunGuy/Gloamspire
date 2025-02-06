<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Card;
use App\Models\CardClass;
use App\Models\Class;

class CardClassFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CardClass::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'card_id' => Card::factory(),
            'class_id' => Class::factory(),
        ];
    }
}
