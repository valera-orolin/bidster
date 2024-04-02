<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lot>
 */
class LotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->words(3, true),
            'address' => fake()->address(),
            'description' => fake()->paragraph(15),
            'end_date' => fake()->dateTimeBetween('now', '+30 days'),
            'starting_price' => fake()->randomFloat(2, 1, 100),
        ];
    }
}
