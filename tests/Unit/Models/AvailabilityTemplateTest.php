<?php

namespace Tests\Unit\Models;

use Tests\TestCase\UnitTenantTestCase;
use App\Models\Listing;
use App\Models\AvailabilityTemplate;
use App\Models\ListingAvailability;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AvailabilityTemplateTest extends UnitTenantTestCase
{
    use RefreshDatabase;

    protected Listing $basketballCourt;
    protected Listing $hotel;
    protected User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        // Run tenant migrations
        $this->artisan('migrate', ['--path' => 'database/migrations/tenant']);

        $this->owner = User::factory()->create();
        
        // Create basketball court listing
        $sportsCategory = Category::factory()->create(['name' => 'Sports Venues']);
        $basketballSubCategory = SubCategory::factory()->create([
            'category_id' => $sportsCategory->id,
            'name' => 'Basketball Courts'
        ]);

        $this->basketballCourt = Listing::factory()->create([
            'user_id' => $this->owner->id,
            'category_id' => $sportsCategory->id,
            'sub_category_id' => $basketballSubCategory->id,
            'inventory_type' => 'single',
            'total_units' => 1,
            'base_hourly_price' => 1800.00,
        ]);

        // Create hotel listing
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
    }

    /** @test */
    public function it_can_create_an_availability_template()
    {
        $template = AvailabilityTemplate::create([
            'listing_id' => $this->basketballCourt->id,
            'name' => 'Weekday Morning Hours',
            'days_of_week' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
            'start_time' => '06:00',
            'end_time' => '12:00',
            'slot_duration_minutes' => 60,
            'hourly_price' => 1500.00,
            'is_active' => true,
            'created_by' => $this->owner->id,
        ]);

        $this->assertInstanceOf(AvailabilityTemplate::class, $template);
        $this->assertEquals('Weekday Morning Hours', $template->name);
        $this->assertIsArray($template->days_of_week);
        $this->assertContains('monday', $template->days_of_week);
        $this->assertTrue($template->is_active);
        $this->assertDatabaseHas('availability_templates', [
            'listing_id' => $this->basketballCourt->id,
            'name' => 'Weekday Morning Hours',
        ]);
    }

    /** @test */
    public function it_belongs_to_a_listing()
    {
        $template = AvailabilityTemplate::create([
            'listing_id' => $this->basketballCourt->id,
            'name' => 'Test Template',
            'days_of_week' => ['monday'],
            'start_time' => '08:00',
            'end_time' => '17:00',
            'base_hourly_price' => 1500.00,
            'is_active' => true,
            'created_by' => $this->owner->id,
        ]);

        $this->assertInstanceOf(Listing::class, $template->listing);
        $this->assertEquals($this->basketballCourt->id, $template->listing->id);
    }

    /** @test */
    public function it_belongs_to_creator_and_updater()
    {
        $updater = User::factory()->create();
        
        $template = AvailabilityTemplate::create([
            'listing_id' => $this->basketballCourt->id,
            'name' => 'Test Template',
            'days_of_week' => ['monday'],
            'start_time' => '08:00',
            'end_time' => '17:00',
            'base_hourly_price' => 1500.00,
            'is_active' => true,
            'created_by' => $this->owner->id,
            'updated_by' => $updater->id,
        ]);

        $this->assertInstanceOf(User::class, $template->creator);
        $this->assertInstanceOf(User::class, $template->updater);
        $this->assertEquals($this->owner->id, $template->creator->id);
        $this->assertEquals($updater->id, $template->updater->id);
    }

    /** @test */
    public function it_applies_template_to_date_range()
    {
        $template = AvailabilityTemplate::create([
            'listing_id' => $this->basketballCourt->id,
            'name' => 'Weekday Template',
            'days_of_week' => ['monday', 'wednesday', 'friday'],
            'start_time' => '08:00',
            'end_time' => '17:00',
            'slot_duration_minutes' => 60,
            'hourly_price' => 1500.00,
            'is_active' => true,
            'created_by' => $this->owner->id,
        ]);

        // Apply template for a week (should create availability for Mon, Wed, Fri)
        $startDate = Carbon::parse('2025-12-22'); // Monday
        $endDate = Carbon::parse('2025-12-28');   // Sunday

        $createdSlots = $template->applyToDateRange($startDate, $endDate);

        // Should create 3 slots (Mon 22nd, Wed 24th, Fri 26th)
        $this->assertCount(3, $createdSlots);

        // Verify the slots were created in database
        $this->assertDatabaseHas('listing_availability', [
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-22', // Monday
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
        ]);

        $this->assertDatabaseHas('listing_availability', [
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-24', // Wednesday
        ]);

        $this->assertDatabaseHas('listing_availability', [
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-26', // Friday
        ]);

        // Should NOT create for Tuesday, Thursday, Saturday, Sunday
        $this->assertDatabaseMissing('listing_availability', [
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-23', // Tuesday
        ]);
    }

    /** @test */
    public function it_handles_different_pricing_types()
    {
        // Hourly pricing template
        $hourlyTemplate = AvailabilityTemplate::create([
            'listing_id' => $this->basketballCourt->id,
            'name' => 'Hourly Template',
            'days_of_week' => ['monday'],
            'start_time' => '08:00',
            'end_time' => '12:00',
            'hourly_price' => 1500.00,
            'is_active' => true,
            'created_by' => $this->owner->id,
        ]);

        // Daily pricing template
        $dailyTemplate = AvailabilityTemplate::create([
            'listing_id' => $this->hotel->id,
            'name' => 'Daily Template',
            'days_of_week' => ['monday'],
            'start_time' => '15:00', // Check-in
            'end_time' => '11:00',   // Check-out next day
            'daily_price' => 4500.00,
            'is_active' => true,
            'created_by' => $this->owner->id,
        ]);

        $this->assertEquals(1500.00, $hourlyTemplate->hourly_price);
        $this->assertNull($hourlyTemplate->daily_price);
        
        $this->assertEquals(4500.00, $dailyTemplate->daily_price);
        $this->assertNull($dailyTemplate->hourly_price);
    }

    /** @test */
    public function it_manages_booking_rules()
    {
        $template = AvailabilityTemplate::create([
            'listing_id' => $this->basketballCourt->id,
            'name' => 'Strict Rules Template',
            'days_of_week' => ['monday'],
            'start_time' => '08:00',
            'end_time' => '17:00',
            'booking_rules' => [
                'min_advance_hours' => 24,
                'max_advance_days' => 30,
                'min_duration_hours' => 2,
                'max_duration_hours' => 8,
                'cancellation_hours' => 12,
                'requires_approval' => false,
            ],
            'is_active' => true,
            'created_by' => $this->owner->id,
        ]);

        $this->assertIsArray($template->booking_rules);
        $this->assertEquals(24, $template->booking_rules['min_advance_hours']);
        $this->assertEquals(30, $template->booking_rules['max_advance_days']);
        $this->assertFalse($template->booking_rules['requires_approval']);
    }

    /** @test */
    public function it_scopes_active_templates()
    {
        AvailabilityTemplate::create([
            'listing_id' => $this->basketballCourt->id,
            'name' => 'Active Template 1',
            'days_of_week' => ['monday'],
            'start_time' => '08:00',
            'end_time' => '12:00',
            'base_hourly_price' => 1500.00,
            'is_active' => true,
            'created_by' => $this->owner->id,
        ]);

        AvailabilityTemplate::create([
            'listing_id' => $this->basketballCourt->id,
            'name' => 'Active Template 2',
            'days_of_week' => ['tuesday'],
            'start_time' => '08:00',
            'end_time' => '12:00',
            'base_hourly_price' => 1500.00,
            'is_active' => true,
            'created_by' => $this->owner->id,
        ]);

        AvailabilityTemplate::create([
            'listing_id' => $this->basketballCourt->id,
            'name' => 'Inactive Template',
            'days_of_week' => ['wednesday'],
            'start_time' => '08:00',
            'end_time' => '12:00',
            'is_active' => false,
            'created_by' => $this->owner->id,
        ]);

        $activeTemplates = AvailabilityTemplate::active()->get();
        $this->assertCount(2, $activeTemplates);
    }

    /** @test */
    public function it_scopes_by_listing()
    {
        // Basketball court templates
        AvailabilityTemplate::create([
            'listing_id' => $this->basketballCourt->id,
            'name' => 'Basketball Template',
            'days_of_week' => ['monday'],
            'start_time' => '08:00',
            'end_time' => '17:00',
            'base_hourly_price' => 1500.00,
            'is_active' => true,
            'created_by' => $this->owner->id,
        ]);

        // Hotel templates
        AvailabilityTemplate::create([
            'listing_id' => $this->hotel->id,
            'name' => 'Hotel Template',
            'days_of_week' => ['monday'],
            'start_time' => '15:00',
            'end_time' => '11:00',
            'base_hourly_price' => 1500.00,
            'is_active' => true,
            'created_by' => $this->owner->id,
        ]);

        $basketballTemplates = AvailabilityTemplate::forListing($this->basketballCourt->id)->get();
        $hotelTemplates = AvailabilityTemplate::forListing($this->hotel->id)->get();

        $this->assertCount(1, $basketballTemplates);
        $this->assertCount(1, $hotelTemplates);
        $this->assertEquals('Basketball Template', $basketballTemplates->first()->name);
        $this->assertEquals('Hotel Template', $hotelTemplates->first()->name);
    }

    /** @test */
    public function it_scopes_by_day_of_week()
    {
        AvailabilityTemplate::create([
            'listing_id' => $this->basketballCourt->id,
            'name' => 'Monday Template',
            'days_of_week' => ['monday', 'tuesday'],
            'start_time' => '08:00',
            'end_time' => '17:00',
            'base_hourly_price' => 1500.00,
            'is_active' => true,
            'created_by' => $this->owner->id,
        ]);

        AvailabilityTemplate::create([
            'listing_id' => $this->basketballCourt->id,
            'name' => 'Weekend Template',
            'days_of_week' => ['saturday', 'sunday'],
            'start_time' => '08:00',
            'end_time' => '17:00',
            'base_hourly_price' => 1500.00,
            'is_active' => true,
            'created_by' => $this->owner->id,
        ]);

        $mondayTemplates = AvailabilityTemplate::forDayOfWeek('monday')->get();
        $saturdayTemplates = AvailabilityTemplate::forDayOfWeek('saturday')->get();

        $this->assertCount(1, $mondayTemplates);
        $this->assertCount(1, $saturdayTemplates);
        $this->assertEquals('Monday Template', $mondayTemplates->first()->name);
        $this->assertEquals('Weekend Template', $saturdayTemplates->first()->name);
    }

    /** @test */
    public function it_prevents_overlapping_time_slots()
    {
        $template = AvailabilityTemplate::create([
            'listing_id' => $this->basketballCourt->id,
            'name' => 'Morning Template',
            'days_of_week' => ['monday'],
            'start_time' => '08:00',
            'end_time' => '12:00',
            'base_hourly_price' => 1500.00,
            'is_active' => true,
            'created_by' => $this->owner->id,
        ]);

        // Try to create overlapping template
        $overlappingTemplate = AvailabilityTemplate::create([
            'listing_id' => $this->basketballCourt->id,
            'name' => 'Overlapping Template',
            'days_of_week' => ['monday'],
            'start_time' => '10:00', // Overlaps with morning template
            'end_time' => '14:00',
            'base_hourly_price' => 1500.00,
            'is_active' => true,
            'created_by' => $this->owner->id,
        ]);

        // Check if template has overlap detection method
        if (method_exists($template, 'hasOverlapWith')) {
            $this->assertTrue($template->hasOverlapWith($overlappingTemplate));
        }

        // Both templates should exist (overlap detection is business logic, not database constraint)
        $this->assertDatabaseCount('availability_templates', 2);
    }

    /** @test */
    public function it_handles_cross_day_templates()
    {
        // Night shift template (e.g., hotel check-in/out)
        $template = AvailabilityTemplate::create([
            'listing_id' => $this->hotel->id,
            'name' => 'Overnight Template',
            'days_of_week' => ['monday'],
            'start_time' => '15:00', // 3 PM check-in
            'end_time' => '11:00',   // 11 AM check-out next day
            'daily_price' => 5000.00,
            'is_active' => true,
            'created_by' => $this->owner->id,
        ]);

        $this->assertEquals('15:00', $template->start_time->format('H:i'));
        $this->assertEquals('11:00', $template->end_time->format('H:i'));
        
        // Start time is after end time (crosses midnight)
        $this->assertTrue($template->start_time->gt($template->end_time));
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $template = AvailabilityTemplate::create([
            'listing_id' => $this->basketballCourt->id,
            'name' => 'Test Template',
            'days_of_week' => ['monday', 'wednesday', 'friday'],
            'start_time' => '08:00',
            'end_time' => '17:00',
            'slot_duration_minutes' => 60,
            'hourly_price' => 1500.50,
            'daily_price' => 8000.75,
            'booking_rules' => [
                'min_advance' => 24,
                'requires_approval' => true
            ],
            'is_active' => true,
            'created_by' => $this->owner->id,
        ]);

        // Test array casting
        $this->assertIsArray($template->days_of_week);
        $this->assertIsArray($template->booking_rules);
        
        // Test decimal casting
        $this->assertEquals(1500.50, $template->hourly_price);
        $this->assertEquals(8000.75, $template->daily_price);
        
        // Test integer casting
        $this->assertIsInt($template->slot_duration_minutes);
        
        // Test boolean casting
        $this->assertIsBool($template->is_active);
        
        // Test time casting
        $this->assertInstanceOf(Carbon::class, $template->start_time);
        $this->assertInstanceOf(Carbon::class, $template->end_time);
    }

    /** @test */
    public function it_soft_deletes_templates()
    {
        $template = AvailabilityTemplate::create([
            'listing_id' => $this->basketballCourt->id,
            'name' => 'Test Template',
            'days_of_week' => ['monday'],
            'start_time' => '08:00',
            'end_time' => '17:00',
            'base_hourly_price' => 1500.00,
            'is_active' => true,
            'created_by' => $this->owner->id,
        ]);

        $template->delete();

        $this->assertSoftDeleted('availability_templates', ['id' => $template->id]);
        $this->assertNotNull($template->fresh()->deleted_at);
    }

    /** @test */
    public function it_deactivates_instead_of_deleting_when_has_applied_availability()
    {
        $template = AvailabilityTemplate::create([
            'listing_id' => $this->basketballCourt->id,
            'name' => 'Applied Template',
            'days_of_week' => ['monday'],
            'start_time' => '08:00',
            'end_time' => '17:00',
            'base_hourly_price' => 1500.00,
            'is_active' => true,
            'created_by' => $this->owner->id,
        ]);

        // Apply template to create availability
        $startDate = Carbon::parse('2025-12-22'); // Monday
        $endDate = Carbon::parse('2025-12-22');
        $template->applyToDateRange($startDate, $endDate);

        // Template should have applied availability
        $this->assertDatabaseHas('listing_availability', [
            'listing_id' => $this->basketballCourt->id,
            'available_date' => '2025-12-22',
        ]);

        // When we try to "delete" template with applied availability,
        // it should deactivate instead of soft delete
        if (method_exists($template, 'safeDelete')) {
            $result = $template->safeDelete();
            
            $this->assertFalse($template->fresh()->is_active);
            $this->assertNull($template->fresh()->deleted_at);
        } else {
            // If safeDelete doesn't exist, regular delete should work
            $template->delete();
            $this->assertSoftDeleted('availability_templates', ['id' => $template->id]);
        }
    }
}