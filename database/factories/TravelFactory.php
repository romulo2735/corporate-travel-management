<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Travel>
 */
class TravelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requester_name' => $this->faker->name(),
            'destination' => $this->faker->city(),
            'departure_date' => $this->faker->date(),
            'return_date' => $this->faker->date(),
        ];
    }
}
