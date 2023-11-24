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
    #TODO: Add validation columns created_by, updated_by, deleted_by

    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAdmins(Request $request)
    {
         // show users that are not the currently authenticated user
         if ($request->searchTerm == null) {
            $admins = User::where('id', '!=', auth()->user()->id)
                ->with('createdByUser', 'updatedByUser')
                ->where('role', '=', User::ROLE_ADMIN)
                ->paginate(10);
        } else {

            $admins = User::where('id', '!=', auth()->user()->id)
                ->where(function ($query) use ($request) {
                    $query->where('first_name', 'like', "%{$request->searchTerm}%")
                        ->orWhere('last_name', 'like', "%{$request->searchTerm}%")
                        ->orWhere('email', 'like', "%{$request->searchTerm}%")
                        ->orWhere('mobile_number', 'like', "%{$request->searchTerm}%");
                })
                ->with('createdByUser', 'updatedByUser')
                ->where('role', '=', User::ROLE_ADMIN)
                ->paginate(10);
        }

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

    public function getSubOwners()
    {
        return Inertia::render('tenants/admin/user-management/sub-owners/index');
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

    public function createSubOwner()
    {
        return Inertia::render('tenants/admin/user-management/sub-owners/create-sub-owner/index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        // $verificationCode = rand(1000, 9999);

        $user = User::create($request->validated());

        // When the user is created by an admin, the credentials will be sent to the user email address.
        // No need to send the verification code to the user email address. Since it will be verified when the user logs in.
        // $user->mobileVerification()->create([
        //     'mobile_number' => $request->mobile_number,
        //     'code' => $verificationCode,
        //     'expires_at' => Carbon::now()->addSeconds(300),
        // ]);

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
        return Inertia::render('tenants/admin/user-management/admins/update-admin/index', ['admin' => $admin]);
    }

    public function editOwner($id)
    {
        $owner = User::findOrFail($id);
        return Inertia::render('tenants/admin/user-management/owners/update-owner/index', ['owner' => $owner]);
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return Inertia::render('tenants/admin/user-management/users/update-user/index', ['user' => $user]);
    }

    public function editSubOwner($id)
    {
        return Inertia::render('tenants/admin/user-management/sub-owners/update-sub-owner/index');
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
        #TODO: Send email notification to user for the changes made to his account.

        return back()->with(['success' => 'You have successfully updated a user.']);
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

        $user->deleted_by = auth()->user()->id;
        $user->save(); // update the user object to reflect the change in deleted_by.
        $user->delete(); // once object is updated, soft delete the record.

        return back()->with(['success' => 'You have successfully deleted a user.']);
    }
}
