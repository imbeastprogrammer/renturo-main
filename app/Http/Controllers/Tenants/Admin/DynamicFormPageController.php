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
    public function index()
    {
        $formPages = DynamicFormPage::with(['dynamicForm.subCategory', 'dynamicFormFields'])->paginate(15);

        $formPagesData = $formPages->map(function ($formPage) {

            // Check if dynamicFormFields is not null
            $fields = collect($formPage->dynamicFormFields)->map(function ($field) {
               
                // Map DynamicFormFields
                $fields = $formPage->dynamicFormFields->map(function ($field) {
                    return [
                        'id' => $field->id,
                        'user_id' => $field->user_id,
                        'dynamic_form_page_id' => $field->dynamic_form_page_id,
                        'input_field_label' => $field->input_field_label,
                        'input_field_name' => $field->input_field_name,
                        'input_field_type' => $field->input_field_type,
                        'is_required' => $field->is_required,
                        'is_multiple' => $field->is_multiple,
                        'data' => $field->data,
                    ];
                });
            });
            
            // Extract DynamicForm data if available
            $form = optional($formPage->dynamicForm)->toArray();

            // Check if DynamicForm has fields and map them
            if ($formPage->dynamicForm && $formPage->dynamicForm->dynamicFields) {
                $form['fields'] = $formPage->dynamicForm->dynamicFields->map(function ($dynamicField) {
                    return [
                        'id' => $dynamicField->id,
                        'user_id' => $dynamicField->user_id,
                        'dynamic_form_page_id' => $dynamicField->dynamic_form_page_id,
                        'input_field_label' => $dynamicField->input_field_label,
                        'input_field_name' => $dynamicField->input_field_name,
                        'input_field_type' => $dynamicField->input_field_type,
                        'is_required' => $dynamicField->is_required,
                        'is_multiple' => $dynamicField->is_multiple,
                        'data' => $dynamicField->data,
                    ];
                });
            }

            // Return the mapped formPage data along with DynamicForm data
            return [
                'id' => $formPage->id,
                'title' => $formPage->title,
                'created_at' => $formPage->created_at,
                'updated_at' => $formPage->updated_at,
                'deleted_at' => $formPage->deleted_at,
                'dynamic_form' => $form,
                'dynamic_form_fields' => $fields,
            ];
        });

        $formPages->setCollection($formPagesData);

        return response()->json($formPages);
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
        $formPage = DynamicFormPage::create($request->validated());

        return response()->json([
            'data' => $formPage,
            'message' => 'Form page created.'
        ], 201);
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
    public function update(UpdateFormPageRequest $request, $id)
    {
        // Utilize route model binding for cleaner code and direct access
        $formPage = DynamicFormPage::find($id);

        // Mass assignment for updating data
        $formPage->update($request->only(['title']));

        return response()->json($formPage);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $formPage = DynamicFormPage::find($id);

        if (!$formPage) {
            return response()->json([
                "status" => "failed",
                'message' => 'Dynamic form page not found',
                "error" => [
                    "errorCode" => "FORM_PAGE_NOT_FOUND",
                    "errorDescription" => "The dynamic form page ID you are looking for could not be found."
                ]
            ], 404); 
        }

        $formPage->delete();

        // Return the created category along with a success message
        return response()->json([
            "status" => "success",
            'message' => 'Dynamic form page was successfully deleted.',
        ], 200); 
    }

    public function restore($id) {

        $record = DynamicFormPage::withTrashed()->where('id', $id)->first();

        if (!$record) {
            return response()->json([
                "status" => "failed",
                'message' => 'Dynamic form page not found',
                "error" => [
                    "errorCode" => "FORM_PAGE_NOT_FOUND",
                    "errorDescription" => "The dynamic form page ID you are looking for could not be found."
                ]
            ], 404); 
        }

        $record->restore();
        return response()->json([
            "status" => "success",
            'message' => 'Dynamic form was successfully restored.',
        ], 200); 
    }

    public function sortFormPages(Request $request)
    {
        $request->validate([
            'form_page_id.*' => 'required'
        ]);

        foreach ($request->form_page_id as $key => $formPageId) {
            DynamicFormPage::where('id', $formPageId)
                ->where('user_id', Auth::user()->id)
                ->update([
                    'sort_no' => ++$key
                ]);
        }

        return response()->json(['message' => 'Form pages sorted.']);
    }
}
