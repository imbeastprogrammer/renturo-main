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
        $formPages = DynamicFormPage::with('subCategory', 'dynamicFormFields')->get();

        // Prepare the data for response, including sub_category_id and sub_category name
        $formPagesData = $formPages->map(function ($formPage) {

            // Extract dynamicFormFields IDs or other needed data
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
            
            return [
                'id' => $formPage->id,
                'sub_category_id' => $formPage->subCategory->id ?? null,
                'sub_category_name' => $formPage->subCategory->name ?? 'No SubCategory',
                'title' => $formPage->title,
                'description' => $formPage->description,
                'created_at' => $formPage->created_at,
                'updated_at' => $formPage->updated_at,
                'deleted_at' => $formPage->deleted_at,
                'form_fields' => $fields,
            ];
        });

        return response()->json(['form_pages' => $formPagesData]);
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

        return response()->json([
            'data' => $dynamicFormPage,
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
        $formPage = DynamicFormPage::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->firstOrFail();

        $formPage->update([
            'title' => $request->title
        ]);

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
        $formPage = DynamicFormPage::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->firstOrFail();

        $formPage->delete();

        return response()->json(['message' => 'Form page deleted.']);
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
