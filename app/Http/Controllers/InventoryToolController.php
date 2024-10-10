<?php

namespace App\Http\Controllers;

use App\Models\Asset_condition;
use App\Models\Asset_item;
use App\Models\Inventory_room;
use App\Models\Inventory_tools;
use App\Models\Location;
use App\Models\Tool_img;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InventoryToolController extends Controller
{
    public function tool_inventory(Request $request)
    {
        $locations = Location::all(); // For filter dropdown
        $selectedLocation = $request->locationFilter ?? '-1';
        $assetCondition = Asset_condition::all();

        // Check if it's an AJAX request for DataTables
        if ($request->ajax()) {
            $query = Inventory_tools::with(['location', 'condition', 'img']);

            if ($selectedLocation != '-1') {
                $query->where('location_id', $selectedLocation);
            }

            return DataTables::of($query)
                ->addColumn('tool', function ($item) {
                    $hoursDifference = \Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($item->last_maintenance));

                    $html = '
                    <div class="row">
                        <div class="col-md-4 d-flex justify-content-center align-items-start mt-2">
                            <a class="animateBox" href="' . route('preview-asset', $item->id) . '">
                                <img src="' . asset($item->img->filepath) . '" style="height: 150px; width: 160px; border: 1px solid rgb(202, 202, 202);" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
                            </a>
                        </div>
                        <div class="col-md-8 text-left mt-sm-2">
                            <h5 class="card-title font-weight-bold">' . $item->asset_name . '</h5>
                            <div class="ml-2">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td style="width: 200px;" class="mb-2"><i class="fa fa-chevron-right mr-2"></i> Nomor Aset</td>
                                        <td style="text-align: start;">: ' . $item->asset_id . '</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 200px;" class="mb-2"><i class="fa fa-chevron-right mr-2"></i> Location</td>
                                        <td style="text-align: start;">: ' . $item->location->description . '</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 200px;" class="mb-2"><i class="fa fa-chevron-right mr-2"></i> Running Hour</td>
                                        <td style="text-align: start;">: ' . $hoursDifference . ' hours</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 200px;" class="mb-2"><i class="fa fa-chevron-right mr-2"></i> Next Maintenance</td>
                                        <td style="text-align: start;">: ' . \Carbon\Carbon::parse($item->next_maintenance)->format('d-M-Y') . '</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>';

                    return $html;
                })
                ->addColumn('stock', function ($item) {
                    return $item->asset_stock ? $item->asset_stock . ' Unit(s)' : 'Out of Stock';
                })
                ->addColumn('used', function ($item) {
                    return $item->used_amount ? $item->used_amount . ' Unit(s)' : '0 Unit';
                })
                ->addColumn('condition', function ($item) {
                    // Group asset items by condition and count them
                    $itemConditions = $item->items->groupBy('asset_condition_id')->map(function ($group) {
                        return [
                            'count' => $group->count(),
                            'condition' => $group->first()->condition->badge, // Assuming condition has a 'badge' field
                        ];
                    });

                    // Start building the table HTML for the conditions
                    $conditionHtml = '<table class="table table-borderless table-sm">';

                    // Iterate through the grouped conditions and add each to the table
                    foreach ($itemConditions as $condition) {
                        $conditionHtml .= '
                            <tr>
                                <td><i class="ti-minus mr-2"></i>' . $condition['count'] . ' Items are ' . $condition['condition'] . '</td>
                            </tr>';
                    }

                    // Check if maintenance is required and add it to the table
                    $nextMaintenanceDate = strtotime($item->next_maintenance);
                    $currentDate = strtotime(date('Y-m-d'));

                    if ($nextMaintenanceDate < $currentDate) {
                        $conditionHtml .= '
                            <tr>
                                <td><i class="ti-minus mr-2"></i><span class="badge out-of-stock">Maintenance Required</span></td>
                            </tr>';
                    }

                    $conditionHtml .= '</table>';

                    return $conditionHtml;
                })
                ->addColumn('action', function ($item) {
                    return '
                    <div class="text-center">
                        <a data-id="' . $item->id . '" href="#" class="btn btn-outline-secondary btn-md mr-2 edit-tool">
                            <i class="fa fa-edit"></i>
                        </a>
                        <button data-id="' . $item->id . '" class="btn btn-outline-secondary btn-md mr-2 view-tool">
                            <i class="fa fa-info-circle"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['tool', 'condition', 'action']) // Indicate which columns contain HTML
                ->make(true);
        }

        // Get count of assets that are out of stock
        $OutOfStockCount = Inventory_tools::where('asset_stock', '=', 0)->count();

        // Get count of assets that are reaching their next maintenance date
        $requiredMaintenanceCount = Inventory_tools::where('next_maintenance', '<=', now())->count();

        if($OutOfStockCount){
            Session::flash('out-of-stock', "$OutOfStockCount Assets is out of Stock!");
        }

        if($requiredMaintenanceCount){
            Session::flash('maintenance', "$requiredMaintenanceCount Assets is require Maintenances!");
        }

        return view('operation.submenu.tool_inventory', [
            'selectedLocation' => $selectedLocation,
            'locations' => $locations,
            'assetCondition' => $assetCondition,
            'OutOfStockCount',
            'requiredMaintenanceCount'
        ]);
    }

    public function store(Request $request)
    {
        // Validate the form inputs
        $validatedData = $request->validate([
            'asset_name' => 'required',
            'asset_number' => 'nullable|string|max:20',
            'maker' => 'required',
            'condition' => 'required',
            'location' => 'required',
            'last_maintenance' => 'required',
            'next_maintenance' => 'required',
            'stock' => 'required',
            'maintenance_guide' => 'sometimes|file|mimes:pdf,doc,docx,xlsx,xls', // Assuming the guide is a file
            'tool_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        // Store the main data in the inventory tool table
        $tool = new Inventory_tools();
        $tool->asset_name = $request->input('asset_name');
        $tool->asset_id = $request->input('asset_number'); // Assuming 'Nomor Asset' corresponds to 'asset_id'
        $tool->asset_maker = $request->input('maker');
        $tool->asset_stock = $request->input('stock');
        $tool->initial_stock = $request->input('stock');
        $tool->location_id = $request->input('location');
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

        // Loop through and create individual asset items
        for ($i = 0; $i < $request->input('stock'); $i++) {
            $items = new Asset_item();
            // Use str_pad to ensure asset codes like 01, 02, etc.
            $items->asset_code = $request->input('asset_number') . '-' . str_pad($i + 1, 2, '0', STR_PAD_LEFT);
            $items->asset_condition_id = $request->input('condition');
            $items->inventory_tool_id = $tool->id;
            $items->save();
        }

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

    public function preview_asset($id)
    {
        // Load the inventory tool along with its items and their conditions
        $data = Inventory_tools::with(['img', 'items.condition'])->findOrFail($id);
        $locations = Location::all();
        $assetCondition = Asset_condition::all();

        // Group the asset items by their condition
        $itemConditions = $data->items->groupBy('asset_condition_id')->map(function ($group) {
            return [
                'count' => $group->count(),
                'condition' => $group->first()->condition->badge, // Assuming condition has a 'name' field
            ];
        });

        return view('operation.submenu.preview-tool', [
            'data' => $data,
            'locations' => $locations,
            'assetCondition' => $assetCondition,
            'itemConditions' => $itemConditions
        ]);
    }

    public function fetch_info($id)
    {
        $tool = Inventory_tools::with('img')->findOrFail($id);

        // Prepare the data, including the image path if available
        $toolData = [
            'id' => $tool->id,
            'asset_name' => $tool->asset_name,
            'asset_id' => $tool->asset_id,
            'asset_maker' => $tool->asset_maker,
            'initial_stock' => $tool->initial_stock,
            'asset_stock' => $tool->asset_stock,
            'location' => $tool->location->description,
            'used_amount' => $tool->used_amount,
            'last_maintenance' => $tool->last_maintenance,
            'next_maintenance' => $tool->next_maintenance,
            'asset_guidance' => $tool->asset_guidance ? asset($tool->asset_guidance) : null, // Ensure the file path is correct
            'tool_image' => $tool->img ? asset($tool->img->filepath) : null,
        ];

        return response()->json($toolData);
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
        // Start a transaction to ensure data consistency
        DB::beginTransaction();

        try {
            // Find the tool
            $tool = Inventory_tools::findOrFail($id);
            $currentUsedAmount = $tool->used_amount;
            $newUsedAmount = $request->input('used_amount');
            $initialStocks = $tool->initial_stock; // Use the current stock

            // Check if the new used amount is greater than the stock
            if($newUsedAmount > $initialStocks){
                // Rollback and return error response
                DB::rollBack();
                return response()->json(['message' => 'Used amount cannot be greater than stocks!'], 400);
            }

            // Calculate the new stock
            $stocks = $initialStocks - $newUsedAmount;

            // Update tool details
            $tool->asset_name = $request->input('asset_name');
            $tool->asset_id = $request->input('asset_number');
            $tool->asset_maker = $request->input('maker');
            $tool->location_id = $request->input('location');
            $tool->used_amount = $newUsedAmount;
            $tool->asset_stock = $stocks;
            $tool->last_maintenance = $request->input('last_maintenance');
            $tool->next_maintenance = $request->input('next_maintenance');

            // Sync the used items based on the updated used amount
            if ($newUsedAmount > $currentUsedAmount) {
                $itemsToMark = $newUsedAmount - $currentUsedAmount;

                $items = Asset_item::where('inventory_tool_id', $tool->id)
                    ->where(function ($query) {
                        $query->where('isUsed', false)
                            ->orWhereNull('isUsed');
                    })
                    ->orderBy('asset_code', 'asc')
                    ->take($itemsToMark)
                    ->get();

                foreach ($items as $item) {
                    $item->isUsed = true;
                    $item->save();
                }
            } elseif ($newUsedAmount < $currentUsedAmount) {
                $itemsToUnmark = $currentUsedAmount - $newUsedAmount;

                $items = Asset_item::where('inventory_tool_id', $tool->id)
                    ->where('isUsed', true)
                    ->orderBy('asset_code', 'asc')
                    ->take($itemsToUnmark)
                    ->get();

                foreach ($items as $item) {
                    $item->isUsed = false;
                    $item->save();
                }
            }

            // Handle tool image upload
            if ($request->hasFile('tool_image')) {
                if ($tool->img && file_exists(public_path($tool->img->filepath))) {
                    unlink(public_path($tool->img->filepath));
                }

                $image = $request->file('tool_image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/tools'), $filename);

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

            // Handle maintenance guide upload
            if ($request->hasFile('maintenance_guide')) {
                if ($tool->asset_guidance && file_exists(public_path($tool->asset_guidance))) {
                    unlink(public_path($tool->asset_guidance));
                }

                $guideFile = $request->file('maintenance_guide');
                $guideFilename = time() . '_' . $guideFile->getClientOriginalName();
                $guideFile->move(public_path('uploads/guides'), $guideFilename);

                $tool->asset_guidance = 'uploads/guides/' . $guideFilename;
            }

            // Save the tool
            $tool->save();

            // Commit the transaction
            DB::commit();

            // Flash success message and return success response
            Session::flash('success', 'Success Updating Asset!');
            return response()->json(['message' => 'Tool updated successfully'], 200);

        } catch (\Exception $e) {
            // Rollback the transaction in case of any error
            DB::rollBack();

            // Return error response
            return response()->json(['message' => 'Failed to update tool. Error: ' . $e->getMessage()], 500);
        }
    }

    public function update_partially(Request $request, $id)
    {
        // Start the DB transaction
        DB::beginTransaction();

        try {
            $tool = Inventory_tools::findOrFail($id);
            $currentUsedAmount = $tool->used_amount;
            $newUsedAmount = $request->input('used_amount');
            $initialStocks = $request->input('stock');

            // Check if the new used amount is greater than the stock
            if($newUsedAmount > $initialStocks){
                // Rollback and return error response
                DB::rollBack();
                return response()->json(['message' => 'Used amount cannot be greater than stocks!'], 400);
            }

            // Adjust the stock
            $stocks = $initialStocks - $newUsedAmount;

            // Update tool details
            $tool->initial_stock = $initialStocks;
            $tool->used_amount = $newUsedAmount;
            $tool->asset_stock = $stocks;
            $tool->last_maintenance = $request->input('last_maintenance');
            $tool->next_maintenance = $request->input('next_maintenance');

            // Sync the used items based on the new used amount
            if ($newUsedAmount > $currentUsedAmount) {
                $itemsToMark = $newUsedAmount - $currentUsedAmount;

                $items = Asset_item::where('inventory_tool_id', $tool->id)
                    ->where(function($query) {
                        $query->where('isUsed', false)
                            ->orWhereNull('isUsed');
                    })
                    ->orderBy('asset_code', 'asc')
                    ->take($itemsToMark)
                    ->get();

                foreach ($items as $item) {
                    $item->isUsed = true;
                    $item->save();
                }
            } elseif ($newUsedAmount < $currentUsedAmount) {
                $itemsToUnmark = $currentUsedAmount - $newUsedAmount;

                $items = Asset_item::where('inventory_tool_id', $tool->id)
                    ->where('isUsed', true)
                    ->orderBy('asset_code', 'asc')
                    ->take($itemsToUnmark)
                    ->get();

                foreach ($items as $item) {
                    $item->isUsed = false;
                    $item->save();
                }
            }

            // Handle tool image upload
            if ($request->hasFile('tool_image')) {
                if ($tool->img && $tool->img->filepath && file_exists(public_path($tool->img->filepath))) {
                    unlink(public_path($tool->img->filepath));
                }

                $image = $request->file('tool_image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/tools'), $filename);

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

            // Save the tool
            $tool->save();

            // Commit the transaction
            DB::commit();

            return response()->json(['message' => 'Tool updated successfully'], 200);
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();
            return response()->json(['message' => 'An error occurred during the update. Please try again.'], 500);
        }
    }

    public function delete_asset($id)
    {
        try {
            // Check if the asset exists in the Inventory_tools model
            $asset = Inventory_tools::findOrFail($id);

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

    public function markAsUsed($id)
    {
        // Find the asset item by ID and update 'isUsed' to true
        $item = Asset_item::findOrFail($id);
        if (!$item->isUsed) {
            $item->isUsed = true;
            $item->save();

            // Update the parent tool (Inventory_tools)
            $tool = $item->tools;

            // Increment the used amount and decrement the stock
            $tool->used_amount += 1;
            $tool->asset_stock -= 1;

            // Save the changes
            $tool->save();
        }

        // Redirect back with success message
        return redirect()->back()->with('success', 'Asset marked as used successfully.');
    }

    public function markAsUnused($id)
    {
        // Find the asset item by ID and update 'isUsed' to false
        $item = Asset_item::findOrFail($id);
        if ($item->isUsed) {
            $item->isUsed = false;
            $item->save();

            // Update the parent tool (Inventory_tools)
            $tool = $item->tools;

            // Decrement the used amount and increment the stock
            $tool->used_amount -= 1;
            $tool->asset_stock += 1;

            // Save the changes
            $tool->save();
        }

        // Redirect back with success message
        return redirect()->back()->with('success', 'Asset marked as unused successfully.');
    }

    public function generateQrCode($id)
    {
        $item = Asset_item::findOrFail($id);

        // Encrypt the asset ID
        $encryptedId = Crypt::encryptString($item->id);

        // Generate the QR code for the validate-asset route with the encrypted ID
        $qrCodeData = QrCode::format('png')
            ->size(200)
            ->generate(route('validate-asset', $encryptedId));

        // Encode the QR code as base64
        $base64QrCode = base64_encode($qrCodeData);

        return response()->json([
            'asset_code' => $item->asset_code,
            'link' => route('validate-asset', $encryptedId),
            'qr_code' => 'data:image/png;base64,' . $base64QrCode
        ]);
    }

    public function validate_asset($encryptedId)
    {
        try {
            // Decrypt the asset ID
            $id = Crypt::decryptString($encryptedId);

            // Find the asset using the decrypted ID
            $findAsset = Asset_item::findOrFail($id);
            $data = Inventory_tools::with('img')->findOrFail($findAsset->inventory_tool_id);

            return view('operation.submenu.validate_asset', ['data' => $data, 'asset' => $findAsset]);
        } catch (\Exception $e) {
            // Handle errors (e.g., invalid decryption or asset not found)
            return redirect()->route('preview-asset', $id)->withErrors(['error' => 'Invalid QR code or asset not found.']);
        }
    }

    public function updateAssetCondition(Request $request)
    {
        // Validate the request data
        $request->validate([
            'asset_id' => 'required',
            'condition' => 'required', // Assuming you have a separate table for asset conditions
        ]);

        // Find the asset item by ID
        $asset = Asset_item::findOrFail($request->asset_id);

        // Update the asset_condition_id column
        $asset->asset_condition_id = $request->condition;

        // Save the updated asset
        $asset->save();

        // Optionally, add a success message or return a response
        return redirect()->back()->with('success', 'Asset condition updated successfully!');
    }

    public function destroy_asset_per_item($id)
    {
        DB::beginTransaction();

        try {
            // Find the asset
            $asset = Asset_item::findOrFail($id);

            // Check if the asset is currently in use
            if ($asset->isUsed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete asset. It is currently in use.'
                ], 403);  // Forbidden status code
            }

            // Find the associated Inventory Tool
            $inventoryTool = $asset->tools;  // Access the related Inventory Tool via the relationship

            if ($inventoryTool) {
                // Reduce the initial_stock by 1
                $inventoryTool->initial_stock = $inventoryTool->initial_stock - 1;
                $inventoryTool->save();  // Save the updated inventory stock
            }

            // Proceed with deletion if the asset is not in use
            $asset->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asset deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error deleting asset.'
            ], 500);
        }
    }
}
