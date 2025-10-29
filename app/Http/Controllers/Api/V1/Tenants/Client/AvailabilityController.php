<?php

namespace App\Http\Controllers\Api\V1\Tenants\Client;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\ListingAvailability;
use App\Models\ListingUnit;
use App\Models\AvailabilityTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

/**
 * @OA\Tag(
 *     name="Client - Availability",
 *     description="Universal availability management for all property types (sports courts, hotels, cars, venues, etc.)"
 * )
 */
class AvailabilityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/availability/listing/{listingId}",
     *     tags={"Client - Availability"},
     *     summary="Get availability for a specific listing",
     *     description="Retrieve availability slots for any property type with flexible filtering",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="listingId",
     *         in="path",
     *         required=true,
     *         description="ID of the listing",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         required=false,
     *         description="Start date for availability search (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         required=false,
     *         description="End date for availability search (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="unit_identifier",
     *         in="query",
     *         required=false,
     *         description="Filter by specific unit (for multi-unit properties)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filter by availability status",
     *         @OA\Schema(type="string", enum={"available", "booked", "blocked", "maintenance"})
     *     ),
     *     @OA\Parameter(
     *         name="duration_type",
     *         in="query",
     *         required=false,
     *         description="Filter by duration type",
     *         @OA\Schema(type="string", enum={"hourly", "daily", "weekly", "monthly"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Availability retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Availability retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="listing_id", type="integer", example=1),
     *                 @OA\Property(property="inventory_type", type="string", example="single"),
     *                 @OA\Property(property="total_units", type="integer", example=1),
     *                 @OA\Property(
     *                     property="availability",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="available_date", type="string", format="date", example="2025-11-01"),
     *                         @OA\Property(property="start_time", type="string", format="time", example="09:00:00"),
     *                         @OA\Property(property="end_time", type="string", format="time", example="10:00:00"),
     *                         @OA\Property(property="unit_identifier", type="string", example="court-1"),
     *                         @OA\Property(property="status", type="string", example="available"),
     *                         @OA\Property(property="duration_type", type="string", example="hourly"),
     *                         @OA\Property(property="effective_price", type="number", format="float", example=50.00),
     *                         @OA\Property(property="available_units", type="integer", example=1),
     *                         @OA\Property(property="category_format", type="object")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Listing not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function getListingAvailability(Request $request, $listingId): JsonResponse
    {
        try {
            $listing = Listing::with(['category', 'subCategory', 'units'])->findOrFail($listingId);
            
            $query = $listing->availability();
            
            // Apply filters
            if ($request->has('start_date')) {
                $query->where('available_date', '>=', $request->start_date);
            }
            
            if ($request->has('end_date')) {
                $query->where('available_date', '<=', $request->end_date);
            }
            
            if ($request->has('unit_identifier')) {
                $query->forUnit($request->unit_identifier);
            }
            
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->has('duration_type')) {
                $query->where('duration_type', $request->duration_type);
            }
            
            $availability = $query->ordered()->get();
            
            // Format availability with category-specific data
            $formattedAvailability = $availability->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'available_date' => $slot->available_date,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'unit_identifier' => $slot->unit_identifier,
                    'status' => $slot->status,
                    'duration_type' => $slot->duration_type,
                    'effective_price' => $slot->effective_price,
                    'available_units' => $slot->available_units,
                    'category_format' => $slot->formatForCategory()
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Availability retrieved successfully',
                'data' => [
                    'listing_id' => $listing->id,
                    'inventory_type' => $listing->inventory_type,
                    'total_units' => $listing->total_units,
                    'availability' => $formattedAvailability
                ]
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Listing not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error retrieving availability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving availability'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/v1/availability",
     *     tags={"Client - Availability"},
     *     summary="Create availability slots",
     *     description="Create new availability slots for any property type",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"listing_id", "available_date", "start_time", "end_time", "duration_type"},
     *             @OA\Property(property="listing_id", type="integer", example=1),
     *             @OA\Property(property="available_date", type="string", format="date", example="2025-11-01"),
     *             @OA\Property(property="start_time", type="string", format="time", example="09:00:00"),
     *             @OA\Property(property="end_time", type="string", format="time", example="17:00:00"),
     *             @OA\Property(property="unit_identifier", type="string", example="court-1"),
     *             @OA\Property(property="duration_type", type="string", enum={"hourly", "daily", "weekly", "monthly"}, example="hourly"),
     *             @OA\Property(property="slot_duration_minutes", type="integer", example=60),
     *             @OA\Property(property="available_units", type="integer", example=1),
     *             @OA\Property(property="peak_hour_price", type="number", format="float", example=75.00),
     *             @OA\Property(property="weekend_price", type="number", format="float", example=65.00),
     *             @OA\Property(property="holiday_price", type="number", format="float", example=85.00),
     *             @OA\Property(property="min_duration_hours", type="integer", example=1),
     *             @OA\Property(property="max_duration_hours", type="integer", example=8),
     *             @OA\Property(property="booking_rules", type="object"),
     *             @OA\Property(property="metadata", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Availability created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Availability slots created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="slots_created", type="integer", example=8),
     *                 @OA\Property(
     *                     property="availability",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="available_date", type="string", format="date", example="2025-11-01"),
     *                         @OA\Property(property="start_time", type="string", format="time", example="09:00:00"),
     *                         @OA\Property(property="end_time", type="string", format="time", example="10:00:00"),
     *                         @OA\Property(property="status", type="string", example="available")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'listing_id' => 'required|exists:listings,id',
                'available_date' => 'required|date|after_or_equal:today',
                'start_time' => 'required|date_format:H:i:s',
                'end_time' => 'required|date_format:H:i:s|after:start_time',
                'unit_identifier' => 'nullable|string|max:50',
                'duration_type' => 'required|in:hourly,daily,weekly,monthly',
                'slot_duration_minutes' => 'nullable|integer|min:15|max:1440',
                'available_units' => 'nullable|integer|min:1',
                'peak_hour_price' => 'nullable|numeric|min:0',
                'weekend_price' => 'nullable|numeric|min:0',
                'holiday_price' => 'nullable|numeric|min:0',
                'min_duration_hours' => 'nullable|integer|min:1',
                'max_duration_hours' => 'nullable|integer|min:1',
                'booking_rules' => 'nullable|array',
                'metadata' => 'nullable|array'
            ]);

            $listing = Listing::findOrFail($validated['listing_id']);
            
            // Check if user owns this listing (through store)
            if ($listing->store->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to manage this listing'
                ], 403);
            }

            $validated['created_by'] = Auth::id();
            $validated['status'] = 'available';

            // Generate time slots if hourly duration
            if ($validated['duration_type'] === 'hourly') {
                $slotDuration = $validated['slot_duration_minutes'] ?? 60;
                $slots = ListingAvailability::generateTimeSlots(
                    $validated['start_time'],
                    $validated['end_time'],
                    $slotDuration
                );

                $createdSlots = [];
                foreach ($slots as $slot) {
                    $slotData = array_merge($validated, [
                        'start_time' => $slot['start_time'],
                        'end_time' => $slot['end_time']
                    ]);
                    
                    $createdSlots[] = ListingAvailability::create($slotData);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Availability slots created successfully',
                    'data' => [
                        'slots_created' => count($createdSlots),
                        'availability' => $createdSlots
                    ]
                ], 201);
            } else {
                // Create single slot for daily/weekly/monthly
                $availability = ListingAvailability::create($validated);

                return response()->json([
                    'success' => true,
                    'message' => 'Availability created successfully',
                    'data' => [
                        'slots_created' => 1,
                        'availability' => [$availability]
                    ]
                ], 201);
            }

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Listing not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error creating availability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating availability'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/v1/availability/{id}",
     *     tags={"Client - Availability"},
     *     summary="Get specific availability slot",
     *     description="Retrieve details of a specific availability slot",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the availability slot",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Availability slot retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Availability retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="listing_id", type="integer", example=1),
     *                 @OA\Property(property="available_date", type="string", format="date", example="2025-11-01"),
     *                 @OA\Property(property="start_time", type="string", format="time", example="09:00:00"),
     *                 @OA\Property(property="end_time", type="string", format="time", example="10:00:00"),
     *                 @OA\Property(property="unit_identifier", type="string", example="court-1"),
     *                 @OA\Property(property="status", type="string", example="available"),
     *                 @OA\Property(property="duration_type", type="string", example="hourly"),
     *                 @OA\Property(property="effective_price", type="number", format="float", example=50.00),
     *                 @OA\Property(property="category_format", type="object"),
     *                 @OA\Property(
     *                     property="listing",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Premium Basketball Court"),
     *                     @OA\Property(property="inventory_type", type="string", example="single")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Availability slot not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            $availability = ListingAvailability::with(['listing:id,title,inventory_type,total_units'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Availability retrieved successfully',
                'data' => [
                    'id' => $availability->id,
                    'listing_id' => $availability->listing_id,
                    'available_date' => $availability->available_date,
                    'start_time' => $availability->start_time,
                    'end_time' => $availability->end_time,
                    'unit_identifier' => $availability->unit_identifier,
                    'status' => $availability->status,
                    'duration_type' => $availability->duration_type,
                    'effective_price' => $availability->effective_price,
                    'available_units' => $availability->available_units,
                    'category_format' => $availability->formatForCategory(),
                    'listing' => $availability->listing
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Availability slot not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error retrieving availability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving availability'
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/v1/availability/{id}",
     *     tags={"Client - Availability"},
     *     summary="Update availability slot",
     *     description="Update an existing availability slot",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the availability slot",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="available_date", type="string", format="date", example="2025-11-01"),
     *             @OA\Property(property="start_time", type="string", format="time", example="09:00:00"),
     *             @OA\Property(property="end_time", type="string", format="time", example="10:00:00"),
     *             @OA\Property(property="status", type="string", enum={"available", "booked", "blocked", "maintenance"}, example="available"),
     *             @OA\Property(property="peak_hour_price", type="number", format="float", example=75.00),
     *             @OA\Property(property="weekend_price", type="number", format="float", example=65.00),
     *             @OA\Property(property="holiday_price", type="number", format="float", example=85.00),
     *             @OA\Property(property="available_units", type="integer", example=1),
     *             @OA\Property(property="booking_rules", type="object"),
     *             @OA\Property(property="metadata", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Availability updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Availability updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="available"),
     *                 @OA\Property(property="effective_price", type="number", format="float", example=75.00)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Availability slot not found"),
     *     @OA\Response(response=403, description="Unauthorized to update this availability"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $availability = ListingAvailability::with('listing.store')->findOrFail($id);
            
            // Check if user owns this listing
            if ($availability->listing->store->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this availability'
                ], 403);
            }

            $validated = $request->validate([
                'available_date' => 'sometimes|date|after_or_equal:today',
                'start_time' => 'sometimes|date_format:H:i:s',
                'end_time' => 'sometimes|date_format:H:i:s|after:start_time',
                'status' => 'sometimes|in:available,booked,blocked,maintenance',
                'peak_hour_price' => 'nullable|numeric|min:0',
                'weekend_price' => 'nullable|numeric|min:0',
                'holiday_price' => 'nullable|numeric|min:0',
                'available_units' => 'nullable|integer|min:1',
                'booking_rules' => 'nullable|array',
                'metadata' => 'nullable|array'
            ]);

            $validated['updated_by'] = Auth::id();
            
            $availability->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Availability updated successfully',
                'data' => [
                    'id' => $availability->id,
                    'status' => $availability->status,
                    'effective_price' => $availability->effective_price,
                    'available_date' => $availability->available_date,
                    'start_time' => $availability->start_time,
                    'end_time' => $availability->end_time
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Availability slot not found'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating availability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating availability'
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/v1/availability/{id}",
     *     tags={"Client - Availability"},
     *     summary="Delete availability slot",
     *     description="Delete an availability slot (soft delete)",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the availability slot",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Availability deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Availability deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Availability slot not found"),
     *     @OA\Response(response=403, description="Unauthorized to delete this availability")
     * )
     */
    public function destroy($id): JsonResponse
    {
        try {
            $availability = ListingAvailability::with('listing.store')->findOrFail($id);
            
            // Check if user owns this listing
            if ($availability->listing->store->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this availability'
                ], 403);
            }

            // Check if slot is booked
            if ($availability->status === 'booked') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete booked availability slot'
                ], 422);
            }

            $availability->delete();

            return response()->json([
                'success' => true,
                'message' => 'Availability deleted successfully'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Availability slot not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting availability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting availability'
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/v1/availability/bulk",
     *     tags={"Client - Availability"},
     *     summary="Create bulk availability",
     *     description="Create availability for multiple dates using templates or date ranges",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"listing_id", "start_date", "end_date"},
     *             @OA\Property(property="listing_id", type="integer", example=1),
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-11-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-11-30"),
     *             @OA\Property(property="template_id", type="integer", example=1, description="Use existing template"),
     *             @OA\Property(property="days_of_week", type="array", @OA\Items(type="integer", minimum=0, maximum=6), example={1,2,3,4,5}, description="0=Sunday, 6=Saturday"),
     *             @OA\Property(property="start_time", type="string", format="time", example="09:00:00"),
     *             @OA\Property(property="end_time", type="string", format="time", example="17:00:00"),
     *             @OA\Property(property="duration_type", type="string", enum={"hourly", "daily"}, example="hourly"),
     *             @OA\Property(property="slot_duration_minutes", type="integer", example=60),
     *             @OA\Property(property="unit_identifier", type="string", example="court-1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Bulk availability created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Bulk availability created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="dates_processed", type="integer", example=22),
     *                 @OA\Property(property="slots_created", type="integer", example=176),
     *                 @OA\Property(property="template_used", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=404, description="Listing or template not found")
     * )
     */
    public function bulkCreate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'listing_id' => 'required|exists:listings,id',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after:start_date',
                'template_id' => 'nullable|exists:availability_templates,id',
                'days_of_week' => 'nullable|array',
                'days_of_week.*' => 'integer|min:0|max:6',
                'start_time' => 'required_without:template_id|date_format:H:i:s',
                'end_time' => 'required_without:template_id|date_format:H:i:s|after:start_time',
                'duration_type' => 'required_without:template_id|in:hourly,daily',
                'slot_duration_minutes' => 'nullable|integer|min:15|max:1440',
                'unit_identifier' => 'nullable|string|max:50'
            ]);

            $listing = Listing::findOrFail($validated['listing_id']);
            
            // Check ownership
            if ($listing->store->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to manage this listing'
                ], 403);
            }

            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);
            $daysOfWeek = $validated['days_of_week'] ?? [0,1,2,3,4,5,6]; // All days if not specified
            $slotsCreated = 0;
            $datesProcessed = 0;

            // Use template if provided
            if (isset($validated['template_id'])) {
                $template = AvailabilityTemplate::findOrFail($validated['template_id']);
                $slotsCreated = $template->applyToDateRange($startDate, $endDate, $daysOfWeek);
                $datesProcessed = $startDate->diffInDays($endDate) + 1;
                $templateUsed = true;
            } else {
                // Create manually
                $current = $startDate->copy();
                while ($current->lte($endDate)) {
                    if (in_array($current->dayOfWeek, $daysOfWeek)) {
                        $datesProcessed++;
                        
                        if ($validated['duration_type'] === 'hourly') {
                            $slotDuration = $validated['slot_duration_minutes'] ?? 60;
                            $slots = ListingAvailability::generateTimeSlots(
                                $validated['start_time'],
                                $validated['end_time'],
                                $slotDuration
                            );

                            foreach ($slots as $slot) {
                                ListingAvailability::create([
                                    'listing_id' => $validated['listing_id'],
                                    'available_date' => $current->format('Y-m-d'),
                                    'start_time' => $slot['start_time'],
                                    'end_time' => $slot['end_time'],
                                    'unit_identifier' => $validated['unit_identifier'] ?? null,
                                    'duration_type' => $validated['duration_type'],
                                    'slot_duration_minutes' => $slotDuration,
                                    'status' => 'available',
                                    'created_by' => Auth::id()
                                ]);
                                $slotsCreated++;
                            }
                        } else {
                            // Daily slot
                            ListingAvailability::create([
                                'listing_id' => $validated['listing_id'],
                                'available_date' => $current->format('Y-m-d'),
                                'start_time' => $validated['start_time'],
                                'end_time' => $validated['end_time'],
                                'unit_identifier' => $validated['unit_identifier'] ?? null,
                                'duration_type' => $validated['duration_type'],
                                'status' => 'available',
                                'created_by' => Auth::id()
                            ]);
                            $slotsCreated++;
                        }
                    }
                    $current->addDay();
                }
                $templateUsed = false;
            }

            return response()->json([
                'success' => true,
                'message' => 'Bulk availability created successfully',
                'data' => [
                    'dates_processed' => $datesProcessed,
                    'slots_created' => $slotsCreated,
                    'template_used' => $templateUsed
                ]
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Listing or template not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error creating bulk availability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating bulk availability'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/v1/availability/check",
     *     tags={"Client - Availability"},
     *     summary="Check availability for booking",
     *     description="Check if a specific time slot is available for booking",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="listing_id",
     *         in="query",
     *         required=true,
     *         description="ID of the listing",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         required=true,
     *         description="Date to check (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="start_time",
     *         in="query",
     *         required=true,
     *         description="Start time (HH:MM:SS)",
     *         @OA\Schema(type="string", format="time")
     *     ),
     *     @OA\Parameter(
     *         name="end_time",
     *         in="query",
     *         required=true,
     *         description="End time (HH:MM:SS)",
     *         @OA\Schema(type="string", format="time")
     *     ),
     *     @OA\Parameter(
     *         name="unit_identifier",
     *         in="query",
     *         required=false,
     *         description="Specific unit to check",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Availability check completed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Availability check completed"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="available", type="boolean", example=true),
     *                 @OA\Property(property="total_price", type="number", format="float", example=150.00),
     *                 @OA\Property(property="available_units", type="integer", example=2),
     *                 @OA\Property(
     *                     property="slots",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="start_time", type="string", format="time", example="09:00:00"),
     *                         @OA\Property(property="end_time", type="string", format="time", example="10:00:00"),
     *                         @OA\Property(property="price", type="number", format="float", example=50.00)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=404, description="Listing not found")
     * )
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'listing_id' => 'required|exists:listings,id',
                'date' => 'required|date|after_or_equal:today',
                'start_time' => 'required|date_format:H:i:s',
                'end_time' => 'required|date_format:H:i:s|after:start_time',
                'unit_identifier' => 'nullable|string|max:50'
            ]);

            $listing = Listing::findOrFail($validated['listing_id']);
            
            $available = $listing->hasAvailabilityAt(
                $validated['date'],
                $validated['start_time'],
                $validated['end_time'],
                $validated['unit_identifier'] ?? null
            );

            if ($available) {
                // Get available slots and calculate pricing
                $query = $listing->availability()
                    ->where('available_date', $validated['date'])
                    ->where('status', 'available')
                    ->where('start_time', '>=', $validated['start_time'])
                    ->where('end_time', '<=', $validated['end_time']);

                if (isset($validated['unit_identifier'])) {
                    $query->forUnit($validated['unit_identifier']);
                }

                $slots = $query->ordered()->get();
                $totalPrice = $slots->sum('effective_price');
                $availableUnits = $slots->sum('available_units');

                return response()->json([
                    'success' => true,
                    'message' => 'Availability check completed',
                    'data' => [
                        'available' => true,
                        'total_price' => $totalPrice,
                        'available_units' => $availableUnits,
                        'slots' => $slots->map(function ($slot) {
                            return [
                                'id' => $slot->id,
                                'start_time' => $slot->start_time,
                                'end_time' => $slot->end_time,
                                'price' => $slot->effective_price,
                                'unit_identifier' => $slot->unit_identifier
                            ];
                        })
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Availability check completed',
                    'data' => [
                        'available' => false,
                        'total_price' => 0,
                        'available_units' => 0,
                        'slots' => []
                    ]
                ]);
            }

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Listing not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error checking availability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while checking availability'
            ], 500);
        }
    }
}
