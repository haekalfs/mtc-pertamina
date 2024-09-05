<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Users_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MyProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function reset_password(Request $request)
    {
        // Validate the request
        $request->validate([
            'password_reset' => 'required|string|min:8', // Add confirmed for password confirmation
        ]);

        // Hash the new password
        $hash_pwd = Hash::make($request->password_reset);

        // Get the authenticated user
        $userId = Auth::id();
        $user = User::find($userId);

        // Update the password
        $user->password = $hash_pwd;
        $user->save();

        // Log the user out
        Auth::logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate the token to prevent session fixation
        $request->session()->regenerateToken();

        // Redirect to the login page with a success message
        return redirect('/login')->with('success', 'Password has been reset! Please log in with your new password.');
    }

    public function change_picture(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'picture' => 'required|file|mimes:png,jpg,jpeg'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('failed', $validator->errors());
        }

        $employeeId = Auth::user()->users_detail->employee_id;
        $pictureFile = $request->file('picture');

        $pictureFileName = $employeeId . "_picture." . $pictureFile->getClientOriginalExtension();
        $pictureStoragePath = public_path('img/avatar');

        // Remove old CV file if it exists
        $oldpicturePath = $pictureStoragePath . '/' . $pictureFileName;
        if (file_exists($oldpicturePath)) {
            unlink($oldpicturePath);
        }

        // Move new CV file to storage
        $pictureFile->move($pictureStoragePath, $pictureFileName);

        // Update user details with the new CV file
        $userDetail = Users_detail::where('user_id', Auth::id())->first();
        $userDetail->profile_pic = $pictureFileName;
        $userDetail->save();

        return redirect()->back()->with('success', "Your profile pic has been uploaded successfully.");
    }
}
