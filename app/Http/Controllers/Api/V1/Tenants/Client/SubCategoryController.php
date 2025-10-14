<?php

namespace App\Http\Controllers\API\V1\Tenants\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubCategory;
use App\Models\Category;

class SubCategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/client/v1/subcategories",
     *     summary="Get all subcategories",
     *     description="Retrieve a list of all subcategories with their parent categories",
     *     operationId="getSubCategories",
     *     tags={"SubCategories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="SubCategories successfully fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="SubCategories was successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="category_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Apartment"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true),
     *                     @OA\Property(
     *                         property="category",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Residential"),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Mobile number not verified")
     * )
     */
    public function index()
    {
        $subCategories = SubCategory::with('category')->get();

        return response()->json([
            "status" => "success",
            "message" => "SubCategories was successfully fetched.",
            "data" => $subCategories,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/client/v1/subcategories",
     *     summary="Create a new subcategory",
     *     description="Create a new subcategory under a specific category",
     *     operationId="storeSubCategory",
     *     tags={"SubCategories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"category_id", "name"},
     *             @OA\Property(property="category_id", type="integer", example=1, description="Parent category ID"),
     *             @OA\Property(property="name", type="string", example="Apartment", description="SubCategory name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="SubCategory created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="SubCategory created successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Apartment"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Mobile number not verified")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
            'name' => 'required|string|max:255',
        ]);

        $subCategory = SubCategory::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'SubCategory created successfully.',
            'data' => $subCategory
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/client/v1/subcategories/{id}",
     *     summary="Get a specific subcategory",
     *     description="Retrieve a single subcategory with its parent category by ID",
     *     operationId="showSubCategory",
     *     tags={"SubCategories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="SubCategory ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="SubCategory found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="SubCategory found."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Apartment"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(
     *                     property="category",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Residential"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="SubCategory not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="SubCategory not found.")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Mobile number not verified")
     * )
     */
    public function show($id)
    {
        $subCategory = SubCategory::with('category')->find($id);

        if (!$subCategory) {
            return response()->json([
                'status' => 'error',
                'message' => 'SubCategory not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'SubCategory found.',
            'data' => $subCategory
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/client/v1/subcategories/{id}",
     *     summary="Update a subcategory",
     *     description="Update an existing subcategory by ID",
     *     operationId="updateSubCategory",
     *     tags={"SubCategories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="SubCategory ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="category_id", type="integer", example=1, description="Parent category ID"),
     *             @OA\Property(property="name", type="string", example="Luxury Apartment", description="SubCategory name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="SubCategory updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="SubCategory updated successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Luxury Apartment"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="SubCategory not found"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Mobile number not verified")
     * )
     */
    public function update(Request $request, $id)
    {
        $subCategory = SubCategory::find($id);

        if (!$subCategory) {
            return response()->json([
                'status' => 'error',
                'message' => 'SubCategory not found.'
            ], 404);
        }

        $validated = $request->validate([
            'category_id' => 'sometimes|integer|exists:categories,id',
            'name' => 'sometimes|string|max:255',
        ]);

        $subCategory->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'SubCategory updated successfully.',
            'data' => $subCategory
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/client/v1/subcategories/{id}",
     *     summary="Delete a subcategory",
     *     description="Soft delete a subcategory by ID",
     *     operationId="destroySubCategory",
     *     tags={"SubCategories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="SubCategory ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="SubCategory deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="SubCategory deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="SubCategory not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="SubCategory not found.")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Mobile number not verified")
     * )
     */
    public function destroy($id)
    {
        $subCategory = SubCategory::find($id);

        if (!$subCategory) {
            return response()->json([
                'status' => 'error',
                'message' => 'SubCategory not found.'
            ], 404);
        }

        $subCategory->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'SubCategory deleted successfully.'
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/client/v1/categories/{categoryId}/subcategories",
     *     summary="Get subcategories by category",
     *     description="Retrieve all subcategories for a specific category",
     *     operationId="getSubCategoriesByCategory",
     *     tags={"SubCategories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="categoryId",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="SubCategories found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="SubCategories found."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="category_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Apartment"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Category not found.")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Mobile number not verified")
     * )
     */
    public function getByCategory($categoryId)
    {
        $category = Category::find($categoryId);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found.'
            ], 404);
        }

        $subCategories = SubCategory::where('category_id', $categoryId)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'SubCategories found.',
            'data' => $subCategories
        ], 200);
    }
}

