<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $validatedData = $request->validate([
            'location_code' => 'required|string|max:25', // Ensure unique location codes
            'description' => 'required|string|max:255',
        ]);

        try {
            // Sanitize input
            $validatedData['location_code'] = trim(strip_tags($validatedData['location_code']));
            $validatedData['description'] = trim(strip_tags($validatedData['description']));

            // Create a new location entry
            Location::create($validatedData);

            // Redirect back with success message
            return redirect()->back()->with('success', 'Location has been successfully added.');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error storing location: ' . $e->getMessage());

            // Redirect back with error message
            return redirect()->back()->with('failed', 'Failed to add location. Please try again.');
        }
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
        // Find the location by ID early
        $location = Location::find($id);

        // Check if location exists
        if (!$location) {
            return redirect()->back()->with('failed', 'Location failed to update. Not found.');
        }

        // Validate the input data
        $validatedData = $request->validate([
            'location_code' => 'required|string|max:25', // Ensure uniqueness except for current record
            'description' => 'required|string|max:255',
        ]);

        try {
            // Sanitize input
            $validatedData['location_code'] = trim(strip_tags($validatedData['location_code']));
            $validatedData['description'] = trim(strip_tags($validatedData['description']));

            // Update the location details
            $location->update($validatedData);

            return redirect()->back()->with('success', 'Location has been successfully updated.');
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', 'An error occurred while updating the location.');
        }
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
