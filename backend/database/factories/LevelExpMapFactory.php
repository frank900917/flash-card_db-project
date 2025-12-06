<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LevelExpMap>
 */
class LevelExpMapFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'desc' => fake()->sentence(8),
            'exp' => fake()->unique()->numberBetween(0, 1000000),
            'level' => fake()->unique()->numberBetween(1, 10),
            'created_at' => fake()->dateTimeBetween('-2 year'),
            'updated_at' => fake()->dateTimeBetween('-1 year'),
        ];
    }
}
