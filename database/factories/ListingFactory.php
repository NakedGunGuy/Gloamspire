<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listing>
 */
class ListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'user_id' => \App\Models\User::inRandomOrder()->value('id'), // Associate with a user
            'card_count' => $this->faker->numberBetween(1, 50), // Random card count
            'edition_id' => \App\Models\Edition::inRandomOrder()->value('id'), // Random existing edition_id
        ];
    }
}
