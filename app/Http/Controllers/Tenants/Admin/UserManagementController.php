<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Tenants\Admin\UserManagement\StoreUserRequest;
use App\Http\Requests\Tenants\Admin\UserManagement\UpdateUserRequest;

use App\Mail\Tenants\UserManagement\UserCreated;

use App\Models\User;

use Inertia\Inertia;
use Carbon\Carbon;

use Mail;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAdmins()
    {
        $admins = User::where('role', '=', 'ADMIN')->get();

        return Inertia::render('tenants/admin/user-management/admins/index', [
            'admins' => $admins
        ]);
    }

    public function getOwners()
    {
        $owners = User::where('role', '=', 'OWNER')->get();

        return Inertia::render('tenants/admin/user-management/owners/index', [
            'owners' => $owners
        ]);
    }
    
    public function getUsers()
    {
        $users = User::where('role', '=', 'USER')->get();

        return Inertia::render('tenants/admin/user-management/users/index', [
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createUser()
    {
        return Inertia::render('tenants/admin/user-management/users/create-user/index');
    }

    public function createOwner()
    {
        return Inertia::render('tenants/admin/user-management/owners/create-owner/index');
    }

    public function createAdmin()
    {
        return Inertia::render('tenants/admin/user-management/admins/create-admin/index');
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
            'code' => $verificationCode,
            'expires_at' => Carbon::now()->addSeconds(300),
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
    public function editAdmin($id)
    {
        $admin = User::findOrFail($id);
        return Inertia::render('tenants/admin/user-management/admins/update-admin/index', ['admin'=> $admin]);
    }

    public function editOwner($id)
    {
        $owner = User::findOrFail($id);
        return Inertia::render('tenants/admin/user-management/owners/update-owner/index', ['owner'=> $owner]);
    }

    public function editUser($id)
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
