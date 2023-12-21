<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Requests\Tenants\Admin\FormBuilder\StoreFormRequest;
use App\Http\Requests\Tenants\Admin\FormBuilder\UpdateFormRequest;
use App\Http\Controllers\Controller;
use App\Models\DynamicForm;
use App\Models\SubCategory;
use Illuminate\Http\Request;
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

        return Inertia::render('tenants/admin/post-management/dynamic-forms/index', ['dynamic_forms' => $paginated_reponse, 'sub_categories' => $subCategories]);
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
        // Fetch the category by its ID
        $dynamicForm = DynamicForm::with('subCategory.category')
            ->find($id);

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
        $response = [
            'id' => $dynamicForm->id,
            'name' => $dynamicForm->name,
            'description' => $dynamicForm->description,
            'subcategory' => [
                'id' => $dynamicForm->subCategory ? $dynamicForm->subCategory->id : null,
                'name' => $dynamicForm->subCategory ? $dynamicForm->subCategory->name : 'No Subcategory',
                'category' => [
                    'id' => $dynamicForm->subCategory && $dynamicForm->subCategory->category ? $dynamicForm->subCategory->category->id : null,
                    'name' => $dynamicForm->subCategory && $dynamicForm->subCategory->category ? $dynamicForm->subCategory->category->name : 'No Category'
                ],
            ],
            'created_at' => $dynamicForm->created_at,
            'updated_at' => $dynamicForm->updated_at,
            'deleted_at' => $dynamicForm->deleted_at
        ];

        // Return the created category along with a success message
        return response()->json([
            "status" => "success",
            'message' => 'Form was successfully fetched.',
            'data' => $response,
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
}
