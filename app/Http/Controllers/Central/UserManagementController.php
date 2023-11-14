<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Central\UserManagement\StoreUserRequest;
use App\Http\Requests\Central\UserManagement\UpdateUserRequest;
use App\Http\Requests\Central\UserManagement\UpdateUserPasswordRequest;
use App\Http\Requests\Central\UserManagement\UpdateUserProfileRequest; 
use App\Http\Controllers\Central\Exception;
use Illuminate\Support\Facades\Hash;
use App\Models\Central\User;
use Inertia\Inertia;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // show users that are not the currently authenticated user:
        $users = User::where('id', '!=', auth()->user()->id)
            ->with('createdByUser','updatedByUser')
            ->get();

        return Inertia::render('central/super-admin/administration/user-management/index', ['users'=> $users]);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('central/super-admin/administration/user-management/add-user/index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        User::create($request->validated());

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
        $user =User::findOrFail($id);
        return Inertia::render('central/super-admin/administration/user-management/edit-user/index', ['user'=> $user]);
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

        $user->deleted_by = auth()->user()->id;
        $user->save(); // update the user object to reflect the change in deleted_by.
        $user->delete(); // once object is updated, soft delete the record.

        return back()->with(['success' => 'You have successfully deleted a user.']);
    }

    /**
     * Show the form for changing the password.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     * */
    public function changePassword()
    {
        // Retrieve the currently logged in user
        $user = auth()->user();
        return Inertia::render('central/super-admin/settings/change-password/index', ['user'=> $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * 
     *  * @return \Illuminate\Http\Response
     * */
    public function updatePassword(UpdateUserPasswordRequest $request) {
        
        $user = auth()->user();

        try {
            if (!Hash::check($request->old_password, $user->password)) {
                return redirect()->back()->with('error', 'Current password is incorrect.');
            }

            $user->password = $request->new_password;
            $user->save();
    
            return back()->with(['success' => 'You have successfully updated your password.']);
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * */
    public function userProfile() {
        // Retrieve the currently logged in user
        $user = auth()->user();
        return Inertia::render('central/super-admin/settings/user-profile/index', ['user'=> $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * 
     * * @return \Illuminate\Http\Response
     *  
     * */
     public function updateUserProfile(UpdateUserProfileRequest $request) {
        // Retrieve the currently logged in user
        $user = auth()->user();

        try {
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->mobile_number = $request->mobile_number;
            $user->save();

            return back()->with(['success' => 'You have successfully updated your profile.']);

        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
     }
}
