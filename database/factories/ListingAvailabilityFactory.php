<?php

namespace Database\Factories;

use App\Models\ListingAvailability;
use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListingAvailability>
 */
class ListingAvailabilityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ListingAvailability::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'listing_id' => Listing::factory(),
            'availability_type' => ListingAvailability::TYPE_RECURRING,
            'day_of_week' => $this->faker->numberBetween(0, 6),
            'start_time' => $this->faker->randomElement(['06:00:00', '07:00:00', '08:00:00', '09:00:00']),
            'end_time' => $this->faker->randomElement(['17:00:00', '18:00:00', '20:00:00', '22:00:00']),
            'available_date' => null,
            'start_date' => null,
            'end_date' => null,
            'price_override' => null,
            'is_available' => true,
            'notes' => $this->faker->optional(0.3)->sentence,
        ];
    }

    /**
     * Indicate that this is a recurring availability.
     */
    public function recurring(): static
    {
        return $this->state(fn (array $attributes) => [
            'availability_type' => ListingAvailability::TYPE_RECURRING,
            'day_of_week' => $this->faker->numberBetween(0, 6),
            'start_time' => $this->faker->randomElement(['06:00:00', '07:00:00', '08:00:00', '09:00:00']),
            'end_time' => $this->faker->randomElement(['17:00:00', '18:00:00', '20:00:00', '22:00:00']),
            'available_date' => null,
            'start_date' => null,
            'end_date' => null,
        ]);
    }

    /**
     * Indicate that this is a specific date availability.
     */
    public function specificDate($date = null): static
    {
        return $this->state(fn (array $attributes) => [
            'availability_type' => ListingAvailability::TYPE_SPECIFIC_DATE,
            'day_of_week' => null,
            'available_date' => $date ?? $this->faker->dateTimeBetween('now', '+30 days'),
            'start_date' => null,
            'end_date' => null,
        ]);
    }

    /**
     * Indicate that this is a date range availability.
     */
    public function dateRange($startDate = null, $endDate = null): static
    {
        $start = $startDate ?? $this->faker->dateTimeBetween('now', '+30 days');
        $end = $endDate ?? $this->faker->dateTimeBetween($start, '+60 days');

        return $this->state(fn (array $attributes) => [
            'availability_type' => ListingAvailability::TYPE_DATE_RANGE,
            'day_of_week' => null,
            'available_date' => null,
            'start_date' => $start,
            'end_date' => $end,
        ]);
    }

    /**
     * Indicate that this slot is blocked/unavailable.
     */
    public function blocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'availability_type' => ListingAvailability::TYPE_BLOCKED,
            'is_available' => false,
            'notes' => $this->faker->randomElement([
                'Maintenance day',
                'Private event',
                'Holiday',
                'Closed for repairs',
            ]),
        ]);
    }

    /**
     * Indicate that this slot has a price override.
     */
    public function withPriceOverride(float $price = null): static
    {
        return $this->state(fn (array $attributes) => [
            'price_override' => $price ?? $this->faker->randomFloat(2, 300, 2000),
        ]);
    }

    /**
     * Indicate that this is for a specific day of the week.
     */
    public function forDay(int $dayOfWeek): static
    {
        return $this->state(fn (array $attributes) => [
            'availability_type' => ListingAvailability::TYPE_RECURRING,
            'day_of_week' => $dayOfWeek,
        ]);
    }

    /**
     * Morning hours (6 AM - 12 PM).
     */
    public function morning(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_time' => '06:00:00',
            'end_time' => '12:00:00',
        ]);
    }

    /**
     * Afternoon hours (12 PM - 6 PM).
     */
    public function afternoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_time' => '12:00:00',
            'end_time' => '18:00:00',
        ]);
    }

    /**
     * Evening hours (6 PM - 10 PM).
     */
    public function evening(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_time' => '18:00:00',
            'end_time' => '22:00:00',
        ]);
    }

    /**
     * Full day availability (6 AM - 10 PM).
     */
    public function fullDay(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_time' => '06:00:00',
            'end_time' => '22:00:00',
        ]);
    }
}
