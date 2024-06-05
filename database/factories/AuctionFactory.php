<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Auction>
 */
class AuctionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $updated_at = $this->faker->dateTimeBetween('-1 month');
        $created_at = $this->faker->dateTimeBetween('-1 month', $updated_at);

        return [
            'status' => $this->faker->randomElement(['Active', 'Finished', 'Failed']),
            'created_at' => $created_at,
            'updated_at' => $updated_at,
        ];
    }
}
