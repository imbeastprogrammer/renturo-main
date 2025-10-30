<?php

namespace Tests\Unit\Models;

use Tests\TestCase\UnitTenantTestCase;
use App\Models\Listing;
use App\Models\ListingAvailability;
use App\Models\ListingUnit;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListingAvailabilityTest extends UnitTenantTestCase
{
    use RefreshDatabase;

    protected Listing $basketballCourt;
    protected Listing $hotel;
    protected Listing $carRental;
    protected User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        // Run tenant migrations
        $this->artisan('migrate', ['--path' => 'database/migrations/tenant']);

        $this->owner = User::factory()->create();
        
        // Create categories for different property types
        $sportsCategory = Category::factory()->create(['name' => 'Sports Venues']);
        $basketballSubCategory = SubCategory::factory()->create([
            'category_id' => $sportsCategory->id,
            'name' => 'Basketball Courts'
        ]);

        $hotelCategory = Category::factory()->create(['name' => 'Hotels']);
        $hotelSubCategory = SubCategory::factory()->create([
            'category_id' => $hotelCategory->id,
            'name' => 'Hotel Rooms'
        ]);

        $transportCategory = Category::factory()->create(['name' => 'Transportation']);
        $carSubCategory = SubCategory::factory()->create([
            'category_id' => $transportCategory->id,
            'name' => 'Car Rental'
        ]);

        // Create different types of listings for testing
        $this->basketballCourt = Listing::factory()->create([
            'user_id' => $this->owner->id,
            'category_id' => $sportsCategory->id,
            'sub_category_id' => $basketballSubCategory->id,
            'inventory_type' => 'single',
            'total_units' => 1,
            'base_hourly_price' => 1800.00,
            'duration_unit' => 'hours',
        ]);

        $this->hotel = Listing::factory()->create([
            'user_id' => $this->owner->id,
            'category_id' => $hotelCategory->id,
            'sub_category_id' => $hotelSubCategory->id,
            'inventory_type' => 'multiple',
            'total_units' => 3,
            'base_daily_price' => 5000.00,
            'duration_unit' => 'days',
        ]);

        $this->carRental = Listing::factory()->create([
            'user_id' => $this->owner->id,
            'category_id' => $transportCategory->id,
            'sub_category_id' => $carSubCategory->id,
            'inventory_type' => 'multiple',
            'total_units' => 5,
            'base_daily_price' => 2500.00,
            'duration_unit' => 'days',
        ]);
    }

    /** @test */
    public function it_can_create_universal_availability()
    {
        $availability = ListingAvailability::create([
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-25',
            'start_time' => '08:00',
            'end_time' => '22:00',
            'slot_duration_minutes' => 60,
            'duration_type' => 'hourly',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        $this->assertInstanceOf(ListingAvailability::class, $availability);
        $this->assertEquals('available', $availability->status);
        $this->assertEquals(60, $availability->slot_duration_minutes);
        $this->assertDatabaseHas('listing_availability', [
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-25',
        ]);
    }

    /** @test */
    public function it_belongs_to_a_listing()
    {
        $availability = ListingAvailability::create([
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-25',
            'start_time' => '08:00',
            'end_time' => '22:00',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        $this->assertInstanceOf(Listing::class, $availability->listing);
        $this->assertEquals($this->basketballCourt->id, $availability->listing->id);
    }

    /** @test */
    public function it_can_have_unit_identifier_for_multi_unit_properties()
    {
        // Create hotel room units
        $room101 = ListingUnit::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'unit_name' => 'Deluxe Ocean View',
            'status' => 'active',
            'created_by' => $this->owner->id,
        ]);

        $availability = ListingAvailability::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'available_date' => '2025-12-25',
            'start_time' => '15:00', // Check-in
            'end_time' => '11:00',   // Check-out next day
            'duration_type' => 'daily',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        $this->assertEquals('Room-101', $availability->unit_identifier);
        $this->assertInstanceOf(ListingUnit::class, $availability->unit);
    }

    /** @test */
    public function it_generates_time_slots_for_hourly_bookings()
    {
        $availability = ListingAvailability::create([
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-25',
            'start_time' => '08:00',
            'end_time' => '12:00',
            'slot_duration_minutes' => 60,
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        $slots = $availability->generateTimeSlots();

        $this->assertCount(4, $slots); // 8-9, 9-10, 10-11, 11-12
        $this->assertEquals('08:00', $slots[0]['start']);
        $this->assertEquals('09:00', $slots[0]['end']);
        $this->assertEquals(60, $slots[0]['duration_minutes']);
        $this->assertTrue($slots[0]['available']);
    }

    /** @test */
    public function it_calculates_effective_pricing_with_modifiers()
    {
        // Create availability with different pricing
        $availability = ListingAvailability::create([
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-28', // Saturday
            'start_time' => '19:00', // Peak hour
            'end_time' => '20:00',
            'peak_hour_price' => 2200.00,
            'weekend_price' => 2000.00,
            'holiday_price' => 2500.00,
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        // Test peak hour pricing (should take precedence)
        $this->assertEquals(2200.00, $availability->effective_price);
    }

    /** @test */
    public function it_formats_for_different_categories()
    {
        // Basketball court (sports)
        $basketballAvailability = ListingAvailability::create([
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-25',
            'start_time' => '08:00',
            'end_time' => '22:00',
            'slot_duration_minutes' => 60,
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        $sportsFormat = $basketballAvailability->formatForCategory();
        $this->assertEquals('sports', $sportsFormat['type']);
        $this->assertArrayHasKey('time_slots', $sportsFormat);
        $this->assertArrayHasKey('hourly_rate', $sportsFormat);

        // Hotel room (accommodation)
        $hotelAvailability = ListingAvailability::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'available_date' => '2025-12-25',
            'duration_type' => 'daily',
            'category_rules' => [
                'check_in_time' => '15:00',
                'check_out_time' => '11:00'
            ],
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        $hotelFormat = $hotelAvailability->formatForCategory();
        $this->assertEquals('hotel', $hotelFormat['type']);
        $this->assertEquals('Room-101', $hotelFormat['room']);
        $this->assertEquals('15:00', $hotelFormat['check_in']);
        $this->assertEquals('11:00', $hotelFormat['check_out']);
    }

    /** @test */
    public function it_checks_availability_conflicts()
    {
        $availability = ListingAvailability::create([
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-25',
            'start_time' => '10:00',
            'end_time' => '12:00',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        // Overlapping request
        $this->assertTrue($availability->checkAvailabilityConflict('11:00', '13:00'));
        
        // Non-overlapping request
        $this->assertFalse($availability->checkAvailabilityConflict('13:00', '15:00'));
        
        // Exact match
        $this->assertTrue($availability->checkAvailabilityConflict('10:00', '12:00'));
    }

    /** @test */
    public function it_manages_status_transitions()
    {
        $availability = ListingAvailability::create([
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-25',
            'start_time' => '10:00',
            'end_time' => '12:00',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        $this->assertTrue($availability->isAvailable());
        $this->assertFalse($availability->isBooked());
        $this->assertFalse($availability->isBlocked());

        // Mark as booked
        $availability->markAsBooked();
        $this->assertTrue($availability->isBooked());
        $this->assertFalse($availability->isAvailable());

        // Mark as available again
        $availability->markAsAvailable();
        $this->assertTrue($availability->isAvailable());
        $this->assertFalse($availability->isBooked());

        // Block with reason
        $availability->block('Maintenance required');
        $this->assertTrue($availability->isBlocked());
        $this->assertEquals('Maintenance required', $availability->notes);
    }

    /** @test */
    public function it_scopes_by_status()
    {
        // Create different status availability
        ListingAvailability::create([
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-25',
            'start_time' => '08:00',
            'end_time' => '10:00',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        ListingAvailability::create([
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-25',
            'start_time' => '10:00',
            'end_time' => '12:00',
            'status' => 'booked',
            'created_by' => $this->owner->id,
        ]);

        ListingAvailability::create([
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-25',
            'start_time' => '12:00',
            'end_time' => '14:00',
            'status' => 'maintenance',
            'created_by' => $this->owner->id,
        ]);

        $available = ListingAvailability::available()->get();
        $booked = ListingAvailability::booked()->get();
        $blocked = ListingAvailability::blocked()->get();

        $this->assertCount(1, $available);
        $this->assertCount(1, $booked);
        $this->assertCount(1, $blocked);
    }

    /** @test */
    public function it_scopes_by_date_and_time_range()
    {
        $date = '2025-12-25';
        
        ListingAvailability::create([
            'listing_id' => $this->basketballCourt->id,
            'available_date' => $date,
            'start_time' => '08:00',
            'end_time' => '10:00',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        ListingAvailability::create([
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-26', // Different date
            'start_time' => '08:00',
            'end_time' => '10:00',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        $forDate = ListingAvailability::forDate($date)->get();
        $this->assertCount(1, $forDate);

        $dateRange = ListingAvailability::forDateRange('2025-12-25', '2025-12-26')->get();
        $this->assertCount(2, $dateRange);
    }

    /** @test */
    public function it_scopes_by_unit_identifier()
    {
        ListingAvailability::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'available_date' => '2025-12-25',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        ListingAvailability::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-102',
            'available_date' => '2025-12-25',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        $room101 = ListingAvailability::forUnit('Room-101')->get();
        $this->assertCount(1, $room101);
        $this->assertEquals('Room-101', $room101->first()->unit_identifier);
    }

    /** @test */
    public function it_calculates_duration_in_minutes()
    {
        $availability = ListingAvailability::create([
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-25',
            'start_time' => '10:00',
            'end_time' => '12:30',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        $this->assertEquals(150, $availability->duration_in_minutes); // 2.5 hours = 150 minutes
    }

    /** @test */
    public function it_gets_time_range_attribute()
    {
        $availability = ListingAvailability::create([
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-25',
            'start_time' => '08:00',
            'end_time' => '17:00',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        $timeRange = $availability->time_range;
        $this->assertStringContainsString('8:00 AM', $timeRange);
        $this->assertStringContainsString('5:00 PM', $timeRange);
        $this->assertStringContainsString('-', $timeRange);
    }

    /** @test */
    public function it_handles_all_day_availability()
    {
        $availability = ListingAvailability::create([
            'listing_id' => $this->hotel->id,
            'unit_identifier' => 'Room-101',
            'available_date' => '2025-12-25',
            'start_time' => null,
            'end_time' => null,
            'duration_type' => 'daily',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        $this->assertEquals('All day', $availability->time_range);
        $this->assertEquals(0, $availability->duration_in_minutes);
        $this->assertEmpty($availability->generateTimeSlots());
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $availability = ListingAvailability::create([
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-25',
            'start_time' => '08:00',
            'end_time' => '17:00',
            'peak_hour_price' => 2200.50,
            'weekend_price' => 2000.75,
            'available_units' => 3,
            'slot_duration_minutes' => 60,
            'recurrence_pattern' => ['monday', 'wednesday', 'friday'],
            'category_rules' => ['check_in' => '15:00'],
            'booking_rules' => ['min_advance' => 24],
            'metadata' => ['notes' => 'Premium court'],
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        // Test decimal casting
        $this->assertEquals(2200.50, $availability->peak_hour_price);
        $this->assertEquals(2000.75, $availability->weekend_price);
        
        // Test integer casting
        $this->assertIsInt($availability->available_units);
        $this->assertIsInt($availability->slot_duration_minutes);
        
        // Test array casting
        $this->assertIsArray($availability->recurrence_pattern);
        $this->assertIsArray($availability->category_rules);
        $this->assertIsArray($availability->booking_rules);
        $this->assertIsArray($availability->metadata);
        
        // Test date casting
        $this->assertInstanceOf(Carbon::class, $availability->available_date);
    }

    /** @test */
    public function it_orders_availability_correctly()
    {
        // Create availability in random order
        ListingAvailability::create([
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-26',
            'start_time' => '10:00',
            'end_time' => '12:00',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        ListingAvailability::create([
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-25',
            'start_time' => '14:00',
            'end_time' => '16:00',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        ListingAvailability::create([
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-25',
            'start_time' => '08:00',
            'end_time' => '10:00',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        $ordered = ListingAvailability::ordered()->get();
        
        // Should be ordered by date, then time
        $this->assertEquals('2025-12-25', $ordered[0]->available_date->format('Y-m-d'));
        $this->assertEquals('08:00', $ordered[0]->start_time->format('H:i'));
        
        $this->assertEquals('2025-12-25', $ordered[1]->available_date->format('Y-m-d'));
        $this->assertEquals('14:00', $ordered[1]->start_time->format('H:i'));
        
        $this->assertEquals('2025-12-26', $ordered[2]->available_date->format('Y-m-d'));
    }

    /** @test */
    public function it_soft_deletes_availability()
    {
        $availability = ListingAvailability::create([
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-25',
            'start_time' => '08:00',
            'end_time' => '10:00',
            'status' => 'available',
            'created_by' => $this->owner->id,
        ]);

        $availability->delete();

        $this->assertSoftDeleted('listing_availability', ['id' => $availability->id]);
        $this->assertNotNull($availability->fresh()->deleted_at);
    }
}