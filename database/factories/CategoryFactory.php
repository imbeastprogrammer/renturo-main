<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true) . ' ' . fake()->randomNumber(3),
        ];
    }

    /**
     * Indicate that the category is for residential properties.
     */
    public function residential(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Residential',
        ]);
    }

    /**
     * Indicate that the category is for commercial properties.
     */
    public function commercial(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Commercial',
        ]);
    }

    /**
     * Indicate that the category is for vehicles.
     */
    public function vehicles(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Vehicles',
        ]);
    }
}

