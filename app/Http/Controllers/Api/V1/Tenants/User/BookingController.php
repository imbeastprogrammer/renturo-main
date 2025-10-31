<?php

namespace App\Http\Controllers\Api\V1\Tenants\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Listing;
use App\Models\ListingAvailability;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

/**
 * @OA\Tag(
 *     name="User - Bookings",
 *     description="Booking management for renters/end users"
 * )
 */
class BookingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user/v1/bookings",
     *     tags={"User - Bookings"},
     *     summary="Get my bookings",
     *     description="Retrieve all bookings made by the authenticated user with filtering options",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filter by status (pending, confirmed, paid, checked_in, in_progress, completed, cancelled)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=false,
     *         description="Filter by type (upcoming, current, past)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="My bookings retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="My bookings retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Booking::with(['listing', 'listingUnit', 'owner'])
                ->forUser(Auth::id()); // Only bookings created by THIS user

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('type')) {
                match($request->type) {
                    'upcoming' => $query->upcoming(),
                    'current' => $query->current(),
                    'past' => $query->past(),
                    default => null
                };
            }

            $bookings = $query->orderBy('check_in_date', 'desc')->get();

            return response()->json([
                'success' => true,
                'message' => 'My bookings retrieved successfully',
                'data' => $bookings
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving user bookings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving bookings'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user/v1/bookings",
     *     tags={"User - Bookings"},
     *     summary="Create a new booking",
     *     description="Create a booking with automatic conflict detection and availability locking",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"listing_id", "check_in_date", "check_out_date"},
     *             @OA\Property(property="listing_id", type="integer", example=1),
     *             @OA\Property(property="listing_unit_id", type="integer", example=1),
     *             @OA\Property(property="check_in_date", type="string", format="date", example="2025-11-01"),
     *             @OA\Property(property="check_out_date", type="string", format="date", example="2025-11-03"),
     *             @OA\Property(property="check_in_time", type="string", format="time", example="14:00"),
     *             @OA\Property(property="check_out_time", type="string", format="time", example="16:00"),
     *             @OA\Property(property="number_of_guests", type="integer", example=2),
     *             @OA\Property(property="number_of_players", type="integer", example=10),
     *             @OA\Property(property="special_requests", type="string", example="Need extra towels"),
     *             @OA\Property(property="guest_details", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Booking created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Booking created successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=409, description="Booking conflict detected"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'listing_id' => 'required|exists:listings,id',
                'listing_unit_id' => 'nullable|exists:listing_units,id',
                'check_in_date' => 'required|date|after_or_equal:today',
                'check_out_date' => 'required|date|after:check_in_date',
                'check_in_time' => 'nullable|date_format:H:i',
                'check_out_time' => 'nullable|date_format:H:i|after:check_in_time',
                'number_of_guests' => 'nullable|integer|min:1',
                'number_of_players' => 'nullable|integer|min:1',
                'number_of_vehicles' => 'nullable|integer|min:1',
                'special_requests' => 'nullable|string|max:1000',
                'guest_details' => 'nullable|array',
            ]);

            // Start transaction
            DB::beginTransaction();

            // Get listing
            $listing = Listing::findOrFail($validated['listing_id']);

            // Check for booking conflicts
            if (Booking::hasConflict(
                $validated['listing_id'],
                $validated['check_in_date'],
                $validated['check_out_date'],
                $validated['check_in_time'] ?? null,
                $validated['check_out_time'] ?? null
            )) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Booking conflict detected. The selected dates/times are already booked.'
                ], 409);
            }

            // Calculate duration
            $checkIn = Carbon::parse($validated['check_in_date']);
            $checkOut = Carbon::parse($validated['check_out_date']);
            $durationDays = $checkIn->diffInDays($checkOut);
            
            $durationHours = null;
            if (isset($validated['check_in_time']) && isset($validated['check_out_time'])) {
                $checkInDateTime = Carbon::parse($validated['check_in_date'] . ' ' . $validated['check_in_time']);
                $checkOutDateTime = Carbon::parse($validated['check_out_date'] . ' ' . $validated['check_out_time']);
                $durationHours = $checkInDateTime->diffInHours($checkOutDateTime);
            }

            // Determine duration type
            $durationType = $durationHours ? 'hourly' : 'daily';

            // Calculate pricing
            $basePrice = $durationType === 'hourly' 
                ? ($listing->base_hourly_price ?? $listing->price_per_hour)
                : ($listing->base_daily_price ?? $listing->price_per_day);
            
            $duration = $durationType === 'hourly' ? $durationHours : $durationDays;
            $subtotal = $basePrice * $duration;
            
            // Calculate fees
            $serviceFee = $subtotal * ($listing->service_fee_percentage ?? 5) / 100;
            $cleaningFee = $listing->cleaning_fee ?? 0;
            $taxAmount = $subtotal * 0.12; // 12% tax (adjust as needed)
            
            $totalPrice = $subtotal + $serviceFee + $cleaningFee + $taxAmount;

            // Create booking
            $booking = Booking::create([
                'booking_number' => Booking::generateBookingNumber(),
                'booking_type' => 'rental',
                'listing_id' => $validated['listing_id'],
                'listing_unit_id' => $validated['listing_unit_id'] ?? null,
                'user_id' => Auth::id(), // The renter/user
                'owner_id' => $listing->user_id, // The property owner
                'booking_date' => now(),
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'check_in_time' => $validated['check_in_time'] ?? null,
                'check_out_time' => $validated['check_out_time'] ?? null,
                'duration_hours' => $durationHours,
                'duration_days' => $durationDays,
                'duration_type' => $durationType,
                'base_price' => $basePrice,
                'subtotal' => $subtotal,
                'service_fee' => $serviceFee,
                'cleaning_fee' => $cleaningFee,
                'security_deposit' => $listing->security_deposit ?? 0,
                'tax_amount' => $taxAmount,
                'discount_amount' => 0,
                'total_price' => $totalPrice,
                'currency' => $listing->currency ?? 'PHP',
                'number_of_guests' => $validated['number_of_guests'] ?? 1,
                'number_of_players' => $validated['number_of_players'] ?? null,
                'number_of_vehicles' => $validated['number_of_vehicles'] ?? null,
                'guest_details' => $validated['guest_details'] ?? null,
                'status' => $listing->instant_booking ? 'confirmed' : 'pending',
                'payment_status' => 'pending',
                'special_requests' => $validated['special_requests'] ?? null,
                'auto_confirmed' => $listing->instant_booking ?? false,
                'requires_approval' => !($listing->instant_booking ?? false),
                'booking_source' => 'mobile_app',
                'platform' => $request->header('User-Agent'),
            ]);

            // Auto-confirm if instant booking
            if ($listing->instant_booking) {
                $booking->confirm();
            }

            // Mark availability slots as booked
            $this->markAvailabilityAsBooked($booking);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully',
                'data' => $booking->load(['listing', 'listingUnit', 'owner'])
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating booking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the booking'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user/v1/bookings/{id}",
     *     tags={"User - Bookings"},
     *     summary="Get my booking details",
     *     description="Retrieve detailed information about a specific booking I created",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Booking ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Booking details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Booking details retrieved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Booking not found"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            $booking = Booking::with(['listing', 'listingUnit', 'owner'])
                ->where('user_id', Auth::id()) // Only MY bookings
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Booking details retrieved successfully',
                'data' => $booking
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/user/v1/bookings/{id}/cancel",
     *     tags={"User - Bookings"},
     *     summary="Cancel my booking",
     *     description="Cancel a booking with automatic refund calculation based on cancellation policy",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Booking ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="reason", type="string", example="Change of plans")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Booking cancelled successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Booking cancelled successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Booking cannot be cancelled"),
     *     @OA\Response(response=404, description="Booking not found"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $booking = Booking::where('user_id', Auth::id())->findOrFail($id);

            if (!$booking->isCancellable()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'This booking cannot be cancelled at this time'
                ], 400);
            }

            $booking->cancel(Auth::id(), $request->input('reason'));

            // Release availability slots
            $this->releaseAvailability($booking);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully',
                'data' => [
                    'booking' => $booking->fresh(['listing', 'owner']),
                    'refund_amount' => $booking->refund_amount,
                    'cancellation_fee' => $booking->cancellation_fee
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling booking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while cancelling the booking'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user/v1/bookings/check-availability",
     *     tags={"User - Bookings"},
     *     summary="Check booking availability",
     *     description="Check if a booking can be made for the specified dates/times before creating the booking",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="listing_id",
     *         in="query",
     *         required=true,
     *         description="Listing ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="check_in_date",
     *         in="query",
     *         required=true,
     *         description="Check-in date (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="check_out_date",
     *         in="query",
     *         required=true,
     *         description="Check-out date (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="check_in_time",
     *         in="query",
     *         required=false,
     *         description="Check-in time (HH:mm)",
     *         @OA\Schema(type="string", format="time")
     *     ),
     *     @OA\Parameter(
     *         name="check_out_time",
     *         in="query",
     *         required=false,
     *         description="Check-out time (HH:mm)",
     *         @OA\Schema(type="string", format="time")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Availability check completed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Availability check completed"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="available", type="boolean", example=true),
     *                 @OA\Property(property="has_conflict", type="boolean", example=false)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'listing_id' => 'required|exists:listings,id',
                'check_in_date' => 'required|date',
                'check_out_date' => 'required|date|after:check_in_date',
                'check_in_time' => 'nullable|date_format:H:i',
                'check_out_time' => 'nullable|date_format:H:i',
            ]);

            $hasConflict = Booking::hasConflict(
                $validated['listing_id'],
                $validated['check_in_date'],
                $validated['check_out_date'],
                $validated['check_in_time'] ?? null,
                $validated['check_out_time'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Availability check completed',
                'data' => [
                    'available' => !$hasConflict,
                    'has_conflict' => $hasConflict
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error checking availability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while checking availability'
            ], 500);
        }
    }

    /**
     * Mark availability slots as booked
     */
    protected function markAvailabilityAsBooked(Booking $booking): void
    {
        $query = ListingAvailability::where('listing_id', $booking->listing_id)
            ->where('available_date', '>=', $booking->check_in_date)
            ->where('available_date', '<=', $booking->check_out_date)
            ->where('status', 'available');

        if ($booking->listing_unit_id) {
            $query->where('unit_identifier', $booking->listingUnit->unit_identifier);
        }

        $query->update([
            'status' => 'booked',
            'updated_by' => Auth::id()
        ]);
    }

    /**
     * Release availability slots
     */
    protected function releaseAvailability(Booking $booking): void
    {
        $query = ListingAvailability::where('listing_id', $booking->listing_id)
            ->where('available_date', '>=', $booking->check_in_date)
            ->where('available_date', '<=', $booking->check_out_date)
            ->where('status', 'booked');

        if ($booking->listing_unit_id) {
            $query->where('unit_identifier', $booking->listingUnit->unit_identifier);
        }

        $query->update([
            'status' => 'available',
            'updated_by' => Auth::id()
        ]);
    }
}

