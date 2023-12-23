<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Requests\Tenants\Admin\FormBuilder\StoreFormRequest;
use App\Http\Requests\Tenants\Admin\FormBuilder\UpdateFormRequest;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\DynamicForm;
use Inertia\Inertia;

class DynamicFormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = 15;

        $dynamicForms = DynamicForm::with("subCategory.category")->paginate($perPage);

        // Format the response
        $response = $dynamicForms->getCollection()->map(function ($form) {
            return [
                "id" => $form->id,
                "name" => $form->name,
                "description" => $form->description,
                "subcategory" => [
                    "id" => $form->subCategory ? $form->subCategory->id : null,
                    "name" => $form->subCategory ? $form->subCategory->name : "No Subcategory",
                    "category" => [
                        "id" => $form->subCategory && $form->subCategory->category ? $form->subCategory->category->id : null,
                        "name" => $form->subCategory && $form->subCategory->category ? $form->subCategory->category->name : "No Category"
                    ],
                ],
                "created_at" => $form->created_at,
                "updated_at" => $form->updated_at,
                "deleted_at" => $form->deleted_at
            ];
        });

        $dynamicForms = [
            "status" => "success",
            "message" => "Dynamic Form was successfully fetched.",
            "data" => $response,
            "pagination" => [
                "total" => $dynamicForms->total(),
                "perPage" => $dynamicForms->perPage(),
                "currentPage" => $dynamicForms->currentPage(),
                "lastPage" => $dynamicForms->lastPage(),
            ],
        ];

        // Fetch list of categories
        $categories = Category::all();

        // Fetch list of categories
        $subCategories = SubCategory::all();

        // For JSON request, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Dynamic form was successfully fetched.",
                "data" => $dynamicForms,
            ], 201);
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return Inertia::render("tenants/admin/post-management/dynamic-forms/index", ["dynamicForms" => $dynamicForms, "subCategories" => $subCategories, "categories" => $categories]);
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
    public function store(StoreFormRequest $request)
    {
        $dynamicForm = DynamicForm::create($request->validated());

        // For JSON request, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Dynamic form was successfully created.",
                "data" => $dynamicForm,
            ], 201);
        }
       
        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return redirect()->back()->with("success", "Dynamic form was successfully created.");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DynamicForm  $dynamicForm
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // Fetch the dynamic form by its ID along with its related subcategory, category, pages, and fields
        $dynamicForm = DynamicForm::with([
            "subCategory.category",
            "dynamicFormPages.dynamicFormFields",
        ])->findOrFail($id);

        // Format the response
        $dynamicForm = [
            "id" => $dynamicForm->id,
            "name" => $dynamicForm->name,
            "description" => $dynamicForm->description,
            "subcategory" => $dynamicForm->subCategory ? [
                "id" => $dynamicForm->subCategory->id,
                "name" => $dynamicForm->subCategory->name,
                "category" => $dynamicForm->subCategory->category ? [
                    "id" => $dynamicForm->subCategory->category->id,
                    "name" => $dynamicForm->subCategory->category->name
                ] : null
            ] : null,
            "dynamicFormPages" => $dynamicForm->dynamicFormPages->map(function ($page) {
                return [
                    "id" => $page->id,
                    "title" => $page->title,
                    "dynamicFormFields" => $page->dynamicFormFields->map(function ($field) {
                        return [
                            "id" => $field->id,
                            "label" => $field->label,
                            "type" => $field->type,
                            // Add other field attributes as needed
                        ];
                    })
                ];
            }),
            "created_at" => $dynamicForm->created_at,
            "updated_at" => $dynamicForm->updated_at,
            "deleted_at" => $dynamicForm->deleted_at
        ];

        // For JSON request, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Form was successfully fetched.",
                "data" => $dynamicForm,
            ], 200);
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DynamicForm  $dynamicForm
     * @return \Illuminate\Http\Response
     */
    public function edit(DynamicForm $dynamicForm)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DynamicForm  $dynamicForm
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFormRequest $request, $id)
    {
        // Update specific resource
        $dynamicForm = DynamicForm::findOrFail($id);
        $dynamicForm->update($request->validated());

        // For JSON request, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Dynamic form was successfully updated.",
                "data" => $dynamicForm->fresh(),
            ], 200);
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return redirect()->back()->with("success", "Dynamic form was successfully updated.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DynamicForm  $dynamicForm
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $dynamicForm = DynamicForm::findOrFail($id);
        $dynamicForm->delete();

        // For JSON request, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Dynamic form was successfully deleted.",
            ], 200); 
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return redirect()->back()->with("success", "Dynamic form was successfully deleted.");
    }

    public function restore(Request $request, $id)
    {
        $record = DynamicForm::withTrashed()->findOrFail($id);
        $record->restore();

        // For JSON request, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Dynamic form was successfully restored.",
            ], 200);
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return redirect()->back()->with("success", "Dynamic form was successfully restored.");
    }

    public function getFormPagesAndFields(Request $request, $id)
    {
        // Retrieve the dynamic form with its pages and fields
        $dynamicForm = DynamicForm::with("dynamicFormPages.dynamicFormFields")
            ->findOrFail($id);

        // For JSON request, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Form was successfully fetched.",
                "data" => $dynamicForm,
            ], 200);
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
    }

    public function updateFormPagesAndFields(Request $request, $id)
    {
        // Start a transaction
        DB::beginTransaction();

        try {

            // Find the DynamicForm
            $dynamicForm = DynamicForm::findOrFail($id);

            // Update DynamicForm details
            $dynamicForm->update($request->only(["name", "description"]));

            // Retrieve all current page titles for the dynamic form
            // $existingPageTitles = $dynamicForm->dynamicFormPages->pluck("title")->toArray();

            // Retrieve all current page titles with their IDs for the dynamic form
            $existingPages = $dynamicForm->dynamicFormPages->pluck("title", "id");

            foreach ($request->input("dynamic_form_pages") as $index => $pageData) {
                
                // Check for duplicate title in new pages
                if (!isset($pageData["id"]) && in_array($pageData["title"], $existingPageTitles)) {
                    throw new \Exception("Duplicate page title: " . $pageData["title"]);
                }

                if (isset($pageData["id"])) {
                    // Update existing DynamicFormPage
                    $formPage = $dynamicForm->dynamicFormPages()->findOrFail($pageData["id"]);
                    $formPage->update([
                        "title" => $pageData["title"],
                        "sort_no" => $index + 1
                    ]);

                } else {
                    // Create new DynamicFormPage
                    $formPage = $dynamicForm->dynamicFormPages()->create([
                        "title" => $pageData["title"],
                        "sort_no" => $index + 1
                    ]);
                }
           }
            DB::commit();
            return response()->json(["message" => "Dynamic form updated successfully"]);
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollBack();
            return response()->json(["message" => "Failed to update dynamic form", "error" => $e->getMessage()], 500);
        }
    }
}
