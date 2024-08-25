<?php

namespace App\Http\Controllers;

use App\Models\Certificates_catalog;
use Illuminate\Http\Request;
use App\Models\Instructor;
use App\Models\Instructor_certificate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

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
            'pendukung'         => 'sometimes',
            'working_hour'      => 'required',
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
            'working_hours'       => $validatedData['working_hour'],
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

        if ($request->hasFile('pendukung')) {
            $pendukung = $request->file('pendukung');
            $pendukungName = 'document_pendukung.' . $pendukung->getClientOriginalExtension();
            $pendukung->move(public_path($instructorDir), $pendukungName);
            $instructor->documentPendukungFilepath = $instructorDir . '/' . $pendukungName;
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

    public function edit_instructor($instructorId)
    {
        $data = Instructor::find($instructorId);
        $certificate = Certificates_catalog::all();
        return view('plan_dev.submenu.update-instructor', ['data' => $data, 'certificate' => $certificate]);
    }

    public function update(Request $request, $instructorId)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'full_name'         => 'required',
            'email'             => 'required',
            'dob'               => 'required',
            'address'           => 'required',
            'status'            => 'required', // Updated key from 'user_status' to 'status'
            'profile_picture'   => 'nullable|mimes:jpeg,png,jpg,gif,svg',
            'cvFilepath'        => 'nullable|mimes:pdf,doc,docx', // Adjust according to allowed CV formats
            'ijazahFilepath'    => 'nullable|mimes:pdf,doc,docx', // Adjust according to allowed Ijazah formats
            'documentPendukungFilepath' => 'sometimes',
            'working_hour'      => 'required',
            'certificates'      => 'required|array',
            'certificates.*'    => 'required|integer',
        ]);

        // Find the instructor by ID
        $instructor = Instructor::findOrFail($instructorId);

        // Update the instructor data
        $instructor->instructor_name = $validatedData['full_name'];
        $instructor->instructor_email = $validatedData['email'];
        $instructor->instructor_dob = $validatedData['dob'];
        $instructor->instructor_address = $validatedData['address'];
        $instructor->working_hours = $validatedData['working_hour'];
        $instructor->status = $validatedData['status'];

        // Define the directory path
        $instructorDir = 'uploads/instructor/instructor_' . $instructor->id;

        // Handle Profile Picture Upload
        if ($request->hasFile('profile_picture')) {
            // Unlink existing profile picture if it exists
            if ($instructor->imgFilepath && file_exists(public_path($instructor->imgFilepath))) {
                unlink(public_path($instructor->imgFilepath));
            }

            // Upload new profile picture
            $profilePicture = $request->file('profile_picture');
            $profilePictureName = 'profile_picture.' . $profilePicture->getClientOriginalExtension();
            $profilePicture->move(public_path($instructorDir), $profilePictureName);
            $instructor->imgFilepath = $instructorDir . '/' . $profilePictureName;
        }

        // Handle CV Upload
        if ($request->hasFile('cvFilepath')) {
            // Unlink existing CV if it exists
            if ($instructor->cvFilepath && file_exists(public_path($instructor->cvFilepath))) {
                unlink(public_path($instructor->cvFilepath));
            }

            // Upload new CV
            $cv = $request->file('cvFilepath');
            $cvName = 'cv.' . $cv->getClientOriginalExtension();
            $cv->move(public_path($instructorDir), $cvName);
            $instructor->cvFilepath = $instructorDir . '/' . $cvName;
        }

        // Handle Ijazah Upload
        if ($request->hasFile('ijazahFilepath')) {
            // Unlink existing Ijazah if it exists
            if ($instructor->ijazahFilepath && file_exists(public_path($instructor->ijazahFilepath))) {
                unlink(public_path($instructor->ijazahFilepath));
            }

            // Upload new Ijazah
            $ijazah = $request->file('ijazahFilepath');
            $ijazahName = 'ijazah.' . $ijazah->getClientOriginalExtension();
            $ijazah->move(public_path($instructorDir), $ijazahName);
            $instructor->ijazahFilepath = $instructorDir . '/' . $ijazahName;
        }

        if ($request->hasFile('documentPendukungFilepath')) {
            // Unlink existing Ijazah if it exists
            if ($instructor->documentPendukungFilepath && file_exists(public_path($instructor->documentPendukungFilepath))) {
                unlink(public_path($instructor->documentPendukungFilepath));
            }

            // Upload new Ijazah
            $documentPendukungFilepath = $request->file('documentPendukungFilepath');
            $documentPendukungFilepathName = 'document_pendukung.' . $documentPendukungFilepath->getClientOriginalExtension();
            $documentPendukungFilepath->move(public_path($instructorDir), $documentPendukungFilepathName);
            $instructor->documentPendukungFilepath = $instructorDir . '/' . $documentPendukungFilepathName;
        }

        // Update Certificates (Assuming a Many-to-Many relationship)
        if (isset($validatedData['certificates'])) {
            // Sync certificates: Remove old ones not in the request and add new ones
            $existingCertificates = $instructor->certificates()->pluck('certificates_catalog_id')->toArray();

            // Remove certificates that are not in the current request
            $certificatesToRemove = array_diff($existingCertificates, $validatedData['certificates']);
            $instructor->certificates()->whereIn('certificates_catalog_id', $certificatesToRemove)->delete();

            // Update or create certificates based on the request
            foreach ($validatedData['certificates'] as $certId) {
                Instructor_certificate::updateOrCreate(
                    [
                        'instructor_id' => $instructor->id,
                        'certificates_catalog_id' => $certId
                    ],
                    [] // Add more fields here if you want to update other attributes
                );
            }
        }

        // Save the updated instructor data
        $instructor->save();

        // Redirect or return response
        return redirect()->route('instructor')->with('success', 'Instructor updated successfully.');
    }

    public function deleteInstructor($instructorId)
    {
        // Find the instructor
        $instructor = Instructor::findOrFail($instructorId);

        // Define the path to the instructor's directory in the public folder
        $folder = 'uploads/instructor/';
        $instructorDir = public_path($folder . 'instructor_' . $instructor->id);

        // Check if the directory exists and delete it
        if (File::exists($instructorDir)) {
            File::deleteDirectory($instructorDir); // This deletes the directory and all its contents
        }

        // Delete related certificates
        $instructor->certificates()->delete();

        // Delete the instructor
        $instructor->delete();

        // Return a success response
        return response()->json(['success' => 'Instructor, related certificates, and files deleted successfully.']);
    }
}