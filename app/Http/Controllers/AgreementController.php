<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use Illuminate\Http\Request;

class AgreementController extends Controller
{
    public function store(Request $request)
    {
        // Validate the input
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_details' => 'nullable|string|max:255',
            'status' => 'nullable|string',
            'spk_file' => 'required',
            'img' => 'required',
        ]);

        // Create new agreement
        $agreement = new Agreement();
        $agreement->company_name = $request->input('company_name');
        $agreement->company_details = $request->input('company_details');
        $agreement->status = $request->input('status');
        $agreement->user_id = auth()->user()->id; // assuming the user is authenticated
        // If an image was uploaded, store it in the related ToolImg table
        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/agreement'), $filename);
            $agreement->img_filepath = 'uploads/agreement/' . $filename;
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
}
