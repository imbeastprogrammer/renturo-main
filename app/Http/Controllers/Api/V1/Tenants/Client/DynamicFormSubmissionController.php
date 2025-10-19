<?php

namespace App\Http\Controllers\Api\V1\Tenants\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenants\DynamicFormSubmissionManagement\StoreFormSubmissionRequest;
use App\Http\Requests\Tenants\DynamicFormSubmissionManagement\UpdateFormSubmissionRequest;
use App\Models\DynamicFormSubmission;
use App\Models\DynamicFormField;
use App\Models\DynamicFormPage;
use App\Models\DynamicForm;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Client - Dynamic Form Submissions",
 *     description="API endpoints for submitting and managing dynamic form submissions (Client App)"
 * )
 */
class DynamicFormSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFormSubmissionRequest $request)
    {
    }

    /**
     * @OA\Post(
     *     path="/api/v1/dynamic-forms/{formId}/submit",
     *     summary="Submit a dynamic form",
     *     description="Submit a completed dynamic form with all field values",
     *     operationId="submitDynamicForm",
     *     tags={"Client - Dynamic Form Submissions"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="formId",
     *         in="path",
     *         description="Dynamic Form ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"dynamic_form_id", "store_id", "name"},
     *             @OA\Property(property="dynamic_form_id", type="integer", example=1, description="Dynamic Form ID"),
     *             @OA\Property(property="store_id", type="integer", example=1, description="Store ID"),
     *             @OA\Property(property="name", type="string", example="Basketball Court Registration", description="Submission name"),
     *             @OA\Property(property="about", type="string", example="Main court for weekend games", description="Submission description"),
     *             @OA\Property(
     *                 property="dynamic_form_pages",
     *                 type="array",
     *                 description="Array of form pages with field submissions",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="dynamic_form_page_id", type="integer", example=1),
     *                     @OA\Property(
     *                         property="dynamic_form_fields",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="field_id", type="integer", example=1),
     *                             @OA\Property(property="value", type="string", example="Full Court")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Form submitted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Form was submitted successfully."),
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="dynamic_form_id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="store_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Basketball Court Registration"),
     *                     @OA\Property(property="about", type="string", example="Main court"),
     *                     @OA\Property(property="data", type="string", example="{...}"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or duplicate submission",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(property="errors", type="string", example="You have already submitted this form.")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function submit(StoreFormSubmissionRequest $request, $formId)
    {
        $userId = $request->user()->id; // Assuming user authentication

        // Check if the user has already made a submission for this form
        $existingSubmission = DynamicFormSubmission::where('user_id', $userId)
            ->where('dynamic_form_id', $request->dynamic_form_id)
            ->where('store_id', $request->store_id)
            ->where('name', $request->name)
            ->first();
    
        if ($existingSubmission) {
            return response()->json([
                'message' => 'failed',
                'errors' => 'You have already submitted this form.'
            ], 422);
        }
    
        // Here, instead of using the validation rules, we're taking the validated data from the request.
        $pagesData = $request->input('dynamic_form_pages', []);
    
        // Process the data as necessary for your application
        $processedData = $this->processSubmissionData($pagesData);
    
        // Store the data
        $submission = DynamicFormSubmission::create([
            'store_id' => $request->store_id,
            'dynamic_form_id' => $request->dynamic_form_id,
            'user_id' => $userId,
            'name' => $request->name,
            'about' => $request->about,
            'data' => json_encode($processedData), // Ensure you're saving the processed data, not the validation rules
        ]);
    
        return response()->json([
            'message' => 'success',
            'body' => [
                'message' => 'Form was submitted successfully.',
                'data' => $submission,
            ]
        ], 201);
    }

    private function processSubmissionData($pagesData) {
        // You can add any necessary data processing here
        return $pagesData;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/dynamic-forms/submissions/{id}",
     *     summary="Get submission by ID",
     *     description="Retrieve a specific form submission with all field values and metadata",
     *     operationId="getFormSubmissionById",
     *     tags={"Client - Dynamic Form Submissions"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Submission ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form submission retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Form submission was fetched successfully."),
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(property="dynamic_form_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Basketball Court Form"),
     *                     @OA\Property(property="description", type="string", example="Form for basketball courts"),
     *                     @OA\Property(
     *                         property="dynamic_form_pages",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="dynamic_form_page_id", type="integer"),
     *                             @OA\Property(property="title", type="string"),
     *                             @OA\Property(property="sort_no", type="integer"),
     *                             @OA\Property(property="dynamic_form_fields", type="array", @OA\Items(type="object")),
     *                             @OA\Property(property="remarks", type="string", example="existing")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Form submission not found"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function show(Request $request, $id)
    {
        $userId = $request->user()->id; // Assuming user authentication

        $submission = DynamicFormSubmission::where('user_id', $userId)
                                       ->where('id', $id)
                                       ->first();

        if (!$submission) {
            return response()->json([
                'message' => 'failed',
                'errors' => 'Form submission not found.'
            ], 404);
        }

        $formData = json_decode($submission->data, true);
        $submissionDetails = [];

        // Fetch the dynamic form
        $dynamicForm = DynamicForm::find($submission->dynamic_form_id);

        if (!$dynamicForm) {
            return response()->json([
                'message' => 'failed',
                'errors' => 'Dynamic form not found'
            ], 404);
        }

        // Fetch all pages for the form
        $allPages = DynamicFormPage::where('dynamic_form_id', $submission->dynamic_form_id)
                                ->with('dynamicFormFields')
                                ->get();

        foreach ($allPages as $page) {
            $pageDetails = [
                'dynamic_form_page_id' => $page->id,
                'title' => $page->title,
                'sort_no' => $page->sort_no,    
                'dynamic_form_fields' => [],
                'remarks' => 'existing' // Default remark
            ];

            // Check if this page exists in the submission data
            $submittedPage = collect($formData)->firstWhere('dynamic_form_page_id', $page->id);

            if (!$submittedPage) {
                // If the page is not in the submission, mark it as new
                $pageDetails['remarks'] = 'new';
            }

            foreach ($page->dynamicFormFields as $field) {
                $fieldDetails = [
                    'field_id' => $field->id,
                    'input_field_label' => $field->input_field_label,
                    'input_field_name' => $field->input_field_name,
                    'input_field_type' => $field->input_field_type,
                    'is_required' => $field->is_required, // Include the is_required attribute
                    'sort_no' => $field->sort_no, // Include the sort_no attribute
                    'data' => $field->data, // Include the data attribute
                    'value' => null, // Default to null
                    'remarks' => 'existing' // Default remark
                ];

                // Check if this field exists in the submission data
                $submittedField = collect($submittedPage['dynamic_form_fields'] ?? [])->firstWhere('field_id', $field->id);

                if ($submittedField) {
                    $fieldDetails['value'] = $submittedField['value'];
                } else {
                    // If the field is not in the submission, mark it as new
                    $fieldDetails['remarks'] = 'new';
                }

                $pageDetails['dynamic_form_fields'][] = $fieldDetails;
            }

            $submissionDetails[] = $pageDetails;
        }

        $data = [
            'dynamic_form_id' => $submission->dynamic_form_id,
            'name' => $dynamicForm->name,
            'description' => $dynamicForm->description,
            'dynamic_form_pages' => $submissionDetails
        ];
        
        return response()->json([
            'message' =>'success',
            'body' => [
                'message' => 'Form submission was fetched successfully.',
                'data' => $data
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
        // Not used - editing is handled by update method
        return response()->json([
            'message' => 'failed',
            'errors' => 'Method not implemented. Use update method instead.'
        ], 501);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/dynamic-forms/{formId}/submit",
     *     summary="Update form submission",
     *     description="Update an existing form submission or create new if doesn't exist",
     *     operationId="updateFormSubmission",
     *     tags={"Client - Dynamic Form Submissions"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="formId",
     *         in="path",
     *         description="Dynamic Form ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"store_id", "dynamic_form_pages"},
     *             @OA\Property(property="store_id", type="integer", example=1),
     *             @OA\Property(
     *                 property="dynamic_form_pages",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="dynamic_form_page_id", type="integer"),
     *                     @OA\Property(
     *                         property="dynamic_form_fields",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="field_id", type="integer"),
     *                             @OA\Property(property="value", type="string")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Form updated successfully"),
     *                 @OA\Property(property="data", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Form submitted successfully (new submission)",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Form submitted successfully"),
     *                 @OA\Property(property="data", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function update(UpdateFormSubmissionRequest $request, $formId)
    {
        $userId = $request->user()->id; // Assuming user authentication

        $storeId = $request->input('store_id');
        // Check if the user has already made a submission for this form
        $existingSubmission = DynamicFormSubmission::where('user_id', $userId)
            ->where('dynamic_form_id', $formId)
            ->first();

        // Process the data as necessary for your application
        $pagesData = $request->input('dynamic_form_pages', []);
        $processedData = $this->processSubmissionData($pagesData);

     
        if ($existingSubmission) {

            // Update the existing submission
            $existingSubmission->data = json_encode($processedData);
            $existingSubmission->store_id = $storeId; // Update the store_id
            $existingSubmission->save();

            return response()->json([
                'message' => 'success',
                'body' => [
                  'message' => 'Form updated successfully',
                    'data' => $existingSubmission
                ]
            ], 200);
        
        } else {
            // Create a new submission
            $submission = DynamicFormSubmission::create([
                'store_id' => $storeId,
                'dynamic_form_id' => $formId,
                'user_id' => $userId,
                'data' => json_encode($processedData),
            ]);

            return response()->json([
                'message' => 'success',
                'body' => [
                  'message' => 'Form submitted successfully',
                    'data' => $submission
                ]
            ], 201);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/dynamic-forms/submissions/{formId}",
     *     summary="Delete form submission",
     *     description="Delete a specific form submission",
     *     operationId="deleteFormSubmission",
     *     tags={"Client - Dynamic Form Submissions"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="formId",
     *         in="path",
     *         description="Form Submission ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form submission deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Form submission was successfully deleted."),
     *                 @OA\Property(property="data", type="array", @OA\Items())
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Form submission not found"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function destroy(Request $request, $formId)
    {
        $userId = $request->user()->id; // Assuming user authentication

        // Find the submission by its ID and user ID
        $dynamicFormSubmission = DynamicFormSubmission::where('id', $formId)
                                                      ->where('user_id', $userId)
                                                      ->first();
    
        if (!$dynamicFormSubmission) {
            return response()->json([
                'message' => 'failed',
                'errors' => 'Form submission not found.'    
            ], 404); // Return a 404 not found error if the submission doesn't exist 
        }
    
        // Proceed with deletion
        $dynamicFormSubmission->delete();
    
        return response()->json([
            'status' => 'success',
            'body' => [
                'message' => 'Form submission was successfully deleted.',
                'data' => []
            ]
        ], 200);

    }
    
    /**
     * @OA\Get(
     *     path="/api/v1/dynamic-forms/user/{userId}",
     *     summary="Get all user submissions",
     *     description="Retrieve all form submissions for a specific user",
     *     operationId="getUserFormSubmissions",
     *     tags={"Client - Dynamic Form Submissions"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User submissions retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Form submissions were fetched successfully."),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="form_name", type="string"),
     *                         @OA\Property(property="form_description", type="string"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="about", type="string"),
     *                         @OA\Property(property="category", type="object"),
     *                         @OA\Property(property="sub_category", type="object"),
     *                         @OA\Property(property="dynamic_form_submission", type="object")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Unauthorized access"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getUserDynamicFormSubmissions(Request $request, $userId) 
    {
        $authUserId = $request->user()->id; // Use authenticated user ID to view their submissions
        
        // Security: Compare the UserID on URL against the current authenticated userId
        if ((int)$authUserId !== (int)$userId) {
            return response()->json([
                'message' => 'failed',
                'errors' => 'Unauthorized. You can only view your own submissions.'
            ], 403); // Use 403 Forbidden instead of 404
        }
        
        // Retrieve all submissions for the specific user with eager loading
        $formSubmissions = DynamicFormSubmission::with(['dynamicForm.subCategory.category'])
                                                ->where('user_id', $userId) 
                                                ->orderBy('created_at', 'desc')
                                                ->get();

        
        if ($formSubmissions->isEmpty()) {
            return response()->json([
                'message' => 'success',
                'body' => [
                    'message' => 'No submissions found.',
                    'data' => []
                ]
            ], 200); // Return 200 with empty array instead of 404
        }

        $userSubmissions = [];
        foreach ($formSubmissions as $submission) {
            $dynamicForm = $submission->dynamicForm;
            if ($dynamicForm) {
                $userSubmissions[] = [
                    'form_name' => $dynamicForm->name,
                    'form_description' => $dynamicForm->description,
                    'name' => $submission->name,
                    'about' => $submission->about,
                    'created_at' => $dynamicForm->created_at,
                    'updated_at' => $dynamicForm->updated_at,
                    'category' => [
                        'id' => $dynamicForm->subCategory->category->id?? null,
                        'name' => $dynamicForm->subCategory->category->name?? null,
                        'created_at' => $dynamicForm->subCategory->category->created_at,
                        'updated_at' => $dynamicForm->subCategory->category->updated_at,
                    ],
                    'sub_category' => [ 
                        'id' => $dynamicForm->subCategory->id?? null,
                        'name' => $dynamicForm->subCategory->name?? null,
                        'created_at' => $dynamicForm->subCategory->created_at,
                        'updated_at' => $dynamicForm->subCategory->updated_at,
                    ],
                    'dynamic_form_submission' => [
                        'id' => $submission->id,
                        'user_id' => $submission->user_id,
                        'dynamic_form_id' => $submission->dynamic_form_id,
                        'created_at' => $submission->created_at,
                        'updated_at' => $submission->updated_at
                    ]
                ];
            }
        }
                                            
        return response()->json([
            'message' =>'success',
            'body' => [
                'message' => 'Form submissions were fetched successfully.',
                'data' => $userSubmissions
            ]
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/dynamic-forms/user/{userId}/store/{storeId}",
     *     summary="Get user submissions by store",
     *     description="Retrieve all form submissions for a user filtered by store ID",
     *     operationId="getUserFormSubmissionsByStore",
     *     tags={"Client - Dynamic Form Submissions"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="storeId",
     *         in="path",
     *         description="Store ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Store submissions retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Form submissions were fetched successfully."),
     *                 @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Resource not found"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getUserDynamicFormSubmissionByStoreId(Request $request, $userId, $storeId) 
    {

        $authUserId = $request->user()->id; // Use authenticated user ID to view their submissions
        
        // Compare the UserID on URL against the current authenticated userId
        if ($authUserId != $userId) {
            return response()->json([
                'message' => 'failed',
                'errors' => 'Resource not found.'
            ], 404); 
        }
        
        // Retrieve all submissions for the specific user
        $formSubmissions = DynamicFormSubmission::with('dynamicForm')
                                                ->where('user_id', $userId) 
                                                ->where('store_id', $storeId) 
                                                ->get();

        if (empty($formSubmissions)) {
            return response()->json([
                'message' => 'failed',
                'errors' => 'Form submission not found.'
            ], 404);
        }

        $userSubmissions = [];
        foreach ($formSubmissions as $submission) {
            $dynamicForm = $submission->dynamicForm;
            if ($dynamicForm) {
                $userSubmissions[] = [
                    'id' => $dynamicForm->id,
                    'name' => $dynamicForm->name,
                    'form_description' => $dynamicForm->description,
                    'category' => [
                        'id' => $dynamicForm->subCategory->category->id?? null,
                        'name' => $dynamicForm->subCategory->category->name?? null
                    ],
                    'sub_category' => [ 
                        'id' => $dynamicForm->subCategory->id?? null,
                        'name' => $dynamicForm->subCategory->name?? null
                    ],
                    'dynamic_form_submission' => [
                        'id' => $submission->id,
                        'user_id' => $submission->user_id,
                        'dynamic_form_id' => $submission->dynamic_form_id,
                        'name' => $submission->name,
                        'about' => $submission->about,
                        'created_at' => $submission->created_at,
                        'updated_at' => $submission->updated_at
                    ]
                ];
            }
        }
                                            
        return response()->json([
            'message' =>'success',
            'body' => [
                'message' => 'Form submissions were fetched successfully.',
                'data' => $userSubmissions
            ]
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/dynamic-forms/user/{userId}/form/{formId}",
     *     summary="Get user submission by form",
     *     description="Retrieve a specific form submission for a user by form ID with detailed field values",
     *     operationId="getUserFormSubmissionByForm",
     *     tags={"Client - Dynamic Form Submissions"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="formId",
     *         in="path",
     *         description="Dynamic Form ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form submission details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Form submission details were fetched successfully."),
     *                 @OA\Property(
     *                     property="data",
     *                     type="object",
     *                     @OA\Property(property="form_id", type="integer"),
     *                     @OA\Property(property="form_name", type="string"),
     *                     @OA\Property(property="form_description", type="string"),
     *                     @OA\Property(
     *                         property="dynamic_form_pages",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="page_id", type="integer"),
     *                             @OA\Property(property="page_title", type="string"),
     *                             @OA\Property(property="sort_no", type="integer"),
     *                             @OA\Property(
     *                                 property="fields",
     *                                 type="array",
     *                                 @OA\Items(
     *                                     type="object",
     *                                     @OA\Property(property="field_id", type="integer"),
     *                                     @OA\Property(property="field_label", type="string"),
     *                                     @OA\Property(property="sort_no", type="integer"),
     *                                     @OA\Property(property="submitted_value", type="string")
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Form submission not found"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getUserDynamicFormSubmissionByFormId(Request $request, $userId, $formId)
    {
        $authUserId = $request->user()->id; // Use authenticated user ID to view their submissions

        // Compare the UserID on URL against the current authenticated userId
        if ($authUserId != $userId) {
            return response()->json([
                'message' => 'failed',
                'errors' => 'Resource not found.'
            ], 404); 
        }

        // Retrieve the submission for a specific user and form
        $submission = DynamicFormSubmission::where('user_id', $userId)
            ->where('dynamic_form_id', $formId)
            ->first();

        if (empty($submission)) {
            return response()->json([
                'message' => 'failed',
                'errors' => 'Form submission not found.'
            ], 404);
        }

        // Retrieve the dynamic form details
        $dynamicForm = DynamicForm::find($formId);
        if (!$dynamicForm) {
            return response()->json([
                'message' => 'failed',
                'errors' => 'Dynamic form not found'
            ], 404);
        }

        // Decode the JSON data back into an array
        $formData = json_decode($submission->data, true);

        $dynamicFormPages = [];

        foreach ($formData as $pageData) {
            // Find the page to get the title and sort_no
            $page = DynamicFormPage::find($pageData['dynamic_form_page_id']);

            if (!$page) {
                continue; // Skip if the page doesn't exist
            }

            $pageDetails = [
                'page_id' => $page->id,
                'page_title' => $page->title,
                'sort_no' => $page->sort_no,
                'fields' => []
            ];

            foreach ($pageData['dynamic_form_fields'] as $fieldData) {
                // Find the field to get the input_field_label
                $field = DynamicFormField::find($fieldData['field_id']);

                if ($field) {
                    $pageDetails['fields'][] = [
                        'field_id' => $field->id,
                        'field_label' => $field->input_field_label,
                        'sort_no' => $field->sort_no, // Include sort_no
                        'submitted_value' => $fieldData['value']
                    ];
                }
            }

            $dynamicFormPages[] = $pageDetails;
        }

        $submissionDetails = [
            'form_id' => $dynamicForm->id,
            'form_name' => $dynamicForm->name,
            'form_description' => $dynamicForm->description,
            'dynamic_form_pages' => $dynamicFormPages
        ];

        return response()->json([
           'message' =>'success',
            'body' => [
                'message' => 'Form submission details were fetched successfully.',
                'data' => $submissionDetails
            ], 
        ], 200);
    }
}
