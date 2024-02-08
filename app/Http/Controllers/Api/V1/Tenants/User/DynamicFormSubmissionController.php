<?php

namespace App\Http\Controllers\Api\V1\Tenants\User;


use App\Http\Controllers\Controller;
use App\Models\DynamicFormSubmission;
use App\Models\DynamicForm;
use App\Models\DynamicFormPage;
use App\Models\DynamicFormField;
use Illuminate\Http\Request;

class DynamicFormSubmissionController extends Controller
{
    public function index()
    {
        $submissions = DynamicFormSubmission::with(['dynamicForm', 'store'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $submissions,
        ]);
    }

    public function show(Request $request, $id)
    {
        $submission = DynamicFormSubmission::where('id', $id)->first();

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
}
