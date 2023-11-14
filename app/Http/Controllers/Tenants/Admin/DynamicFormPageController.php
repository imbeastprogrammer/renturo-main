<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Tenants\Admin\FormBuilder\StoreFormPageRequest;
use App\Http\Requests\Tenants\Admin\FormBuilder\UpdateFormPageRequest;
use App\Models\DynamicFormPage;

class DynamicFormPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $formPages = DynamicFormPage::all();

        return response()->json(['form_pages' => $formPages]);
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
        $formPage = DynamicFormPage::findOrFail($id);

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
        //
    }

    public function sortFormPages(Request $request)
    {
        $request->validate([
            'form_page_id.*' => 'required'
        ]);

        foreach ($request->form_page_id as $key => $formPageId) {
            DynamicFormPage::where('id', $formPageId)->update([
                'sort_no' => ++$key
            ]);
        }
    }
}
