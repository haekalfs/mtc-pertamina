<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Campaign_type;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CampaignController extends Controller
{
    public function store(Request $request)
    {
        // Validate the input
        $request->validate([
            'activity_name' => 'required|string|max:255',
            'jenisKegiatan' => 'required',
            'activity_info' => 'nullable|string|max:255',
            'activity_result' => 'nullable|string',
            'person_in_charge' => 'nullable|string|max:255',
            'activity_date' => 'nullable|date',
            'img' => 'required',
        ]);

        // Create new campaign
        $campaign = new Campaign();
        $campaign->campaign_name = $request->input('activity_name');
        $campaign->campaign_type_id = $request->input('jenisKegiatan');
        $campaign->campaign_details = $request->input('activity_info');
        $campaign->campaign_result = $request->input('activity_result');
        $campaign->user_id = $request->input('person_in_charge');
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
        $users = User::all();
        $campaignType = Campaign_type::all();
        return view('marketing.submenu.preview_campaign', ['data' => $data, 'users' => $users,'campaignType' => $campaignType]);
    }

    public function show($id)
    {
        $campaign = Campaign::findOrFail($id);
        return response()->json($campaign);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'activity_name' => 'required|string|max:255',
            'jenisKegiatan' => 'required',
            'activity_info' => 'nullable|string|max:255',
            'activity_result' => 'nullable|string',
            'person_in_charge' => 'nullable|string|max:255',
            'activity_date' => 'nullable|date',
            'img' => 'nullable',
        ]);

        $campaign = Campaign::findOrFail($id);
        $campaign->campaign_name = $request->input('activity_name');
        $campaign->campaign_type_id = $request->input('jenisKegiatan');
        $campaign->campaign_details = $request->input('activity_info');
        $campaign->campaign_result = $request->input('activity_result');
        $campaign->user_id = $request->input('person_in_charge');
        $campaign->date = $request->input('activity_date');

        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/campaign'), $filename);
            $campaign->img_filepath = 'uploads/campaign/' . $filename;
        }

        $campaign->save();

        return redirect()->back()->with('success', 'Campaign updated successfully.');
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
            if ($campaign->img_filepath && file_exists(public_path($campaign->img_filepath))) {
                unlink(public_path($campaign->img_filepath));
            }
            // Delete the campaign
            $campaign->delete();

            return response()->json(['status' => 'success', 'message' => 'Campaign data deleted successfully!']);
        } else {
            // Log the exception for debugging
            return response()->json(['status' => 'failed', 'message' => 'Failed to delete record due to an unexpected error!']);
        }
    }
}
