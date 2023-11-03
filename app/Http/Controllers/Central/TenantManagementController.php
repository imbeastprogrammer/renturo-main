<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Passport\ClientRepository;
use App\Http\Requests\Central\TenantManagement\StoreTenantRequest;

use App\Models\Central\Tenant;
use App\Models\User;

use Artisan;
use Str;

class TenantManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(StoreTenantRequest $request)
    {
        $tenant = Tenant::create([
            'id' => $request->tenant_id,
            'name' => $request->name
        ]);

        $tenantDomain = Str::lower(Str::replace(' ', '-', $request->name)) . '.' . config('tenancy.central_domains')[2];

        $tenant->domains()->create([
            'domain' => $tenantDomain
        ]);

        $tenant->run(function () use ($tenant, $request) {
            $verificationCode = rand(1000, 9999);

            $admin = User::create($request->safe()->except(['tenant_id', 'name']));

            $admin->mobileVerification()->create([
                'mobile_no' => $request->mobile_no,
                'code' => $verificationCode,
            ]);

            //install passport personal access tokens
            $client = new ClientRepository();

            $client->createPersonalAccessClient(null, 'Default personal access client', config('app.url'));
        });

        return back()->with(['success' => 'You have successfully created a new tenant.']);
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
        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $tenant = Tenant::findOrFail($id);

        $tenant->update([
            'status' => $request->status
        ]);

        return back()->with(['success' => 'You have successfully updated the tenant.']);
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
}
