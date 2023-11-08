<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $dummyOwner = User::where('email', 'owner@main.renturo.test')->first();

        return [
            'user_id' => $dummyOwner->id,
            'title' => fake()->title(),
            'description' => fake()->sentence(3),
            'address' => fake()->address()
        ];
    }
}
