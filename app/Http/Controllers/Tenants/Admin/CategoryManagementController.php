<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Inertia\Inertia;

class CategoryManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Define the number of items per page
        $perPage = 15;

        // Fetch categories with pagination
        $categories = Category::paginate($perPage);

        if ($request->expectsJson()) {
            // Return the created category along with a success message
            return response()->json([
                "status" => "success",
                "message" => "Categories was successfully fetched.",
                "data" => $categories,
            ], 201);
        }

        // For non-JSON requests, return an Inertia response
        return Inertia::render("tenants/admin/post-management/categories/index", ["categories" => $categories]);
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
            "name" => "required|string|max:255|unique:categories,name"
        ]);
    
        // Create and store the new category
        $newCategory = Category::create([
            "name" => $request->name
        ]);
    
        if ($request->expectsJson()) {
            // Return the created category along with a success message
            return response()->json([
                "status" => "success",
                "message" => "Category was successfully created.",
                "data" => $newCategory,
            ], 201);
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return redirect()->back()->with('success', 'Category was successfully created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // Fetch the category by its ID
        $category = Category::findOrFail($id);

        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Category was successfully fetched.",
                "data" => $category,
            ], 200);
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data

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
            "name" => "required|string|max:255|unique:categories,name," . $id,
        ]);
    
        $category = Category::findOrFail($id);
        $category->update([
            "name" => $request->name
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                "status" => "success",
                "message" => "Category was successfully updated.",
                "data" => $category->fresh(),
            ], 200);
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return redirect()->back()->with('success', 'Category was successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $category->delete();

        if ($request->expectsJson()) {
            // Return a success message after deletion
            return response()->json([
                "status" => "success",
                "message" => "Category was successfully deleted.",
            ], 200); 
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return redirect()->back()->with('success', 'Category was successfully deleted.');
    }

    public function restore(Request $request, $id)
    {
        $record = Category::withTrashed()->findOrFail($id);

        $record->restore();

        if ($request->expectsJson()) {
            // Return a success message after deletion
            return response()->json([
                "status" => "success",
                "message" => "Category was successfully restored.",
            ], 200);
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return redirect()->back()->with('success', 'Category was successfully restored.');
    }
}
