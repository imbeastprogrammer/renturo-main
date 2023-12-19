<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Define the number of items per page
        $perPage = 15; 

        // Fetch categories with pagination
        $categories = Category::paginate($perPage);

        // Return the paginated response
        return response()->json($categories, 200);
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
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name'
        ]);
    
        // Create and store the new category
        $newCategory = Category::create([
            'name' => $request->name
        ]);
    
        // Return the created category along with a success message
        return response()->json([
            "status" => "success",
            'message' => 'Category created successfully.',
            'data' => $newCategory,
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
        // Fetch the category by its ID
        $category = Category::find($id);

        // Check if the category was found
        if (!$category) {
            return response()->json([
                "status" => "failed",
                'message' => 'Category not found',
                "error" => [
                    "errorCode" => "CATEGORY_NOT_FOUND",
                    "errorDescription" => "The category ID you are looking for could not be found."
                ]
            ], 404); 
        }

        // Return the created category along with a success message
        return response()->json([
            "status" => "success",
            'message' => 'Category was successfully fetched.',
            'data' => $category,
        ], 201); 
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
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ]);

        $category = Category::find($id);

         // Check if the category was found
         if (!$category) {
            return response()->json([
                "status" => "failed",
                'message' => 'Category not found',
                "error" => [
                    "errorCode" => "CATEGORY_NOT_FOUND",
                    "errorDescription" => "The category ID you are looking for could not be found."
                ]
            ], 404); 
        }

        $category->update([
            'name' => $request->name
        ]);

         // Return the created category along with a success message
         return response()->json([
            "status" => "success",
            'message' => 'Category was successfully updated.',
            'data' => $category,
        ], 200); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                "status" => "failed",
                'message' => 'Category not found',
                "error" => [
                    "errorCode" => "CATEGORY_NOT_FOUND",
                    "errorDescription" => "The category ID you are looking for could not be found."
                ]
            ], 404); 
        }

        $category->delete();

         // Return the created category along with a success message
         return response()->json([
            "status" => "success",
            'message' => 'Category was successfully deleted.',
        ], 200); 
    }

    public function restore($id) {

        $record = Category::withTrashed()->where('id', $id)->first();

        if (!$record) {
            return response()->json([
                "status" => "failed",
                'message' => 'Category not found',
                "error" => [
                    "errorCode" => "CATEGORY_NOT_FOUND",
                    "errorDescription" => "The category ID you are looking for could not be found."
                ]
            ], 404); 
        }

        $record->restore();
        return response()->json([
            "status" => "success",
            'message' => 'Category was successfully restored.',
        ], 200); 
    }
}
