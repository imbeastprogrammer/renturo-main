<?php

namespace App\Http\Controllers\Api\V1\Tenants\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenants\StoreManagement\CreateStoreRequest;
use App\Http\Requests\Tenants\StoreManagement\UpdateStoreRequest;
use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        echo "No query implemented yet";
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        echo "No query implemented yet";
    }

    /**
     * @OA\Post(
     *     path="/api/client/v1/stores",
     *     summary="Client - Create a new store",
     *     description="Create a new store/venue for the authenticated user. This is typically used when a property owner registers their venue on the platform.",
     *     operationId="createStore",
     *     tags={"Client - Stores"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "url", "category_id"},
     *             @OA\Property(property="name", type="string", example="Elite Sports Complex"),
     *             @OA\Property(property="url", type="string", example="elite-sports-complex"),
     *             @OA\Property(property="category_id", type="integer", example=9),
     *             @OA\Property(property="sub_category_id", type="integer", example=56),
     *             @OA\Property(property="logo", type="string", nullable=true, example="https://example.com/logo.png"),
     *             @OA\Property(property="address", type="string", nullable=true, example="123 Sports Avenue"),
     *             @OA\Property(property="city", type="string", nullable=true, example="Manila"),
     *             @OA\Property(property="state", type="string", nullable=true, example="Metro Manila"),
     *             @OA\Property(property="zip_code", type="string", nullable=true, example="1000"),
     *             @OA\Property(property="latitude", type="number", format="float", nullable=true, example=14.5995),
     *             @OA\Property(property="longitude", type="number", format="float", nullable=true, example=120.9842),
     *             @OA\Property(property="about", type="string", nullable=true, example="Premium sports facility")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Store created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Store has been created."),
     *                 @OA\Property(property="data", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateStoreRequest $request)
    {
        Auth::user()->store()->create($request->validated());

        return response()->json([
            'message' => 'success',
            'body' => [
              'message' => 'Store has been created.',
              'data' => $request->all()
            ]
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/client/v1/stores/{storeId}",
     *     summary="Client - Get store details",
     *     description="Retrieve details of a specific store owned by the authenticated user, including category and subcategory information.",
     *     operationId="getStore",
     *     tags={"Client - Stores"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="storeId",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Store retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Store has been fetched."),
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Elite Sports Complex"),
     *                     @OA\Property(property="url", type="string", example="elite-sports-complex"),
     *                     @OA\Property(property="category_id", type="integer", example=9),
     *                     @OA\Property(property="sub_category_id", type="integer", example=56),
     *                     @OA\Property(property="category", type="object"),
     *                     @OA\Property(property="subCategory", type="object")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Store not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     *
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($storeId)
    {
        // Retrieve the authenticated user's ID
        $userId = Auth::user()->id;

        // Retrieve all store with the authenticated user
        $store = Store::with(['category', 'subCategory'])
            ->where('id', $storeId) // store id
            ->where('user_id', $userId)->first();

        if (!$store) { 
            return response()->json([
              'message' => 'failed',
              'errors' => 'Store not found.'
            ], 404);
        }

        return response()->json([
            'message' => 'success',
            'body' => [
                'message' => 'Store has been fetched.',
                'data' => $store
            ]
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/api/client/v1/stores/{id}",
     *     summary="Client - Update store",
     *     description="Update an existing store owned by the authenticated user.",
     *     operationId="updateStore",
     *     tags={"Client - Stores"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Elite Sports Complex Updated"),
     *             @OA\Property(property="url", type="string", example="elite-sports-complex-updated"),
     *             @OA\Property(property="category_id", type="integer", example=9),
     *             @OA\Property(property="sub_category_id", type="integer", example=56),
     *             @OA\Property(property="logo", type="string", nullable=true),
     *             @OA\Property(property="address", type="string", nullable=true),
     *             @OA\Property(property="city", type="string", nullable=true),
     *             @OA\Property(property="state", type="string", nullable=true),
     *             @OA\Property(property="zip_code", type="string", nullable=true),
     *             @OA\Property(property="latitude", type="number", format="float", nullable=true),
     *             @OA\Property(property="longitude", type="number", format="float", nullable=true),
     *             @OA\Property(property="about", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Store updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Store has been updated."),
     *                 @OA\Property(property="data", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Store not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     *
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStoreRequest $request, $id)
    {
        $store = Auth::user()->store()->find($id);

        if (!$store) { 
            return response()->json([
                'message' => 'failed',
                'errors' => 'Store not found.'
            ], 404);
        }

        $store->update($request->all());
        
        return response()->json([
            'message' => 'success',
            'body' => [
                'message' => 'Store has been updated.',
                'data' => $request->all()
            ]
        ], 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/client/v1/stores/{id}",
     *     summary="Client - Delete store",
     *     description="Delete a store owned by the authenticated user. This is a soft delete.",
     *     operationId="deleteStore",
     *     tags={"Client - Stores"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Store deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Store has been deleted."),
     *                 @OA\Property(property="data", type="array", @OA\Items())
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Store not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $store = Auth::user()->store()->find($id);

        if (!$store) { 
            return response()->json([
               'message' => 'failed',
               'errors' => 'Store not found.'
            ], 404);
        }

        $store->delete();

        return response()->json([
           'message' =>'success',
            'body' => [
               'message' => 'Store has been deleted.',
               'data' => []
            ]
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/client/v1/users/{userId}/stores",
     *     summary="Client - Get all user stores",
     *     description="Retrieve all stores/venues owned by the authenticated user, including category, subcategory, and available dynamic forms.",
     *     operationId="getUserStores",
     *     tags={"Client - Stores"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="User ID (must match authenticated user)",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User stores retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="User store(s) was fetch successfully."),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Elite Sports Complex"),
     *                         @OA\Property(property="url", type="string", example="elite-sports-complex"),
     *                         @OA\Property(property="category_id", type="integer", example=9),
     *                         @OA\Property(property="sub_category_id", type="integer", example=56),
     *                         @OA\Property(property="category", type="object"),
     *                         @OA\Property(property="subCategory", type="object",
     *                             @OA\Property(property="dynamicForms", type="array", @OA\Items())
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Store not found or user ID mismatch"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function getUserStores($userId) {

        // Retrieve the authenticated user's ID
        $authUserId = Auth::user()->id;

        // Compare the UserID on URL against the current authenticated userId
        if ($authUserId != $userId) {
            return response()->json([
                'message' => 'failed',
                'errors' => 'Resource not found.'
            ], 404); 
        }

        // Retrieve all stores associated with the authenticated user
        $stores = Store::with(['category', 'subCategory.dynamicForms'])
            ->where('user_id', $authUserId)->get();

        // Check if any stores records were found
        if ($stores->isEmpty()) {
            return response()->json([
                'message' => 'failed',
                'errors' => 'Store not found.'
            ], 404); 
        }

        return response()->json([
            'message' => 'success',
            'body' => [
                'message' => 'User store(s) was fetch successfully.',
                'data' => $stores,
            ]
        ]);
    }
}
