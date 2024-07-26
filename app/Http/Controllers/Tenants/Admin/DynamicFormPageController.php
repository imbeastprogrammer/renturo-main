<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Tenants\Admin\FormBuilder\StoreFormPageRequest;
use App\Http\Requests\Tenants\Admin\FormBuilder\UpdateFormPageRequest;
use App\Models\DynamicFormPage;
use Auth;

class DynamicFormPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        return response()->json([
            "status" => "success",
            "message" => "Dynamic form page was successfully fetched.",
            "data" => 'index'
        ], 201);
        $formPages = DynamicFormPage::with([
            "dynamicForm.subCategory", 
            "dynamicFormFields"
        ])->paginate(15);

        $formPagesData = $formPages->map(function ($formPage) {

            // Check if dynamicFormFields is not null
            $fields = collect($formPage->dynamicFormFields)->map(function ($field) {
               
                // Map DynamicFormFields
                $fields = $formPage->dynamicFormFields->map(function ($field) {
                    return [
                        "id" => $field->id,
                        "user_id" => $field->user_id,
                        "dynamic_form_page_id" => $field->dynamic_form_page_id,
                        "input_field_label" => $field->input_field_label,
                        "input_field_name" => $field->input_field_name,
                        "input_field_type" => $field->input_field_type,
                        "is_required" => $field->is_required,
                        "data" => $field->data,
                    ];
                });
            });
            
            // Extract DynamicForm data if available
            $form = optional($formPage->dynamicForm)->toArray();

            // Check if DynamicForm has fields and map them
            if ($formPage->dynamicForm && $formPage->dynamicForm->dynamicFields) {
                $form["fields"] = $formPage->dynamicForm->dynamicFields->map(function ($dynamicField) {
                    return [
                        "id" => $dynamicField->id,
                        "user_id" => $dynamicField->user_id,
                        "dynamic_form_page_id" => $dynamicField->dynamic_form_page_id,
                        "input_field_label" => $dynamicField->input_field_label,
                        "input_field_name" => $dynamicField->input_field_name,
                        "input_field_type" => $dynamicField->input_field_type,
                        "is_required" => $dynamicField->is_required,
                        "data" => $dynamicField->data,
                    ];
                });
            }

            // Return the mapped formPage data along with DynamicForm data
            return [
                "id" => $formPage->id,
                "title" => $formPage->title,
                "created_at" => $formPage->created_at,
                "updated_at" => $formPage->updated_at,
                "deleted_at" => $formPage->deleted_at,
                "dynamic_form" => $form,
                "dynamic_form_fields" => $fields,
            ];
        });

        $formPages->setCollection($formPagesData);

        // For JSON request, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Dynamic form page was successfully fetched.",
                "data" => $formPages,
            ], 201);
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
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
    public function store(StoreFormPageRequest $request)
    {
        $dynamicFormPage = DynamicFormPage::create($request->validated());

        // For JSON request, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Dynamic form page was successfully created.",
                "data" => $dynamicFormPage,
            ], 201);
        }
       
        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return redirect()->back()->with("success", "Dynamic form page was successfully created.");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
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
    public function update(UpdateFormPageRequest $request, $id)
    {
        #TODO: Add update validation to prevent duplicate titles

        // Utilize route model binding for cleaner code and direct access
        $dynamicFormPage = DynamicFormPage::findOrFail($id);
        $dynamicFormPage->update($request->only(["title"]));

        // For JSON request, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Dynamic form page was successfully updated.",
                "data" => $dynamicFormPage->fresh(),
            ], 200);
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return redirect()->back()->with("success", "Dynamic form page was successfully updated.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $dynamicFormPage = DynamicFormPage::findOrFail($id);
        $dynamicFormPage->delete();

        // For JSON request, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Dynamic form page was successfully deleted.",
            ], 200); 
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return redirect()->back()->with("success", "Dynamic form page was successfully deleted.");
    }

    public function restore(Request $request, $id) {

        $record = DynamicFormPage::withTrashed()->findOrFail($id);
        $record->restore();

        // For JSON request, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Dynamic form page was successfully restored.",
            ], 200);
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return redirect()->back()->with("success", "Dynamic form page was successfully restored.");
    }

    public function search(Request $request)
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
        
        // For JSON request, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Dynamic form page was successfully fetched.",
                "data" => $dynamicFormPages
            ], 200); 
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return redirect()->back()->with("success", "Dynamic form page was successfully fetched.");
    }

}
