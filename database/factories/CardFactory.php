<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Card;

class CardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Card::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'element' => $this->faker->word(),
            'name' => $this->faker->name(),
            'slug' => $this->faker->slug(),
            'effect' => $this->faker->text(),
            'effect_raw' => $this->faker->text(),
            'flavor' => $this->faker->word(),
            'cost_memory' => $this->faker->word(),
            'cost_reserve' => $this->faker->word(),
            'level' => $this->faker->word(),
            'power' => $this->faker->word(),
            'life' => $this->faker->word(),
            'durability' => $this->faker->word(),
            'speed' => $this->faker->word(),
            'last_update' => $this->faker->dateTime(),
        ];
    }
}
