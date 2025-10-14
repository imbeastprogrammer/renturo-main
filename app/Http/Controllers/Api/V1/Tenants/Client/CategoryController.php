<?php

namespace App\Http\Controllers\API\V1\Tenants\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/client/v1/categories",
     *     summary="Get all categories with subcategories",
     *     description="Retrieve a list of all categories along with their subcategories. This endpoint returns all active categories that are not soft-deleted.",
     *     operationId="getCategories",
     *     tags={"Categories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Categories successfully fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Categories was successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Residential"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T10:30:00.000000Z"),
     *                     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null),
     *                     @OA\Property(
     *                         property="sub_categories",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="category_id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Apartment"),
     *                             @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00.000000Z"),
     *                             @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T10:30:00.000000Z"),
     *                             @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Mobile number not verified",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="The mobile number has not been verified yet. Please check your mobile for the verification code.")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        // Fetch categories with pagination
        $categories = Category::with('subCategories')->get();

        return response()->json([
            "status" => "success",
            "message" => "Categories was successfully fetched.",
            "data" => $categories,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/client/v1/categories",
     *     summary="Create a new category",
     *     description="Create a new category. Only accessible by authenticated users.",
     *     operationId="storeCategory",
     *     tags={"Categories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Residential", description="Category name (unique)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Category created successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Residential"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The name field is required."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Mobile number not verified")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully.',
            'data' => $category
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/client/v1/categories/{id}",
     *     summary="Get a specific category",
     *     description="Retrieve a single category with its subcategories by ID",
     *     operationId="showCategory",
     *     tags={"Categories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Category found."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Residential"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(
     *                     property="sub_categories",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="category_id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time")
     *                     )
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
    public function show($id)
    {
        $category = Category::with('subCategories')->find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Category found.',
            'data' => $category
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/client/v1/categories/{id}",
     *     summary="Update a category",
     *     description="Update an existing category by ID",
     *     operationId="updateCategory",
     *     tags={"Categories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Residential Updated", description="Category name (unique)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Category updated successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Residential Updated"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Category not found"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Mobile number not verified")
     * )
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found.'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ]);

        $category->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully.',
            'data' => $category
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/client/v1/categories/{id}",
     *     summary="Delete a category",
     *     description="Soft delete a category by ID. This will not delete associated subcategories.",
     *     operationId="destroyCategory",
     *     tags={"Categories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Category deleted successfully.")
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
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found.'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Category deleted successfully.'
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/client/v1/categories/search",
     *     summary="Search categories and subcategories",
     *     description="Search for categories by category name or subcategory name. The search is case-insensitive and requires a minimum of 3 characters. Returns a maximum of 10 results.",
     *     operationId="searchCategories",
     *     tags={"Categories"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         description="Search term (minimum 3 characters). Searches both category and subcategory names.",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             minLength=3,
     *             example="Resi"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search results (empty array if query is less than 3 characters or no matches found)",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Residential"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T10:30:00.000000Z"),
     *                 @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null),
     *                 @OA\Property(
     *                     property="sub_categories",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="category_id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Apartment"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00.000000Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T10:30:00.000000Z"),
     *                         @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null)
     *                     )
     *                 )
     *             ),
     *             example={
     *                 {
     *                     "id": 1,
     *                     "name": "Residential",
     *                     "created_at": "2024-01-15T10:30:00.000000Z",
     *                     "updated_at": "2024-01-15T10:30:00.000000Z",
     *                     "deleted_at": null,
     *                     "sub_categories": {
     *                         {
     *                             "id": 1,
     *                             "category_id": 1,
     *                             "name": "Apartment",
     *                             "created_at": "2024-01-15T10:30:00.000000Z",
     *                             "updated_at": "2024-01-15T10:30:00.000000Z",
     *                             "deleted_at": null
     *                         },
     *                         {
     *                             "id": 2,
     *                             "category_id": 1,
     *                             "name": "House",
     *                             "created_at": "2024-01-15T10:30:00.000000Z",
     *                             "updated_at": "2024-01-15T10:30:00.000000Z",
     *                             "deleted_at": null
     *                         }
     *                     }
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Mobile number not verified",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="The mobile number has not been verified yet. Please check your mobile for the verification code.")
     *             )
     *         )
     *     )
     * )
     */
    public function search(Request $request) {
        
        $searchTerm = $request->input('query', '');

        // Check if search term length is at least 3 characters
        if (strlen($searchTerm) < 3) {
            // Return empty array or all categories without filtering
            return response()->json([]); // Uncomment this to return an empty array
            // return response()->json(Category::with('subCategories')->get()); // Return all categories if search term is too short
        } else {

            $categories = Category::query()
                ->where('name', 'LIKE', "%{$searchTerm}%")
                ->orWhereHas('subCategories', function($query) use ($searchTerm) {
                    $query->where('name', 'LIKE', "%{$searchTerm}%");
                })
                ->with('subCategories')
                ->limit(10) // Limit the number of results
                ->get();

            return response()->json($categories);
        }
    }
}
