<?php

namespace Tests\Unit\Models;

use Tests\TestCase\TenantTestCase;
use App\Models\Listing;
use App\Models\ListingPhoto;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListingPhotoTest extends TenantTestCase
{
    use RefreshDatabase;

    protected Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();

        // Run tenant migrations
        $this->artisan('migrate', ['--path' => 'database/migrations/tenant']);

        $user = User::factory()->create();
        $category = Category::factory()->create();
        
        $this->listing = Listing::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
    }

    /** @test */
    public function it_can_create_a_listing_photo()
    {
        $photo = ListingPhoto::factory()->create([
            'listing_id' => $this->listing->id,
            'photo_url' => 'https://example.com/photo.jpg',
        ]);

        $this->assertInstanceOf(ListingPhoto::class, $photo);
        $this->assertEquals('https://example.com/photo.jpg', $photo->photo_url);
        $this->assertDatabaseHas('listing_photos', [
            'photo_url' => 'https://example.com/photo.jpg',
        ]);
    }

    /** @test */
    public function it_belongs_to_a_listing()
    {
        $photo = ListingPhoto::factory()->create([
            'listing_id' => $this->listing->id,
        ]);

        $this->assertInstanceOf(Listing::class, $photo->listing);
        $this->assertEquals($this->listing->id, $photo->listing->id);
    }

    /** @test */
    public function it_auto_sets_sort_order_when_creating()
    {
        $photo1 = ListingPhoto::create([
            'listing_id' => $this->listing->id,
            'photo_url' => 'https://example.com/photo1.jpg',
            'storage_disk' => 'public',
        ]);

        $photo2 = ListingPhoto::create([
            'listing_id' => $this->listing->id,
            'photo_url' => 'https://example.com/photo2.jpg',
            'storage_disk' => 'public',
        ]);

        $photo3 = ListingPhoto::create([
            'listing_id' => $this->listing->id,
            'photo_url' => 'https://example.com/photo3.jpg',
            'storage_disk' => 'public',
        ]);

        $this->assertEquals(1, $photo1->sort_order);
        $this->assertEquals(2, $photo2->sort_order);
        $this->assertEquals(3, $photo3->sort_order);
    }

    /** @test */
    public function it_auto_unsets_other_primary_photos_when_setting_primary()
    {
        $photo1 = ListingPhoto::factory()->primary()->create([
            'listing_id' => $this->listing->id,
        ]);

        $photo2 = ListingPhoto::factory()->primary()->create([
            'listing_id' => $this->listing->id,
        ]);

        $photo1->refresh();
        $photo2->refresh();

        $this->assertFalse($photo1->is_primary);
        $this->assertTrue($photo2->is_primary);
    }

    /** @test */
    public function it_only_unsets_primary_photos_for_same_listing()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        
        $listing2 = Listing::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $photo1 = ListingPhoto::factory()->primary()->create([
            'listing_id' => $this->listing->id,
        ]);

        $photo2 = ListingPhoto::factory()->primary()->create([
            'listing_id' => $listing2->id,
        ]);

        $photo1->refresh();
        $photo2->refresh();

        // Both should still be primary since they're for different listings
        $this->assertTrue($photo1->is_primary);
        $this->assertTrue($photo2->is_primary);
    }

    /** @test */
    public function it_checks_if_photo_is_primary()
    {
        $primaryPhoto = ListingPhoto::factory()->primary()->create([
            'listing_id' => $this->listing->id,
        ]);

        $regularPhoto = ListingPhoto::factory()->create([
            'listing_id' => $this->listing->id,
            'is_primary' => false,
        ]);

        $this->assertTrue($primaryPhoto->isPrimary());
        $this->assertFalse($regularPhoto->isPrimary());
    }

    /** @test */
    public function it_can_set_photo_as_primary()
    {
        $photo = ListingPhoto::factory()->create([
            'listing_id' => $this->listing->id,
            'is_primary' => false,
        ]);

        $photo->setPrimary();
        $photo->refresh();

        $this->assertTrue($photo->is_primary);
    }

    /** @test */
    public function it_gets_url_with_thumbnail_option()
    {
        $photo = ListingPhoto::factory()->create([
            'listing_id' => $this->listing->id,
            'photo_url' => 'https://example.com/photo.jpg',
            'thumbnail_url' => 'https://example.com/thumb.jpg',
        ]);

        $this->assertEquals('https://example.com/photo.jpg', $photo->getUrl());
        $this->assertEquals('https://example.com/thumb.jpg', $photo->getUrl(true));
    }

    /** @test */
    public function it_returns_main_url_when_thumbnail_not_available()
    {
        $photo = ListingPhoto::factory()->create([
            'listing_id' => $this->listing->id,
            'photo_url' => 'https://example.com/photo.jpg',
            'thumbnail_url' => null,
        ]);

        $this->assertEquals('https://example.com/photo.jpg', $photo->getUrl(true));
    }

    /** @test */
    public function it_gets_human_readable_file_size()
    {
        $photo1 = ListingPhoto::factory()->create([
            'listing_id' => $this->listing->id,
            'file_size' => 2048, // 2 KB
        ]);

        $photo2 = ListingPhoto::factory()->create([
            'listing_id' => $this->listing->id,
            'file_size' => 2097152, // 2 MB
        ]);

        $photo3 = ListingPhoto::factory()->create([
            'listing_id' => $this->listing->id,
            'file_size' => 524288000, // ~500 MB
        ]);

        $this->assertEquals('2 KB', $photo1->getHumanFileSize());
        $this->assertEquals('2 MB', $photo2->getHumanFileSize());
        $this->assertEquals('500 MB', $photo3->getHumanFileSize());
    }

    /** @test */
    public function it_returns_null_when_file_size_not_set()
    {
        $photo = ListingPhoto::factory()->create([
            'listing_id' => $this->listing->id,
            'file_size' => null,
        ]);

        $this->assertNull($photo->getHumanFileSize());
    }

    /** @test */
    public function it_gets_image_dimensions()
    {
        $photo = ListingPhoto::factory()->create([
            'listing_id' => $this->listing->id,
            'width' => 1920,
            'height' => 1080,
        ]);

        $this->assertEquals('1920x1080', $photo->getDimensions());
    }

    /** @test */
    public function it_returns_null_when_dimensions_not_set()
    {
        $photo = ListingPhoto::factory()->create([
            'listing_id' => $this->listing->id,
            'width' => null,
            'height' => null,
        ]);

        $this->assertNull($photo->getDimensions());
    }

    /** @test */
    public function it_checks_if_file_is_an_image()
    {
        $imagePhoto = ListingPhoto::factory()->create([
            'listing_id' => $this->listing->id,
            'mime_type' => 'image/jpeg',
        ]);

        $videoFile = ListingPhoto::factory()->create([
            'listing_id' => $this->listing->id,
            'mime_type' => 'video/mp4',
        ]);

        $this->assertTrue($imagePhoto->isImage());
        $this->assertFalse($videoFile->isImage());
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $photo = ListingPhoto::factory()->create([
            'listing_id' => $this->listing->id,
            'is_primary' => true,
            'sort_order' => 5,
            'file_size' => 1024000,
        ]);

        $this->assertIsBool($photo->is_primary);
        $this->assertIsInt($photo->sort_order);
        $this->assertIsInt($photo->file_size);
    }

    /** @test */
    public function it_soft_deletes_photo()
    {
        $photo = ListingPhoto::factory()->create([
            'listing_id' => $this->listing->id,
        ]);

        $photo->delete();

        $this->assertSoftDeleted('listing_photos', ['id' => $photo->id]);
        $this->assertNotNull($photo->fresh()->deleted_at);
    }
}
