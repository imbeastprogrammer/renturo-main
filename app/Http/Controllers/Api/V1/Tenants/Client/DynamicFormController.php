<?php

namespace App\Http\Controllers\API\V1\Tenants\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DynamicForm;
use App\Models\DynamicFormSubmission;

class DynamicFormController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/client/v1/forms",
     *     summary="Get all dynamic forms",
     *     description="Retrieve a list of all dynamic forms with their subcategories and categories",
     *     operationId="listDynamicForms",
     *     tags={"Client - Dynamic Forms"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Forms fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Forms fetched successfully."),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Apartment Rental Form"),
     *                         @OA\Property(property="description", type="string", example="Form for renting apartments"),
     *                         @OA\Property(
     *                             property="subcategory",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Apartment"),
     *                             @OA\Property(
     *                                 property="category",
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=1),
     *                                 @OA\Property(property="name", type="string", example="Residential")
     *                             )
     *                         )
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
        $forms = DynamicForm::with(['subCategory.category'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'success',
            'body' => [
                'message' => 'Forms fetched successfully.',
                'data' => $forms
            ]
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/client/v1/forms/subcategory/{subcategoryId}",
     *     summary="Get forms by subcategory",
     *     description="Retrieve all dynamic forms for a specific subcategory",
     *     operationId="getFormsBySubcategory",
     *     tags={"Client - Dynamic Forms"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="subcategoryId",
     *         in="path",
     *         description="ID of the subcategory",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Forms fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Forms fetched successfully."),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Apartment Rental Form"),
     *                         @OA\Property(property="description", type="string", example="Form for renting apartments")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No forms found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="errors", type="string", example="No forms found for this subcategory.")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Mobile number not verified")
     * )
     */
    public function getBySubcategory($subcategoryId)
    {
        $forms = DynamicForm::with(['subCategory.category'])
            ->where('subcategory_id', $subcategoryId)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($forms->isEmpty()) {
            return response()->json([
                'message' => 'failed',
                'errors' => 'No forms found for this subcategory.'
            ], 404);
        }

        return response()->json([
            'message' => 'success',
            'body' => [
                'message' => 'Forms fetched successfully.',
                'data' => $forms
            ]
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/client/v1/forms/{id}",
     *     summary="Get form details",
     *     description="Retrieve a specific dynamic form with all its pages, fields, and submitted values if they exist",
     *     operationId="getFormDetails",
     *     tags={"Client - Dynamic Forms"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the form",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Form was fetched successfully."),
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(property="dynamic_form_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Apartment Rental Form"),
     *                     @OA\Property(property="description", type="string", example="Form for renting apartments"),
     *                     @OA\Property(
     *                         property="subcategory",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Apartment"),
     *                         @OA\Property(
     *                             property="category",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Residential")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="submission",
     *                         type="object",
     *                         nullable=true,
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time")
     *                     ),
     *                     @OA\Property(
     *                         property="dynamic_form_pages",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="dynamic_form_page_id", type="integer", example=1),
     *                             @OA\Property(property="title", type="string", example="Basic Information"),
     *                             @OA\Property(property="sort_no", type="integer", example=1),
     *                             @OA\Property(
     *                                 property="dynamic_form_fields",
     *                                 type="array",
     *                                 @OA\Items(
     *                                     @OA\Property(property="field_id", type="integer", example=1),
     *                                     @OA\Property(property="input_field_label", type="string", example="Full Name"),
     *                                     @OA\Property(property="input_field_name", type="string", example="full_name"),
     *                                     @OA\Property(property="input_field_type", type="string", example="text"),
     *                                     @OA\Property(property="is_required", type="boolean", example=true),
     *                                     @OA\Property(property="sort_no", type="integer", example=1),
     *                                     @OA\Property(property="data", type="object", nullable=true),
     *                                     @OA\Property(property="value", type="string", example="John Doe")
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Form not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="errors", type="string", example="Form not found.")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Mobile number not verified"),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="errors", type="string", example="An error occurred while fetching the form.")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            // Eager load all necessary relationships
            $form = DynamicForm::with([
                'dynamicFormPages' => function ($query) {
                    $query->orderBy('sort_no');
                },
                'dynamicFormPages.dynamicFormFields' => function ($query) {
                    $query->orderBy('sort_no');
                },
                'subCategory.category'
            ])->findOrFail($id);

        $userId = auth()->id();

        // Check if the user has a form submitted
        $submittedUserForm = DynamicFormSubmission::where('dynamic_form_id', $form->id)
            ->where('user_id', $userId)
                ->latest()
            ->first();

            // Transform the form data
            $transformedData = $this->transformFormData($form, $submittedUserForm);

            return response()->json([
                'message' => 'success',
                'body' => [
                    'message' => 'Form was fetched successfully.',
                    'data' => $transformedData,
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'failed',
                'errors' => 'Form not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'failed',
                'errors' => 'An error occurred while fetching the form.'
            ], 500);
        }
    }

    /**
     * Transform form data with or without submitted values
     *
     * @param DynamicForm $form
     * @param DynamicFormSubmission|null $submittedUserForm
     * @return array
     */
    private function transformFormData($form, $submittedUserForm = null)
    {
        $baseData = [
                'dynamic_form_id' => $form->id,
                'name' => $form->name,
                'description' => $form->description,
            'subcategory' => [
                'id' => $form->subCategory->id ?? null,
                'name' => $form->subCategory->name ?? null,
                'category' => [
                    'id' => $form->subCategory->category->id ?? null,
                    'name' => $form->subCategory->category->name ?? null,
                ]
            ]
        ];

        if ($submittedUserForm) {
            $submittedData = json_decode($submittedUserForm->data, true);
            $baseData['submission'] = [
                'id' => $submittedUserForm->id,
                'created_at' => $submittedUserForm->created_at,
                'updated_at' => $submittedUserForm->updated_at
            ];
        }

        $baseData['dynamic_form_pages'] = $form->dynamicFormPages->map(function ($page) use ($submittedUserForm, $submittedData) {
            $submittedPage = $submittedData ? collect($submittedData)->firstWhere('dynamic_form_page_id', $page->id) : null;

                    return [
                        'dynamic_form_page_id' => $page->id,
                        'title' => $page->title,
                        'sort_no' => $page->sort_no,
                'dynamic_form_fields' => $page->dynamicFormFields->map(function ($field) use ($submittedPage) {
                            $submittedField = $submittedPage ? collect($submittedPage['dynamic_form_fields'])->firstWhere('field_id', $field->id) : null;

                            return [
                                'field_id' => $field->id,
                                'input_field_label' => $field->input_field_label,
                                'input_field_name' => $field->input_field_name,
                                'input_field_type' => $field->input_field_type,
                                'is_required' => $field->is_required,
                                'sort_no' => $field->sort_no,
                                'data' => $field->data,
                        'value' => $submittedField['value'] ?? ''
                    ];
                })->sortBy('sort_no')->values()
            ];
        })->sortBy('sort_no')->values();

        return $baseData;
    }

    /**
     * @OA\Get(
     *     path="/api/client/v1/forms/{id}/edit",
     *     summary="Edit form (Not Supported)",
     *     description="This endpoint is not supported as forms can only be edited by administrators",
     *     operationId="editForm",
     *     tags={"Client - Dynamic Forms"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the form",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Method not supported",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="errors", type="string", example="Method not supported.")
     *         )
     *     )
     * )
     */
    public function edit($id)
    {
        // Not used in API context
        return response()->json([
            'message' => 'failed',
            'errors' => 'Method not supported.'
        ], 405);
    }

    /**
     * @OA\Put(
     *     path="/api/client/v1/forms/{id}",
     *     summary="Update form (Admin Only)",
     *     description="This endpoint is not available for clients as forms can only be updated by administrators",
     *     operationId="updateForm",
     *     tags={"Client - Dynamic Forms"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the form",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Admin only",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="errors", type="string", example="Forms can only be managed by administrators.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        // Not used - forms are managed by admin
        return response()->json([
            'message' => 'failed',
            'errors' => 'Forms can only be managed by administrators.'
        ], 403);
    }

    /**
     * @OA\Delete(
     *     path="/api/client/v1/forms/{id}",
     *     summary="Delete form (Admin Only)",
     *     description="This endpoint is not available for clients as forms can only be deleted by administrators",
     *     operationId="deleteForm",
     *     tags={"Client - Dynamic Forms"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the form",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Admin only",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="errors", type="string", example="Forms can only be managed by administrators.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        // Not used - forms are managed by admin
        return response()->json([
            'message' => 'failed',
            'errors' => 'Forms can only be managed by administrators.'
        ], 403);
    }
}
