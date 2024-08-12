<?php

namespace App\Http\Controllers;

use App\Models\Inventory_tools;
use App\Models\Tool_img;
use Illuminate\Http\Request;

class InventoryToolController extends Controller
{

    public function store(Request $request)
    {
        // Validate the form inputs
        $validatedData = $request->validate([
            'asset_name' => 'required|string|max:255',
            'asset_number' => 'nullable|string|max:20',
            'maker' => 'required|string|max:100',
            'running_hour' => 'nullable|string|max:145',
            'condition' => 'required|string|max:255',
            'last_maintenance' => 'required|max:255',
            'next_maintenance' => 'required|max:255',
            'stock' => 'required|string|max:25',
            'maintenance_guide' => 'required|file|mimes:pdf,doc,docx|max:2048', // Assuming the guide is a file
            'tool_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Store the main data in the inventory tool table
        $tool = new Inventory_tools();
        $tool->asset_name = $request->input('asset_name');
        $tool->asset_id = $request->input('asset_number'); // Assuming 'Nomor Asset' corresponds to 'asset_id'
        $tool->asset_maker = $request->input('maker');
        $tool->asset_condition_id = $request->input('condition');
        $tool->asset_stock = $request->input('stock');
        $tool->initial_stock = $request->input('stock');
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
            'used_amount' => $tool->used_amount,
            'last_maintenance' => $tool->last_maintenance,
            'next_maintenance' => $tool->next_maintenance,
            'tool_image' => $tool->img ? asset($tool->img->filepath) : null,
        ];

        return response()->json($toolData);
    }

    public function update(Request $request, $id)
    {
        $tool = Inventory_tools::findOrFail($id);
        $tool->update($request->all());

        return response()->json(['message' => 'Tool updated successfully']);
    }

}
