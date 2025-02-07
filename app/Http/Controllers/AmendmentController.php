<?php

namespace App\Http\Controllers;

use App\Models\Penlat;
use App\Models\Regulator;
use App\Models\Regulator_amendment;
use Illuminate\Http\Request;

class AmendmentController extends Controller
{
    public function index()
    {
        $listAmendments = Regulator_amendment::all();
        $listRegulator = Regulator::all();
        $penlatList = Penlat::all();
        return view('master-data.amendment', ['listAmendments' => $listAmendments, 'penlatList' => $penlatList, 'listRegulator' => $listRegulator]);
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

        // Check if regulator is numeric
        if (!is_numeric($request->regulator)) {
            // Create a new regulator entry
            $regulator = Regulator::create([
                'description' => $request->regulator,
            ]);
        } else {
            // Check if regulator ID exists
            $regulator = Regulator::find($request->regulator);

            // If not found, create a new regulator
            if (!$regulator) {
                $regulator = Regulator::create([
                    'description' => 'New Regulator ID ' . $request->regulator, // You can change this message
                ]);
            }
        }

        // Create a new regulator amendment entry
        Regulator_amendment::create([
            'penlat_id' => $request->input('penlatId'),
            'translation' => $request->input('translation'),
            'description' => $request->input('description'),
            'regulator_id' => $regulator->id,
        ]);

        // Redirect back with success message
        return redirect()->back()->with('success', 'Regulator amendment has been successfully added.');
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
        // Validate required fields, but allow optional fields
        $request->validate([
            'edit_translation' => 'nullable',
            'edit_description' => 'nullable',
            'penlatId' => 'nullable',
            'edit_regulator' => 'nullable',
        ]);

        // Find the location by ID
        $location = Regulator_amendment::find($id);

        if (!$location) {
            return redirect()->back()->with('failed', 'Regulator amendment failed to update!');
        }

        // Prepare the update data (excluding null/empty values)
        $updateData = array_filter([
            'penlat_id' => $request->input('penlatId'),
            'translation' => $request->input('edit_translation'),
            'description' => $request->input('edit_description'),
            'regulator_id' => $request->input('edit_regulator'),
        ], function ($value) {
            return !is_null($value) && $value !== ''; // Exclude null and empty values
        });

        // Update only if there are fields to update
        if (!empty($updateData)) {
            $location->update($updateData);
            return redirect()->back()->with('success', 'Regulator amendment has been successfully updated.');
        }

        return redirect()->back()->with('info', 'No changes were made.');
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
