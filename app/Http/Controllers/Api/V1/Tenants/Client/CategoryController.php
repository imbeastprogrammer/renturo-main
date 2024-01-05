<?php

namespace App\Http\Controllers\API\V1\Tenants\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Fetch categories with pagination
        $categories = Category::with('subCategories')->get();

        return response()->json([
            "status" => "success",
            "message" => "Categories was successfully fetched.",
            "data" => $categories,
        ], 200);
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

    public function search(Request $request) {
        
        $searchTerm = $request->input('query', '');

        // Check if search term length is at least 3 characters
        if (strlen($searchTerm) < 3) {
            // Return empty array or all categories without filtering
            return response()->json([]); // Uncomment this to return an empty array
            // return response()->json(Category::with('subcategories')->get()); // Return all categories if search term is too short
        } else {

            $categories = Category::query()
            ->where('name', 'LIKE', "%{$searchTerm}%")
            ->orWhereHas('subCategories', function($query) use ($searchTerm) {
                $query->where('name', 'LIKE', "%{$searchTerm}%");
            })
            ->with('subCategories')
            ->get();

            return response()->json($categories);
        }
    }
}
