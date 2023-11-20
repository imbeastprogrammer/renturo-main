<?php

namespace App\Http\Controllers\Api\V1\Tenants;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenants\StoreManagement\CreateStoreRequest;
use App\Http\Requests\Tenants\StoreManagement\UpdateStoreRequest;

use Auth;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stores = Auth::user()->store()->get();

        if (!$stores) {
            return response()->json([
              'message' => 'No stores found!'
            ], 404);
        }

        return response()->json([
            'message' => 'Store has been successfully fetched!',
            'data' => $stores
        ], 201);
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
            'message' => 'Store has been created!'
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
        $store = Auth::user()->store()->findOrFail($id);

        if (!$store) {
            return response()->json([
              'message' => 'No store found!'
            ], 404);
        }

        return response()->json([
           'message' => 'Store has been successfully fetched!',
            'data' => $store
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
    public function update(UpdateStoreRequest $request, $id)
    {
        $store = Auth::user()->store()->find($id);

        if (!$store) { 
            return response()->json([
              'message' => 'Store not found!'
            ], 404);
        }

        $store->update($request->all());
        
        return response()->json([
           'message' => 'Store has been updated!'
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
        //
        #TODO: Implement destroy() method for Store
    }
}
