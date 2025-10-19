<?php

namespace Database\Factories;

use App\Models\Listing;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listing>
 */
class ListingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Listing::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->randomElement([
            'Premium Basketball Court',
            'Indoor Basketball Facility',
            'Outdoor Basketball Court',
            'Professional Basketball Arena',
            'Community Basketball Court',
            'Half Court Basketball',
            'Full Court Basketball Complex',
        ]) . ' - ' . $this->faker->city;

        $pricePerHour = $this->faker->randomFloat(2, 200, 1500);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'sub_category_id' => SubCategory::inRandomOrder()->first()?->id ?? null,
            'listing_type' => Listing::TYPE_SPORTS,
            'dynamic_form_id' => null,
            'dynamic_form_submission_id' => null,
            'title' => $title,
            'description' => $this->faker->paragraphs(3, true),
            'slug' => Str::slug($title),
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'province' => $this->faker->randomElement([
                'Metro Manila', 'Cebu', 'Davao', 'Quezon City', 'Manila',
                'Makati', 'Pasig', 'Taguig', 'Parañaque', 'Las Piñas',
            ]),
            'postal_code' => $this->faker->postcode,
            'latitude' => $this->faker->latitude(14.4, 14.8), // Metro Manila range
            'longitude' => $this->faker->longitude(120.9, 121.2),
            'price_per_hour' => $pricePerHour,
            'price_per_day' => $pricePerHour * 8 * 0.9, // 10% discount for full day
            'price_per_week' => null,
            'price_per_month' => null,
            'currency' => 'PHP',
            'max_capacity' => $this->faker->randomElement([10, 12, 15, 20, 24, 30]),
            'amenities' => $this->faker->randomElements([
                'parking',
                'restroom',
                'changing_room',
                'water_station',
                'wifi',
                'lockers',
                'scoreboard',
                'sound_system',
                'air_conditioning',
                'lighting',
                'seating',
                'referee',
            ], $this->faker->numberBetween(3, 8)),
            'status' => $this->faker->randomElement([
                Listing::STATUS_ACTIVE,
                Listing::STATUS_ACTIVE,
                Listing::STATUS_ACTIVE, // Higher chance of active
                Listing::STATUS_DRAFT,
                Listing::STATUS_INACTIVE,
            ]),
            'visibility' => Listing::VISIBILITY_PUBLIC,
            'is_featured' => $this->faker->boolean(20), // 20% chance
            'is_verified' => $this->faker->boolean(70), // 70% chance
            'instant_booking' => $this->faker->boolean(60), // 60% chance
            'minimum_booking_hours' => $this->faker->randomElement([1, 2, 3]),
            'maximum_booking_hours' => $this->faker->randomElement([4, 6, 8, null]),
            'advance_booking_days' => $this->faker->randomElement([7, 14, 30, 60]),
            'cancellation_hours' => $this->faker->randomElement([6, 12, 24, 48]),
            'views_count' => $this->faker->numberBetween(0, 1000),
            'bookings_count' => $this->faker->numberBetween(0, 50),
            'average_rating' => $this->faker->randomFloat(2, 3.5, 5.0),
            'reviews_count' => $this->faker->numberBetween(0, 30),
            'meta_title' => null,
            'meta_description' => null,
            'meta_keywords' => null,
            'published_at' => $this->faker->optional(0.8)->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * Indicate that the listing is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Listing::STATUS_ACTIVE,
            'published_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ]);
    }

    /**
     * Indicate that the listing is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Listing::STATUS_DRAFT,
            'published_at' => null,
        ]);
    }

    /**
     * Indicate that the listing is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'status' => Listing::STATUS_ACTIVE,
            'published_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ]);
    }

    /**
     * Indicate that the listing is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
        ]);
    }

    /**
     * Indicate that the listing allows instant booking.
     */
    public function instantBooking(): static
    {
        return $this->state(fn (array $attributes) => [
            'instant_booking' => true,
        ]);
    }

    /**
     * Indicate that the listing is in a specific city.
     */
    public function inCity(string $city): static
    {
        return $this->state(fn (array $attributes) => [
            'city' => $city,
        ]);
    }

    /**
     * Indicate that the listing is for a specific type.
     */
    public function ofType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'listing_type' => $type,
        ]);
    }

    /**
     * Indicate that the listing has high ratings.
     */
    public function highRating(): static
    {
        return $this->state(fn (array $attributes) => [
            'average_rating' => $this->faker->randomFloat(2, 4.5, 5.0),
            'reviews_count' => $this->faker->numberBetween(20, 100),
        ]);
    }

    /**
     * Indicate that the listing is popular (many views and bookings).
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'views_count' => $this->faker->numberBetween(500, 2000),
            'bookings_count' => $this->faker->numberBetween(50, 200),
            'average_rating' => $this->faker->randomFloat(2, 4.0, 5.0),
            'reviews_count' => $this->faker->numberBetween(30, 100),
        ]);
    }
}
