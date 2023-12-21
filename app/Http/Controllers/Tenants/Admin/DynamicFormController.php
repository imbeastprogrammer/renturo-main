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
    public function index()
    {

        $perPage = 15;

        $dynamicForms = DynamicForm::with('subCategory.category')->paginate($perPage);
        $subCategories = SubCategory::all();
        $categories = Category::all();

        // Format the response
        $response = $dynamicForms->getCollection()->map(function ($form) {
            return [
                'id' => $form->id,
                'name' => $form->name,
                'description' => $form->description,
                'subcategory' => [
                    'id' => $form->subCategory ? $form->subCategory->id : null,
                    'name' => $form->subCategory ? $form->subCategory->name : 'No Subcategory',
                    'category' => [
                        'id' => $form->subCategory && $form->subCategory->category ? $form->subCategory->category->id : null,
                        'name' => $form->subCategory && $form->subCategory->category ? $form->subCategory->category->name : 'No Category'
                    ],
                ],
                'created_at' => $form->created_at,
                'updated_at' => $form->updated_at,
                'deleted_at' => $form->deleted_at
            ];
        });

        $paginated_reponse = [
            "status" => "success",
            'message' => 'Dynamic Form was successfully fetched.',
            'data' => $response,
            'pagination' => [
                'total' => $dynamicForms->total(),
                'perPage' => $dynamicForms->perPage(),
                'currentPage' => $dynamicForms->currentPage(),
                'lastPage' => $dynamicForms->lastPage(),
            ],
        ];

        return Inertia::render('tenants/admin/post-management/dynamic-forms/index', ['dynamicForms' => $paginated_reponse, 'subCategories' => $subCategories, 'categories' => $categories]);
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
        $dynamicFormPage = DynamicForm::create($request->validated());

        return response()->json([
            "status" => "success",
            'message' => 'Dynamic Form created successfully.',
            'data' => $dynamicFormPage,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DynamicForm  $dynamicForm
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Fetch the dynamic form by its ID along with its related subcategory, category, pages, and fields
        $dynamicForm = DynamicForm::with([
            'subCategory.category',
            'dynamicFormPages.dynamicFormFields',
        ])->find($id);

        // Check if the category was found
        if (!$dynamicForm) {
            return response()->json([
                "status" => "failed",
                'message' => 'Dynamic form not found',
                "error" => [
                    "errorCode" => "FORM_NOT_FOUND",
                    "errorDescription" => "The dynamic form ID you are looking for could not be found."
                ]
            ], 404);
        }

        // Format the response
        $dynamicForm = [
            'id' => $dynamicForm->id,
            'name' => $dynamicForm->name,
            'description' => $dynamicForm->description,
            'subcategory' => $dynamicForm->subCategory ? [
                'id' => $dynamicForm->subCategory->id,
                'name' => $dynamicForm->subCategory->name,
                'category' => $dynamicForm->subCategory->category ? [
                    'id' => $dynamicForm->subCategory->category->id,
                    'name' => $dynamicForm->subCategory->category->name
                ] : null
            ] : null,
            'formPages' => $dynamicForm->dynamicFormPages->map(function ($page) {
                return [
                    'id' => $page->id,
                    'title' => $page->title,
                    'dynamicFormFields' => $page->dynamicFormFields->map(function ($field) {
                        return [
                            'id' => $field->id,
                            'label' => $field->label,
                            'type' => $field->type,
                            // Add other field attributes as needed
                        ];
                    })
                ];
            }),
            'created_at' => $dynamicForm->created_at,
            'updated_at' => $dynamicForm->updated_at,
            'deleted_at' => $dynamicForm->deleted_at
        ];

        // Return the created category along with a success message
        return response()->json([
            "status" => "success",
            'message' => 'Form was successfully fetched.',
            'data' => $dynamicForm,
        ], 200);
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
        $dynamicForm = DynamicForm::find($id);
        $dynamicForm->update($request->validated());

        return response()->json($dynamicForm);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DynamicForm  $dynamicForm
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $dynamicForm = DynamicForm::find($id);

        if (!$dynamicForm) {
            return response()->json([
                "status" => "failed",
                'message' => 'Dynamic form not found',
                "error" => [
                    "errorCode" => "FORM_NOT_FOUND",
                    "errorDescription" => "The dynamic form ID you are looking for could not be found."
                ]
            ], 404);
        }

        $dynamicForm->delete();

        // Return the created category along with a success message
        return response()->json([
            "status" => "success",
            'message' => 'Dynamic form was successfully deleted.',
        ], 200);
    }

    public function restore($id)
    {

        $record = DynamicForm::withTrashed()->where('id', $id)->first();

        if (!$record) {
            return response()->json([
                "status" => "failed",
                'message' => 'Dynamic form not found',
                "error" => [
                    "errorCode" => "FORM_NOT_FOUND",
                    "errorDescription" => "The dynamic form ID you are looking for could not be found."
                ]
            ], 404);
        }

        $record->restore();
        return response()->json([
            "status" => "success",
            'message' => 'Dynamic form was successfully restored.',
        ], 200);
    }

    public function getFormPagesAndFields($id)
    {
        // Retrieve the dynamic form with its pages and fields
        $dynamicForm = DynamicForm::with('dynamicFormPages.dynamicFormFields')
            ->where('id', $id)
            ->first();

        if ($dynamicForm) {
            // Return the dynamic form with its nested relations
            return response()->json($dynamicForm);
        } else {
            // Handle the case where the dynamic form is not found
            return response()->json([
                "status" => "failed",
                'message' => 'Dynamic form not found',
                "error" => [
                    "errorCode" => "FORM_NOT_FOUND",
                    "errorDescription" => "The dynamic form ID you are looking for could not be found."
                ]
            ], 404);
        }
    }

    public function updateFormPagesAndFields(Request $request, $id)
    {
        // Start a transaction
        DB::beginTransaction();

        try {

            // Find the DynamicForm
            $dynamicForm = DynamicForm::findOrFail($id);

            // Update DynamicForm details
            $dynamicForm->update($request->only(['name', 'description']));

            // Retrieve all current page titles for the dynamic form
            $existingPageTitles = $dynamicForm->dynamicFormPages->pluck('title')->toArray();

            foreach ($request->input('dynamic_form_pages') as $index => $pageData) {

                // Check for duplicate title in new pages
                if (!isset($pageData['id']) && in_array($pageData['title'], $existingPageTitles)) {
                    throw new \Exception("Duplicate page title: " . $pageData['title']);
                }

                if (isset($pageData['id'])) {
                    // Update existing DynamicFormPage
                    $formPage = $dynamicForm->dynamicFormPages()->findOrFail($pageData['id']);
                    $formPage->update([
                        'title' => $pageData['title'],
                        'sort_no' => $index + 1
                    ]);
                } else {
                    // Create new DynamicFormPage
                    $formPage = $dynamicForm->dynamicFormPages()->create([
                        'title' => $pageData['title'],
                        'sort_no' => $index + 1
                        // Other necessary fields can be added here
                    ]);
                    $existingPageTitles[] = $pageData['title']; // Add new title to existing titles array
                }
            }
            DB::commit();
            return response()->json(['message' => 'Dynamic form updated successfully']);
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollBack();
            return response()->json(['message' => 'Failed to update dynamic form', 'error' => $e->getMessage()], 500);
        }
    }
}
