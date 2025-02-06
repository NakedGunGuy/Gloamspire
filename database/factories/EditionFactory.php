<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Card;
use App\Models\Edition;
use App\Models\Set;

class EditionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Edition::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'card_id' => Card::factory()->create()->uuid,
            'collector_number' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'illustrator' => $this->faker->word(),
            'rarity' => $this->faker->word(),
            'last_update' => $this->faker->dateTime(),
            'set_id' => Set::factory(),
        ];
    }
}
