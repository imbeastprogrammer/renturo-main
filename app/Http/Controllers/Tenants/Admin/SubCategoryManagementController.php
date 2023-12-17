<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;

class SubCategoryManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $perPage = 10;

        // Fetch all sub-categories and eager load their parent categories
        $subCategories = SubCategory::with('category')->paginate($perPage);

        // Transform the paginated items
        $transformedSubCategories = $subCategories->getCollection()->map(function ($subCategory) {
            return [
                'category_id' => $subCategory->category_id,
                'category_name' => $subCategory->category ? $subCategory->category->name : 'No Category',
                'sub_category_id' => $subCategory->id,
                'sub_category_name' => $subCategory->name,
            ];
        });

        // Replace the original items with the transformed collection
        $subCategories->setCollection($transformedSubCategories);

        // Return the paginated response
        return response()->json($subCategories);
        
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
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255'
        ]);

        $category = Category::findOrFail($request->category_id);

        if (!$category) {
            return response()->json(['message' => 'Category id not found'], 404);
        }

        $category->subCategories()->create([
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'Sub category name created.'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Fetch the sub-category by ID and eager load its parent category
        $subCategory = SubCategory::with('category')->find($id);

        // Check if the sub-category was found
        if (!$subCategory) {
            return response()->json(['message' => 'Sub-category not found'], 404);
        }

        // Optional: Transform the sub-category data for the response
        $subCategoryData = [
            'category_id' => $subCategory->category_id,
            'category_name' => $subCategory->category ? $subCategory->category->name : 'No Category',
            'sub_category_id' => $subCategory->id,
            'sub_category_name' => $subCategory->name,
        ];

        // Return the sub-category data
        return response()->json($subCategoryData);
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
            'name' => 'required|string|max:255'
        ]);

        $subCategory = SubCategory::findOrFail($id);

        $subCategory->update([
            'category_id' => $request->category_id,
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'Sub category name updated.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subCategory = SubCategory::find($id);

        if (!$subCategory) {
            return response()->json(['message' => 'Sub-category not found'], 404);
        }

        $subCategory->delete();

        return response()->json([
            'message' => 'Sub category name was updated.'
        ]);
    }

    public function restore($id) {

        $record = SubCategory::withTrashed()->find($id);

        if (!$record) {
            return response()->json(['message' => 'Sub-category not found'], 404);
        }

        $record->restore();
        return response()->json(['message' => 'Sub-category restored successfully']);
    }
}
