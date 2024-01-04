<?php

namespace App\Http\Controllers\Api\V1\Tenants\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenants\StoreManagement\CreateStoreRequest;
use App\Http\Requests\Tenants\StoreManagement\UpdateStoreRequest;
use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        echo "No query implemented yet";
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
    public function store(CreateStoreRequest $request)
    {
        Auth::user()->store()->create($request->validated());

        return response()->json([
            'message' => 'success',
            'body' => [
              'message' => 'Store has been created.',
              'data' => $request->all()
            ]
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
        $store = Auth::user()->store()->find($id);

        if (!$store) { 
            return response()->json([
              'message' => 'failed',
              'errors' => 'Store not found.'
            ], 404);
        }

        return response()->json([
            'message' => 'success',
            'body' => [
                'message' => 'Store has been fetched.',
                'data' => $store
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
    public function update(UpdateStoreRequest $request, $id)
    {
        $store = Auth::user()->store()->find($id);

        if (!$store) { 
            return response()->json([
                'message' => 'failed',
                'errors' => 'Store not found.'
            ], 404);
        }

        $store->update($request->all());
        
        return response()->json([
            'message' => 'success',
            'body' => [
                'message' => 'Store has been updated.',
                'data' => $request->all()
            ]
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $store = Auth::user()->store()->find($id);

        if (!$store) { 
            return response()->json([
               'message' => 'failed',
                'errors' => 'Store not found.'
            ], 404);
        }

        $store->delete();

        return response()->json([
           'message' =>'success',
            'body' => [
               'message' => 'Store has been deleted.',
               'data' => []
            ]
        ], 200);
    }

    public function getUserStores(Request $request) {

        // Retrieve the authenticated user's ID
        $userId = $request->user()->id;

        // Retrieve all banks associated with the authenticated user
        $stores = Store::where('user_id', $userId)->get();

        // Check if any bank records were found
        if ($stores->isEmpty()) {
            return response()->json([
                'message' => 'failed',
                'errors' => 'No store found.'
            ], 404); 
        }

        return response()->json([
            'message' => 'success',
            'body' => [
                'message' => 'User store(s) was fetch successfully.',
                'data' => $stores,
            ]
        ]);
    }
}
