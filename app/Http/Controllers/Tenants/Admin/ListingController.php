<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Admin Listing Controller
 * 
 * Manages CRUD operations for listings via the admin web panel.
 */
class ListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        try {
            $perPage = min($request->get('per_page', 15), 100);

            $query = Listing::with(['category', 'subCategory', 'owner:id,name,email', 'photos'])
                ->withCount('photos');

            // Search
            if ($search = $request->get('search')) {
                $query->search($search);
            }

            // Status filter
            if ($status = $request->get('status')) {
                $query->where('status', $status);
            }

            // Category filter
            if ($categoryId = $request->get('category_id')) {
                $query->byCategory($categoryId);
            }

            // Featured filter
            if ($request->has('featured')) {
                $query->featured();
            }

            // Verified filter
            if ($request->has('verified')) {
                $query->verified();
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'latest');
            $sortOrder = $request->get('sort_order', 'desc');

            switch ($sortBy) {
                case 'title':
                    $query->orderBy('title', $sortOrder);
                    break;
                case 'price':
                    $query->orderBy('price_per_hour', $sortOrder);
                    break;
                case 'rating':
                    $query->orderBy('average_rating', $sortOrder);
                    break;
                case 'views':
                    $query->orderBy('views_count', $sortOrder);
                    break;
                case 'bookings':
                    $query->orderBy('bookings_count', $sortOrder);
                    break;
                case 'latest':
                default:
                    $query->latest();
                    break;
            }

            $listings = $query->paginate($perPage);

            // Get filter options
            $categories = Category::select('id', 'name')->orderBy('name')->get();
            $statuses = [
                Listing::STATUS_DRAFT,
                Listing::STATUS_ACTIVE,
                Listing::STATUS_INACTIVE,
                Listing::STATUS_SUSPENDED,
                Listing::STATUS_ARCHIVED,
            ];

            return Inertia::render('Admin/Listings/Index', [
                'listings' => $listings,
                'categories' => $categories,
                'statuses' => $statuses,
                'filters' => $request->only(['search', 'status', 'category_id', 'featured', 'verified']),
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading listings index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Inertia::render('Admin/Listings/Index', [
                'listings' => [],
                'categories' => [],
                'statuses' => [],
                'error' => 'Failed to load listings'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        try {
            $categories = Category::with('subCategories')->orderBy('name')->get();

            return Inertia::render('Admin/Listings/Create', [
                'categories' => $categories,
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading create listing form', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Inertia::render('Admin/Listings/Create', [
                'categories' => [],
                'error' => 'Failed to load form'
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'sub_category_id' => 'nullable|exists:sub_categories,id',
                'listing_type' => 'required|string|in:' . implode(',', [
                    Listing::TYPE_SPORTS,
                    Listing::TYPE_ACCOMMODATION,
                    Listing::TYPE_TRANSPORT,
                    Listing::TYPE_EVENT_SPACE,
                    Listing::TYPE_EQUIPMENT,
                    Listing::TYPE_OTHER,
                ]),
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:100',
                'province' => 'required|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'price_per_hour' => 'nullable|numeric|min:0',
                'price_per_day' => 'nullable|numeric|min:0',
                'price_per_week' => 'nullable|numeric|min:0',
                'price_per_month' => 'nullable|numeric|min:0',
                'max_capacity' => 'nullable|integer|min:1',
                'amenities' => 'nullable|array',
                'amenities.*' => 'string',
                'status' => 'nullable|in:' . implode(',', [
                    Listing::STATUS_DRAFT,
                    Listing::STATUS_ACTIVE,
                    Listing::STATUS_INACTIVE,
                ]),
                'visibility' => 'nullable|in:' . implode(',', [
                    Listing::VISIBILITY_PUBLIC,
                    Listing::VISIBILITY_PRIVATE,
                    Listing::VISIBILITY_UNLISTED,
                ]),
                'instant_booking' => 'nullable|boolean',
                'minimum_booking_hours' => 'nullable|integer|min:1',
                'maximum_booking_hours' => 'nullable|integer|min:1',
                'advance_booking_days' => 'nullable|integer|min:1',
                'cancellation_hours' => 'nullable|integer|min:0',
            ]);

            DB::beginTransaction();

            try {
                // Add user_id
                $validated['user_id'] = auth()->id();

                $listing = Listing::create($validated);

                // Handle photo uploads if provided
                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $index => $photo) {
                        $path = $photo->store('listings/' . $listing->id, 'public');
                        
                        $listing->photos()->create([
                            'photo_url' => asset('storage/' . $path),
                            'storage_path' => $path,
                            'storage_disk' => 'public',
                            'sort_order' => $index,
                            'is_primary' => $index === 0,
                            'file_size' => $photo->getSize(),
                            'mime_type' => $photo->getMimeType(),
                        ]);
                    }
                }

                DB::commit();

                return redirect()
                    ->route('admin.listings.show', $listing->id)
                    ->with('success', 'Listing created successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Error creating listing', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to create listing: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        try {
            $listing = Listing::with([
                    'category',
                    'subCategory',
                    'owner',
                    'photos',
                    'availability',
                    'dynamicFormSubmission.dynamicFormFieldData.dynamicFormField'
                ])
                ->withCount(['photos'])
                ->findOrFail($id);

            return Inertia::render('Admin/Listings/Show', [
                'listing' => $listing,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return Inertia::render('Error', [
                'status' => 404,
                'message' => 'Listing not found'
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading listing', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Inertia::render('Error', [
                'status' => 500,
                'message' => 'Failed to load listing'
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): Response
    {
        try {
            $listing = Listing::with(['photos', 'category', 'subCategory'])->findOrFail($id);
            $categories = Category::with('subCategories')->orderBy('name')->get();

            return Inertia::render('Admin/Listings/Edit', [
                'listing' => $listing,
                'categories' => $categories,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return Inertia::render('Error', [
                'status' => 404,
                'message' => 'Listing not found'
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading edit listing form', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Inertia::render('Error', [
                'status' => 500,
                'message' => 'Failed to load edit form'
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        try {
            $listing = Listing::findOrFail($id);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'sub_category_id' => 'nullable|exists:sub_categories,id',
                'listing_type' => 'required|string|in:' . implode(',', [
                    Listing::TYPE_SPORTS,
                    Listing::TYPE_ACCOMMODATION,
                    Listing::TYPE_TRANSPORT,
                    Listing::TYPE_EVENT_SPACE,
                    Listing::TYPE_EQUIPMENT,
                    Listing::TYPE_OTHER,
                ]),
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:100',
                'province' => 'required|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'price_per_hour' => 'nullable|numeric|min:0',
                'price_per_day' => 'nullable|numeric|min:0',
                'price_per_week' => 'nullable|numeric|min:0',
                'price_per_month' => 'nullable|numeric|min:0',
                'max_capacity' => 'nullable|integer|min:1',
                'amenities' => 'nullable|array',
                'amenities.*' => 'string',
                'status' => 'nullable|in:' . implode(',', [
                    Listing::STATUS_DRAFT,
                    Listing::STATUS_ACTIVE,
                    Listing::STATUS_INACTIVE,
                    Listing::STATUS_SUSPENDED,
                    Listing::STATUS_ARCHIVED,
                ]),
                'visibility' => 'nullable|in:' . implode(',', [
                    Listing::VISIBILITY_PUBLIC,
                    Listing::VISIBILITY_PRIVATE,
                    Listing::VISIBILITY_UNLISTED,
                ]),
                'is_featured' => 'nullable|boolean',
                'is_verified' => 'nullable|boolean',
                'instant_booking' => 'nullable|boolean',
                'minimum_booking_hours' => 'nullable|integer|min:1',
                'maximum_booking_hours' => 'nullable|integer|min:1',
                'advance_booking_days' => 'nullable|integer|min:1',
                'cancellation_hours' => 'nullable|integer|min:0',
            ]);

            DB::beginTransaction();

            try {
                $listing->update($validated);

                // Handle new photo uploads if provided
                if ($request->hasFile('photos')) {
                    $currentMaxOrder = $listing->photos()->max('sort_order') ?? 0;
                    
                    foreach ($request->file('photos') as $index => $photo) {
                        $path = $photo->store('listings/' . $listing->id, 'public');
                        
                        $listing->photos()->create([
                            'photo_url' => asset('storage/' . $path),
                            'storage_path' => $path,
                            'storage_disk' => 'public',
                            'sort_order' => $currentMaxOrder + $index + 1,
                            'is_primary' => false,
                            'file_size' => $photo->getSize(),
                            'mime_type' => $photo->getMimeType(),
                        ]);
                    }
                }

                DB::commit();

                return redirect()
                    ->route('admin.listings.show', $listing->id)
                    ->with('success', 'Listing updated successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()
                ->route('admin.listings.index')
                ->with('error', 'Listing not found');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Error updating listing', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to update listing: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id): RedirectResponse
    {
        try {
            $listing = Listing::findOrFail($id);
            $listing->delete();

            return redirect()
                ->route('admin.listings.index')
                ->with('success', 'Listing deleted successfully');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()
                ->route('admin.listings.index')
                ->with('error', 'Listing not found');

        } catch (\Exception $e) {
            Log::error('Error deleting listing', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to delete listing: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted listing.
     */
    public function restore(string $id): RedirectResponse
    {
        try {
            $listing = Listing::withTrashed()->findOrFail($id);
            $listing->restore();

            return redirect()
                ->route('admin.listings.show', $id)
                ->with('success', 'Listing restored successfully');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()
                ->route('admin.listings.index')
                ->with('error', 'Listing not found');

        } catch (\Exception $e) {
            Log::error('Error restoring listing', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to restore listing: ' . $e->getMessage());
        }
    }

    /**
     * Publish a listing (set status to active).
     */
    public function publish(string $id): RedirectResponse
    {
        try {
            $listing = Listing::findOrFail($id);
            $listing->publish();

            return redirect()
                ->back()
                ->with('success', 'Listing published successfully');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()
                ->route('admin.listings.index')
                ->with('error', 'Listing not found');

        } catch (\Exception $e) {
            Log::error('Error publishing listing', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to publish listing: ' . $e->getMessage());
        }
    }

    /**
     * Unpublish a listing (set status to inactive).
     */
    public function unpublish(string $id): RedirectResponse
    {
        try {
            $listing = Listing::findOrFail($id);
            $listing->unpublish();

            return redirect()
                ->back()
                ->with('success', 'Listing unpublished successfully');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()
                ->route('admin.listings.index')
                ->with('error', 'Listing not found');

        } catch (\Exception $e) {
            Log::error('Error unpublishing listing', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to unpublish listing: ' . $e->getMessage());
        }
    }

    /**
     * Feature a listing.
     */
    public function feature(string $id): RedirectResponse
    {
        try {
            $listing = Listing::findOrFail($id);
            $listing->feature();

            return redirect()
                ->back()
                ->with('success', 'Listing featured successfully');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()
                ->route('admin.listings.index')
                ->with('error', 'Listing not found');

        } catch (\Exception $e) {
            Log::error('Error featuring listing', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to feature listing: ' . $e->getMessage());
        }
    }

    /**
     * Verify a listing.
     */
    public function verify(string $id): RedirectResponse
    {
        try {
            $listing = Listing::findOrFail($id);
            $listing->verify();

            return redirect()
                ->back()
                ->with('success', 'Listing verified successfully');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()
                ->route('admin.listings.index')
                ->with('error', 'Listing not found');

        } catch (\Exception $e) {
            Log::error('Error verifying listing', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to verify listing: ' . $e->getMessage());
        }
    }
}
