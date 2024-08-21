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
        echo "No query implemented yet";
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
    public function show($storeId)
    {
        // Retrieve the authenticated user's ID
        $userId = Auth::user()->id;

        // Retrieve all store with the authenticated user
        $store = Store::with(['category', 'subCategory'])
            ->where('id', $storeId) // store id
            ->where('user_id', $userId)->first();

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

    public function getUserStores($userId) {

        // Retrieve the authenticated user's ID
        $authUserId = Auth::user()->id;

        // Compare the UserID on URL against the current authenticated userId
        if ($authUserId != $userId) {
            return response()->json([
                'message' => 'failed',
                'errors' => 'Resource not found.'
            ], 404); 
        }

        // Retrieve all stores associated with the authenticated user
        $stores = Store::with(['category', 'subCategory.dynamicForms'])
            ->where('user_id', $authUserId)->get();

        // Check if any stores records were found
        if ($stores->isEmpty()) {
            return response()->json([
                'message' => 'failed',
                'errors' => 'Store not found.'
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
