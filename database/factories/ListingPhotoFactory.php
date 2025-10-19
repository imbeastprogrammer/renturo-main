<?php

namespace Database\Factories;

use App\Models\ListingPhoto;
use App\Models\Listing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListingPhoto>
 */
class ListingPhotoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ListingPhoto::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $width = $this->faker->randomElement([1920, 1600, 1280, 1024]);
        $height = $this->faker->randomElement([1080, 900, 720, 768]);
        
        // Generate basketball court placeholder images
        $photoUrl = $this->faker->randomElement([
            'https://via.placeholder.com/' . $width . 'x' . $height . '/4CAF50/ffffff?text=Basketball+Court',
            'https://via.placeholder.com/' . $width . 'x' . $height . '/2196F3/ffffff?text=Indoor+Court',
            'https://via.placeholder.com/' . $width . 'x' . $height . '/FF9800/ffffff?text=Outdoor+Court',
            'https://via.placeholder.com/' . $width . 'x' . $height . '/9C27B0/ffffff?text=Premium+Facility',
        ]);

        $thumbnailUrl = str_replace($width . 'x' . $height, '400x300', $photoUrl);

        return [
            'listing_id' => Listing::factory(),
            'photo_url' => $photoUrl,
            'thumbnail_url' => $thumbnailUrl,
            'original_filename' => $this->faker->uuid . '.jpg',
            'caption' => $this->faker->optional(0.5)->sentence,
            'alt_text' => $this->faker->sentence(3),
            'sort_order' => 0, // Will be auto-set in model boot
            'is_primary' => false,
            'storage_path' => 'listings/' . $this->faker->uuid . '.jpg',
            'storage_disk' => 'public',
            'file_size' => $this->faker->numberBetween(500000, 5000000), // 500KB to 5MB
            'mime_type' => 'image/jpeg',
            'width' => $width,
            'height' => $height,
        ];
    }

    /**
     * Indicate that this is the primary photo.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
            'sort_order' => 0,
        ]);
    }

    /**
     * Indicate that this photo has a specific sort order.
     */
    public function sortOrder(int $order): static
    {
        return $this->state(fn (array $attributes) => [
            'sort_order' => $order,
        ]);
    }
}
