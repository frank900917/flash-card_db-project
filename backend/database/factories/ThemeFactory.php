<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Theme>
 */
class ThemeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word() . ' Theme ' . fake()->numberBetween(10, 99),
            'bg_color' => fake()->unique()->hexColor(),
            'desc' => fake()->sentence(8),
            'unlock_level' => fake()->numberBetween(1, 10),
            'created_at' => fake()->dateTimeBetween('-2 year'),
            'updated_at' => fake()->dateTimeBetween('-1 year'),
        ];
    }
}
