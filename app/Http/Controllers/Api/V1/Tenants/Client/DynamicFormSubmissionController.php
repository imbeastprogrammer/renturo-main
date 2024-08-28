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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
    
    public function getUserDynamicFormSubmissions(Request $request, $userId) 
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
