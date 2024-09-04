<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use App\Models\Users_detail;
use App\Models\Usr_role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function manage()
    {
        $users = User::all();
        return view('manage-users.index', ['users' => $users]);
    }

    public function register()
    {
        $roles = Role::all();
        $departments = Department::all();
        $positions = Position::all();
        return view('manage-users.registration', ['roles' => $roles, 'departments' => $departments, 'positions' => $positions]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'department' => 'required',
            'position' => 'required',
            'user_status' => 'required',
            'roles' => 'required',
            'profile_picture' => 'sometimes',
        ]);

        if ($validator->fails()) {
            Session::flash('failed',"Error Database has Occured! Failed to create request! You need to fill all the required fields");
            return redirect('/manage-users');
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $profile_picture_path = $request->file('profile_picture')->store('profile_pictures', 'public');
        } else {
            $profile_picture_path = null;
        }

        // Create User
        $user = User::create([
            'id' => $request->user_id,
            'name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create Users_detail
        Users_detail::create([
            'user_id' => $user->id,
            'employee_id' => $request->employee_id,
            'department_id' => $request->department,
            'position_id' => $request->position,
            'employment_status' => $request->user_status,
            'profile_pic' => $profile_picture_path,
        ]);

        // Assign roles
        foreach ($request->roles as $role_id) {
            $role = Role::find($role_id);
            Usr_role::create([
                'user_id' => $user->id,
                'role_id' => $role->id,
                'role_name' => $role->role,
            ]);
        }

        return redirect('/manage-users')->with('success', 'User registered successfully');
    }

    public function edit($userId)
    {
        $getUser = User::findOrFail($userId);
        $roles = Role::all();
        $departments = Department::all();
        $positions = Position::all();
        return view('manage-users.preview-user', ['data' => $getUser,'roles' => $roles, 'departments' => $departments, 'positions' => $positions]);
    }

    public function update(Request $request, $id)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'email' => 'required|email',
            'password' => 'sometimes|nullable',
            'department' => 'required',
            'position' => 'required',
            'user_status' => 'required',
            'roles' => 'required',
            'profile_picture' => 'sometimes|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            Session::flash('failed', "Error occurred! Failed to update user. Please ensure all required fields are filled correctly.");
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Find the user and related details
        $user = User::findOrFail($id);
        $userDetail = Users_detail::where('user_id', $id)->first();

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($userDetail->profile_pic) {
                $oldImagePath = public_path('img/' . $userDetail->profile_pic);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            // Save the new profile picture to public/img directory
            $profile_picture = $request->file('profile_picture');
            $profile_picture_name = time() . '_' . $profile_picture->getClientOriginalName();
            $profile_picture->move(public_path('img/avatar/'), $profile_picture_name);
        } else {
            $profile_picture_name = $userDetail->profile_pic; // Retain old profile picture
        }

        // Update user information
        $user->update([
            'name' => $request->full_name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        // Update Users_detail
        $userDetail->update([
            'employee_id' => $request->employee_id,
            'department_id' => $request->department,
            'position_id' => $request->position,
            'employment_status' => $request->user_status,
            'profile_pic' => $profile_picture_name,
        ]);

        // Update roles (first remove all roles, then add the selected ones)
        Usr_role::where('user_id', $user->id)->delete();
        foreach ($request->roles as $role_id) {
            $role = Role::find($role_id);
            Usr_role::create([
                'user_id' => $user->id,
                'role_id' => $role->id,
                'role_name' => $role->role,
            ]);
        }

        return redirect('/manage-users')->with('success', 'User updated successfully');
    }
}
