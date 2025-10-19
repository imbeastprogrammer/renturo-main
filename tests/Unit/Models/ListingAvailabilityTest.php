<?php

namespace Tests\Unit\Models;

use Tests\TestCase\TenantTestCase;
use App\Models\Listing;
use App\Models\ListingAvailability;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListingAvailabilityTest extends TenantTestCase
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
    public function it_can_create_availability()
    {
        $availability = ListingAvailability::factory()->create([
            'listing_id' => $this->listing->id,
            'availability_type' => ListingAvailability::TYPE_RECURRING,
            'day_of_week' => ListingAvailability::MONDAY,
        ]);

        $this->assertInstanceOf(ListingAvailability::class, $availability);
        $this->assertEquals(ListingAvailability::TYPE_RECURRING, $availability->availability_type);
        $this->assertDatabaseHas('listing_availability', [
            'listing_id' => $this->listing->id,
        ]);
    }

    /** @test */
    public function it_belongs_to_a_listing()
    {
        $availability = ListingAvailability::factory()->create([
            'listing_id' => $this->listing->id,
        ]);

        $this->assertInstanceOf(Listing::class, $availability->listing);
        $this->assertEquals($this->listing->id, $availability->listing->id);
    }

    /** @test */
    public function it_scopes_available_slots()
    {
        ListingAvailability::factory()->count(3)->create([
            'listing_id' => $this->listing->id,
            'is_available' => true,
        ]);

        ListingAvailability::factory()->count(2)->blocked()->create([
            'listing_id' => $this->listing->id,
        ]);

        $availableSlots = ListingAvailability::available()->get();
        $this->assertCount(3, $availableSlots);
    }

    /** @test */
    public function it_scopes_blocked_slots()
    {
        ListingAvailability::factory()->count(2)->create([
            'listing_id' => $this->listing->id,
        ]);

        ListingAvailability::factory()->count(3)->blocked()->create([
            'listing_id' => $this->listing->id,
        ]);

        $blockedSlots = ListingAvailability::blocked()->get();
        $this->assertCount(3, $blockedSlots);
    }

    /** @test */
    public function it_scopes_recurring_availability()
    {
        ListingAvailability::factory()->count(5)->recurring()->create([
            'listing_id' => $this->listing->id,
        ]);

        ListingAvailability::factory()->specificDate()->create([
            'listing_id' => $this->listing->id,
        ]);

        $recurringAvailability = ListingAvailability::recurring()->get();
        $this->assertCount(5, $recurringAvailability);
    }

    /** @test */
    public function it_scopes_by_day_of_week()
    {
        ListingAvailability::factory()->forDay(ListingAvailability::MONDAY)->create([
            'listing_id' => $this->listing->id,
        ]);

        ListingAvailability::factory()->forDay(ListingAvailability::TUESDAY)->create([
            'listing_id' => $this->listing->id,
        ]);

        ListingAvailability::factory()->forDay(ListingAvailability::MONDAY)->create([
            'listing_id' => $this->listing->id,
        ]);

        $mondayAvailability = ListingAvailability::forDayOfWeek(ListingAvailability::MONDAY)->get();
        $this->assertCount(2, $mondayAvailability);
    }

    /** @test */
    public function it_scopes_by_specific_date()
    {
        $testDate = Carbon::parse('2025-12-25');

        // Specific date match
        ListingAvailability::factory()->specificDate($testDate)->create([
            'listing_id' => $this->listing->id,
        ]);

        // Date range match
        ListingAvailability::factory()->dateRange(
            $testDate->copy()->subDays(5),
            $testDate->copy()->addDays(5)
        )->create([
            'listing_id' => $this->listing->id,
        ]);

        // Recurring match (Wednesday = 3)
        ListingAvailability::factory()->forDay($testDate->dayOfWeek)->create([
            'listing_id' => $this->listing->id,
        ]);

        // Non-matching
        ListingAvailability::factory()->specificDate($testDate->copy()->addMonths(1))->create([
            'listing_id' => $this->listing->id,
        ]);

        $availability = ListingAvailability::forDate($testDate)->get();
        $this->assertCount(3, $availability);
    }

    /** @test */
    public function it_gets_day_name_from_day_of_week()
    {
        $availability = ListingAvailability::factory()->forDay(ListingAvailability::MONDAY)->create([
            'listing_id' => $this->listing->id,
        ]);

        $this->assertEquals('Monday', $availability->getDayName());
    }

    /** @test */
    public function it_gets_time_range()
    {
        $availability = ListingAvailability::factory()->create([
            'listing_id' => $this->listing->id,
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
        ]);

        $timeRange = $availability->getTimeRange();
        $this->assertStringContainsString('AM', $timeRange);
        $this->assertStringContainsString('PM', $timeRange);
    }

    /** @test */
    public function it_gets_date_range_for_specific_date()
    {
        $date = Carbon::parse('2025-12-25');
        
        $availability = ListingAvailability::factory()->specificDate($date)->create([
            'listing_id' => $this->listing->id,
        ]);

        $dateRange = $availability->getDateRange();
        $this->assertStringContainsString('Dec 25', $dateRange);
        $this->assertStringContainsString('2025', $dateRange);
    }

    /** @test */
    public function it_gets_date_range_for_date_range_type()
    {
        $startDate = Carbon::parse('2025-12-01');
        $endDate = Carbon::parse('2025-12-31');
        
        $availability = ListingAvailability::factory()->dateRange($startDate, $endDate)->create([
            'listing_id' => $this->listing->id,
        ]);

        $dateRange = $availability->getDateRange();
        $this->assertStringContainsString('Dec 01', $dateRange);
        $this->assertStringContainsString('Dec 31', $dateRange);
        $this->assertStringContainsString('-', $dateRange);
    }

    /** @test */
    public function it_gets_human_readable_description()
    {
        $availability = ListingAvailability::factory()->forDay(ListingAvailability::MONDAY)->create([
            'listing_id' => $this->listing->id,
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
        ]);

        $description = $availability->getDescription();
        $this->assertStringContainsString('Monday', $description);
        $this->assertStringContainsString('AM', $description);
        $this->assertStringContainsString('PM', $description);
    }

    /** @test */
    public function it_checks_if_is_recurring()
    {
        $recurring = ListingAvailability::factory()->recurring()->create([
            'listing_id' => $this->listing->id,
        ]);

        $specific = ListingAvailability::factory()->specificDate()->create([
            'listing_id' => $this->listing->id,
        ]);

        $this->assertTrue($recurring->isRecurring());
        $this->assertFalse($specific->isRecurring());
    }

    /** @test */
    public function it_checks_if_is_blocked()
    {
        $blocked = ListingAvailability::factory()->blocked()->create([
            'listing_id' => $this->listing->id,
        ]);

        $available = ListingAvailability::factory()->create([
            'listing_id' => $this->listing->id,
            'is_available' => true,
        ]);

        $this->assertTrue($blocked->isBlocked());
        $this->assertFalse($available->isBlocked());
    }

    /** @test */
    public function it_checks_if_applies_to_date()
    {
        $testDate = Carbon::parse('2025-12-25'); // Wednesday

        // Recurring on Wednesday
        $recurring = ListingAvailability::factory()->forDay($testDate->dayOfWeek)->create([
            'listing_id' => $this->listing->id,
        ]);

        // Specific date
        $specific = ListingAvailability::factory()->specificDate($testDate)->create([
            'listing_id' => $this->listing->id,
        ]);

        // Date range
        $range = ListingAvailability::factory()->dateRange(
            $testDate->copy()->subDays(5),
            $testDate->copy()->addDays(5)
        )->create([
            'listing_id' => $this->listing->id,
        ]);

        // Non-matching
        $nonMatching = ListingAvailability::factory()->forDay(ListingAvailability::MONDAY)->create([
            'listing_id' => $this->listing->id,
        ]);

        $this->assertTrue($recurring->appliesToDate($testDate));
        $this->assertTrue($specific->appliesToDate($testDate));
        $this->assertTrue($range->appliesToDate($testDate));
        $this->assertFalse($nonMatching->appliesToDate($testDate));
    }

    /** @test */
    public function it_gets_price_with_override()
    {
        $listing = Listing::factory()->create([
            'user_id' => User::factory()->create()->id,
            'category_id' => Category::factory()->create()->id,
            'price_per_hour' => 500,
        ]);

        $availabilityWithOverride = ListingAvailability::factory()->withPriceOverride(1000)->create([
            'listing_id' => $listing->id,
        ]);

        $availabilityWithoutOverride = ListingAvailability::factory()->create([
            'listing_id' => $listing->id,
            'price_override' => null,
        ]);

        $this->assertEquals(1000, $availabilityWithOverride->getPrice());
        $this->assertEquals(500, $availabilityWithoutOverride->getPrice());
    }

    /** @test */
    public function it_detects_overlapping_recurring_slots()
    {
        $slot1 = ListingAvailability::factory()->forDay(ListingAvailability::MONDAY)->create([
            'listing_id' => $this->listing->id,
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
        ]);

        // Overlapping (same day, overlapping time)
        $slot2 = ListingAvailability::factory()->forDay(ListingAvailability::MONDAY)->create([
            'listing_id' => $this->listing->id,
            'start_time' => '10:00:00',
            'end_time' => '14:00:00',
        ]);

        // Non-overlapping (same day, different time)
        $slot3 = ListingAvailability::factory()->forDay(ListingAvailability::MONDAY)->create([
            'listing_id' => $this->listing->id,
            'start_time' => '14:00:00',
            'end_time' => '18:00:00',
        ]);

        // Non-overlapping (different day)
        $slot4 = ListingAvailability::factory()->forDay(ListingAvailability::TUESDAY)->create([
            'listing_id' => $this->listing->id,
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
        ]);

        $this->assertTrue($slot1->overlapsWith($slot2));
        $this->assertFalse($slot1->overlapsWith($slot3));
        $this->assertFalse($slot1->overlapsWith($slot4));
    }

    /** @test */
    public function it_detects_overlapping_specific_dates()
    {
        $date1 = Carbon::parse('2025-12-25');
        $date2 = Carbon::parse('2025-12-26');

        $slot1 = ListingAvailability::factory()->specificDate($date1)->create([
            'listing_id' => $this->listing->id,
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
        ]);

        // Overlapping (same date, overlapping time)
        $slot2 = ListingAvailability::factory()->specificDate($date1)->create([
            'listing_id' => $this->listing->id,
            'start_time' => '10:00:00',
            'end_time' => '14:00:00',
        ]);

        // Non-overlapping (different date)
        $slot3 = ListingAvailability::factory()->specificDate($date2)->create([
            'listing_id' => $this->listing->id,
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
        ]);

        $this->assertTrue($slot1->overlapsWith($slot2));
        $this->assertFalse($slot1->overlapsWith($slot3));
    }

    /** @test */
    public function it_detects_overlapping_date_ranges()
    {
        $slot1 = ListingAvailability::factory()->dateRange(
            Carbon::parse('2025-12-01'),
            Carbon::parse('2025-12-15')
        )->create([
            'listing_id' => $this->listing->id,
        ]);

        // Overlapping range
        $slot2 = ListingAvailability::factory()->dateRange(
            Carbon::parse('2025-12-10'),
            Carbon::parse('2025-12-20')
        )->create([
            'listing_id' => $this->listing->id,
        ]);

        // Non-overlapping range
        $slot3 = ListingAvailability::factory()->dateRange(
            Carbon::parse('2025-12-20'),
            Carbon::parse('2025-12-31')
        )->create([
            'listing_id' => $this->listing->id,
        ]);

        $this->assertTrue($slot1->overlapsWith($slot2));
        $this->assertFalse($slot1->overlapsWith($slot3));
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $availability = ListingAvailability::factory()->create([
            'listing_id' => $this->listing->id,
            'day_of_week' => 1,
            'is_available' => true,
            'price_override' => 1234.56,
        ]);

        $this->assertIsInt($availability->day_of_week);
        $this->assertIsBool($availability->is_available);
        // Price is cast as string in database, check if it's numeric
        $this->assertTrue(is_numeric($availability->price_override));
        $this->assertEquals('1234.56', $availability->price_override);
    }

    /** @test */
    public function it_soft_deletes_availability()
    {
        $availability = ListingAvailability::factory()->create([
            'listing_id' => $this->listing->id,
        ]);

        $availability->delete();

        $this->assertSoftDeleted('listing_availability', ['id' => $availability->id]);
        $this->assertNotNull($availability->fresh()->deleted_at);
    }
}
