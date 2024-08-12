<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function store(Request $request)
    {
        // Validate the input
        $request->validate([
            'activity_name' => 'required|string|max:255',
            'activity_info' => 'nullable|string|max:255',
            'activity_result' => 'nullable|string',
            'person_in_charge' => 'nullable|string|max:255',
            'activity_date' => 'nullable|date',
            'img' => 'required',
        ]);

        // Create new campaign
        $campaign = new Campaign();
        $campaign->campaign_name = $request->input('activity_name');
        $campaign->campaign_details = $request->input('activity_info');
        $campaign->campaign_result = $request->input('activity_result');
        $campaign->user_id = auth()->user()->id; // assuming the user is authenticated
        $campaign->date = $request->input('activity_date');
        // If an image was uploaded, store it in the related ToolImg table
        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/campaign'), $filename);
            $campaign->img_filepath = 'uploads/campaign/' . $filename;
        }
        $campaign->created_at = now();

        // Save to the database
        $campaign->save();

        // Redirect with a success message
        return redirect()->back()->with('success', 'Campaign created successfully.');
    }

    public function preview_campaign($id)
    {
        $data = Campaign::find($id);
        return view('marketing.submenu.preview_campaign', ['data' => $data]);
    }

    /**
     * Delete a specific campaign by its ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete_campaign($id)
    {
        // Find the campaign by ID
        $campaign = Campaign::find($id);

        // Check if the campaign exists
        if ($campaign) {
            // Delete the campaign
            $campaign->delete();

            // Redirect back with a success message
            return redirect()->route('marketing-campaign')->with('success', 'Campaign deleted successfully.');
        } else {
            // Redirect back with an error message if not found
            return redirect()->route('marketing-campaign')->with('error', 'Campaign not found.');
        }
    }
}
