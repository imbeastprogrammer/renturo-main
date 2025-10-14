<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SubCategory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubCategory>
 */
class SubCategoryFactory extends Factory
{
    protected $model = SubCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->words(2, true) . ' ' . fake()->randomNumber(3),
        ];
    }

    /**
     * Indicate that the subcategory belongs to a specific category.
     */
    public function forCategory(Category $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $category->id,
        ]);
    }

    /**
     * Indicate that the subcategory is an apartment.
     */
    public function apartment(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Apartment',
        ]);
    }

    /**
     * Indicate that the subcategory is a car.
     */
    public function car(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Car',
        ]);
    }
}

