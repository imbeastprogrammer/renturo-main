<?php

namespace Tests\Unit\Models;

use Tests\TestCase\UnitTenantTestCase;
use App\Models\Booking;
use App\Models\Listing;
use App\Models\ListingUnit;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingTest extends UnitTenantTestCase
{
    use RefreshDatabase;

    protected $owner;
    protected $renter;
    protected $category;
    protected $subcategory;
    protected $listing;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->owner = User::factory()->create(['email' => 'owner@test.com']);
        $this->renter = User::factory()->create(['email' => 'renter@test.com']);

        // Create category and subcategory
        $this->category = Category::create(['name' => 'Test Category']);
        $this->subcategory = SubCategory::create([
            'name' => 'Test Subcategory',
            'category_id' => $this->category->id
        ]);

        // Create test listing
        $this->listing = Listing::create([
            'user_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'sub_category_id' => $this->subcategory->id,
            'title' => 'Test Property',
            'description' => 'A test property for booking',
            'slug' => 'test-property',
            'address' => '123 Test St',
            'city' => 'Test City',
            'province' => 'Test Province',
            'status' => 'active',
            'visibility' => 'public',
            'price_per_hour' => 100.00,
            'price_per_day' => 1000.00,
            'base_hourly_price' => 100.00,
            'base_daily_price' => 1000.00,
            'inventory_type' => 'single',
            'total_units' => 1,
            'instant_booking' => false,
            'cancellation_hours' => 24,
        ]);
    }

    /** @test */
    public function it_can_create_a_booking()
    {
        $booking = Booking::create([
            'booking_number' => 'BK-2025-000001',
            'listing_id' => $this->listing->id,
            'user_id' => $this->renter->id,
            'owner_id' => $this->owner->id,
            'booking_date' => now(),
            'check_in_date' => now()->addDays(1),
            'check_out_date' => now()->addDays(3),
            'duration_days' => 2,
            'duration_type' => 'daily',
            'base_price' => 1000.00,
            'subtotal' => 2000.00,
            'service_fee' => 100.00,
            'total_price' => 2100.00,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $this->assertDatabaseHas('bookings', [
            'booking_number' => 'BK-2025-000001',
            'listing_id' => $this->listing->id,
            'user_id' => $this->renter->id,
            'status' => 'pending',
        ]);

        $this->assertEquals('BK-2025-000001', $booking->booking_number);
        $this->assertEquals($this->listing->id, $booking->listing_id);
    }

    /** @test */
    public function it_generates_unique_booking_number()
    {
        // Create first booking with generated number
        $booking1 = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'listing_id' => $this->listing->id,
            'user_id' => $this->renter->id,
            'owner_id' => $this->owner->id,
            'booking_date' => now(),
            'check_in_date' => now()->addDays(1),
            'check_out_date' => now()->addDays(3),
            'duration_days' => 2,
            'duration_type' => 'daily',
            'base_price' => 1000.00,
            'subtotal' => 2000.00,
            'total_price' => 2000.00,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        // Create second booking with generated number
        $booking2 = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'listing_id' => $this->listing->id,
            'user_id' => $this->renter->id,
            'owner_id' => $this->owner->id,
            'booking_date' => now(),
            'check_in_date' => now()->addDays(5),
            'check_out_date' => now()->addDays(7),
            'duration_days' => 2,
            'duration_type' => 'daily',
            'base_price' => 1000.00,
            'subtotal' => 2000.00,
            'total_price' => 2000.00,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $this->assertNotEquals($booking1->booking_number, $booking2->booking_number);
        $this->assertStringStartsWith('BK-', $booking1->booking_number);
        $this->assertMatchesRegularExpression('/^BK-\d{6}-\d{6}$/', $booking1->booking_number);
    }

    /** @test */
    public function it_has_relationships()
    {
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'listing_id' => $this->listing->id,
            'user_id' => $this->renter->id,
            'owner_id' => $this->owner->id,
            'booking_date' => now(),
            'check_in_date' => now()->addDays(1),
            'check_out_date' => now()->addDays(3),
            'duration_days' => 2,
            'duration_type' => 'daily',
            'base_price' => 1000.00,
            'subtotal' => 2000.00,
            'total_price' => 2000.00,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $this->assertInstanceOf(Listing::class, $booking->listing);
        $this->assertInstanceOf(User::class, $booking->user);
        $this->assertInstanceOf(User::class, $booking->owner);
        $this->assertEquals($this->listing->id, $booking->listing->id);
        $this->assertEquals($this->renter->id, $booking->user->id);
        $this->assertEquals($this->owner->id, $booking->owner->id);
    }

    /** @test */
    public function it_calculates_total_price_correctly()
    {
        $booking = new Booking([
            'subtotal' => 2000.00,
            'service_fee' => 100.00,
            'cleaning_fee' => 50.00,
            'tax_amount' => 240.00,
            'discount_amount' => 100.00,
        ]);

        $total = $booking->calculateTotalPrice();

        $this->assertEquals(2290.00, $total);
    }

    /** @test */
    public function it_calculates_duration_in_days()
    {
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'listing_id' => $this->listing->id,
            'user_id' => $this->renter->id,
            'owner_id' => $this->owner->id,
            'booking_date' => now(),
            'check_in_date' => '2025-11-01',
            'check_out_date' => '2025-11-05',
            'duration_days' => 4,
            'duration_type' => 'daily',
            'base_price' => 1000.00,
            'subtotal' => 4000.00,
            'total_price' => 4000.00,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $this->assertEquals(4, $booking->getDurationInDays());
    }

    /** @test */
    public function it_calculates_duration_in_hours()
    {
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'listing_id' => $this->listing->id,
            'user_id' => $this->renter->id,
            'owner_id' => $this->owner->id,
            'booking_date' => now(),
            'check_in_date' => '2025-11-01',
            'check_out_date' => '2025-11-01',
            'check_in_time' => '10:00:00',
            'check_out_time' => '14:00:00',
            'duration_hours' => 4,
            'duration_type' => 'hourly',
            'base_price' => 100.00,
            'subtotal' => 400.00,
            'total_price' => 400.00,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $this->assertEquals(4, $booking->getDurationInHours());
    }

    /** @test */
    public function it_detects_booking_conflicts()
    {
        // Create first booking
        Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'listing_id' => $this->listing->id,
            'user_id' => $this->renter->id,
            'owner_id' => $this->owner->id,
            'booking_date' => now(),
            'check_in_date' => '2025-11-10',
            'check_out_date' => '2025-11-15',
            'duration_days' => 5,
            'duration_type' => 'daily',
            'base_price' => 1000.00,
            'subtotal' => 5000.00,
            'total_price' => 5000.00,
            'status' => 'confirmed',
            'payment_status' => 'pending',
        ]);

        // Test overlapping dates
        $hasConflict = Booking::hasConflict(
            $this->listing->id,
            '2025-11-12',
            '2025-11-17'
        );

        $this->assertTrue($hasConflict);

        // Test non-overlapping dates
        $hasConflict = Booking::hasConflict(
            $this->listing->id,
            '2025-11-20',
            '2025-11-25'
        );

        $this->assertFalse($hasConflict);
    }

    /** @test */
    public function it_can_confirm_pending_booking()
    {
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'listing_id' => $this->listing->id,
            'user_id' => $this->renter->id,
            'owner_id' => $this->owner->id,
            'booking_date' => now(),
            'check_in_date' => now()->addDays(1),
            'check_out_date' => now()->addDays(3),
            'duration_days' => 2,
            'duration_type' => 'daily',
            'base_price' => 1000.00,
            'subtotal' => 2000.00,
            'total_price' => 2000.00,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $result = $booking->confirm();

        $this->assertTrue($result);
        $this->assertEquals('confirmed', $booking->status);
        $this->assertNotNull($booking->confirmed_at);
        $this->assertNotNull($booking->confirmation_code);
    }

    /** @test */
    public function it_cannot_confirm_non_pending_booking()
    {
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'listing_id' => $this->listing->id,
            'user_id' => $this->renter->id,
            'owner_id' => $this->owner->id,
            'booking_date' => now(),
            'check_in_date' => now()->addDays(1),
            'check_out_date' => now()->addDays(3),
            'duration_days' => 2,
            'duration_type' => 'daily',
            'base_price' => 1000.00,
            'subtotal' => 2000.00,
            'total_price' => 2000.00,
            'status' => 'confirmed',
            'payment_status' => 'pending',
        ]);

        $result = $booking->confirm();

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_mark_as_paid()
    {
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'listing_id' => $this->listing->id,
            'user_id' => $this->renter->id,
            'owner_id' => $this->owner->id,
            'booking_date' => now(),
            'check_in_date' => now()->addDays(1),
            'check_out_date' => now()->addDays(3),
            'duration_days' => 2,
            'duration_type' => 'daily',
            'base_price' => 1000.00,
            'subtotal' => 2000.00,
            'total_price' => 2000.00,
            'status' => 'confirmed',
            'payment_status' => 'pending',
        ]);

        $result = $booking->markAsPaid('TXN-123456');

        $this->assertTrue($result);
        $this->assertEquals('paid', $booking->status);
        $this->assertEquals('paid', $booking->payment_status);
        $this->assertEquals('TXN-123456', $booking->payment_transaction_id);
        $this->assertNotNull($booking->payment_completed_at);
    }

    /** @test */
    public function it_can_check_in()
    {
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'listing_id' => $this->listing->id,
            'user_id' => $this->renter->id,
            'owner_id' => $this->owner->id,
            'booking_date' => now(),
            'check_in_date' => now(),
            'check_out_date' => now()->addDays(2),
            'duration_days' => 2,
            'duration_type' => 'daily',
            'base_price' => 1000.00,
            'subtotal' => 2000.00,
            'total_price' => 2000.00,
            'status' => 'paid',
            'payment_status' => 'paid',
        ]);

        $result = $booking->checkIn();

        $this->assertTrue($result);
        $this->assertEquals('checked_in', $booking->status);
        $this->assertNotNull($booking->checked_in_at);
    }

    /** @test */
    public function it_can_check_out()
    {
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'listing_id' => $this->listing->id,
            'user_id' => $this->renter->id,
            'owner_id' => $this->owner->id,
            'booking_date' => now()->subDays(2),
            'check_in_date' => now()->subDays(2),
            'check_out_date' => now(),
            'duration_days' => 2,
            'duration_type' => 'daily',
            'base_price' => 1000.00,
            'subtotal' => 2000.00,
            'total_price' => 2000.00,
            'status' => 'checked_in',
            'payment_status' => 'paid',
            'checked_in_at' => now()->subDays(2),
        ]);

        $result = $booking->checkOut();

        $this->assertTrue($result);
        $this->assertEquals('completed', $booking->status);
        $this->assertNotNull($booking->checked_out_at);
    }

    /** @test */
    public function it_determines_if_booking_is_cancellable()
    {
        // Booking far in the future (cancellable)
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'listing_id' => $this->listing->id,
            'user_id' => $this->renter->id,
            'owner_id' => $this->owner->id,
            'booking_date' => now(),
            'check_in_date' => now()->addDays(10),
            'check_out_date' => now()->addDays(12),
            'duration_days' => 2,
            'duration_type' => 'daily',
            'base_price' => 1000.00,
            'subtotal' => 2000.00,
            'total_price' => 2000.00,
            'status' => 'confirmed',
            'payment_status' => 'pending',
        ]);

        $this->assertTrue($booking->isCancellable());

        // Already cancelled booking (not cancellable)
        $booking->status = 'cancelled';
        $this->assertFalse($booking->isCancellable());
    }

    /** @test */
    public function it_can_cancel_booking()
    {
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'listing_id' => $this->listing->id,
            'user_id' => $this->renter->id,
            'owner_id' => $this->owner->id,
            'booking_date' => now(),
            'check_in_date' => now()->addDays(10),
            'check_out_date' => now()->addDays(12),
            'duration_days' => 2,
            'duration_type' => 'daily',
            'base_price' => 1000.00,
            'subtotal' => 2000.00,
            'total_price' => 2000.00,
            'status' => 'confirmed',
            'payment_status' => 'pending',
        ]);

        $result = $booking->cancel($this->renter->id, 'Change of plans');

        $this->assertTrue($result);
        $this->assertEquals('cancelled', $booking->status);
        $this->assertNotNull($booking->cancelled_at);
        $this->assertEquals($this->renter->id, $booking->cancelled_by);
        $this->assertEquals('Change of plans', $booking->cancellation_reason);
        $this->assertGreaterThanOrEqual(0, $booking->refund_amount);
    }

    /** @test */
    public function it_can_reject_booking()
    {
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'listing_id' => $this->listing->id,
            'user_id' => $this->renter->id,
            'owner_id' => $this->owner->id,
            'booking_date' => now(),
            'check_in_date' => now()->addDays(5),
            'check_out_date' => now()->addDays(7),
            'duration_days' => 2,
            'duration_type' => 'daily',
            'base_price' => 1000.00,
            'subtotal' => 2000.00,
            'total_price' => 2000.00,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $result = $booking->reject('Property not available');

        $this->assertTrue($result);
        $this->assertEquals('rejected', $booking->status);
        $this->assertEquals('Property not available', $booking->cancellation_reason);
    }

    /** @test */
    public function it_checks_if_booking_is_active()
    {
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'listing_id' => $this->listing->id,
            'user_id' => $this->renter->id,
            'owner_id' => $this->owner->id,
            'booking_date' => now(),
            'check_in_date' => now()->addDays(1),
            'check_out_date' => now()->addDays(3),
            'duration_days' => 2,
            'duration_type' => 'daily',
            'base_price' => 1000.00,
            'subtotal' => 2000.00,
            'total_price' => 2000.00,
            'status' => 'confirmed',
            'payment_status' => 'pending',
        ]);

        $this->assertTrue($booking->isActive());

        $booking->status = 'cancelled';
        $this->assertFalse($booking->isActive());
    }

    /** @test */
    public function it_uses_scopes_correctly()
    {
        // Create bookings with different statuses
        $pending = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'listing_id' => $this->listing->id,
            'user_id' => $this->renter->id,
            'owner_id' => $this->owner->id,
            'booking_date' => now(),
            'check_in_date' => now()->addDays(1),
            'check_out_date' => now()->addDays(3),
            'duration_days' => 2,
            'duration_type' => 'daily',
            'base_price' => 1000.00,
            'subtotal' => 2000.00,
            'total_price' => 2000.00,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $confirmed = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'listing_id' => $this->listing->id,
            'user_id' => $this->renter->id,
            'owner_id' => $this->owner->id,
            'booking_date' => now(),
            'check_in_date' => now()->addDays(5),
            'check_out_date' => now()->addDays(7),
            'duration_days' => 2,
            'duration_type' => 'daily',
            'base_price' => 1000.00,
            'subtotal' => 2000.00,
            'total_price' => 2000.00,
            'status' => 'confirmed',
            'payment_status' => 'pending',
        ]);

        $cancelled = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'listing_id' => $this->listing->id,
            'user_id' => $this->renter->id,
            'owner_id' => $this->owner->id,
            'booking_date' => now(),
            'check_in_date' => now()->addDays(10),
            'check_out_date' => now()->addDays(12),
            'duration_days' => 2,
            'duration_type' => 'daily',
            'base_price' => 1000.00,
            'subtotal' => 2000.00,
            'total_price' => 2000.00,
            'status' => 'cancelled',
            'payment_status' => 'pending',
        ]);

        $this->assertEquals(1, Booking::pending()->count());
        $this->assertEquals(1, Booking::confirmed()->count());
        $this->assertEquals(1, Booking::cancelled()->count());
        $this->assertEquals(1, Booking::active()->count());
        $this->assertEquals(3, Booking::forUser($this->renter->id)->count());
        $this->assertEquals(3, Booking::forOwner($this->owner->id)->count());
        $this->assertEquals(3, Booking::forListing($this->listing->id)->count());
    }

    /** @test */
    public function it_returns_correct_status_color()
    {
        $booking = new Booking(['status' => 'pending']);
        $this->assertEquals('warning', $booking->getStatusColor());

        $booking->status = 'confirmed';
        $this->assertEquals('success', $booking->getStatusColor());

        $booking->status = 'cancelled';
        $this->assertEquals('danger', $booking->getStatusColor());
    }

    /** @test */
    public function it_returns_correct_status_label()
    {
        $booking = new Booking(['status' => 'checked_in']);
        $this->assertEquals('Checked in', $booking->getStatusLabel());

        $booking->status = 'in_progress';
        $this->assertEquals('In progress', $booking->getStatusLabel());
    }
}
