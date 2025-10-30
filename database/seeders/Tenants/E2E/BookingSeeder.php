<?php

namespace Database\Seeders\Tenants\E2E;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Listing;
use App\Models\ListingUnit;
use App\Models\ListingAvailability;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎫 Starting Booking Seeder for Universal Property Types...');

        DB::beginTransaction();

        try {
            // Create renter users
            $this->command->info('👥 Creating renter users...');
            $renters = $this->createRenters();

            // Get all listings
            $listings = Listing::with(['units', 'owner'])->get();

            if ($listings->isEmpty()) {
                $this->command->warn('⚠️  No listings found. Please run property seeders first.');
                DB::rollBack();
                return;
            }

            $this->command->info('📋 Creating bookings for ' . $listings->count() . ' listings...');

            $totalBookings = 0;
            
            foreach ($listings as $listing) {
                $bookingsCreated = $this->createBookingsForListing($listing, $renters);
                $totalBookings += $bookingsCreated;
            }

            DB::commit();

            $this->command->info("✅ Successfully created {$totalBookings} bookings!");
            $this->displaySummary();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Booking seeder failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create renter users
     */
    private function createRenters(): array
    {
        $renters = [];
        
        $renterData = [
            [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john.smith@example.com',
                'mobile_number' => '+639171234567',
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@example.com',
                'mobile_number' => '+639171234568',
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Brown',
                'email' => 'michael.brown@example.com',
                'mobile_number' => '+639171234569',
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily.davis@example.com',
                'mobile_number' => '+639171234570',
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Wilson',
                'email' => 'david.wilson@example.com',
                'mobile_number' => '+639171234571',
            ],
        ];

        foreach ($renterData as $data) {
            // Check if user exists by email or mobile number
            $user = User::where('email', $data['email'])
                ->orWhere('mobile_number', $data['mobile_number'])
                ->first();
            
            if (!$user) {
                $user = User::create(array_merge($data, [
                    'password' => bcrypt('password123'),
                    'email_verified_at' => now(),
                    'role' => 'user',
                ]));
            }
            
            $renters[] = $user;
        }

        return $renters;
    }

    /**
     * Create bookings for a specific listing
     */
    private function createBookingsForListing(Listing $listing, array $renters): int
    {
        $bookingsCreated = 0;
        $category = $listing->category->name ?? 'Unknown';

        // Determine booking type based on listing
        if (str_contains($listing->title, 'Basketball') || str_contains($category, 'Sports')) {
            $bookingsCreated = $this->createSportsBookings($listing, $renters);
        } elseif (str_contains($category, 'Accommodation') || str_contains($listing->title, 'Hotel')) {
            $bookingsCreated = $this->createHotelBookings($listing, $renters);
        } elseif (str_contains($category, 'Transportation') || str_contains($listing->title, 'Rental')) {
            $bookingsCreated = $this->createCarRentalBookings($listing, $renters);
        } elseif (str_contains($category, 'Event') || str_contains($listing->title, 'Venue')) {
            $bookingsCreated = $this->createEventVenueBookings($listing, $renters);
        } else {
            $bookingsCreated = $this->createGenericBookings($listing, $renters);
        }

        return $bookingsCreated;
    }

    /**
     * Create bookings for sports venues (hourly)
     */
    private function createSportsBookings(Listing $listing, array $renters): int
    {
        $bookings = [];

        // Past bookings (completed)
        for ($i = 0; $i < 3; $i++) {
            $daysAgo = rand(7, 30);
            $checkInDate = Carbon::now()->subDays($daysAgo);
            $startHour = rand(8, 18);
            $duration = rand(2, 4);

            $bookings[] = $this->createBooking([
                'listing' => $listing,
                'renter' => $renters[array_rand($renters)],
                'check_in_date' => $checkInDate->format('Y-m-d'),
                'check_out_date' => $checkInDate->format('Y-m-d'),
                'check_in_time' => sprintf('%02d:00:00', $startHour),
                'check_out_time' => sprintf('%02d:00:00', $startHour + $duration),
                'duration_hours' => $duration,
                'duration_type' => 'hourly',
                'base_price' => $listing->base_hourly_price ?? 100,
                'status' => 'completed',
                'payment_status' => 'paid',
                'number_of_players' => rand(10, 20),
                'checked_in_at' => $checkInDate->copy()->addHours($startHour),
                'checked_out_at' => $checkInDate->copy()->addHours($startHour + $duration),
            ]);
        }

        // Current/Upcoming bookings (confirmed/paid)
        for ($i = 0; $i < 2; $i++) {
            $daysAhead = rand(1, 14);
            $checkInDate = Carbon::now()->addDays($daysAhead);
            $startHour = rand(8, 18);
            $duration = rand(2, 4);

            $bookings[] = $this->createBooking([
                'listing' => $listing,
                'renter' => $renters[array_rand($renters)],
                'check_in_date' => $checkInDate->format('Y-m-d'),
                'check_out_date' => $checkInDate->format('Y-m-d'),
                'check_in_time' => sprintf('%02d:00:00', $startHour),
                'check_out_time' => sprintf('%02d:00:00', $startHour + $duration),
                'duration_hours' => $duration,
                'duration_type' => 'hourly',
                'base_price' => $listing->base_hourly_price ?? 100,
                'status' => rand(0, 1) ? 'confirmed' : 'paid',
                'payment_status' => rand(0, 1) ? 'pending' : 'paid',
                'number_of_players' => rand(10, 20),
                'confirmed_at' => now(),
            ]);
        }

        return count($bookings);
    }

    /**
     * Create bookings for hotels (daily)
     */
    private function createHotelBookings(Listing $listing, array $renters): int
    {
        $bookings = [];
        $units = $listing->units->take(3); // Book only some rooms

        foreach ($units as $unit) {
            // Past booking (completed)
            $daysAgo = rand(10, 60);
            $checkInDate = Carbon::now()->subDays($daysAgo);
            $nights = rand(2, 5);

            $bookings[] = $this->createBooking([
                'listing' => $listing,
                'unit' => $unit,
                'renter' => $renters[array_rand($renters)],
                'check_in_date' => $checkInDate->format('Y-m-d'),
                'check_out_date' => $checkInDate->copy()->addDays($nights)->format('Y-m-d'),
                'duration_days' => $nights,
                'duration_type' => 'daily',
                'base_price' => $listing->base_daily_price ?? 1000,
                'status' => 'completed',
                'payment_status' => 'paid',
                'number_of_guests' => rand(1, 3),
                'cleaning_fee' => 500,
                'checked_in_at' => $checkInDate->copy()->addHours(15),
                'checked_out_at' => $checkInDate->copy()->addDays($nights)->addHours(11),
            ]);

            // Upcoming booking (confirmed)
            $daysAhead = rand(5, 30);
            $checkInDate = Carbon::now()->addDays($daysAhead);
            $nights = rand(1, 4);

            $bookings[] = $this->createBooking([
                'listing' => $listing,
                'unit' => $unit,
                'renter' => $renters[array_rand($renters)],
                'check_in_date' => $checkInDate->format('Y-m-d'),
                'check_out_date' => $checkInDate->copy()->addDays($nights)->format('Y-m-d'),
                'duration_days' => $nights,
                'duration_type' => 'daily',
                'base_price' => $listing->base_daily_price ?? 1000,
                'status' => 'paid',
                'payment_status' => 'paid',
                'number_of_guests' => rand(1, 3),
                'cleaning_fee' => 500,
                'confirmed_at' => now(),
                'payment_completed_at' => now(),
            ]);
        }

        return count($bookings);
    }

    /**
     * Create bookings for car rentals (daily)
     */
    private function createCarRentalBookings(Listing $listing, array $renters): int
    {
        $bookings = [];
        $units = $listing->units->take(2); // Book only some vehicles

        foreach ($units as $unit) {
            // Past booking (completed)
            $daysAgo = rand(15, 45);
            $checkInDate = Carbon::now()->subDays($daysAgo);
            $days = rand(3, 7);

            $bookings[] = $this->createBooking([
                'listing' => $listing,
                'unit' => $unit,
                'renter' => $renters[array_rand($renters)],
                'check_in_date' => $checkInDate->format('Y-m-d'),
                'check_out_date' => $checkInDate->copy()->addDays($days)->format('Y-m-d'),
                'check_in_time' => '09:00:00',
                'check_out_time' => '09:00:00',
                'duration_days' => $days,
                'duration_type' => 'daily',
                'base_price' => $listing->base_daily_price ?? 500,
                'status' => 'completed',
                'payment_status' => 'paid',
                'security_deposit' => 5000,
                'checked_in_at' => $checkInDate->copy()->addHours(9),
                'checked_out_at' => $checkInDate->copy()->addDays($days)->addHours(9),
            ]);

            // Upcoming booking (confirmed)
            $daysAhead = rand(3, 20);
            $checkInDate = Carbon::now()->addDays($daysAhead);
            $days = rand(2, 5);

            $bookings[] = $this->createBooking([
                'listing' => $listing,
                'unit' => $unit,
                'renter' => $renters[array_rand($renters)],
                'check_in_date' => $checkInDate->format('Y-m-d'),
                'check_out_date' => $checkInDate->copy()->addDays($days)->format('Y-m-d'),
                'check_in_time' => '09:00:00',
                'check_out_time' => '09:00:00',
                'duration_days' => $days,
                'duration_type' => 'daily',
                'base_price' => $listing->base_daily_price ?? 500,
                'status' => 'confirmed',
                'payment_status' => 'pending',
                'security_deposit' => 5000,
                'confirmed_at' => now(),
            ]);
        }

        return count($bookings);
    }

    /**
     * Create bookings for event venues (daily)
     */
    private function createEventVenueBookings(Listing $listing, array $renters): int
    {
        $bookings = [];

        // Past booking (completed)
        $daysAgo = rand(20, 60);
        $checkInDate = Carbon::now()->subDays($daysAgo);

        $bookings[] = $this->createBooking([
            'listing' => $listing,
            'renter' => $renters[array_rand($renters)],
            'check_in_date' => $checkInDate->format('Y-m-d'),
            'check_out_date' => $checkInDate->format('Y-m-d'),
            'check_in_time' => '08:00:00',
            'check_out_time' => '23:00:00',
            'duration_hours' => 15,
            'duration_type' => 'daily',
            'base_price' => $listing->base_daily_price ?? 5000,
            'status' => 'completed',
            'payment_status' => 'paid',
            'number_of_guests' => rand(50, 200),
            'cleaning_fee' => 2000,
            'checked_in_at' => $checkInDate->copy()->addHours(8),
            'checked_out_at' => $checkInDate->copy()->addHours(23),
        ]);

        // Upcoming booking (paid)
        $daysAhead = rand(30, 90);
        $checkInDate = Carbon::now()->addDays($daysAhead);

        $bookings[] = $this->createBooking([
            'listing' => $listing,
            'renter' => $renters[array_rand($renters)],
            'check_in_date' => $checkInDate->format('Y-m-d'),
            'check_out_date' => $checkInDate->format('Y-m-d'),
            'check_in_time' => '08:00:00',
            'check_out_time' => '23:00:00',
            'duration_hours' => 15,
            'duration_type' => 'daily',
            'base_price' => $listing->base_daily_price ?? 5000,
            'status' => 'paid',
            'payment_status' => 'paid',
            'number_of_guests' => rand(50, 200),
            'cleaning_fee' => 2000,
            'confirmed_at' => now(),
            'payment_completed_at' => now(),
        ]);

        // Pending booking (awaiting confirmation)
        $daysAhead = rand(15, 45);
        $checkInDate = Carbon::now()->addDays($daysAhead);

        $bookings[] = $this->createBooking([
            'listing' => $listing,
            'renter' => $renters[array_rand($renters)],
            'check_in_date' => $checkInDate->format('Y-m-d'),
            'check_out_date' => $checkInDate->format('Y-m-d'),
            'check_in_time' => '10:00:00',
            'check_out_time' => '18:00:00',
            'duration_hours' => 8,
            'duration_type' => 'daily',
            'base_price' => ($listing->base_daily_price ?? 5000) * 0.6, // Half-day rate
            'status' => 'pending',
            'payment_status' => 'pending',
            'number_of_guests' => rand(30, 100),
            'cleaning_fee' => 1000,
        ]);

        return count($bookings);
    }

    /**
     * Create generic bookings for other property types
     */
    private function createGenericBookings(Listing $listing, array $renters): int
    {
        $bookings = [];

        // Create 2 bookings
        for ($i = 0; $i < 2; $i++) {
            $daysAhead = rand(-30, 30); // Some past, some future
            $checkInDate = Carbon::now()->addDays($daysAhead);
            $duration = rand(1, 5);

            $status = $daysAhead < 0 ? 'completed' : 'confirmed';

            $bookings[] = $this->createBooking([
                'listing' => $listing,
                'renter' => $renters[array_rand($renters)],
                'check_in_date' => $checkInDate->format('Y-m-d'),
                'check_out_date' => $checkInDate->copy()->addDays($duration)->format('Y-m-d'),
                'duration_days' => $duration,
                'duration_type' => 'daily',
                'base_price' => $listing->price_per_day ?? 500,
                'status' => $status,
                'payment_status' => $status === 'completed' ? 'paid' : 'pending',
                'confirmed_at' => $status === 'confirmed' ? now() : null,
            ]);
        }

        return count($bookings);
    }

    /**
     * Create a single booking
     */
    private function createBooking(array $data): Booking
    {
        $listing = $data['listing'];
        $renter = $data['renter'];
        $unit = $data['unit'] ?? null;

        // Calculate pricing
        $duration = $data['duration_hours'] ?? $data['duration_days'] ?? 1;
        $subtotal = $data['base_price'] * $duration;
        $serviceFee = $subtotal * 0.05; // 5% service fee
        $cleaningFee = $data['cleaning_fee'] ?? 0;
        $securityDeposit = $data['security_deposit'] ?? 0;
        $taxAmount = $subtotal * 0.12; // 12% tax
        $totalPrice = $subtotal + $serviceFee + $cleaningFee + $taxAmount;

        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'booking_type' => 'rental',
            'listing_id' => $listing->id,
            'listing_unit_id' => $unit->id ?? null,
            'user_id' => $renter->id,
            'owner_id' => $listing->user_id,
            'booking_date' => now()->subDays(rand(0, 7)),
            'check_in_date' => $data['check_in_date'],
            'check_out_date' => $data['check_out_date'],
            'check_in_time' => $data['check_in_time'] ?? null,
            'check_out_time' => $data['check_out_time'] ?? null,
            'duration_hours' => $data['duration_hours'] ?? null,
            'duration_days' => $data['duration_days'] ?? null,
            'duration_type' => $data['duration_type'],
            'base_price' => $data['base_price'],
            'subtotal' => $subtotal,
            'service_fee' => $serviceFee,
            'cleaning_fee' => $cleaningFee,
            'security_deposit' => $securityDeposit,
            'tax_amount' => $taxAmount,
            'discount_amount' => 0,
            'total_price' => $totalPrice,
            'currency' => 'PHP',
            'number_of_guests' => $data['number_of_guests'] ?? 1,
            'number_of_players' => $data['number_of_players'] ?? null,
            'status' => $data['status'],
            'payment_status' => $data['payment_status'],
            'confirmed_at' => $data['confirmed_at'] ?? null,
            'checked_in_at' => $data['checked_in_at'] ?? null,
            'checked_out_at' => $data['checked_out_at'] ?? null,
            'payment_completed_at' => $data['payment_completed_at'] ?? null,
            'auto_confirmed' => $listing->instant_booking ?? false,
            'requires_approval' => !($listing->instant_booking ?? false),
            'booking_source' => 'mobile_app',
            'platform' => 'ios',
        ]);

        return $booking;
    }

    /**
     * Display summary of created bookings
     */
    private function displaySummary(): void
    {
        $total = Booking::count();
        $pending = Booking::where('status', 'pending')->count();
        $confirmed = Booking::where('status', 'confirmed')->count();
        $paid = Booking::where('status', 'paid')->count();
        $completed = Booking::where('status', 'completed')->count();

        $this->command->info("\n" . str_repeat('=', 60));
        $this->command->info('   📊 BOOKING SEEDER SUMMARY');
        $this->command->info(str_repeat('=', 60));
        $this->command->info("Total Bookings Created: {$total}");
        $this->command->info("├─ Pending: {$pending}");
        $this->command->info("├─ Confirmed: {$confirmed}");
        $this->command->info("├─ Paid: {$paid}");
        $this->command->info("└─ Completed: {$completed}");
        $this->command->info(str_repeat('=', 60) . "\n");
    }
}
