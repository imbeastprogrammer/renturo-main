<?php

namespace App\Http\Controllers\Api\V1\Tenants\Client;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Client - Listings",
 *     description="API endpoints for browsing and viewing listings (Client/Mobile App)"
 * )
 */
class ListingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/listings",
     *     summary="Get all listings (paginated, filterable)",
     *     tags={"Client - Listings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page (max 50)",
     *         required=false,
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search keyword (title, description, address, city)",
     *         required=false,
     *         @OA\Schema(type="string", example="basketball")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category ID",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="sub_category_id",
     *         in="query",
     *         description="Filter by sub-category ID",
     *         required=false,
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         description="Filter by city",
     *         required=false,
     *         @OA\Schema(type="string", example="Manila")
     *     ),
     *     @OA\Parameter(
     *         name="province",
     *         in="query",
     *         description="Filter by province",
     *         required=false,
     *         @OA\Schema(type="string", example="Metro Manila")
     *     ),
     *     @OA\Parameter(
     *         name="min_price",
     *         in="query",
     *         description="Minimum price",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=100)
     *     ),
     *     @OA\Parameter(
     *         name="max_price",
     *         in="query",
     *         description="Maximum price",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=1000)
     *     ),
     *     @OA\Parameter(
     *         name="latitude",
     *         in="query",
     *         description="User's latitude for radius search",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=14.5995)
     *     ),
     *     @OA\Parameter(
     *         name="longitude",
     *         in="query",
     *         description="User's longitude for radius search",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=120.9842)
     *     ),
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *         description="Search radius in kilometers (default 10km, max 50km)",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="featured",
     *         in="query",
     *         description="Show only featured listings (1 or 0)",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="instant_booking",
     *         in="query",
     *         description="Show only instant booking listings (1 or 0)",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort by field (price_asc, price_desc, rating, newest, popular)",
     *         required=false,
     *         @OA\Schema(type="string", example="newest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Listings retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Listings retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=100)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 15), 50); // Max 50 items per page

            $query = Listing::query()
                ->with(['category', 'subCategory', 'photos', 'owner:id,first_name,last_name,email'])
                ->published() // Only active and published listings
                ->public(); // Only public visibility

            // Search
            if ($search = $request->get('search')) {
                $query->search($search);
            }

            // Category filters
            if ($categoryId = $request->get('category_id')) {
                $query->byCategory($categoryId);
            }

            if ($subCategoryId = $request->get('sub_category_id')) {
                $query->bySubCategory($subCategoryId);
            }

            // Location filters
            if ($city = $request->get('city')) {
                $query->byLocation($city);
            }

            if ($province = $request->get('province')) {
                $query->byLocation(null, $province);
            }

            // Price range filter
            if ($request->has('min_price') || $request->has('max_price')) {
                $query->byPriceRange(
                    $request->get('min_price'),
                    $request->get('max_price'),
                    'price_per_hour' // Default to hourly rate
                );
            }

            // Radius search (location-based)
            if ($request->has('latitude') && $request->has('longitude')) {
                $latitude = $request->get('latitude');
                $longitude = $request->get('longitude');
                $radius = min($request->get('radius', 10), 50); // Max 50km radius

                $query->withinRadius($latitude, $longitude, $radius);
            }

            // Featured filter
            if ($request->get('featured')) {
                $query->featured();
            }

            // Instant booking filter
            if ($request->get('instant_booking')) {
                $query->where('instant_booking', true);
            }

            // Amenities filter (AND logic - must have all specified amenities)
            if ($amenities = $request->get('amenities')) {
                $amenitiesArray = is_array($amenities) ? $amenities : explode(',', $amenities);
                
                foreach ($amenitiesArray as $amenity) {
                    $query->whereJsonContains('amenities', trim($amenity));
                }
            }

            // Availability filter for specific dates (NEW!)
            if ($request->has('check_in_date') && $request->has('check_out_date')) {
                $query->availableForDateRange(
                    $request->get('check_in_date'),
                    $request->get('check_out_date'),
                    $request->get('check_in_time'),
                    $request->get('check_out_time')
                );
            } elseif ($request->get('available_only')) {
                // Filter to show only listings with any availability
                $query->hasAvailability();
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'newest');
            switch ($sortBy) {
                case 'price_asc':
                    $query->orderBy('price_per_hour', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price_per_hour', 'desc');
                    break;
                case 'rating':
                    $query->orderBy('average_rating', 'desc')->orderBy('reviews_count', 'desc');
                    break;
                case 'popular':
                    $query->orderBy('bookings_count', 'desc')->orderBy('views_count', 'desc');
                    break;
                case 'newest':
                default:
                    $query->latest('published_at');
                    break;
            }

            $listings = $query->paginate($perPage);

            // Add availability status to each listing
            $listings->getCollection()->transform(function ($listing) use ($request) {
                $availabilityStatus = $listing->getAvailabilityStatus(
                    $request->get('check_in_date'),
                    $request->get('check_out_date')
                );
                
                $listing->availability_status = $availabilityStatus;
                return $listing;
            });

            return response()->json([
                'success' => true,
                'message' => 'Listings retrieved successfully',
                'data' => $listings,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error retrieving listings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve listings',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/listings/{id}",
     *     summary="Get a single listing by ID",
     *     tags={"Client - Listings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Listing ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Listing retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Listing retrieved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Listing not found"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function show(string $id): JsonResponse
    {
        try {
            $listing = Listing::with([
                    'category',
                    'subCategory',
                    'photos',
                    'owner:id,first_name,last_name,email',
                    'availability',
                    'dynamicFormSubmission.dynamicFormFieldData.dynamicFormField'
                ])
                ->published()
                ->public()
                ->findOrFail($id);

            // Increment views count
            $listing->incrementViews();

            // Add availability status
            $listing->availability_status = $listing->getAvailabilityStatus();

            return response()->json([
                'success' => true,
                'message' => 'Listing retrieved successfully',
                'data' => $listing,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Listing not found',
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error retrieving listing', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve listing',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/listings/slug/{slug}",
     *     summary="Get a listing by slug",
     *     tags={"Client - Listings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Listing slug",
     *         required=true,
     *         @OA\Schema(type="string", example="premium-basketball-court-manila")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Listing retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Listing retrieved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Listing not found"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function showBySlug(string $slug): JsonResponse
    {
        try {
            $listing = Listing::with([
                    'category',
                    'subCategory',
                    'photos',
                    'owner:id,first_name,last_name,email',
                    'availability',
                    'dynamicFormSubmission.dynamicFormFieldData.dynamicFormField'
                ])
                ->published()
                ->public()
                ->where('slug', $slug)
                ->firstOrFail();

            // Increment views count
            $listing->incrementViews();

            return response()->json([
                'success' => true,
                'message' => 'Listing retrieved successfully',
                'data' => $listing,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Listing not found',
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error retrieving listing by slug', [
                'slug' => $slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve listing',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/listings/featured",
     *     summary="Get featured listings",
     *     tags={"Client - Listings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of featured listings to retrieve (max 20)",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Featured listings retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Featured listings retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function featured(Request $request): JsonResponse
    {
        try {
            $limit = min($request->get('limit', 10), 20); // Max 20 featured

            $listings = Listing::with(['category', 'subCategory', 'photos', 'owner:id,first_name,last_name'])
                ->published()
                ->public()
                ->featured()
                ->orderBy('average_rating', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Featured listings retrieved successfully',
                'data' => $listings,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error retrieving featured listings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve featured listings',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }
}
