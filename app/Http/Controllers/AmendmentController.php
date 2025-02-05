<?php

namespace App\Http\Controllers;

use App\Models\Penlat;
use App\Models\Regulator_amendment;
use Illuminate\Http\Request;

class AmendmentController extends Controller
{
    public function index()
    {
        $listAmendments = Regulator_amendment::all();
        $penlatList = Penlat::all();
        return view('master-data.amendment', ['listAmendments' => $listAmendments, 'penlatList' => $penlatList]);
    }

    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'penlatId' => 'required',
            'translation' => 'required',
            'description' => 'required',
            'regulator' => 'required',
        ]);

        // Create a new location entry
        Regulator_amendment::create([
            'penlat_id' => $request->input('penlatId'),
            'translation' => $request->input('translation'),
            'description' => $request->input('description'),
            'regulator_id' => $request->input('regulator'),
        ]);

        // Redirect back with success message
        return redirect()->back()->with('success', 'Regulator_amendment has been successfully added.');
    }

    public function destroy(Request $request, $id)
    {
        // Find the location by ID
        $location = Regulator_amendment::find($id);

        // Check if the location exists
        if (!$location) {
            return response()->json([
                'status' => 'error',
                'message' => 'Regulator amendment not found.',
            ]);
        }

        // Check if the location is linked to any tools
        if ($location->certificates()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete. This item is linked to certificates.',
            ]);
        }

        // Delete the location
        $location->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Regulator amendment has been successfully deleted.',
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validate the input data
        $request->validate([
            'edit_translation' => 'required',
            'edit_description' => 'required',
            'penlatId' => 'required',
            'edit_regulator' => 'required',
        ]);

        // Find the location by ID
        $location = Regulator_amendment::find($id);

        // Check if location exists
        if (!$location) {
            return redirect()->back()->with('failed', 'Regulator amendment fail to update!.');
        }

        // Update the location details
        $location->update([
            'penlat_id' => $request->input('penlatId'),
            'translation' => $request->input('edit_translation'),
            'description' => $request->input('edit_description'),
            'regulator_id' => $request->input('edit_regulator'),
        ]);

        return redirect()->back()->with('success', 'Regulator amendment has been successfully updated.');
    }

    public function edit($id)
    {
        $location = Regulator_amendment::find($id);

        if (!$location) {
            return response()->json([
                'status' => 'error',
                'message' => 'Regulator_amendment not found.',
            ]);
        }

        return response()->json($location);
    }
}
