<?php

namespace App\Http\Controllers;

use App\Models\Certificates_catalog;
use Illuminate\Http\Request;
use App\Models\Instructor;
use App\Models\Instructor_certificate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InstructorController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'full_name'         => 'required',
            'email'             => 'required',
            'dob'               => 'required',
            'address'           => 'required',
            'user_status'       => 'required',
            'profile_picture'   => 'required|mimes:jpeg,png,jpg,gif,svg',
            'cv'                => 'required',
            'ijazah'            => 'required',
            'certificates'      => 'required',
            'certificates.*'    => 'required',
        ]);

        // Create the instructor
        $instructor = Instructor::create([
            'instructor_name'   => $validatedData['full_name'],
            'instructor_email'       => $validatedData['email'],
            'instructor_dob'         => $validatedData['dob'],
            'instructor_address'     => $validatedData['address'],
            'status' => $validatedData['user_status'],
        ]);

        // Define the directory path
        $instructorDir = 'uploads/instructor/instructor_' . $instructor->id;

        // Handle Profile Picture Upload
        if ($request->hasFile('profile_picture')) {
            $profilePicture = $request->file('profile_picture');
            $profilePictureName = 'profile_picture.' . $profilePicture->getClientOriginalExtension();
            $profilePicture->move(public_path($instructorDir), $profilePictureName);
            $instructor->imgFilepath = $instructorDir . '/' . $profilePictureName;
        }

        // Handle CV Upload
        if ($request->hasFile('cv')) {
            $cv = $request->file('cv');
            $cvName = 'cv.' . $cv->getClientOriginalExtension();
            $cv->move(public_path($instructorDir), $cvName);
            $instructor->cvFilepath = $instructorDir . '/' . $cvName;
        }

        // Handle Ijazah Upload
        if ($request->hasFile('ijazah')) {
            $ijazah = $request->file('ijazah');
            $ijazahName = 'ijazah.' . $ijazah->getClientOriginalExtension();
            $ijazah->move(public_path($instructorDir), $ijazahName);
            $instructor->ijazahFilepath = $instructorDir . '/' . $ijazahName;
        }

        // Save Certificates (Assuming a Many-to-Many relationship)
        if (isset($validatedData['certificates'])) {
            // Assign roles
            foreach ($request->certificates as $certId) {
                Instructor_certificate::create([
                    'instructor_id' => $instructor->id,
                    'certificates_catalog_id' => $certId
                ]);
            }
        }

        // Save the updated instructor data
        $instructor->save();

        // Redirect or return response
        return redirect()->route('instructor')->with('success', 'Instructor created successfully.');
    }
}
