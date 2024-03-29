<?php

namespace App\Http\Controllers\Api\V1\Tenants\Client;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Tenants\DynamicFormAvailabilityManagement\StoreDynamicFormAvailabilityRequest;
use App\Models\DynamicFormAvailability;

class DynamicFormAvailabilityController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDynamicFormAvailabilityRequest $request)
    {
        $validatedData = $request->validated();

        if (isset($validatedData['recurring'])) {
            $validatedData['recurring'] = json_encode($validatedData['recurring'], true);
        }

        // Create a new DynamicFormAvailability record using the validated data
        $availability = DynamicFormAvailability::create($validatedData);

        // Return a JSON response with the newly created availability and a 201 status code
        return $this->sendSuccessResponse($availability, 'Dynamic Form Availability Created Successfully', 201);
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
}
