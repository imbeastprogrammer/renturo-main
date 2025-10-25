<?php

namespace App\Http\Controllers\API\V1\Tenants\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\UploadMediaRequest;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;

class MediaController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * @OA\Post(
     *     path="/api/client/v1/media/upload",
     *     summary="Client - Upload media file",
     *     description="Upload an image, video, or document to AWS S3. Supports profile photos, listing photos, store logos, posts, and more. Images are automatically resized and thumbnails are generated.",
     *     operationId="uploadMedia",
     *     tags={"Client - Media"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"file", "mediable_type", "mediable_id", "category"},
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                     description="File to upload (Image: max 10MB, Video: max 50MB)"
     *                 ),
     *                 @OA\Property(
     *                     property="mediable_type",
     *                     type="string",
     *                     enum={"User", "Listing", "Store", "Post", "DynamicFormSubmission", "Comment"},
     *                     example="User",
     *                     description="Entity type this media belongs to"
     *                 ),
     *                 @OA\Property(
     *                     property="mediable_id",
     *                     type="integer",
     *                     example=1,
     *                     description="ID of the entity"
     *                 ),
     *                 @OA\Property(
     *                     property="category",
     *                     type="string",
     *                     enum={"profile", "cover", "post", "story", "comment", "listing", "logo", "banner", "document", "attachment", "other"},
     *                     example="profile",
     *                     description="Media category"
     *                 ),
     *                 @OA\Property(
     *                     property="is_primary",
     *                     type="boolean",
     *                     example=true,
     *                     description="Set as primary media (optional)"
     *                 ),
     *                 @OA\Property(
     *                     property="metadata",
     *                     type="object",
     *                     description="Additional metadata (optional)",
     *                     @OA\Property(property="caption", type="string", example="My profile photo"),
     *                     @OA\Property(property="alt_text", type="string", example="User profile picture")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Media uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Media uploaded successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="media_type", type="string", example="image"),
     *                 @OA\Property(property="category", type="string", example="profile"),
     *                 @OA\Property(property="s3_url", type="string", example="https://s3.amazonaws.com/renturo/user/1/profile/uuid.jpg"),
     *                 @OA\Property(property="thumbnail_url", type="string", example="https://s3.amazonaws.com/renturo/user/1/profile/thumb_uuid.jpg"),
     *                 @OA\Property(property="file_size", type="integer", example=245678),
     *                 @OA\Property(property="width", type="integer", example=1920),
     *                 @OA\Property(property="height", type="integer", example=1080)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function upload(UploadMediaRequest $request): JsonResponse
    {
        try {
            $media = $this->mediaService->upload(
                $request->file('file'),
                $request->input('mediable_type'),
                $request->input('mediable_id'),
                $request->input('category'),
                Auth::id()
            );

            // Set as primary if requested
            if ($request->input('is_primary')) {
                $media->setAsPrimary();
            }

            // Update metadata if provided
            if ($request->has('metadata')) {
                $media->metadata = $request->input('metadata');
                $media->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Media uploaded successfully',
                'data' => $media
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload media',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/client/v1/media/{id}",
     *     summary="Client - Get media details",
     *     description="Retrieve details of a specific media file by ID",
     *     operationId="getMedia",
     *     tags={"Client - Media"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Media ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Media details retrieved",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Media retrieved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Media not found"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function show($id): JsonResponse
    {
        $media = Media::find($id);

        if (!$media) {
            return response()->json([
                'success' => false,
                'message' => 'Media not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Media retrieved successfully',
            'data' => $media
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/client/v1/media/entity/{type}/{id}",
     *     summary="Client - Get all media for an entity",
     *     description="Retrieve all media files associated with a specific entity (User, Listing, Store, Post, etc.)",
     *     operationId="getEntityMedia",
     *     tags={"Client - Media"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         description="Entity type (User, Listing, Store, Post)",
     *         required=true,
     *         @OA\Schema(type="string", example="User")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Entity ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by category (optional)",
     *         required=false,
     *         @OA\Schema(type="string", example="profile")
     *     ),
     *     @OA\Parameter(
     *         name="media_type",
     *         in="query",
     *         description="Filter by media type (optional)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"image", "video", "document"}, example="image")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Entity media retrieved",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Media retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getEntityMedia(Request $request, $type, $id): JsonResponse
    {
        $query = Media::where('mediable_type', $type)
            ->where('mediable_id', $id);

        // Filter by category if provided
        if ($request->has('category')) {
            $query->where('category', $request->input('category'));
        }

        // Filter by media type if provided
        if ($request->has('media_type')) {
            $query->where('media_type', $request->input('media_type'));
        }

        $media = $query->ordered()->get();

        return response()->json([
            'success' => true,
            'message' => 'Media retrieved successfully',
            'data' => $media,
            'count' => $media->count()
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/client/v1/media/my-uploads",
     *     summary="Client - Get my uploaded media",
     *     description="Retrieve all media files uploaded by the authenticated user",
     *     operationId="getMyMedia",
     *     tags={"Client - Media"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="media_type",
     *         in="query",
     *         description="Filter by media type",
     *         required=false,
     *         @OA\Schema(type="string", enum={"image", "video", "document"})
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by category",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User media retrieved",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Media retrieved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getMyMedia(Request $request): JsonResponse
    {
        $query = Media::where('user_id', Auth::id());

        // Filter by media type
        if ($request->has('media_type')) {
            $query->where('media_type', $request->input('media_type'));
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->input('category'));
        }

        $perPage = $request->input('per_page', 20);
        $media = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Media retrieved successfully',
            'data' => $media
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/client/v1/media/{id}",
     *     summary="Client - Delete media",
     *     description="Delete a media file from S3 and database. Only the uploader or entity owner can delete.",
     *     operationId="deleteMedia",
     *     tags={"Client - Media"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Media ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Media deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Media deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Media not found"),
     *     @OA\Response(response=403, description="Unauthorized to delete this media"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function destroy($id): JsonResponse
    {
        $media = Media::find($id);

        if (!$media) {
            return response()->json([
                'success' => false,
                'message' => 'Media not found'
            ], 404);
        }

        // Check authorization - only uploader can delete
        if ($media->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this media'
            ], 403);
        }

        try {
            $this->mediaService->delete($media);

            return response()->json([
                'success' => true,
                'message' => 'Media deleted successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete media',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/client/v1/media/{id}/set-primary",
     *     summary="Client - Set media as primary",
     *     description="Set a media file as the primary/featured media for its entity (e.g., primary profile photo, main listing photo)",
     *     operationId="setPrimaryMedia",
     *     tags={"Client - Media"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Media ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Media set as primary",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Media set as primary successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Media not found"),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function setPrimary($id): JsonResponse
    {
        $media = Media::find($id);

        if (!$media) {
            return response()->json([
                'success' => false,
                'message' => 'Media not found'
            ], 404);
        }

        // Check authorization
        if ($media->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $media->setAsPrimary();

        return response()->json([
            'success' => true,
            'message' => 'Media set as primary successfully',
            'data' => $media
        ]);
    }
}
