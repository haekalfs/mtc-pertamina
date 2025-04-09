<?php

namespace App\Http\Controllers;

use App\Models\Asset_approval;
use App\Models\Asset_condition;
use App\Models\Asset_item;
use App\Models\Audit_log;
use App\Models\Inventory_room;
use App\Models\Inventory_tools;
use App\Models\Location;
use App\Models\Tool_img;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InventoryToolController extends Controller
{
    public function main()
    {
        return view('operation.inventory_management.index');
    }

    public function audit_log(Request $request)
    {
        $errors = Audit_log::with('user') // Eager load the user relationship
            ->where('log_id', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($request->ajax()) {
            return DataTables::of($errors)
                ->addIndexColumn() // Automatically add index column
                ->addColumn('user', function ($error) {
                    return $error->user ? $error->user->name : 'Unknown'; // Get user name or default
                })
                ->editColumn('created_at', function ($error) {
                    return $error->created_at->format('Y-m-d H:i:s'); // Format the date
                })
                ->make(true);
        }

        return view('operation.inventory_management.audit_log');
    }

    public function tool_inventory(Request $request)
    {
        $locations = Location::all(); // For filter dropdown
        $selectedLocation = $request->locationFilter ?? '-1';
        $selectedCondition = $request->conditionFilter ?? '-1';
        $assetCondition = Asset_condition::all();

        // Check if it's an AJAX request for DataTables
        if ($request->ajax()) {
            $query = Inventory_tools::with(['location', 'condition', 'img', 'items']);

            if ($selectedLocation != '-1') {
                $query->where('location_id', $selectedLocation);
            }

            if ($selectedCondition != '-1') {
                // Filter based on the related 'items' table
                $query->whereHas('items', function ($q) use ($selectedCondition) {
                    $q->where('asset_condition_id', $selectedCondition);
                });
            }

            if ($request->has('search') && !empty($request->search['value'])) {
                $query->where(function ($q) use ($request) {
                    $q->where('asset_name', 'like', '%' . $request->search['value'] . '%')
                      ->orWhere('asset_id', 'like', '%' . $request->search['value'] . '%');
                });
            }

            if ($request->has('order') && isset($request->order[0]['column'])) {
                $columns = [
                    'inventory_tools.asset_name',       // Column index 0
                    'inventory_tools.asset_stock',     // Column index 1
                    'inventory_tools.used_amount',     // Column index 2
                ];

                $columnIndex = $request->order[0]['column']; // Column index from DataTables
                $sortDirection = $request->order[0]['dir']; // 'asc' or 'desc'

                if (isset($columns[$columnIndex])) {
                    $query->orderBy($columns[$columnIndex], $sortDirection);
                }
            }

            return DataTables::of($query)
                ->addColumn('tool', function ($item) {
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
                                        <td style="width: 200px;" class="mb-2"><i class="fa fa-chevron-right mr-2"></i> Maker</td>
                                        <td style="text-align: start;">: ' . $item->asset_maker . '</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 200px;" class="mb-2"><i class="fa fa-chevron-right mr-2"></i> User Manual</td>
                                        <td style="text-align: start;">: ';
                                        if (!empty($item->asset_guidance)) {
                                            $html .= '<a href="' . asset($item->asset_guidance) . '" target="_blank">
                                                        <span class="">Download File <i class="fa fa-download"></i></span>
                                                    </a>';
                                        } else {
                                            $html .= '<span class=""> - </span>';
                                        }

                                        $html .= '</td>
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
        $tool->created_by = Auth::id();

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
            $items->asset_last_maintenance = $request->input('last_maintenance');
            $items->asset_next_maintenance = $request->input('next_maintenance');
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

        Audit_log::createLog("New Asset has been added : " . $request->input('asset_name'), "critical", Auth::id(), 1);

        return redirect()->back()->with('success', 'Tool data and image uploaded successfully.');
    }

    public function preview_asset($id)
    {
        if (request()->ajax()) {
            // Load the inventory tool along with its items and their conditions
            $data = Inventory_tools::with(['img', 'items.condition', 'items.approvals'])->findOrFail($id);

            // Transform items for DataTables
            $items = $data->items->map(function ($item) {
                $approval = $item->approvals;

                // Check if approval exists meaningfully (e.g., has data)
                $useApproval = $approval && $approval->asset_code !== null;

                $lastMaintenanceDate = $useApproval
                    ? ($approval->asset_last_maintenance ? Carbon::parse($approval->asset_last_maintenance) : null)
                    : ($item->asset_last_maintenance ? Carbon::parse($item->asset_last_maintenance) : null);

                $hoursDifference = $lastMaintenanceDate ? Carbon::now()->diffInHours($lastMaintenanceDate) : 0;

                return [
                    'id' => $item->id,
                    'asset_code' => $item->asset_code,
                    'condition' => $useApproval ? optional($approval->condition)->condition : optional($item->condition)->condition,
                    'isUsed' => $useApproval ? $approval->isUsed : $item->isUsed,
                    'lastMaintenance' => $useApproval ? $approval->asset_last_maintenance : $item->asset_last_maintenance,
                    'nextMaintenance' => $useApproval ? $approval->asset_next_maintenance : $item->asset_next_maintenance,
                    'runningHours' => $hoursDifference . ' Hours',
                    'urlUsed' => route('inventory-tools.mark-as-used', $item->id),
                    'urlUnused' => route('inventory-tools.mark-as-unused', $item->id),
                    'asset_condition_id' => $item->asset_condition_id,
                    'asset_status' => $item->asset_status,
                    'approval_asset_status' => $item->asset_status, // still actual since approval is temp
                ];
            });

            return DataTables::of($items)
                ->setRowClass(function ($row) {
                    return $row['approval_asset_status'] == 1 ? 'conflict-row' : '';
                })
                ->make(true);
        }

        // Non-AJAX request: Return the view with additional data
        $data = Inventory_tools::with(['img', 'items.condition'])->findOrFail($id);
        $locations = Location::all();
        $assetCondition = Asset_condition::all();

        // Group the asset items by their condition
        $itemConditions = $data->items->groupBy('asset_condition_id')->map(function ($group) {
            return [
                'count' => $group->count(),
                'condition' => $group->first()->condition->badge,
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

            Audit_log::createLog("Asset has been updated : " . $tool->asset_name, "critical", Auth::id(), 1);

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

            Audit_log::createLog("Asset has been updated : " . $tool->asset_name, "critical", Auth::id(), 1);

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

            // Check if there are related asset items in the rooms_inventory relationship
            if ($asset->rooms_inventory()->exists() || $asset->penlat_requirement()->exists()) {
                return response()->json([
                    'error' => 'This asset is related to room inventory... remove the items from room inventory then proceed the deletion.'
                ], 400);  // Bad Request status code
            }

            // Delete related image if it exists
            if ($asset->img) {
                $asset->img->delete();
            }

            // Delete the asset
            $asset->delete();

            Audit_log::createLog("Asset has been deleted : " . $asset->asset_name, "critical", Auth::id(), 1);

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

        $message = "$item->asset_code | Asset marked as used successfully.";

        Audit_log::createLog("Asset has been updated to state 'Used' : " . $item->asset_code, "critical", Auth::id(), 1);

        // Redirect back with success message
        return redirect()->back()->with('success', $message);
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

        $message = "$item->asset_code | Asset marked as unused successfully.";
        // Redirect back with success message
        return redirect()->back()->with('success', $message);
    }

    public function generateQrCode($id)
    {
        $item = Asset_item::findOrFail($id);

        // Encrypt the asset ID
        $encryptedId = Crypt::encryptString($item->id);

        // Generate the QR code for the validate-asset route with the encrypted ID
        $qrCodeData = QrCode::format('png')
            ->size(200)
            ->merge('/storage/app/MTC.png', 0.3) // Merge with a 30% size of the QR code
            ->errorCorrection('H') // Use high error correction level
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
            'lastMaintenance' => 'required', // Assuming you have a separate table for asset conditions
            'nextMaintenance' => 'required', // Assuming you have a separate table for asset conditions
            'status' => 'required', // Assuming you have a separate table for asset conditions
        ]);

        // Find the asset item by ID
        $asset = Asset_item::findOrFail($request->asset_id);

        // delaying updates
        // $asset->asset_condition_id = $request->condition;
        // $asset->asset_last_maintenance = $request->lastMaintenance;
        // $asset->asset_next_maintenance = $request->nextMaintenance;
        // $asset->isUsed = $request->status;
        $asset->asset_status = 1;

        // Save the updated asset
        $asset->save();

        // if ($asset->tools && $asset->isUsed != 1) {
        //     $tool = $asset->tools; // Ensure the relationship is correct

        //     // Increment the used amount and decrement the stock
        //     $tool->used_amount += 1;
        //     $tool->asset_stock -= 1;

        //     // Save the changes
        //     $tool->save();
        // } else {
        //     $tool = $asset->tools; // Ensure the relationship is correct

        //     // Increment the used amount and decrement the stock
        //     $tool->used_amount -= 1;
        //     $tool->asset_stock += 1;

        //     // Save the changes
        //     $tool->save();
        // }

        $approval = Asset_approval::updateOrCreate(
            ['asset_item_id' => $asset->id], // Search condition
            [
                'asset_code' => $asset->asset_code,
                'asset_condition_id' => $request->condition,
                'asset_last_maintenance' => $request->lastMaintenance,
                'asset_next_maintenance' => $request->nextMaintenance,
                'isUsed' => $request->status,
            ]
        );

        Audit_log::createLog("Asset item has been updated : " . $asset->asset_code, "critical", Auth::id(), 1);

        // Optionally, add a success message or return a response
        return redirect()->back()->with('success', 'Asset '. $asset->asset_code . ' updated successfully!');
    }

    public function destroy_asset_per_item($id)
    {
        DB::beginTransaction();

        try {
            // Find the asset
            $asset = Asset_item::findOrFail($id);

            $assetCode = $asset->asset_code;
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

            Audit_log::createLog("Asset item has been deleted : " . $assetCode, "critical", Auth::id(), 1);

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

    public function maintenanceUpdate(Request $request)
    {
        $request->validate([
            'formIds' => 'required',
            'lastMaintenanceDate' => 'required|date',
            'nextMaintenanceDate' => 'required|date',
        ]);

        try {
            $formIds = $request->input('formIds');
            $lastMaintenanceDate = Carbon::parse($request->input('lastMaintenanceDate'))->format('Y-m-d');
            $nextMaintenanceDate = Carbon::parse($request->input('nextMaintenanceDate'))->format('Y-m-d');

            Audit_log::createLog("Asset item maintenance date has been updated : $formIds" . " | $lastMaintenanceDate to $nextMaintenanceDate", "critical", Auth::id(), 1);

            // Convert formIds to an array if needed
            if (!is_array($formIds)) {
                $formIds = explode(',', $formIds);
            }

            // Delay Update the database
            // Asset_item::whereIn('id', $formIds)
            //     ->update([
            //         'asset_last_maintenance' => $lastMaintenanceDate,
            //         'asset_next_maintenance' => $nextMaintenanceDate
            //     ]);

            foreach ($formIds as $formId) {
                $asset = Asset_item::find($formId);

                $asset->asset_status = 1;
                $asset->save();

                if ($asset) {
                    $updateData = [
                        'asset_code' => $asset->asset_code,
                        'asset_last_maintenance' => $lastMaintenanceDate,
                        'asset_next_maintenance' => $nextMaintenanceDate,
                    ];

                    // Conditionally add isUsed and asset_condition_id
                    if (!is_null($asset->isUsed) || !is_null($asset->asset_condition_id)) {
                        $updateData['isUsed'] = $asset->isUsed;
                        $updateData['asset_condition_id'] = $asset->asset_condition_id;
                    }

                    Asset_approval::updateOrCreate(
                        ['asset_item_id' => $asset->id],
                        $updateData
                    );
                }
            }
            // Return a JSON response
            return response()->json(['success' => true, 'message' => 'Certificates marked as expired successfully.']);
        } catch (\Exception $e) {
            // Handle errors and return a JSON error response
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function setAsUsed(Request $request)
    {
        $request->validate([
            'formIds' => 'required',
        ]);

        try {
            $formIds = $request->input('formIds');

            Audit_log::createLog("Asset item has been set to Used : $formIds", "critical", Auth::id(), 1);

            // Convert formIds to an array if needed
            if (!is_array($formIds)) {
                $formIds = explode(',', $formIds);
            }

            // Retrieve the assets based on IDs
            $items = Asset_item::whereIn('id', $formIds)->get();

            foreach ($items as $item) {
                if (!$item->isUsed) { // Check if it's not already used

                    $item->asset_status = 1;
                    $item->save();

                    $dataToUpdate = [
                        'asset_code' => $item->asset_code,
                        'asset_condition_id' => $item->asset_condition_id,
                        'isUsed' => true,
                    ];

                    // Conditionally include maintenance dates only if at least one exists
                    if ($item->asset_last_maintenance || $item->asset_next_maintenance) {
                        $dataToUpdate['asset_last_maintenance'] = $item->asset_last_maintenance;
                        $dataToUpdate['asset_next_maintenance'] = $item->asset_next_maintenance;
                    }

                    Asset_approval::updateOrCreate(
                        ['asset_item_id' => $item->id], // Unique key
                        $dataToUpdate
                    );

                    // Check if the item belongs to an Inventory_tool
                    // if ($item->tools) {
                    //     $tool = $item->tools; // Ensure the relationship is correct

                    //     // Increment the used amount and decrement the stock
                    //     $tool->used_amount += 1;
                    //     $tool->asset_stock -= 1;

                    //     // Save the changes
                    //     $tool->save();
                    // }
                }
            }

            // Return a JSON response
            return response()->json(['success' => true, 'message' => 'Items marked as used successfully.']);
        } catch (\Exception $e) {
            // Handle errors and return a JSON error response
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function setAsUnused(Request $request)
    {
        $request->validate([
            'formIds' => 'required',
        ]);

        try {
            $formIds = $request->input('formIds');

            Audit_log::createLog("Asset item has been set to Unused : $formIds", "critical", Auth::id(), 1);

            // Convert formIds to an array if needed
            if (!is_array($formIds)) {
                $formIds = explode(',', $formIds);
            }

            // Retrieve the assets based on IDs
            $items = Asset_item::whereIn('id', $formIds)->get();

            foreach ($items as $item) {
                if ($item->isUsed) { // Check if it's already used

                    $item->asset_status = 1;
                    $item->save();

                    $dataToUpdate = [
                        'asset_code' => $item->asset_code,
                        'asset_condition_id' => $item->asset_condition_id,
                        'isUsed' => false,
                    ];

                    // Conditionally include maintenance dates only if at least one exists
                    if ($item->asset_last_maintenance || $item->asset_next_maintenance) {
                        $dataToUpdate['asset_last_maintenance'] = $item->asset_last_maintenance;
                        $dataToUpdate['asset_next_maintenance'] = $item->asset_next_maintenance;
                    }

                    Asset_approval::updateOrCreate(
                        ['asset_item_id' => $item->id], // Unique key
                        $dataToUpdate
                    );
                    // Check if the item belongs to an Inventory_tool
                    // if ($item->tools) {
                    //     $tool = $item->tools; // Ensure the relationship is correct

                    //     // Increment the used amount and decrement the stock
                    //     $tool->used_amount -= 1;
                    //     $tool->asset_stock += 1;

                    //     // Save the changes
                    //     $tool->save();
                    // }
                }
            }

            // Return a JSON response
            return response()->json(['success' => true, 'message' => 'Items marked as used successfully.']);
        } catch (\Exception $e) {
            // Handle errors and return a JSON error response
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function changeConditions(Request $request)
    {
        $request->validate([
            'formIds' => 'required',
            'assetCondition' => 'required',
        ]);

        try {
            $formIds = $request->input('formIds');

            Audit_log::createLog("Asset item condition has been changed : $formIds", "critical", Auth::id(), 1);

            // Convert formIds to an array if needed
            if (!is_array($formIds)) {
                $formIds = explode(',', $formIds);
            }

            $assetCondition = $request->input('assetCondition');

            // Update the database
            // Asset_item::whereIn('id', $formIds)
            //     ->update([
            //         'asset_condition_id' => $assetCondition
            //     ]);

            foreach ($formIds as $formId) {
                $asset = Asset_item::find($formId);

                $asset->asset_status = 1;
                $asset->save();

                if ($asset) {
                    $data = [
                        'asset_code' => $asset->asset_code,
                        'asset_condition_id' => $assetCondition,
                    ];

                    if (!is_null($asset->asset_last_maintenance)) {
                        $data['asset_last_maintenance'] = $asset->asset_last_maintenance;
                    }

                    if (!is_null($asset->asset_next_maintenance)) {
                        $data['asset_next_maintenance'] = $asset->asset_next_maintenance;
                    }

                    if (!is_null($asset->isUsed)) {
                        $data['isUsed'] = $asset->isUsed;
                    }

                    Asset_approval::updateOrCreate(
                        ['asset_item_id' => $asset->id],
                        $data
                    );
                }
            }
            // Return a JSON response
            return response()->json(['success' => true, 'message' => 'Certificates marked as expired successfully.']);
        } catch (\Exception $e) {
            // Handle errors and return a JSON error response
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getAssetChartData($id)
    {
        $used = Asset_item::where('isUsed', 1)
            ->where('inventory_tool_id', $id)
            ->count();

        $unused = Asset_item::where(function ($query) {
                $query->whereNull('isUsed')->orWhere('isUsed', 0);
            })
            ->where('inventory_tool_id', $id)
            ->count();

        return response()->json([
            ['label' => 'Used', 'y' => $used],
            ['label' => 'Unused', 'y' => $unused],
        ]);
    }
}
