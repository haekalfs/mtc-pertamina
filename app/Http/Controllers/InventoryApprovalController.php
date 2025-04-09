<?php

namespace App\Http\Controllers;

use App\Models\Asset_approval;
use App\Models\Asset_item;
use App\Models\Inventory_tools;
use Illuminate\Http\Request;
use League\CommonMark\Extension\CommonMark\Renderer\Block\ThematicBreakRenderer;
use Yajra\DataTables\Facades\DataTables;

class InventoryApprovalController extends Controller
{
    public function index()
    {
        $listAsset = Asset_item::where('asset_status', 1)->get();

        return view('approval.inventory-tool.index', ['listAsset' => $listAsset]);
    }

    public function getData()
    {
        if (request()->ajax()) {
            $assets = Asset_item::with(['tools.img', 'condition', 'approvals.condition'])->where('asset_status', 1);

            return DataTables::of($assets)
                ->addColumn('details', function ($item) {
                    ob_start();
                    ?>
                    <div class="row">
                        <div class="col-md-3 d-flex justify-content-center align-items-start mt-2">
                            <a class="animateBox" href="<?= route('preview-asset', $item->tools->id) ?>">
                                <img src="<?= asset($item->tools->img->filepath) ?>" style="height: 150px; width: 160px; border: 1px solid rgb(202, 202, 202);" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
                            </a>
                        </div>
                        <div class="col-md-9 text-left mt-sm-2">
                            <h5 class="card-title font-weight-bold"><?= $item->tools->asset_name ?> - <?= $item->asset_code ?></h5>
                            <div class="ml-2">
                                <table class="table table-borderless table-sm">
                                    <?php if(optional($item->condition)->condition !== optional($item->approvals->condition)->condition): ?>
                                        <tr>
                                            <td style="width: 200px;"><i class="fa fa-chevron-right mr-2"></i> Asset Condition</td>
                                            <td style="width: 280px;">: <?= optional($item->condition)->condition ?></td>
                                            <td>&nbsp;<i class="fa fa-arrow-right"></i>&nbsp;</td>
                                            <td><?= optional($item->approvals->condition)->condition ?></td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php if($item->isUsed !== $item->approvals->isUsed): ?>
                                        <tr>
                                            <td><i class="fa fa-chevron-right mr-2"></i> Status</td>
                                            <td>: <?= $item->isUsed ? '<i class="fa fa-lock text-danger"></i> Used' : 'Available' ?></td>
                                            <td>&nbsp;<i class="fa fa-arrow-right"></i>&nbsp;</td>
                                            <td><?= $item->approvals->isUsed ? '<i class="fa fa-lock text-danger"></i> Used' : 'Available' ?></td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php if($item->asset_last_maintenance !== $item->approvals->asset_last_maintenance): ?>
                                        <tr>
                                            <td><i class="fa fa-chevron-right mr-2"></i> Last Maintenance</td>
                                            <td>: <?= $item->asset_last_maintenance ?? 'N/a' ?></td>
                                            <td>&nbsp;<i class="fa fa-arrow-right"></i>&nbsp;</td>
                                            <td><?= $item->approvals->asset_last_maintenance ?? 'N/a' ?></td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php if($item->asset_next_maintenance !== $item->approvals->asset_next_maintenance): ?>
                                        <tr>
                                            <td><i class="fa fa-chevron-right mr-2"></i> Next Maintenance</td>
                                            <td>: <?= $item->asset_next_maintenance ?? 'N/a' ?></td>
                                            <td>&nbsp;<i class="fa fa-arrow-right"></i>&nbsp;</td>
                                            <td><?= $item->approvals->asset_next_maintenance ?? 'N/a' ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php
                    return ob_get_clean();
                })
                ->addColumn('action', function ($item) {
                    return '<a class="btn btn-outline-secondary btn-md mr-2 edit-asset"><i class="fa fa-fw fa-edit"></i></a>';
                })
                ->rawColumns(['checkbox', 'details', 'action'])
                ->make(true);
        }
    }

    public function approveChanges(Request $request)
    {
        $getIds = explode(',', $request->input('formIds'));
        $getIds = array_map('trim', $getIds); // Removes any whitespace around each ID

        foreach ($getIds as $id) {
            $getRow = Asset_approval::where('asset_item_id', $id)->first();

            if (!$getRow) continue; // Skip if not found

            $asset = Asset_item::find($getRow->asset_item_id);

            if (!$asset) continue; // Skip if the original asset doesn't exist

            // Prepare data to update only if the field is not null
            $updateData = [];

            if (!is_null($getRow->asset_condition_id)) {
                $updateData['asset_condition_id'] = $getRow->asset_condition_id;
            }

            if (!is_null($getRow->asset_last_maintenance)) {
                $updateData['asset_last_maintenance'] = $getRow->asset_last_maintenance;
            }

            if (!is_null($getRow->asset_next_maintenance)) {
                $updateData['asset_next_maintenance'] = $getRow->asset_next_maintenance;
            }

            if (!is_null($getRow->isUsed) || $getRow->isUsed === 0) {
                $updateData['isUsed'] = $getRow->isUsed;

                // Check if the item belongs to an Inventory_tool
                if ($asset->tools) {
                    $tool = $asset->tools; // Ensure the relationship is correct

                    // Increment the used amount and decrement the stock
                    $tool->used_amount += 1;
                    $tool->asset_stock -= 1;

                    // Save the changes
                    $tool->save();
                }
            }

            // Only update if there's something to update
            if (!empty($updateData)) {
                $asset->update($updateData);
            }

            // Mark approval row as handled (status = 0)
            $asset->asset_status = 0;
            $asset->save();

            // Delete the approval row since the update was successful
            $getRow->delete();
        }

        return response()->json(['message' => 'Assets updated and approvals cleared.']);
    }

    public function rejectChanges(Request $request)
    {
        $getIds = explode(',', $request->input('formIds'));
        $getIds = array_map('trim', $getIds); // Clean up any whitespace

        foreach ($getIds as $id) {
            // Get the row in Asset_approval by asset_item_id
            $getRow = Asset_approval::where('asset_item_id', $id)->first();

            if (!$getRow) continue;

            // Find the related asset
            $asset = Asset_item::find($getRow->asset_item_id);

            if ($asset) {
                // Reset the status from 1 to 0
                $asset->asset_status = 0;
                $asset->save();
            }

            // Delete the pending approval
            $getRow->delete();
        }

        return response()->json(['message' => 'Asset changes rejected and approvals removed.']);
    }
}
