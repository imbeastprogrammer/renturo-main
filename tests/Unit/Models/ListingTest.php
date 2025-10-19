<?php

namespace Tests\Unit\Models;

use Tests\TestCase\TenantTestCase;
use App\Models\Listing;
use App\Models\ListingPhoto;
use App\Models\ListingAvailability;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListingTest extends TenantTestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Run tenant migrations and seeders
        $this->artisan('migrate', ['--path' => 'database/migrations/tenant']);
        
        // Create test user and category
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
    }

    /** @test */
    public function it_can_create_a_listing()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Test Basketball Court',
        ]);

        $this->assertInstanceOf(Listing::class, $listing);
        $this->assertEquals('Test Basketball Court', $listing->title);
        $this->assertDatabaseHas('listings', [
            'title' => 'Test Basketball Court',
        ]);
    }

    /** @test */
    public function it_automatically_generates_slug_from_title()
    {
        $listing = Listing::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'listing_type' => 'sports',
            'title' => 'Premium Basketball Court Manila',
            'description' => 'A great basketball court',
            'address' => '123 Main St',
            'city' => 'Manila',
            'province' => 'Metro Manila',
            'status' => 'draft',
        ]);

        $this->assertEquals('premium-basketball-court-manila', $listing->slug);
    }

    /** @test */
    public function it_ensures_slug_uniqueness()
    {
        $listing1 = Listing::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'listing_type' => 'sports',
            'title' => 'Basketball Court',
            'description' => 'A great basketball court',
            'address' => '123 Main St',
            'city' => 'Manila',
            'province' => 'Metro Manila',
            'status' => 'draft',
        ]);

        $listing2 = Listing::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'listing_type' => 'sports',
            'title' => 'Basketball Court',
            'description' => 'A great basketball court',
            'address' => '123 Main St',
            'city' => 'Manila',
            'province' => 'Metro Manila',
            'status' => 'draft',
        ]);

        $this->assertEquals('basketball-court', $listing1->slug);
        $this->assertEquals('basketball-court-1', $listing2->slug);
    }

    /** @test */
    public function it_auto_sets_published_at_when_status_changes_to_active()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => Listing::STATUS_DRAFT,
            'published_at' => null,
        ]);

        $this->assertNull($listing->published_at);

        $listing->update(['status' => Listing::STATUS_ACTIVE]);
        $listing->refresh();

        $this->assertNotNull($listing->published_at);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $this->assertInstanceOf(User::class, $listing->user);
        $this->assertEquals($this->user->id, $listing->user->id);
    }

    /** @test */
    public function it_belongs_to_a_category()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $this->assertInstanceOf(Category::class, $listing->category);
        $this->assertEquals($this->category->id, $listing->category->id);
    }

    /** @test */
    public function it_can_have_many_photos()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        ListingPhoto::factory()->count(3)->create([
            'listing_id' => $listing->id,
        ]);

        $this->assertCount(3, $listing->photos);
        $this->assertInstanceOf(ListingPhoto::class, $listing->photos->first());
    }

    /** @test */
    public function it_can_have_a_primary_photo()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $primaryPhoto = ListingPhoto::factory()->primary()->create([
            'listing_id' => $listing->id,
        ]);

        ListingPhoto::factory()->count(2)->create([
            'listing_id' => $listing->id,
        ]);

        $this->assertInstanceOf(ListingPhoto::class, $listing->primaryPhoto);
        $this->assertEquals($primaryPhoto->id, $listing->primaryPhoto->id);
        $this->assertTrue($listing->primaryPhoto->is_primary);
    }

    /** @test */
    public function it_can_have_availability_slots()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        ListingAvailability::factory()->count(5)->create([
            'listing_id' => $listing->id,
        ]);

        $this->assertCount(5, $listing->availability);
        $this->assertInstanceOf(ListingAvailability::class, $listing->availability->first());
    }

    /** @test */
    public function it_scopes_active_listings()
    {
        Listing::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => Listing::STATUS_ACTIVE,
        ]);

        Listing::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => Listing::STATUS_DRAFT,
        ]);

        $activeListings = Listing::active()->get();
        $this->assertCount(3, $activeListings);
    }

    /** @test */
    public function it_scopes_published_listings()
    {
        Listing::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => Listing::STATUS_ACTIVE,
            'published_at' => now()->subDay(),
        ]);

        Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => Listing::STATUS_ACTIVE,
            'published_at' => now()->addDay(),
        ]);

        Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => Listing::STATUS_DRAFT,
            'published_at' => null,
        ]);

        $publishedListings = Listing::published()->get();
        $this->assertCount(2, $publishedListings);
    }

    /** @test */
    public function it_scopes_featured_listings()
    {
        Listing::factory()->count(2)->featured()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        Listing::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'is_featured' => false,
        ]);

        $featuredListings = Listing::featured()->get();
        $this->assertCount(2, $featuredListings);
    }

    /** @test */
    public function it_can_search_by_keyword()
    {
        Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Premium Basketball Court',
            'description' => 'Best court in Manila',
        ]);

        Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => 'Swimming Pool',
            'description' => 'Great pool',
        ]);

        $results = Listing::search('Basketball')->get();
        $this->assertCount(1, $results);
        $this->assertStringContainsString('Basketball', $results->first()->title);
    }

    /** @test */
    public function it_can_filter_by_category()
    {
        $category2 = Category::factory()->create();

        Listing::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category2->id,
        ]);

        $listings = Listing::byCategory($this->category->id)->get();
        $this->assertCount(2, $listings);
    }

    /** @test */
    public function it_can_filter_by_price_range()
    {
        Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'price_per_hour' => 500,
        ]);

        Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'price_per_hour' => 1500,
        ]);

        Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'price_per_hour' => 2500,
        ]);

        $listings = Listing::byPriceRange(1000, 2000)->get();
        $this->assertCount(1, $listings);
        $this->assertEquals(1500, $listings->first()->price_per_hour);
    }

    /** @test */
    public function it_checks_if_listing_is_active()
    {
        $activeListing = Listing::factory()->active()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $draftListing = Listing::factory()->draft()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $this->assertTrue($activeListing->isActive());
        $this->assertFalse($draftListing->isActive());
    }

    /** @test */
    public function it_checks_if_listing_is_published()
    {
        $publishedListing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => Listing::STATUS_ACTIVE,
            'published_at' => now()->subDay(),
        ]);

        $unpublishedListing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => Listing::STATUS_ACTIVE,
            'published_at' => now()->addDay(),
        ]);

        $this->assertTrue($publishedListing->isPublished());
        $this->assertFalse($unpublishedListing->isPublished());
    }

    /** @test */
    public function it_checks_if_listing_is_featured()
    {
        $featuredListing = Listing::factory()->featured()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $regularListing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'is_featured' => false,
        ]);

        $this->assertTrue($featuredListing->isFeatured());
        $this->assertFalse($regularListing->isFeatured());
    }

    /** @test */
    public function it_gets_primary_photo_url()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        ListingPhoto::factory()->primary()->create([
            'listing_id' => $listing->id,
            'photo_url' => 'https://example.com/photo.jpg',
        ]);

        $this->assertEquals('https://example.com/photo.jpg', $listing->getPrimaryPhotoUrl());
    }

    /** @test */
    public function it_returns_placeholder_when_no_primary_photo()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $this->assertStringContainsString('placeholder-listing.jpg', $listing->getPrimaryPhotoUrl());
    }

    /** @test */
    public function it_gets_all_photo_urls()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        ListingPhoto::factory()->create([
            'listing_id' => $listing->id,
            'photo_url' => 'https://example.com/photo1.jpg',
        ]);

        ListingPhoto::factory()->create([
            'listing_id' => $listing->id,
            'photo_url' => 'https://example.com/photo2.jpg',
        ]);

        $urls = $listing->getPhotoUrls();
        $this->assertCount(2, $urls);
        $this->assertContains('https://example.com/photo1.jpg', $urls);
        $this->assertContains('https://example.com/photo2.jpg', $urls);
    }

    /** @test */
    public function it_gets_lowest_price()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'price_per_hour' => 500,
            'price_per_day' => 3000,
            'price_per_week' => 15000,
            'price_per_month' => 50000,
        ]);

        $this->assertEquals(500, $listing->getLowestPrice());
    }

    /** @test */
    public function it_formats_price()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'price_per_hour' => 1250.50,
        ]);

        $formatted = $listing->getFormattedPrice('price_per_hour');
        $this->assertEquals('PHP 1,250.50/hour', $formatted);
    }

    /** @test */
    public function it_gets_full_address()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'address' => '123 Test Street',
            'city' => 'Manila',
            'province' => 'Metro Manila',
            'postal_code' => '1000',
        ]);

        $fullAddress = $listing->getFullAddress();
        $this->assertEquals('123 Test Street, Manila, Metro Manila, 1000', $fullAddress);
    }

    /** @test */
    public function it_increments_views_count()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'views_count' => 10,
        ]);

        $listing->incrementViews();
        $this->assertEquals(11, $listing->fresh()->views_count);
    }

    /** @test */
    public function it_increments_bookings_count()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'bookings_count' => 5,
        ]);

        $listing->incrementBookings();
        $this->assertEquals(6, $listing->fresh()->bookings_count);
    }

    /** @test */
    public function it_updates_average_rating()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'average_rating' => 4.0,
            'reviews_count' => 2,
        ]);

        $listing->updateAverageRating(5.0);
        $listing->refresh();

        $this->assertEquals(4.33, round($listing->average_rating, 2));
        $this->assertEquals(3, $listing->reviews_count);
    }

    /** @test */
    public function it_checks_if_has_amenity()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amenities' => ['parking', 'wifi', 'restroom'],
        ]);

        $this->assertTrue($listing->hasAmenity('parking'));
        $this->assertFalse($listing->hasAmenity('swimming_pool'));
    }

    /** @test */
    public function it_can_add_amenity()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amenities' => ['parking'],
        ]);

        $listing->addAmenity('wifi');
        $listing->refresh();

        $this->assertTrue($listing->hasAmenity('wifi'));
        $this->assertCount(2, $listing->amenities);
    }

    /** @test */
    public function it_can_remove_amenity()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amenities' => ['parking', 'wifi', 'restroom'],
        ]);

        $listing->removeAmenity('wifi');
        $listing->refresh();

        $this->assertFalse($listing->hasAmenity('wifi'));
        $this->assertCount(2, $listing->amenities);
    }

    /** @test */
    public function it_can_publish_listing()
    {
        $listing = Listing::factory()->draft()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $listing->publish();
        $listing->refresh();

        $this->assertEquals(Listing::STATUS_ACTIVE, $listing->status);
        $this->assertNotNull($listing->published_at);
    }

    /** @test */
    public function it_can_unpublish_listing()
    {
        $listing = Listing::factory()->active()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $listing->unpublish();
        $listing->refresh();

        $this->assertEquals(Listing::STATUS_INACTIVE, $listing->status);
    }

    /** @test */
    public function it_can_feature_listing()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'is_featured' => false,
        ]);

        $listing->feature();
        $listing->refresh();

        $this->assertTrue($listing->is_featured);
    }

    /** @test */
    public function it_can_verify_listing()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'is_verified' => false,
        ]);

        $listing->verify();
        $listing->refresh();

        $this->assertTrue($listing->is_verified);
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amenities' => ['parking', 'wifi'],
            'is_featured' => true,
            'price_per_hour' => 1234.56,
        ]);

        $this->assertIsArray($listing->amenities);
        $this->assertIsBool($listing->is_featured);
        // Price is cast as string in database, check if it's numeric
        $this->assertTrue(is_numeric($listing->price_per_hour));
        $this->assertEquals('1234.56', $listing->price_per_hour);
    }

    /** @test */
    public function it_soft_deletes_listing()
    {
        $listing = Listing::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $listing->delete();

        $this->assertSoftDeleted('listings', ['id' => $listing->id]);
        $this->assertNotNull($listing->fresh()->deleted_at);
    }
}
