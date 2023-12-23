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
        $subCategories = SubCategory::with("category")->paginate($perPage);

        // Transform the paginated items
        $transformedSubCategories = $subCategories->getCollection()->map(function ($subCategory) {
            return [
                "category_id" => $subCategory->category_id,
                "category_name" => $subCategory->category ? $subCategory->category->name : "No Category",
                "sub_category_id" => $subCategory->id,
                "sub_category_name" => $subCategory->name,
            ];
        });

        // Replace the original items with the transformed collection
        $subCategories->setCollection($transformedSubCategories);

        // Fetch the list of categories
        $categories = Category::all();
        
        // For JSON request, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Subcategory was successfully fetched.",
                "data" => $subCategories,
            ], 201);
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return Inertia::render("tenants/admin/post-management/sub-categories/index", ["sub_categories" => $subCategories, "categories" => $categories]);
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
            "category_id" => "required|exists:categories,id",
            "name" => [
                "required",
                "string",
                "max:255",
                Rule::unique("sub_categories")->where(function ($query) use ($request) {
                    return $query->where("category_id", $request->category_id);
                }),
            ],
        ]);

        // Fetch the category based on the validated category_id
        $category = Category::findOrFail($request->category_id);

        // Create the subcategory
        $subCategory = $category->subCategories()->create([
            "name" => $request->name
        ]);
        
        // For JSON request, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Subcategory was successfully created.",
                "data" => $subCategory,
            ], 201);
        } 
        
        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return redirect()->back()->with("success", "Subcategory was successfully created.");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // Fetch the sub-category by ID and eager load its parent category
        $subCategory = SubCategory::with("category")->findOrFail($id);

        // Optional: Transform the sub-category data for the response
        $subCategoryData = [
            "category_id" => $subCategory->category_id,
            "category_name" => $subCategory->category ? $subCategory->category->name : "No Category",
            "sub_category_id" => $subCategory->id,
            "sub_category_name" => $subCategory->name,
        ];

        // For JSON requests, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Subcategory was successfully fetched.",
                "data" => $subCategoryData,
            ], 200);
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return Inertia::render("", [
            "message" => "Subcategory was successfully fetched.",
            // Include other necessary data for the component
        ]);
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
            "category_id" => "required|exists:categories,id",
            "name" => [
                "required",
                "string",
                "max:255",
                Rule::unique("sub_categories")->where(function ($query) use ($request) {
                    return $query->where("category_id", $request->category_id);
                })->ignore($id),
            ],
        ]);

        $subCategory = SubCategory::findOrFail($id);

        $subCategory->update([
            "category_id" => $request->category_id,
            "name" => $request->name
        ]);

        // For JSON requests, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Subcategory was successfully updated.",
                "data" => $subCategory,
            ], 201);
        }
       
        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return redirect()->back()->with("success", "Subcategory was successfully updated.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $subCategory = SubCategory::findOrFail($id);
        $subCategory->delete();

        // For JSON requests, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Subcategory was successfully deleted.",
            ], 200);
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return redirect()->back()->with("success", "Subcategory was successfully deleted.");
    }

    public function restore(Request $request, $id)
    {
        $record = SubCategory::withTrashed()->findOrFail($id);
        $record->restore();

        // For JSON requests, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Subcategory was successfully restored.",
            ], 200);
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return redirect()->back()->with("success", "Subcategory was successfully restored.");
    }
}
