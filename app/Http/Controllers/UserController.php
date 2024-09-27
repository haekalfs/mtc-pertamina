<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use App\Models\Users_detail;
use App\Models\Usr_role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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
            Session::flash('failed',"Failed to create request! You need to fill all the required fields");
            return redirect('/manage-users/registration');
        }

        // Check if the User ID exists in the database
        $idExists = User::where('id', $request->user_id)->exists();
        if ($idExists) {
            Session::flash('failed',"User ID is taken! Recreate it with different unique User ID");
            return redirect()->route('register.users');
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Get the file from the request
            $profile_picture = $request->file('profile_picture');

            // Generate a unique file name using the current timestamp and the original file name
            $profile_picture_name = time() . '_' . $profile_picture->getClientOriginalName();

            // Move the file to the 'public/img/avatar/' directory
            $profile_picture->move(public_path('img/avatar/'), $profile_picture_name);

            // Set the path for storing in the database (if needed)
            $profile_picture_path = 'img/avatar/' . $profile_picture_name;
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
        return view('manage-users.edit-user', ['data' => $getUser,'roles' => $roles, 'departments' => $departments, 'positions' => $positions]);
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
            'roles' => 'sometimes',
            'profile_picture' => 'sometimes|file|mimes:jpeg,png,jpg,gif|max:5048',
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
            // Delete old profile picture if it exists
            if ($userDetail->profile_pic) {
                $oldImagePath = public_path($userDetail->profile_pic);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            // Save the new profile picture to public/img directory
            $profile_picture = $request->file('profile_picture');
            $profile_picture_name = time() . '_' . $profile_picture->getClientOriginalName();
            $profile_picture_path = 'img/avatar/' . $profile_picture_name;
            $profile_picture->move(public_path('img/avatar/'), $profile_picture_name);
        } else {
            // If no new profile picture is uploaded, retain the old one
            $profile_picture_path = $userDetail->profile_pic;
        }

        // Update user information
        $user->update([
            'name' => $request->full_name,
            'email' => $request->email,
            // Only update password if it's provided
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        // Update Users_detail
        $userDetail->update([
            'employee_id' => $request->employee_id,
            'department_id' => $request->department,
            'position_id' => $request->position,
            'employment_status' => $request->user_status,
            'profile_pic' => $profile_picture_path, // Will be the new picture if uploaded, or the old one if not
        ]);

        // Update roles (first remove all roles, then add the selected ones)
        Usr_role::where('user_id', $user->id)->delete();
        if (!empty($request->roles) && is_array($request->roles)) {
            foreach ($request->roles as $role_id) {
                // Ensure role_id is not null or invalid
                if ($role_id && $role = Role::find($role_id)) {
                    Usr_role::create([
                        'user_id' => $user->id,
                        'role_id' => $role->id,
                        'role_name' => $role->role,
                    ]);
                }
            }
        }
        return redirect('/manage-users')->with('success', 'User updated successfully');
    }

    public function preview($userId)
    {
        $data = User::find($userId);

        return view('manage-users.preview', ['data' => $data]);
    }

    public function reset_user_password(Request $request, $userId)
    {
        // Validate the request
        $request->validate([
            'password_reset' => 'required|string|min:8', // Add confirmed for password confirmation
        ]);

        // Hash the new password
        $hash_pwd = Hash::make($request->password_reset);

        // Get the user by ID
        $user = User::find($userId);

        // Update the password
        $user->password = $hash_pwd;
        $user->save();

        // Check if the reset user is the currently authenticated user
        if (Auth::id() == $user->id) {
            // Log the user out
            Auth::logout();

            // Invalidate the session
            $request->session()->invalidate();

            // Regenerate the token to prevent session fixation
            $request->session()->regenerateToken();

            // Redirect to the login page with a success message
            return redirect('/login')->with('success', 'Your password has been reset and you have been logged out.');
        }

        // If it's not the authenticated user, redirect back with a success message
        return redirect()->back()->with('success', "Password has been reset for $user->name.");
    }

    public function delete($userId)
    {
        // Find the instructor
        $user = User::findOrFail($userId);
        if(Auth::id() == $userId){
            return response()->json(['error' => 'Cannot delete your own account, must be deleted by someone else!'], 403);
        }

        // Define the path to the instructor's directory in the public folder
        $userDir = public_path($user->users_detail->profile_pic);

        // Check if the directory exists and delete it
        if (file_exists($userDir)) {
            unlink($userDir);
        }

        // Delete related certificates
        $user->users_detail()->delete();
        $user->role_id()->delete();

        // Delete the instructor
        $user->delete();

        // Return a success response
        return response()->json(['success' => 'User and files deleted successfully.']);
    }

    public function checkUserId($userId)
    {
        // Check if the User ID exists in the database
        $exists = User::where('id', $userId)->exists();

        // Return the response in JSON format
        return response()->json(['exists' => $exists]);
    }
}
