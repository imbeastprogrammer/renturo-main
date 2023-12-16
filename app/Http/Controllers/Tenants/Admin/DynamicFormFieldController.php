<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Tenants\Admin\FormBuilder\StoreFormFieldRequest;
use App\Http\Requests\Tenants\Admin\FormBuilder\UpdateFormFieldRequest;
use Illuminate\Support\Facades\DB;
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
        $fields = $request->input('fields'); // Assuming 'fields' is the key that contains the array of fields
        
        // Start a transaction
        DB::beginTransaction();

        try {
            foreach ($fields as $index => $fieldData) {
                
                // Validate $fieldData here if necessary
                DynamicFormField::create([
                    'dynamic_form_page_id' => $request->dynamic_form_page_id,
                    'input_field_label' => $fieldData['input_field_label'],
                    'input_field_name' => $fieldData['input_field_name'],
                    'input_field_type' => $fieldData['input_field_type'],
                    'is_required' => $fieldData['is_required'],
                    'is_multiple' => $fieldData['is_multiple'], 
                    'data' => $fieldData['data'] ?? null,
                    'sort_no' => $index + 1 // Using the array index as the sort number, incremented by 1
                ]);
            }
    
            // Commit the transaction
            DB::commit();
            return response()->json(
                ['message' => 'Fields saved successfully'], 200);
        
        } catch (\Exception $e) {
            // An error occurred; rollback the transaction
            DB::rollBack();
            return response()->json(['message' => 'Failed to save fields', 'error' => $e->getMessage()], 500);
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
        $fields = $request->input('fields'); // Assuming 'fields' is the key that contains the array of fields
        $fieldIds = array_filter(array_column($fields, 'id')); // Extract all field IDs from the request

        // Start a transaction
        DB::beginTransaction();

        try {

            // Remove fields not included in the request
            DynamicFormField::where('dynamic_form_page_id', $request->dynamic_form_page_id)
            ->whereNotIn('id', $fieldIds)
            ->delete();
            
            foreach ($fields as $index => $fieldData) {

                if (isset($fieldData['id'])) {

                    // Update existing field
                    $formField = DynamicFormField::where('id', $fieldData['id'])
                                                 ->where('dynamic_form_page_id', $request->dynamic_form_page_id)
                                                 ->first();
                    
                    if ($formField) {
                        $formField->update([
                            'input_field_label' => $fieldData['input_field_label'],
                            'input_field_name' => $fieldData['input_field_name'],
                            'input_field_type' => $fieldData['input_field_type'],
                            'is_required' => $fieldData['is_required'],
                            'is_multiple' => $fieldData['is_multiple'], 
                            'data' => $fieldData['data'] ?? null,
                            'sort_no' => $index + 1 // Using the array index as the sort number, incremented by 1
                        ]);
                    } else {

                        // Handle the case where the field does not exist
                        return response()->json(['message' => 'Failed to update fields', 'error' => "Field id {$fieldData['id']} does not exists."], 500);
                    }
                } else {
                    // Validate $fieldData here if necessary
                    DynamicFormField::create([
                        'dynamic_form_page_id' => $request->dynamic_form_page_id,
                        'input_field_label' => $fieldData['input_field_label'],
                        'input_field_name' => $fieldData['input_field_name'],
                        'input_field_type' => $fieldData['input_field_type'],
                        'is_required' => $fieldData['is_required'],
                        'is_multiple' => $fieldData['is_multiple'], 
                        'data' => $fieldData['data'] ?? null,
                        'sort_no' => $index + 1 // Using the array index as the sort number, incremented by 1
                    ]);
                }
            }
        
            // Commit the transaction
            DB::commit();
            return response()->json(['message' => 'Fields updated successfully'], 200);
        
        } catch (\Exception $e) {
            // An error occurred; rollback the transaction
            DB::rollBack();
            return response()->json(['message' => 'Failed to update fields', 'error' => $e->getMessage()], 500);
        }
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
            ->first();

        if (!$formField) {
            return response()->json(['message' => 'Form field not found or already deleted.'], 404);
        }

        $formField->delete();

        return response()->json(['message' => 'Form field deleted.']);
    }

    public function restore($id) {

        $record = DynamicFormField::withTrashed()->find($id);

        if (!$record) {
            return response()->json(['message' => 'Form field found'], 404);
        }

        $record->restore();
        return response()->json(['message' => 'Form field restored successfully']);
    }
}
