<?php

namespace App\Http\Controllers;

use App\Models\MorningBriefing;
use App\Models\User;
use Illuminate\Http\Request;

class MorningBriefingController extends Controller
{
    public function index(Request $request)
    {
        // Get current year and the selected year and month from the request
        $currentYear = now()->year;
        $yearSelected = $request->input('year', 'all');  // Default to 'all' if not provided
        $monthSelected = $request->input('month', 'all'); // Default to 'all' if not provided

        // Query to filter data based on year and month
        $query = MorningBriefing::query();

        if ($yearSelected !== 'all') {
            $query->whereYear('created_at', $yearSelected);
        }

        if ($monthSelected !== 'all') {
            $query->whereMonth('created_at', $monthSelected);
        }

        // Add pagination (6 items per page)
        $data = $query->paginate(6);
        $users = User::all();

        return view('akhlak-view.submenu.morning-briefing', [
            'data' => $data,
            'users' => $users,
            'yearSelected' => $yearSelected,
            'monthSelected' => $monthSelected,
            'currentYear' => $currentYear
        ]);
    }

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

        // Create new briefing
        $briefing = new MorningBriefing();
        $briefing->briefing_name = $request->input('activity_name');
        $briefing->briefing_details = $request->input('activity_info');
        $briefing->briefing_result = $request->input('activity_result');
        $briefing->user_id = $request->input('person_in_charge');
        $briefing->date = $request->input('activity_date');
        // If an image was uploaded, store it in the related ToolImg table
        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/briefing'), $filename);
            $briefing->img_filepath = 'uploads/briefing/' . $filename;
        }
        $briefing->created_at = now();

        // Save to the database
        $briefing->save();

        // Redirect with a success message
        return redirect()->back()->with('success', 'Morning Briefing created successfully.');
    }

    public function preview_briefing($id)
    {
        $data = MorningBriefing::find($id);
        $users = User::all();
        return view('akhlak-view.submenu.preview_briefing', ['data' => $data, 'users' => $users]);
    }

    public function show($id)
    {
        $briefing = MorningBriefing::findOrFail($id);
        return response()->json($briefing);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'activity_name' => 'required|string|max:255',
            'activity_info' => 'nullable|string|max:255',
            'activity_result' => 'nullable|string',
            'person_in_charge' => 'nullable|string|max:255',
            'activity_date' => 'nullable|date',
            'img' => 'nullable',
        ]);

        $briefing = MorningBriefing::findOrFail($id);
        $briefing->briefing_name = $request->input('activity_name');
        $briefing->briefing_details = $request->input('activity_info');
        $briefing->briefing_result = $request->input('activity_result');
        $briefing->user_id = $request->input('person_in_charge');
        $briefing->date = $request->input('activity_date');

        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/briefing'), $filename);
            $briefing->img_filepath = 'uploads/briefing/' . $filename;
        }

        $briefing->save();

        return redirect()->back()->with('success', 'Morning Briefing updated successfully.');
    }
    /**
     * Delete a specific briefing by its ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete_briefing($id)
    {
        // Find the briefing by ID
        $briefing = MorningBriefing::find($id);

        // Check if the briefing exists
        if ($briefing) {
            if ($briefing->img_filepath && file_exists(public_path($briefing->img_filepath))) {
                unlink(public_path($briefing->img_filepath));
            }
            // Delete the briefing
            $briefing->delete();

            return response()->json(['status' => 'success', 'message' => 'Morning Briefing data deleted successfully!']);
        } else {
            // Log the exception for debugging
            return response()->json(['status' => 'failed', 'message' => 'Failed to delete record due to an unexpected error!']);
        }
    }
}
