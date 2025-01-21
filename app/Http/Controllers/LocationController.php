<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $listLocations = Location::all();
        return view('master-data.location', ['listLocation' => $listLocations]);
    }

    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'location_code' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        // Create a new location entry
        Location::create([
            'location_code' => $request->input('location_code'),
            'description' => $request->input('description'),
        ]);

        // Redirect back with success message
        return redirect()->back()->with('success', 'Location has been successfully added.');
    }

    public function destroy(Request $request, $id)
    {
        // Find the location by ID
        $location = Location::find($id);

        // Check if the location exists
        if (!$location) {
            return response()->json([
                'status' => 'error',
                'message' => 'Location not found.',
            ]);
        }

        // Check if the location is linked to any tools
        if ($location->tools()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete. This location is linked to an asset.',
            ]);
        }

        // Delete the location
        $location->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Location has been successfully deleted.',
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validate the input data
        $request->validate([
            'location_code' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        // Find the location by ID
        $location = Location::find($id);

        // Check if location exists
        if (!$location) {
            return redirect()->back()->with('failed', 'Location fail to update!.');
        }

        // Update the location details
        $location->update([
            'location_code' => $request->input('location_code'),
            'description' => $request->input('description'),
        ]);

        return redirect()->back()->with('success', 'Location has been successfully updated.');
    }

    public function edit($id)
    {
        $location = Location::find($id);

        if (!$location) {
            return response()->json([
                'status' => 'error',
                'message' => 'Location not found.',
            ]);
        }

        return response()->json($location);
    }
}
