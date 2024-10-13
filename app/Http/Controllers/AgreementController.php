<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AgreementController extends Controller
{
    public function store(Request $request)
    {
        // Validate the input
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_details' => 'required|string|max:65535', // Max size for 'longtext' field in MySQL
            'status' => 'nullable|string|max:255',
            'agreement_date' => 'required|date',
            'spk_file' => 'sometimes|file|max:10240', // Max file size is 10MB (10240 KB)
            'non_spk_details' => 'sometimes|string|max:65535', // Prevent dangerous tags
            'img' => 'sometimes|file|mimes:png,jpg,jpeg|max:10240', // Image validation for png, jpg, jpeg, max 10MB
        ]);

        // Create new agreement
        $agreement = new Agreement();
        $agreement->company_name = $request->input('company_name');
        $agreement->company_details = $request->input('company_details');
        $agreement->status = $request->input('status');
        $agreement->date = $request->input('agreement_date');
        $agreement->user_id = auth()->user()->id; // assuming the user is authenticated
        // If an image was uploaded, store it in the related ToolImg table
        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/agreement'), $filename);
            $agreement->img_filepath = 'uploads/agreement/' . $filename;
        }

        if($request->input('non_spk_details')){
            $agreement->non_spk = $request->input('non_spk_details');
        }

        if ($request->hasFile('spk_file')) {
            $image = $request->file('spk_file');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/agreement/spk'), $filename);
            $agreement->spk_filepath = 'uploads/agreement/spk/' . $filename;
        }

        // Save to the database
        $agreement->save();

        // Redirect with a success message
        return redirect()->back()->with('success', 'agreement created successfully.');
    }

    public function preview_agreement($id)
    {
        $data = Agreement::find($id);
        $statuses = Status::all();

        $fileExists = false;
        $isPdf = false;
        $filePath = null;

        // Check if SPK file path exists and is not null or empty
        if ($data && !empty($data->spk_filepath) && file_exists(public_path($data->spk_filepath))) {
            $fileExists = true;
            $filePath = public_path($data->spk_filepath);
            $isPdf = pathinfo($filePath, PATHINFO_EXTENSION) === 'pdf';
        }

        return view('marketing.submenu.preview-agreement', [
            'data' => $data,
            'statuses' => $statuses,
            'fileExists' => $fileExists,
            'isPdf' => $isPdf,
            'filePath' => $filePath
        ]);
    }

    public function show($id)
    {
        $agreement = Agreement::findOrFail($id);
        return response()->json($agreement);
    }

    public function update(Request $request, $id)
    {
        // Validate the input
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_details' => 'required|string|max:65535', // Max size for 'longtext' field in MySQL
            'status' => 'nullable|string|max:255',
            'agreement_date' => 'required|date',
            'spk_file' => 'sometimes|file|max:10240', // Max file size is 10MB (10240 KB)
            'non_spk_details' => 'sometimes|string|max:65535', // Prevent dangerous tags
            'img' => 'sometimes|file|mimes:png,jpg,jpeg|max:10240', // Image validation for png, jpg, jpeg, max 10MB
        ]);

        // Find the agreement by id
        $agreement = Agreement::findOrFail($id);
        $agreement->company_name = $request->input('company_name');
        $agreement->company_details = $request->input('company_details');
        $agreement->status = $request->input('status');
        $agreement->date = $request->input('agreement_date');

        // Check if the agreement is being updated to SPK or Non-SPK
        if ($request->input('spk_type') == 'SPK') {
            // If SPK is selected, handle the SPK file and set non_spk to null
            if ($request->hasFile('spk_file')) {
                // Delete the old SPK file if it exists
                if ($agreement->spk_filepath && file_exists(public_path($agreement->spk_filepath))) {
                    unlink(public_path($agreement->spk_filepath));
                }

                // Store the new SPK file
                $spkFile = $request->file('spk_file');
                $spkFilename = time() . '_' . $spkFile->getClientOriginalName();
                $spkFile->move(public_path('uploads/agreement/spk'), $spkFilename);
                $agreement->spk_filepath = 'uploads/agreement/spk/' . $spkFilename;
            }

            // Ensure non_spk is null since it's an SPK agreement
            $agreement->non_spk = null;
        } else {
            // If Non-SPK is selected, set spk_filepath to null and handle non_spk_details
            if (!empty($request->input('non_spk_details'))) {
                $agreement->non_spk = $request->input('non_spk_details');
            }

            // Set SPK file path to null since it's Non-SPK
            if ($agreement->spk_filepath && file_exists(public_path($agreement->spk_filepath))) {
                unlink(public_path($agreement->spk_filepath));
            }
            $agreement->spk_filepath = null;
        }

        // Handle image upload
        if ($request->hasFile('img')) {
            // Delete the old image if it exists
            if ($agreement->img_filepath && file_exists(public_path($agreement->img_filepath))) {
                unlink(public_path($agreement->img_filepath));
            }

            // Store the new image file
            $image = $request->file('img');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/agreement'), $filename);
            $agreement->img_filepath = 'uploads/agreement/' . $filename;
        }

        // Save the updated agreement details
        $agreement->save();

        // Redirect back with a success message
        return redirect()->route('preview-company', $id)->with('success', 'Agreement updated successfully');
    }
    /**
     * Delete a specific campaign by its ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete_agreement($id)
    {
        // Find the campaign by ID
        $agreement = Agreement::find($id);

        // Check if the agree$agreement exists
        if ($agreement) {
            // Delete the agree$agreement
            $agreement->delete();

            return response()->json(['status' => 'success', 'message' => 'Campaign data deleted successfully!']);
        } else {
            // Log the exception for debugging
            return response()->json(['status' => 'failed', 'message' => 'Failed to delete record due to an unexpected error!']);
        }
    }
}
