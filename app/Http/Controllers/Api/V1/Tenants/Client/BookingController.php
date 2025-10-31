<?php

namespace App\Http\Controllers\Api\V1\Tenants\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ListingAvailability;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * @OA\Tag(
 *     name="Client - Bookings",
 *     description="Booking management for property owners/clients"
 * )
 */
class BookingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/client/v1/bookings",
     *     tags={"Client - Bookings"},
     *     summary="Get property bookings",
     *     description="Retrieve all bookings for properties owned by the authenticated client/owner",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filter by status (pending, confirmed, paid, checked_in, in_progress, completed, cancelled, rejected)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=false,
     *         description="Filter by type (upcoming, current, past)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="listing_id",
     *         in="query",
     *         required=false,
     *         description="Filter by specific property ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bookings retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Property bookings retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="statistics", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Booking::with(['listing', 'listingUnit', 'user'])
                ->forOwner(Auth::id()); // Only bookings for MY properties

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('listing_id')) {
                // Verify owner owns this listing
                $query->whereHas('listing', function($q) use ($request) {
                    $q->where('id', $request->listing_id)
                      ->where('user_id', Auth::id());
                });
            }

            if ($request->has('type')) {
                match($request->type) {
                    'upcoming' => $query->upcoming(),
                    'current' => $query->current(),
                    'past' => $query->past(),
                    default => null
                };
            }

            // Get bookings
            $bookings = $query->orderBy('check_in_date', 'desc')->get();

            // Calculate quick statistics
            $stats = [
                'total' => $bookings->count(),
                'pending' => $bookings->where('status', 'pending')->count(),
                'confirmed' => $bookings->whereIn('status', ['confirmed', 'paid'])->count(),
                'active' => $bookings->whereIn('status', ['checked_in', 'in_progress'])->count(),
                'completed' => $bookings->where('status', 'completed')->count(),
                'cancelled' => $bookings->where('status', 'cancelled')->count(),
                'total_revenue' => $bookings->whereIn('status', ['paid', 'completed'])->sum('total_price'),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Property bookings retrieved successfully',
                'data' => $bookings,
                'statistics' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving property bookings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving property bookings'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/client/v1/bookings/{id}",
     *     tags={"Client - Bookings"},
     *     summary="Get booking details",
     *     description="Get detailed information about a specific booking for MY property",
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
     *     @OA\Response(response=404, description="Booking not found or not owned by you"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            $booking = Booking::with(['listing', 'listingUnit', 'user'])
                ->where('owner_id', Auth::id()) // Only MY property bookings
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Booking details retrieved successfully',
                'data' => $booking
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or not owned by you'
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/client/v1/bookings/{id}/confirm",
     *     tags={"Client - Bookings"},
     *     summary="Confirm a pending booking",
     *     description="Accept and confirm a pending booking request for YOUR property",
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
     *         description="Booking confirmed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Booking confirmed successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Cannot confirm this booking"),
     *     @OA\Response(response=409, description="Booking conflict detected"),
     *     @OA\Response(response=404, description="Booking not found"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function confirm($id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $booking = Booking::where('owner_id', Auth::id())->findOrFail($id);

            if ($booking->status !== 'pending') {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending bookings can be confirmed'
                ], 400);
            }

            // Double-check for conflicts
            if (Booking::hasConflict(
                $booking->listing_id,
                $booking->check_in_date,
                $booking->check_out_date,
                $booking->check_in_time,
                $booking->check_out_time,
                $booking->id
            )) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Booking conflict detected. Another booking may have been confirmed for this time.'
                ], 409);
            }

            $booking->confirm();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking confirmed successfully',
                'data' => [
                    'booking' => $booking->fresh(['listing', 'user']),
                    'confirmation_code' => $booking->confirmation_code
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error confirming booking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while confirming the booking'
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/client/v1/bookings/{id}/reject",
     *     tags={"Client - Bookings"},
     *     summary="Reject a pending booking",
     *     description="Decline a booking request with a reason",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Booking ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"reason"},
     *             @OA\Property(property="reason", type="string", example="Property under maintenance")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Booking rejected successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Booking rejected successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Cannot reject this booking"),
     *     @OA\Response(response=404, description="Booking not found"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function reject(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'reason' => 'required|string|max:500'
            ]);

            $booking = Booking::where('owner_id', Auth::id())->findOrFail($id);

            if ($booking->status !== 'pending') {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending bookings can be rejected'
                ], 400);
            }

            $booking->reject($request->input('reason'));

            // Release availability slots
            $this->releaseAvailability($booking);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking rejected successfully',
                'data' => $booking->fresh(['listing', 'user'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting booking: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while rejecting the booking'
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/client/v1/bookings/{id}/cancel",
     *     tags={"Client - Bookings"},
     *     summary="Cancel a booking (owner-initiated)",
     *     description="Owner cancels a booking with a reason (full refund to renter)",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Booking ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"reason"},
     *             @OA\Property(property="reason", type="string", example="Emergency maintenance required")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Booking cancelled successfully. Renter will receive full refund.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Booking cancelled successfully. Renter will receive full refund."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Cannot cancel this booking"),
     *     @OA\Response(response=404, description="Booking not found"),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'reason' => 'required|string|max:500'
            ]);

            $booking = Booking::where('owner_id', Auth::id())->findOrFail($id);

            if (!in_array($booking->status, ['pending', 'confirmed', 'paid'])) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'This booking cannot be cancelled'
                ], 400);
            }

            // Owner cancellation = full refund to renter (no cancellation fee)
            $booking->status = 'cancelled';
            $booking->cancelled_at = now();
            $booking->cancelled_by = Auth::id();
            $booking->cancellation_reason = $request->input('reason');
            $booking->cancellation_fee = 0; // No fee when owner cancels
            $booking->refund_amount = $booking->total_price; // Full refund to renter
            $booking->save();

            // Release availability slots
            $this->releaseAvailability($booking);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully. Renter will receive full refund.',
                'data' => [
                    'booking' => $booking->fresh(['listing', 'user']),
                    'refund_amount' => $booking->refund_amount
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
     *     path="/api/client/v1/bookings/statistics",
     *     tags={"Client - Bookings"},
     *     summary="Get booking statistics",
     *     description="Get comprehensive booking statistics for YOUR properties",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="listing_id",
     *         in="query",
     *         required=false,
     *         description="Filter statistics by specific property",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="from_date",
     *         in="query",
     *         required=false,
     *         description="Start date for statistics (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="to_date",
     *         in="query",
     *         required=false,
     *         description="End date for statistics (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Statistics retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Statistics retrieved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $query = Booking::where('owner_id', Auth::id());

            if ($request->has('listing_id')) {
                $query->where('listing_id', $request->listing_id);
            }

            if ($request->has('from_date')) {
                $query->where('check_in_date', '>=', $request->from_date);
            }

            if ($request->has('to_date')) {
                $query->where('check_out_date', '<=', $request->to_date);
            }

            $bookings = $query->get();

            $statistics = [
                'total_bookings' => $bookings->count(),
                'pending_bookings' => $bookings->where('status', 'pending')->count(),
                'confirmed_bookings' => $bookings->whereIn('status', ['confirmed', 'paid'])->count(),
                'active_bookings' => $bookings->whereIn('status', ['checked_in', 'in_progress'])->count(),
                'completed_bookings' => $bookings->where('status', 'completed')->count(),
                'cancelled_bookings' => $bookings->where('status', 'cancelled')->count(),
                'rejected_bookings' => $bookings->where('status', 'rejected')->count(),
                
                'total_revenue' => round($bookings->whereIn('status', ['paid', 'completed'])->sum('total_price'), 2),
                'pending_revenue' => round($bookings->whereIn('status', ['confirmed', 'paid'])->sum('total_price'), 2),
                'average_booking_value' => round($bookings->whereIn('status', ['paid', 'completed'])->avg('total_price'), 2),
                
                'cancellation_rate' => $bookings->count() > 0 
                    ? round(($bookings->where('status', 'cancelled')->count() / $bookings->count()) * 100, 2) 
                    : 0,
                
                'by_month' => $bookings->groupBy(function($booking) {
                    return Carbon::parse($booking->check_in_date)->format('Y-m');
                })->map(function($group) {
                    return [
                        'count' => $group->count(),
                        'revenue' => round($group->whereIn('status', ['paid', 'completed'])->sum('total_price'), 2)
                    ];
                }),
                
                'by_status' => [
                    'pending' => $bookings->where('status', 'pending')->count(),
                    'confirmed' => $bookings->where('status', 'confirmed')->count(),
                    'paid' => $bookings->where('status', 'paid')->count(),
                    'checked_in' => $bookings->where('status', 'checked_in')->count(),
                    'in_progress' => $bookings->where('status', 'in_progress')->count(),
                    'completed' => $bookings->where('status', 'completed')->count(),
                    'cancelled' => $bookings->where('status', 'cancelled')->count(),
                    'rejected' => $bookings->where('status', 'rejected')->count(),
                ],
            ];

            return response()->json([
                'success' => true,
                'message' => 'Statistics retrieved successfully',
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving booking statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving statistics'
            ], 500);
        }
    }

    /**
     * Release availability slots when booking is cancelled/rejected
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
