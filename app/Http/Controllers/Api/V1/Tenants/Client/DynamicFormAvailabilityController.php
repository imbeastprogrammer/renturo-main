<?php

namespace App\Http\Controllers\Api\V1\Tenants\Client;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Tenants\DynamicFormAvailabilityManagement\StoreDynamicFormAvailabilityRequest;
use App\Models\DynamicFormAvailability;

/**
 * @OA\Tag(
 *     name="Client - Dynamic Form Availability",
 *     description="API endpoints for managing dynamic form availability schedules (Client App)"
 * )
 */
class DynamicFormAvailabilityController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/form-availability",
     *     summary="List all form availabilities",
     *     description="Retrieve all form availability schedules for the authenticated user",
     *     operationId="getFormAvailabilities",
     *     tags={"Client - Dynamic Form Availability"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Form availabilities retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Form availabilities retrieved successfully"),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="dynamic_form_id", type="integer", example=1),
     *                         @OA\Property(property="store_id", type="integer", example=1),
     *                         @OA\Property(property="recurring", type="object"),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index()
    {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/form-availability/{id}",
     *     summary="Get form availability by ID",
     *     description="Retrieve a specific form availability schedule",
     *     operationId="getFormAvailabilityById",
     *     tags={"Client - Dynamic Form Availability"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Form Availability ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form availability retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Form availability retrieved successfully"),
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="dynamic_form_id", type="integer", example=1),
     *                     @OA\Property(property="store_id", type="integer", example=1),
     *                     @OA\Property(property="recurring", type="object"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Form availability not found"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function show($id)
    {
    }

    /**
     * @OA\Post(
     *     path="/api/v1/form-availability",
     *     summary="Create form availability",
     *     description="Create a new form availability schedule for a dynamic form",
     *     operationId="createFormAvailability",
     *     tags={"Client - Dynamic Form Availability"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"dynamic_form_id", "store_id"},
     *             @OA\Property(property="dynamic_form_id", type="integer", example=1, description="Dynamic Form ID"),
     *             @OA\Property(property="store_id", type="integer", example=1, description="Store ID"),
     *             @OA\Property(
     *                 property="recurring",
     *                 type="object",
     *                 description="Recurring availability schedule by day of week. Each day contains array of time slots.",
     *                 @OA\AdditionalProperties(
     *                     type="array",
     *                     @OA\Items(type="string", example="09:00-12:00")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Form availability created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Dynamic Form Availability Created Successfully"),
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="dynamic_form_id", type="integer", example=1),
     *                     @OA\Property(property="store_id", type="integer", example=1),
     *                     @OA\Property(property="recurring", type="string", description="JSON encoded recurring schedule"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function store(StoreDynamicFormAvailabilityRequest $request)
    {
        $validatedData = $request->validated();

        if (isset($validatedData['recurring'])) {
            $validatedData['recurring'] = json_encode($validatedData['recurring'], true);
        }

        // Create a new DynamicFormAvailability record using the validated data
        $availability = DynamicFormAvailability::create($validatedData);

        // Return a JSON response with the newly created availability and a 201 status code
        return $this->sendSuccessResponse($availability, 'Dynamic Form Availability Created Successfully', 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/form-availability/{id}",
     *     summary="Update form availability",
     *     description="Update an existing form availability schedule",
     *     operationId="updateFormAvailability",
     *     tags={"Client - Dynamic Form Availability"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Form Availability ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="dynamic_form_id", type="integer", example=1),
     *             @OA\Property(property="store_id", type="integer", example=1),
     *             @OA\Property(
     *                 property="recurring",
     *                 type="object",
     *                 description="Updated recurring schedule",
     *                 @OA\AdditionalProperties(
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form availability updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Form availability updated successfully"),
     *                 @OA\Property(property="data", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Form availability not found"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/form-availability/{id}",
     *     summary="Delete form availability",
     *     description="Delete a form availability schedule",
     *     operationId="deleteFormAvailability",
     *     tags={"Client - Dynamic Form Availability"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Form Availability ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form availability deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Form availability deleted successfully"),
     *                 @OA\Property(property="data", type="array", @OA\Items())
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Form availability not found"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function destroy($id)
    {
    }
}
