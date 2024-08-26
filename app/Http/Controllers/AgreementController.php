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
            'company_details' => 'nullable|string|max:255',
            'status' => 'nullable|string',
            'agreement_date' => 'required',
            'spk_file' => 'sometimes',
            'non_spk_details' => 'sometimes',
            'img' => 'required',
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
        return view('marketing.submenu.preview-agreement', ['data' => $data, 'statuses' => $statuses]);
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
            'company_details' => 'nullable|string|max:255',
            'status' => 'nullable|string',
            'agreement_date' => 'required',
            'spk_file' => 'sometimes',
            'non_spk_details' => 'sometimes',
            'img' => 'sometimes',
        ]);

        // Find the agreement by id
        $agreement = Agreement::findOrFail($id);
        $agreement->company_name = $request->input('company_name');
        $agreement->company_details = $request->input('company_details');
        $agreement->status = $request->input('status');
        $agreement->date = $request->input('agreement_date');

        // Handle file uploads for SPK and image
        if ($request->hasFile('spk_file')) {
            // Delete the old SPK file if it exists
            if ($agreement->spk_filepath && file_exists(storage_path('app/' . $agreement->spk_filepath))) {
                unlink(storage_path('app/' . $agreement->spk_filepath));
            }
            // Store the new SPK file
            $spkFilePath = $request->file('spk_file')->store('spk_files');
            $agreement->spk_filepath = $spkFilePath;
        } else {
            // Update non-SPK details if no SPK file is uploaded
            $agreement->non_spk = $request->input('non_spk_details');
        }

        // Handle image upload
        if ($request->hasFile('img')) {
            // Delete the old image if it exists
            if ($agreement->img_filepath && file_exists(storage_path('app/' . $agreement->img_filepath))) {
                unlink(storage_path('app/' . $agreement->img_filepath));
            }
            // Store the new image file
            $imgFilePath = $request->file('img')->store('images');
            $agreement->img_filepath = $imgFilePath;
        }

        // Save the updated agreement details
        $agreement->save();

        // Redirect back with a success message
        return redirect()->route('company-agreement')->with('success', 'Agreement updated successfully');
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
