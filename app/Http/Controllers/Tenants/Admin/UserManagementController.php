<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Tenants\Admin\UserManagement\StoreUserRequest;
use App\Http\Requests\Tenants\Admin\UserManagement\UpdateUserRequest;

use App\Mail\Tenants\UserManagement\UserCreated;

use App\Models\User;

use Inertia\Inertia;

use Mail;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return Inertia::render('tenants/admin/user-management/users/index', [
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('tenants/admin/user-management/users/create-user/index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $verificationCode = rand(1000, 9999);

        $user = User::create($request->safe()->except('mobile_no'));

        $user->mobileVerification()->create([
            'mobile_no' => $request->mobile_no,
            'code' => $verificationCode
        ]);

        Mail::to($user->email)->send(new UserCreated([
            'name' => $user->fullName,
            'role' => $user->role,
            'email' => $user->email,
            'password' => $request->password
        ]));

        return back()->with(['success' => 'You have successfully made a new user.']);
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return Inertia::render('tenants/admin/user-management/users/update-user/index', ['user'=> $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $user->update($request->validated());

        return back()->with(['success' => 'You have successfully deleted a user.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return back()->with(['success' => 'You have successfully deleted a user.']);
    }
}
