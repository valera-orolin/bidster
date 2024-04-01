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
            'title' => $this->faker->words(3, true),
            'address' => $this->faker->address,
            'description' => $this->faker->paragraph,
            'end_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'starting_price' => $this->faker->randomFloat(2, 1, 100),
        ];
    }
}
