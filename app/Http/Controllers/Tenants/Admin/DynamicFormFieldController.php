<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Tenants\Admin\FormBuilder\StoreFormFieldRequest;
use App\Http\Requests\Tenants\Admin\FormBuilder\UpdateFormFieldRequest;
use Illuminate\Support\Facades\DB;
use App\Models\DynamicFormField;
use App\Models\DynamicFormPage;

class DynamicFormFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = DynamicFormPage::query();

        $searchTerm = $request->form_page_search;

        // If a search term for DynamicFormPages is provided
        if ($request->has("form_page_search")) {
           
            // Check if the search term is numeric, and search by ID
            if (is_numeric($searchTerm)) {
                $query->where("id", $searchTerm);
            } else {
                // Otherwise, search by title
                $query->where("title", "like", "%" . $searchTerm . "%");
            }
        }

        $dynamicFormPages = $query->with("dynamicForm.subCategory", "dynamicFormFields")->get();
       
        // For JSON requests, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Dynamic form page with fields was successfully fetched.",
                "data" => $dynamicFormPages
            ], 201);
        }
       
        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return redirect()->back()->with("success", "Dynamic form page with fields was successfully fetched.");
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
    public function store(StoreFormFieldRequest $request)
    {
        $fields = $request->input("fields"); // Assuming "fields" is the key that contains the array of fields
        
        // Start a transaction
        DB::beginTransaction();

        try {
            foreach ($fields as $index => $fieldData) {
                
                // Validate $fieldData here if necessary
                DynamicFormField::create([
                    "dynamic_form_page_id" => $request->dynamic_form_page_id,
                    "input_field_label" => $fieldData["input_field_label"],
                    "input_field_name" => $fieldData["input_field_name"],
                    "input_field_type" => $fieldData["input_field_type"],
                    "is_required" => $fieldData["is_required"],
                    "is_multiple" => $fieldData["is_multiple"], 
                    "data" => $fieldData["data"] ?? null,
                    "sort_no" => $index + 1 // Using the array index as the sort number, incremented by 1
                ]);
            }
    
            // Commit the transaction
            DB::commit();

            // For JSON requests, return a success response
            if ($request->expectsJson()) {
                return response()->json([
                    "status" => "success",
                    "message" => "Dynamic form fields was successfully created."
                ], 201);
            }
           
            // For non-JSON requests, return an Inertia response
            // Redirect to the desired page and pass the necessary data
            return redirect()->back()->with("success", "Dynamic form fields was successfully created.");

        } catch (\Exception $e) {
            // An error occurred; rollback the transaction
            DB::rollBack();

            // For JSON requests, return a success response
            if ($request->expectsJson()) {
                return response()->json([
                    "status" => "success",
                    "message" => "Failed to save fields." . $e->getMessage()
                ], 500);
            }
           
            // For non-JSON requests, return an Inertia response
            // Redirect to the desired page and pass the necessary data
            return redirect()->back()->with("success", "Failed to save fields." . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(UpdateFormFieldRequest $request, $id)
    {
        $fields = $request->input("fields"); // Assuming "fields" is the key that contains the array of fields
        $fieldIds = array_filter(array_column($fields, "id")); // Extract all field IDs from the request

        // Start a transaction
        DB::beginTransaction();

        try {

            // Remove fields not included in the request
            DynamicFormField::where("dynamic_form_page_id", $request->dynamic_form_page_id)
            ->whereNotIn("id", $fieldIds)
            ->delete();
            
            foreach ($fields as $index => $fieldData) {

                if (isset($fieldData["id"])) {

                    // Update existing field
                    $formField = DynamicFormField::where("id", $fieldData["id"])
                                                 ->where("dynamic_form_page_id", $request->dynamic_form_page_id)
                                                 ->first();
                    
                    if ($formField) {
                        $formField->update([
                            "input_field_label" => $fieldData["input_field_label"],
                            "input_field_name" => $fieldData["input_field_name"],
                            "input_field_type" => $fieldData["input_field_type"],
                            "is_required" => $fieldData["is_required"],
                            "is_multiple" => $fieldData["is_multiple"], 
                            "data" => $fieldData["data"] ?? null,
                            "sort_no" => $index + 1 // Using the array index as the sort number, incremented by 1
                        ]);
                    } else {

                        // Handle the case where the field does not exist
                        return response()->json(["message" => "Failed to update fields", "error" => "Field id {$fieldData["id"]} does not exists."], 500);
                    }
                } else {
                    // Validate $fieldData here if necessary
                    DynamicFormField::create([
                        "dynamic_form_page_id" => $request->dynamic_form_page_id,
                        "input_field_label" => $fieldData["input_field_label"],
                        "input_field_name" => $fieldData["input_field_name"],
                        "input_field_type" => $fieldData["input_field_type"],
                        "is_required" => $fieldData["is_required"],
                        "is_multiple" => $fieldData["is_multiple"], 
                        "data" => $fieldData["data"] ?? null,
                        "sort_no" => $index + 1 // Using the array index as the sort number, incremented by 1
                    ]);
                }
            }
        
            // Commit the transaction
            DB::commit();

            // For JSON requests, return a success response
            if ($request->expectsJson()) {
                return response()->json([
                    "status" => "success",
                    "message" => "Dynamic form fields was successfully updated."
                ], 200);
            }

            // For non-JSON requests, return an Inertia response
            // Redirect to the desired page and pass the necessary data
            return redirect()->back()->with("success", "Fields updated successfully.");
        
        } catch (\Exception $e) {
            // An error occurred; rollback the transaction
            DB::rollBack();

            // For JSON request, return a success response
            if ($request->expectsJson()) {
                return response()->json([
                    "status" => "success",
                    "message" => "Failed to update fields." . $e->getMessage()
                ], 500);
            }

            // For non-JSON requests, return an Inertia response
            // Redirect to the desired page and pass the necessary data
            return redirect()->back()->with("success", "Failed to update fields." . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $dynamicFormField = DynamicFormField::findOrFail($id);
        $dynamicFormField->delete();

        // For JSON request, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Dynamic form field was successfully deleted.",
            ], 200); 
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return redirect()->back()->with("success", "Dynamic form field was successfully deleted.");
    }

    public function restore(Request $request, $id) {

        $record = DynamicFormField::withTrashed()->findOrFail($id);
        $record->restore();

         // For JSON request, return a success response
         if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Dynamic form field was successfully restored.",
            ], 200); 
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return redirect()->back()->with("success", "Dynamic form field was successfully restored.");
    }
}
