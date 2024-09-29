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

        // Get user details
        $userDetail = Users_detail::where('user_id', Auth::id())->first();

        // Handle profile picture upload
        if ($request->hasFile('picture')) {
            // Delete old profile picture if it exists
            if ($userDetail->profile_pic) {
                $oldImagePath = public_path($userDetail->profile_pic);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Save the new profile picture to public/img/avatar directory
            $pictureFile = $request->file('picture');
            $profile_picture_name = time() . '_' . $pictureFile->getClientOriginalName();
            $profile_picture_path = 'img/avatar/' . $profile_picture_name;
            $pictureFile->move(public_path('img/avatar/'), $profile_picture_name);
        } else {
            // If no new profile picture is uploaded, retain the old one
            $profile_picture_path = $userDetail->profile_pic;
        }

        // Update user details with the new profile picture path
        $userDetail->profile_pic = $profile_picture_path;
        $userDetail->save();

        return redirect()->back()->with('success', "Your profile pic has been uploaded successfully.");
    }
}
