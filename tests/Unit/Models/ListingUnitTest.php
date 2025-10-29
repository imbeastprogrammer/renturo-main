<?php

namespace Tests\Unit\Models;

use Tests\TestCase\UnitTenantTestCase;
use App\Models\Listing;
use App\Models\ListingUnit;
use App\Models\ListingAvailability;
use App\Models\Media;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListingUnitTest extends UnitTenantTestCase
{
    use RefreshDatabase;

    protected Listing $hotel;
    protected Listing $carRental;
    protected User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        // Run tenant migrations
        $this->artisan('migrate', ['--path' => 'database/migrations/tenant']);

        $this->owner = User::factory()->create();
        
        // Create hotel listing (multi-unit)
        $hotelCategory = Category::factory()->create(['name' => 'Hotels']);
        $hotelSubCategory = SubCategory::factory()->create([
            'category_id' => $hotelCategory->id,
            'name' => 'Hotel Rooms'
        ]);

        $this->hotel = Listing::factory()->create([
            'user_id' => $this->owner->id,
            'category_id' => $hotelCategory->id,
            'sub_category_id' => $hotelSubCategory->id,
            'inventory_type' => 'multiple',
            'total_units' => 10,
            'base_daily_price' => 5000.00,
        ]);

        // Create car rental listing (multi-unit)
        $transportCategory = Category::factory()->create(['name' => 'Transportation']);
        $carSubCategory = SubCategory::factory()->create([
            'category_id' => $transportCategory->id,
            'name' => 'Car Rental'
        ]);

        $this->carRental = Listing::factory()->create([
            'user_id' => $this->owner->id,
            'category_id' => $transportCategory->id,
            'sub_category_id' => $carSubCategory->id,
            'inventory_type' => 'multiple',
            'total_units' => 5,
            'base_daily_price' => 2500.00,
        ]);
    }

    /** @test */
    public function it_can_create_a_listing_unit()
    {
        $unit = ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'unit_name' => 'Deluxe Ocean View',
            'unit_description' => 'Spacious room with ocean view and balcony',
            'status' => 'active',
            'max_occupancy' => 4,
            'size_sqm' => 45.5,
            'created_by' => $this->owner->id,
        ]);

        $this->assertInstanceOf(ListingUnit::class, $unit);
        $this->assertEquals('Room-101', $unit->unit_identifier);
        $this->assertEquals('Deluxe Ocean View', $unit->unit_name);
        $this->assertEquals('active', $unit->status);
        $this->assertDatabaseHas('listing_units', [
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
        ]);
    }

    /** @test */
    public function it_belongs_to_a_listing()
    {
        $unit = ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'unit_name' => 'Deluxe Room',
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        $this->assertInstanceOf(Listing::class, $unit->listing);
        $this->assertEquals($this->hotel->id, $unit->listing->id);
    }

    /** @test */
    public function it_belongs_to_creator_and_updater()
    {
        $updater = User::factory()->create();
        
        $unit = ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'unit_name' => 'Deluxe Room',
            'status' => 'active',
            'created_by' => $this->owner->id,
            'updated_by' => $updater->id,
        ]);

        $this->assertInstanceOf(User::class, $unit->creator);
        $this->assertInstanceOf(User::class, $unit->updater);
        $this->assertEquals($this->owner->id, $unit->creator->id);
        $this->assertEquals($updater->id, $unit->updater->id);
    }

    /** @test */
    public function it_calculates_effective_pricing_with_modifiers()
    {
        // Unit with price modifier
        $premiumUnit = ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Suite-301',
            'unit_name' => 'Presidential Suite',
            'price_modifier' => 2.5, // 250% of base price
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        // Unit with override price
        $customUnit = ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'unit_name' => 'Deluxe Room',
            'base_daily_price' => 7500.00, // Override listing price
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        // Test price modifier calculation
        $this->assertEquals(12500.00, $premiumUnit->effective_daily_price); // 5000 * 2.5

        // Test override price
        $this->assertEquals(7500.00, $customUnit->effective_daily_price);
    }

    /** @test */
    public function it_checks_availability_status()
    {
        $activeUnit = ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'unit_name' => 'Active Room',
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        $maintenanceUnit = ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-102',
            'unit_name' => 'Maintenance Room',
            'status' => 'maintenance',
            'created_by' => $this->owner->id,
        ]);

        $this->assertTrue($activeUnit->isAvailable());
        $this->assertFalse($activeUnit->isUnderMaintenance());

        $this->assertFalse($maintenanceUnit->isAvailable());
        $this->assertTrue($maintenanceUnit->isUnderMaintenance());
    }

    /** @test */
    public function it_gets_display_name()
    {
        $unit = ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'unit_name' => 'Deluxe Ocean View',
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        $this->assertEquals('Room-101 - Deluxe Ocean View', $unit->display_name);
    }

    /** @test */
    public function it_manages_unit_features_and_specifications()
    {
        $unit = ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Suite-301',
            'unit_name' => 'Presidential Suite',
            'unit_features' => [
                'ocean_view',
                'balcony',
                'jacuzzi',
                'mini_bar',
                'room_service'
            ],
            'unit_specifications' => [
                'bed_type' => 'King Size',
                'bathroom' => 'En-suite with jacuzzi',
                'wifi' => 'High-speed',
                'tv' => '65" Smart TV',
                'air_conditioning' => 'Central AC'
            ],
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        $this->assertIsArray($unit->unit_features);
        $this->assertIsArray($unit->unit_specifications);
        $this->assertContains('ocean_view', $unit->unit_features);
        $this->assertEquals('King Size', $unit->unit_specifications['bed_type']);
    }

    /** @test */
    public function it_scopes_active_units()
    {
        ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'unit_name' => 'Active Room 1',
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-102',
            'unit_name' => 'Active Room 2',
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-103',
            'unit_name' => 'Maintenance Room',
            'status' => 'maintenance',
            'created_by' => $this->owner->id,
        ]);

        $activeUnits = ListingUnit::active()->get();
        $this->assertCount(2, $activeUnits);
    }

    /** @test */
    public function it_scopes_bookable_units()
    {
        ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'unit_name' => 'Bookable Room',
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-102',
            'unit_name' => 'Cleaning Room',
            'status' => 'cleaning',
            'created_by' => $this->owner->id,
        ]);

        $bookableUnits = ListingUnit::bookable()->get();
        $this->assertCount(1, $bookableUnits);
        $this->assertEquals('Room-101', $bookableUnits->first()->unit_identifier);
    }

    /** @test */
    public function it_scopes_by_listing()
    {
        // Hotel units
        ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'unit_name' => 'Hotel Room',
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        // Car rental units
        ListingUnit::create([
            'listing_id' => $this->carRental->id,
            'unit_identifier' => 'Toyota-ABC123',
            'unit_name' => 'Toyota Vios',
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        $hotelUnits = ListingUnit::forListing($this->hotel->id)->get();
        $carUnits = ListingUnit::forListing($this->carRental->id)->get();

        $this->assertCount(1, $hotelUnits);
        $this->assertCount(1, $carUnits);
        $this->assertEquals('Room-101', $hotelUnits->first()->unit_identifier);
        $this->assertEquals('Toyota-ABC123', $carUnits->first()->unit_identifier);
    }

    /** @test */
    public function it_scopes_by_features()
    {
        ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'unit_name' => 'Ocean View Room',
            'unit_features' => ['ocean_view', 'balcony'],
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-102',
            'unit_name' => 'Garden View Room',
            'unit_features' => ['garden_view', 'balcony'],
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-103',
            'unit_name' => 'Standard Room',
            'unit_features' => ['wifi'],
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        $oceanViewUnits = ListingUnit::withFeatures(['ocean_view'])->get();
        $balconyUnits = ListingUnit::withFeatures(['balcony'])->get();

        $this->assertCount(1, $oceanViewUnits);
        $this->assertCount(2, $balconyUnits);
    }

    /** @test */
    public function it_scopes_by_capacity()
    {
        ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'unit_name' => 'Single Room',
            'max_occupancy' => 2,
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Suite-201',
            'unit_name' => 'Family Suite',
            'max_occupancy' => 6,
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        $unitsForFour = ListingUnit::byCapacity(4, null)->get();
        $unitsUpToFour = ListingUnit::byCapacity(null, 4)->get();

        $this->assertCount(1, $unitsForFour); // Only suite can accommodate 4+
        $this->assertCount(1, $unitsUpToFour); // Only single room is ≤4
    }

    /** @test */
    public function it_creates_availability_for_unit()
    {
        $unit = ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'unit_name' => 'Deluxe Room',
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        $availability = $unit->createAvailability([
            'available_date' => '2025-12-25',
            'start_time' => '15:00',
            'end_time' => '11:00',
            'duration_type' => 'daily',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        $this->assertInstanceOf(ListingAvailability::class, $availability);
        $this->assertEquals('Room-101', $availability->unit_identifier);
        $this->assertEquals($this->hotel->id, $availability->listing_id);
    }

    /** @test */
    public function it_gets_availability_for_date_range()
    {
        $unit = ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'unit_name' => 'Deluxe Room',
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        // Create availability for multiple dates
        $unit->createAvailability([
            'available_date' => '2025-12-25',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        $unit->createAvailability([
            'available_date' => '2025-12-26',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        $unit->createAvailability([
            'available_date' => '2025-12-28', // Outside range
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        $availability = $unit->getAvailabilityForDateRange('2025-12-25', '2025-12-26');
        $this->assertCount(2, $availability);
    }

    /** @test */
    public function it_checks_availability_at_specific_time()
    {
        $unit = ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'unit_name' => 'Deluxe Room',
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        // Unit is not available (no availability record)
        $this->assertFalse($unit->isAvailableAt('2025-12-25', '10:00', '12:00'));

        // Create availability
        $unit->createAvailability([
            'available_date' => '2025-12-25',
            'start_time' => '08:00',
            'end_time' => '18:00',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        // Now it should be available
        $this->assertTrue($unit->isAvailableAt('2025-12-25', '10:00', '12:00'));

        // But not if unit is under maintenance
        $unit->update(['status' => 'maintenance']);
        $this->assertFalse($unit->isAvailableAt('2025-12-25', '10:00', '12:00'));
    }

    /** @test */
    public function it_handles_media_relationships()
    {
        $unit = ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'unit_name' => 'Deluxe Room',
            'primary_image' => 'https://example.com/room101.jpg',
            'image_gallery' => [
                'https://example.com/room101-1.jpg',
                'https://example.com/room101-2.jpg'
            ],
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        // Test primary image URL
        $this->assertEquals('https://example.com/room101.jpg', $unit->primary_image_url);

        // Test all images (primary + gallery)
        $allImages = $unit->all_images;
        $this->assertCount(3, $allImages);
        $this->assertContains('https://example.com/room101.jpg', $allImages);
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $unit = ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'unit_name' => 'Deluxe Room',
            'unit_features' => ['ocean_view', 'balcony'],
            'unit_specifications' => ['bed' => 'King', 'wifi' => true],
            'unit_rules' => ['no_smoking' => true, 'pets' => false],
            'image_gallery' => ['image1.jpg', 'image2.jpg'],
            'metadata' => ['notes' => 'Premium room'],
            'price_modifier' => 1.25,
            'base_daily_price' => 7500.50,
            'size_sqm' => 45.75,
            'max_occupancy' => 4,
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        // Test array casting
        $this->assertIsArray($unit->unit_features);
        $this->assertIsArray($unit->unit_specifications);
        $this->assertIsArray($unit->unit_rules);
        $this->assertIsArray($unit->image_gallery);
        $this->assertIsArray($unit->metadata);

        // Test decimal casting
        $this->assertEquals(1.25, $unit->price_modifier);
        $this->assertEquals(7500.50, $unit->base_daily_price);
        $this->assertEquals(45.75, $unit->size_sqm);

        // Test integer casting
        $this->assertIsInt($unit->max_occupancy);
    }

    /** @test */
    public function it_soft_deletes_units()
    {
        $unit = ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'unit_name' => 'Deluxe Room',
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        $unit->delete();

        $this->assertSoftDeleted('listing_units', ['id' => $unit->id]);
        $this->assertNotNull($unit->fresh()->deleted_at);
    }

    /** @test */
    public function it_enforces_unique_unit_identifier_per_listing()
    {
        ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'unit_name' => 'First Room 101',
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        // This should work (different listing)
        ListingUnit::create([
            'listing_id' => $this->carRental->id,
            'unit_identifier' => 'Room-101', // Same identifier, different listing
            'unit_name' => 'Car with Room-101 ID',
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        $this->assertDatabaseCount('listing_units', 2);

        // This should fail (same listing, same identifier)
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101', // Duplicate in same listing
            'unit_name' => 'Second Room 101',
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);
    }
}