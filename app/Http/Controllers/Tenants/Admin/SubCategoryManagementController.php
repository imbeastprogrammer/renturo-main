<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SubCategoryManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $perPage = 15;

        // Fetch all sub-categories and eager load their parent categories
        $subCategories = SubCategory::with('category')->paginate($perPage);
        $categories = Category::all();

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
        return Inertia::render('tenants/admin/post-management/sub-categories/index', ['sub_categories' => $subCategories, 'categories' => $categories]);
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sub_categories')->where(function ($query) use ($request) {
                    return $query->where('category_id', $request->category_id);
                }),
            ],
        ]);

        $category = Category::find($request->category_id);

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

        $subCategory = $category->subCategories()->create([
            'name' => $request->name
        ]);

        return response()->json([
            "status" => "success",
            'message' => 'Subcategory created successfully.',
            'data' => $subCategory,
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
        // Fetch the sub-category by ID and eager load its parent category
        $subCategory = SubCategory::with('category')->find($id);

        // Check if the sub-category was found
        if (!$subCategory) {
            return response()->json([
                "status" => "failed",
                'message' => 'Subcategory not found',
                "error" => [
                    "errorCode" => "SUBCATEGORY_NOT_FOUND",
                    "errorDescription" => "The subcategory ID you are looking for could not be found."
                ]
            ], 404);
        }

        // Optional: Transform the sub-category data for the response
        $subCategoryData = [
            'category_id' => $subCategory->category_id,
            'category_name' => $subCategory->category ? $subCategory->category->name : 'No Category',
            'sub_category_id' => $subCategory->id,
            'sub_category_name' => $subCategory->name,
        ];

        // Return the created category along with a success message
        return response()->json([
            "status" => "success",
            'message' => 'Subcategory was successfully fetched.',
            'data' => $subCategoryData,
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
            'category_id' => 'required|exists:categories,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sub_categories')->where(function ($query) use ($request) {
                    return $query->where('category_id', $request->category_id);
                })->ignore($id),
            ],
        ]);

        $subCategory = SubCategory::find($id);

        // Check if the subcategory was found
        if (!$subCategory) {
            return response()->json([
                "status" => "failed",
                'message' => 'Subcategory not found',
                "error" => [
                    "errorCode" => "SUBCATEGORY_NOT_FOUND",
                    "errorDescription" => "The subcategory ID you are looking for could not be found."
                ]
            ], 404);
        }

        $subCategory->update([
            'category_id' => $request->category_id,
            'name' => $request->name
        ]);

        return response()->json([
            "status" => "success",
            'message' => 'Subcategory was successfully updated.',
            'data' => $subCategory,
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
        $subCategory = SubCategory::find($id);

        if (!$subCategory) {
            return response()->json([
                "status" => "failed",
                'message' => 'Subcategory not found',
                "error" => [
                    "errorCode" => "SUBCATEGORY_NOT_FOUND",
                    "errorDescription" => "The subcategory ID you are looking for could not be found."
                ]
            ], 404);
        }

        $subCategory->delete();

        // Return the created category along with a success message
        return response()->json([
            "status" => "success",
            'message' => 'Subcategory was successfully deleted.',
        ], 200);
    }

    public function restore($id)
    {

        $record = SubCategory::withTrashed()->where('id', $id)->first();

        if (!$record) {
            return response()->json([
                "status" => "failed",
                'message' => 'Subcategory not found',
                "error" => [
                    "errorCode" => "SUBCATEGORY_NOT_FOUND",
                    "errorDescription" => "The subcategory ID you are looking for could not be found."
                ]
            ], 404);
        }

        $record->restore();
        return response()->json([
            "status" => "success",
            'message' => 'Subcategory was successfully restored.',
        ], 200);
    }
}
