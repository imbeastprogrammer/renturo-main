<?php

namespace App\Http\Controllers\API\V1\Tenants\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DynamicForm;

class DynamicFormController extends Controller
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $form = DynamicForm::with('dynamicFormPages.dynamicFormFields')->findOrFail($id);

        $transformedData = [
            'dynamic_form_id' => $form->id,
            'name' => $form->name,
            'description' => $form->description,
            'dynamic_form_pages' => $form->dynamicFormPages ? $form->dynamicFormPages->map(function ($page) {
                return [
                    'dynamic_form_page_id' => $page->id,
                    'title' => $page->title,
                    'sort_no' => $page->sort_no,
                    'dynamic_form_fields' => $page->dynamicFormFields ? $page->dynamicFormFields->map(function ($field) {
                        return [
                            'field_id' => $field->id,
                            'input_field_label' => $field->input_field_label,
                            'input_field_name' => $field->input_field_name,
                            'input_field_type' => $field->input_field_type,
                            'is_required' => $field->is_required,
                            'sort_no' => $field->sort_no,
                            'data' => $field->data,
                            'value' => '' // set to empty string
                        ];
                    }) : []
                ];
            }) : []
        ];

        return response()->json([
            'message' => 'success',
            'body' => [
                'message' => 'Form was fetched successfully.',
                'data' => $transformedData,
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
