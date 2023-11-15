<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Tenants\Admin\FormBuilder\StoreFormFieldRequest;
use App\Http\Requests\Tenants\Admin\FormBuilder\UpdateFormFieldRequest;
use App\Models\DynamicFormField;
use App\Models\DynamicFormPage;
use Auth;

class DynamicFormFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $formPage = DynamicFormPage::where('id', $request->form_page_id)->first();

        return response()->json(['form_page' => $formPage]);
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
        DynamicFormField::create($request->validated());

        return response()->json(['message' => 'Form field created']);
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
        $formField = DynamicFormField::where('id', $id)
            ->where('dynamic_form_page_id', $request->dynamic_form_page_id)
            ->where('user_id', Auth::user()->id)
            ->firstOrFail();

        $formField->update($request->validated());

        return response()->json($formField);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $formField = DynamicFormField::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->firstOrFail();

        $formField->delete();

        return response()->json(['message' => 'Form field deleted.']);
    }

    public function sortFormFields(Request $request)
    {
        $request->validate([
            'form_page_id' => 'required',
            'form_field_id.*' => 'required'
        ]);

        foreach ($request->form_field_id as $key => $formFieldId) {
            DynamicFormField::where('id', $formFieldId)
                ->where('dynamic_form_page_id', $request->form_page_id)
                ->where('user_id', Auth::user()->id)
                ->update([
                    'sort_no' => ++$key
                ]);
        }

        return response()->json(['message' => 'Form fields sorted.']);
    }
}
