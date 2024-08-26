<?php

namespace App\Http\Controllers;

use App\Models\Inventory_room;
use App\Models\Inventory_tools;
use App\Models\Tool_img;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class InventoryToolController extends Controller
{

    public function store(Request $request)
    {
        // Validate the form inputs
        $validatedData = $request->validate([
            'asset_name' => 'required',
            'asset_number' => 'nullable|string|max:20',
            'maker' => 'required',
            'running_hour' => 'required',
            'condition' => 'required',
            'location' => 'required',
            'last_maintenance' => 'required',
            'next_maintenance' => 'required',
            'stock' => 'required',
            'maintenance_guide' => 'required|file|mimes:pdf,doc,docx', // Assuming the guide is a file
            'tool_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        // Store the main data in the inventory tool table
        $tool = new Inventory_tools();
        $tool->asset_name = $request->input('asset_name');
        $tool->asset_id = $request->input('asset_number'); // Assuming 'Nomor Asset' corresponds to 'asset_id'
        $tool->asset_maker = $request->input('maker');
        $tool->asset_condition_id = $request->input('condition');
        $tool->asset_stock = $request->input('stock');
        $tool->initial_stock = $request->input('stock');
        $tool->location_id = $request->input('location');
        $tool->used_time = $request->input('running_hour');
        $tool->last_maintenance = $request->input('last_maintenance');
        $tool->next_maintenance = $request->input('next_maintenance');

        // If a maintenance guide file is uploaded, save the file path
        if ($request->hasFile('maintenance_guide')) {
            $guideFile = $request->file('maintenance_guide');
            $guideFilename = time() . '_' . $guideFile->getClientOriginalName();
            $guideFile->move(public_path('uploads/guides'), $guideFilename);
            $tool->asset_guidance = 'uploads/guides/' . $guideFilename;
        }

        $tool->save();

        // If an image was uploaded, store it in the related ToolImg table
        if ($request->hasFile('tool_image')) {
            $image = $request->file('tool_image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/tools'), $filename);

            $toolImage = new Tool_img();
            $toolImage->inventory_tool_id = $tool->id; // foreign key relationship
            $toolImage->filename = $filename;
            $toolImage->filepath = 'uploads/tools/' . $filename;
            $toolImage->save();
        }

        return redirect()->back()->with('success', 'Tool data and image uploaded successfully.');
    }

    public function edit($id)
    {
        $tool = Inventory_tools::with('img')->findOrFail($id);

        // Prepare the data, including the image path if available
        $toolData = [
            'id' => $tool->id,
            'asset_name' => $tool->asset_name,
            'asset_id' => $tool->asset_id,
            'asset_maker' => $tool->asset_maker,
            'used_time' => $tool->used_time,
            'asset_condition_id' => $tool->asset_condition_id,
            'initial_stock' => $tool->initial_stock,
            'location_id' => $tool->location_id,
            'used_amount' => $tool->used_amount,
            'last_maintenance' => $tool->last_maintenance,
            'next_maintenance' => $tool->next_maintenance,
            'asset_guidance' => $tool->asset_guidance ? asset($tool->asset_guidance) : null, // Ensure the file path is correct
            'tool_image' => $tool->img ? asset($tool->img->filepath) : null,
        ];

        return response()->json($toolData);
    }

    public function update(Request $request, $id)
    {
        $tool = Inventory_tools::findOrFail($id);
        $existingData = Inventory_tools::findOrFail($id);

        $existingUsedAmount = $existingData->used_amount;

        $usedAmount = $request->input('used_amount');
        $existingUsedAmount = $usedAmount;

        $initialStocks = $request->input('stock');
        $stocks = $initialStocks;
        if($usedAmount <= 0){
            $stocks = $initialStocks;
        } else {
            $stocks -= $existingUsedAmount;
        }

        // Update text fields
        $tool->asset_name = $request->input('asset_name');
        $tool->asset_id = $request->input('asset_number');
        $tool->asset_maker = $request->input('maker');
        $tool->used_time = $request->input('running_hour');
        $tool->asset_condition_id = $request->input('condition');
        $tool->location_id = $request->input('location');
        $tool->initial_stock = $initialStocks;
        $tool->used_amount = $existingUsedAmount;
        $tool->asset_stock = $stocks;
        $tool->last_maintenance = $request->input('last_maintenance');
        $tool->next_maintenance = $request->input('next_maintenance');

        // Handle the tool image update
        if ($request->hasFile('tool_image')) {
            // Delete the old image if it exists
            if ($tool->img && $tool->img->filepath) {
                unlink(public_path($tool->img->filepath));
            }

            // Upload the new image
            $image = $request->file('tool_image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/tools'), $filename);

            // Update or create the related image record
            if ($tool->img) {
                $tool->img->update([
                    'filepath' => 'uploads/tools/' . $filename,
                    'filename' => $filename,
                ]);
            } else {
                $tool->img()->create([
                    'filepath' => 'uploads/tools/' . $filename,
                    'filename' => $filename,
                ]);
            }
        }

        // Handle the optional file upload for the maintenance guide
        if ($request->hasFile('maintenance_guide')) {
            // Delete the old guide if it exists
            if ($tool->asset_guidance) {
                unlink(public_path($tool->asset_guidance));
            }

            // Upload the new guide
            $guideFile = $request->file('maintenance_guide');
            $guideFilename = time() . '_' . $guideFile->getClientOriginalName();
            $guideFile->move(public_path('uploads/guides'), $guideFilename);

            // Update the tool with the new guide path
            $tool->asset_guidance = 'uploads/guides/' . $guideFilename;
        }

        $tool->save();

        Session::flash('success', 'Success Updating Asset!');
        return response()->json(['message' => 'Tool updated successfully']);
    }

    public function delete_asset($id)
    {
        try {
            // Check if the asset exists in the Inventory_tools model
            $asset = Inventory_tools::findOrFail($id);

            // Check if the asset is used in the Penlat_batch table
            $isAssetUsed = Inventory_room::where('inventory_tool_id', $id)->exists();

            if ($isAssetUsed) {
                return response()->json(['error' => 'Asset is currently in use and cannot be deleted.'], 400);
            }

            // Delete related image
            if ($asset->img) {
                $asset->img->delete();
            }

            // Delete the asset
            $asset->delete();

            return response()->json(['success' => 'Record deleted successfully.'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found.'], 404);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to delete asset: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete record due to an unexpected error.'], 500);
        }
    }
}
