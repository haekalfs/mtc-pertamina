<?php

namespace App\Http\Controllers;

use App\Models\Asset_condition;
use App\Models\Infografis_peserta;
use App\Models\Inventory_room;
use App\Models\Inventory_tools;
use App\Models\Location;
use App\Models\Penlat;
use App\Models\Penlat_batch;
use App\Models\Penlat_certificate;
use App\Models\Penlat_requirement;
use App\Models\Penlat_utility_usage;
use App\Models\Profit;
use App\Models\Receivables_participant_certificate;
use App\Models\Room;
use App\Models\Tool_img;
use App\Models\Utility;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class OperationController extends Controller
{
    public function index($yearSelected = null)
    {
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        // Set the selected year
        $yearSelected = $yearSelected ?? $nowYear;

        // Get counts for various items
        $getPesertaCount = Infografis_peserta::whereYear('tgl_pelaksanaan', $yearSelected)->count();
        $getKebutuhanCount = Penlat_requirement::count();
        $getAssetCount = Inventory_tools::count();
        $getAssetStock = Inventory_tools::sum('initial_stock');

        // Get count of assets that are out of stock
        $OutOfStockCount = Inventory_tools::where('asset_stock', '=', 0)->count();

        // Get count of assets that are reaching their next maintenance date
        $requiredMaintenanceCount = Inventory_tools::where('next_maintenance', '<=', now())->count();

        $totalAttention = $OutOfStockCount + $requiredMaintenanceCount;

        return view('operation.index', compact(
            'getPesertaCount',
            'totalAttention',
            'getKebutuhanCount',
            'getAssetCount',
            'getAssetStock',
            'yearsBefore',
            'yearSelected',
            'OutOfStockCount',
            'requiredMaintenanceCount'
        ));
    }

    public function getChartData($year)
    {
        // Fetch and group the data by nama_program and batch, summing the total usage
        $dataBatch = Penlat_batch::select('penlat_batch.nama_program', 'penlat_batch.batch', DB::raw('SUM(penlat_utility_usage.amount) as total_usage'))
        ->join('penlat_utility_usage', 'penlat_batch.id', '=', 'penlat_utility_usage.penlat_batch_id')
        ->whereYear('penlat_batch.date', $year)
        ->groupBy('penlat_batch.nama_program', 'penlat_batch.batch')
        ->orderBy('total_usage', 'DESC')
        ->get();

        // Split the data into top 5 and the rest
        $top5Data = $dataBatch->take(5);
        $otherData = $dataBatch->slice(5);

        // Calculate the total for the 'Lain-lain' category
        $otherTotalUsage = $otherData->sum('total_usage');

        // Prepare the data for the chart, including batch information
        $mostUsedUtility = $top5Data->map(function ($row) {
            return [
                "label" => $row->nama_program,
                "batch" => $row->batch,
                "y" => $row->total_usage
            ];
        })->toArray();

        // Add the 'Lain-lain' category if there are more than 5 programs
        if ($otherTotalUsage > 0) {
            $mostUsedUtility[] = [
                "label" => 'Lain-lain',
                "batch" => null, // No batch info for 'Lain-lain'
                "y" => $otherTotalUsage
            ];
        }

        // Fetch and group the data by nama_program, counting the number of participants
        $data = Infografis_peserta::select('nama_program', DB::raw('count(*) as total'))
            ->whereYear('tgl_pelaksanaan', $year)
            ->groupBy('nama_program')
            ->having('total', '>', 500) // Only include categories with more than 500 participants
            ->get();

        // Calculate the count of participants not included in the main categories
        $otherCount = Infografis_peserta::whereYear('tgl_pelaksanaan', $year)
            ->whereNotIn('nama_program', $data->pluck('nama_program'))
            ->count();

        // If there are other categories, add them as "Others"
        if ($otherCount > 0) {
            $data->push((object)[
                'nama_program' => 'Lain-lain',
                'total' => $otherCount
            ]);
        }

        // Fetch data by month for the spline chart
        $dataByMonth1 = Infografis_peserta::select(DB::raw('DATE_FORMAT(tgl_pelaksanaan, "%Y-%m") as month'), DB::raw('count(*) as total'))
            ->whereYear('tgl_pelaksanaan', $year)
            ->where('kategori_program', 'STCW')
            ->groupBy('month')
            ->get();

        $countSTCW = Infografis_peserta::whereYear('tgl_pelaksanaan', $year)
            ->where('kategori_program', 'STCW')
            ->count();

        $countNonSTCW = Infografis_peserta::whereYear('tgl_pelaksanaan', $year)
            ->where('kategori_program', 'NON STCW')
            ->count();

        $dataByMonth2 = Infografis_peserta::select(DB::raw('DATE_FORMAT(tgl_pelaksanaan, "%Y-%m") as month'), DB::raw('count(*) as total'))
            ->whereYear('tgl_pelaksanaan', $year)
            ->where('kategori_program', 'NON STCW')
            ->groupBy('month')
            ->get();

        // Prepare data points for the spline chart
        $dataPointsSpline1 = [];
        $dataPointsSpline2 = [];
        foreach ($dataByMonth1 as $row) {
            $dataPointsSpline1[] = [
                "x" => Carbon::parse($row->month)->timestamp * 1000,  // Parse month as timestamp
                "y" => $row->total
            ];
        }

        foreach ($dataByMonth2 as $row) {
            $dataPointsSpline2[] = [
                "x" => Carbon::parse($row->month)->timestamp * 1000,  // Parse month as timestamp
                "y" => $row->total
            ];
        }

        // Prepare data points for the pie chart
        $dataPointsPie = [];
        foreach ($data as $row) {
            $dataPointsPie[] = [
                "label" => $row->nama_program,
                "symbol" => substr($row->nama_program, 0, 2),
                "y" => $row->total
            ];
        }

        // Return the data points in a JSON response
        return response()->json([
            'dataPointsSpline1' => $dataPointsSpline1,
            'dataPointsSpline2' => $dataPointsSpline2,
            'pieDataPoints' => $dataPointsPie,
            'mostUsedUtility' => $mostUsedUtility,
            'countSTCW' => $countSTCW,
            'countNonSTCW' => $countNonSTCW
        ]);
    }

    public function participant_infographics(Request $request)
    {
        // Determine the current year and generate the range of years
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        // Set the selected year
        $currentYear = $periodeSelected ?? $nowYear;

        $selectedArray = [
            'namaPenlat' => $request->input('namaPenlat'),
            'stcw' => $request->input('stcw'),
            'jenisPenlat' => $request->input('jenisPenlat'),
            'tw' => $request->input('tw'),
            'periode' => $request->input('periode'),
        ];

        if ($request->ajax()) {
            $query = Infografis_peserta::query();

            // Apply filters based on the request
            if($request->namaPenlat != 1){
                $query->where('nama_program', $request->namaPenlat);
            }

            if($request->stcw != 1){
                $query->where('kategori_program', $request->stcw);
            }

            if($request->jenisPenlat != 1){
                $query->where('jenis_pelatihan', $request->jenisPenlat);
            }

            if($request->tw != 1){
                $query->where('realisasi', $request->tw);
            }

            if($request->periode != 1){
                $query->whereYear('tgl_pelaksanaan', $request->periode);
            }

            // Return the DataTables response
            return DataTables::of($query)
                ->addColumn('action', function($row) {
                    return '<a data-item-id="' . $row->id . '" class="btn btn-outline-secondary btn-sm mr-2 edit-btn"  href="#" data-toggle="modal" data-target="#editModal"><i class="fa fa-edit"></i> Edit</a>';
                })
                ->make(true);
        }

        $filter = Infografis_peserta::all();
        //filtering list
        $listPenlat = $filter->unique('nama_program');
        $listStcw = $filter->unique('kategori_program');
        $listJenisPenlat = $filter->unique('jenis_pelatihan');
        $listTw = $filter->unique('realisasi');

        return view('operation.submenu.participant-infographics', [
            'yearsBefore' => $yearsBefore,
            'listPenlat' => $listPenlat,
            'listStcw' => $listStcw,
            'listJenisPenlat' => $listJenisPenlat,
            'listTw' => $listTw,
            'selectedArray' => $selectedArray
        ]);
    }

    public function participant_infographics_import_page()
    {
        return view('operation.submenu.participant_infographics_import_page');
    }

    public function infografis_store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'nama_peserta' => 'required|string|max:255',
            'nama_program' => 'required|string|max:255',
            'batch' => 'required|string|max:145',
            'tgl_pelaksanaan' => 'required|date',
            'tempat_pelaksanaan' => 'required|string|max:255',
            'jenis_pelatihan' => 'required|string|max:255',
            'keterangan' => 'required|string|max:255',
            'subholding' => 'required|string|max:255',
            'perusahaan' => 'required|string|max:255',
            'kategori_program' => 'required|string|max:255',
            'realisasi' => 'required|string|max:255',
        ]);

        // Create a new infografis_peserta record
        $infografisPeserta = new Infografis_peserta();
        $infografisPeserta->nama_peserta = $request->input('nama_peserta');
        $infografisPeserta->nama_program = $request->input('nama_program');
        $infografisPeserta->batch = $request->input('batch');
        $infografisPeserta->tgl_pelaksanaan = $request->input('tgl_pelaksanaan');
        $infografisPeserta->tempat_pelaksanaan = $request->input('tempat_pelaksanaan');
        $infografisPeserta->jenis_pelatihan = $request->input('jenis_pelatihan');
        $infografisPeserta->keterangan = $request->input('keterangan');
        $infografisPeserta->subholding = $request->input('subholding');
        $infografisPeserta->perusahaan = $request->input('perusahaan');
        $infografisPeserta->kategori_program = $request->input('kategori_program');
        $infografisPeserta->realisasi = $request->input('realisasi');

        // Save the record to the database
        $infografisPeserta->save();

        $findBatch = Penlat_batch::where('batch', $validatedData['batch'])->first();

        if($findBatch){
            // Find the corresponding Penlat_certificate record
            $penlatCert = Penlat_certificate::where('penlat_batch_id', $findBatch->id)->first();

            if ($penlatCert) {
                // Update or create the Receivables_participant_certificate record
                Receivables_participant_certificate::updateOrCreate(
                    [
                        'infografis_peserta_id' => $infografisPeserta->id,
                        'penlat_certificate_id' => $penlatCert->id,
                    ],
                    [
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $message = 'Participant Data & Certificates has been successfully saved!';
        // Redirect back with a success message
        return redirect()->back()->with('success', $message);
    }

    public function edit($id)
    {
        $participant = Infografis_peserta::find($id);
        return response()->json($participant);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_peserta' => 'required|string|max:255',
            'nama_program' => 'required|string|max:255',
            'tgl_pelaksanaan' => 'required|date',
            'tempat_pelaksanaan' => 'required|string|max:255',
            'jenis_pelatihan' => 'required|string|max:255',
            'keterangan' => 'required|string|max:255',
            'subholding' => 'required|string|max:255',
            'perusahaan' => 'required|string|max:255',
            'kategori_program' => 'required|string|max:255',
            'realisasi' => 'required|string|max:255', // Ensure this matches the correct column name
        ]);

        $participant = Infografis_peserta::find($id);
        if ($participant) {
            $participant->nama_peserta = $request->input('nama_peserta');
            $participant->nama_program = $request->input('nama_program');
            $participant->tgl_pelaksanaan = $request->input('tgl_pelaksanaan');
            $participant->tempat_pelaksanaan = $request->input('tempat_pelaksanaan');
            $participant->jenis_pelatihan = $request->input('jenis_pelatihan');
            $participant->keterangan = $request->input('keterangan');
            $participant->subholding = $request->input('subholding');
            $participant->perusahaan = $request->input('perusahaan');
            $participant->kategori_program = $request->input('kategori_program');
            $participant->realisasi = $request->input('realisasi'); // Update this field as per your schema
            $participant->save();

            return response()->json($participant);
        } else {
            return response()->json(['error' => 'Participant not found'], 404);
        }
    }

    public function delete_data_peserta($id)
    {
        try {
            // Check if the Infografis_peserta exists
            $infografisPeserta = Infografis_peserta::find($id);

            if (!$infografisPeserta) {
                return response()->json(['status' => 'failed', 'message' => 'Record not found!']);
            }

            // Delete related certificates
            $infografisPeserta->certificate()->delete();

            // Delete the Infografis_peserta record
            $infografisPeserta->delete();

            return response()->json(['status' => 'success', 'message' => 'Peserta data deleted successfully!']);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to delete Peserta: ' . $e->getMessage());

            return response()->json(['status' => 'failed', 'message' => 'Failed to delete record due to an unexpected error!']);
        }
    }

    public function utility_store(Request $request)
    {
        // Validate the request
        $request->validate([
            'penlat' => 'required',
            'batch' => 'required',
            'date' => 'required',
            'image' => 'sometimes|image',
            'program' => 'sometimes'
        ]);

        DB::beginTransaction();

        try {
            // Handle the image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/penlat_utility'), $filename);
                $imagePath = 'uploads/penlat_utility/' . $filename;
            }

            $checkData = Penlat_batch::where('batch', $request->batch)->exists();

            if (!$checkData) {
                // Create a new Penlat_batch entry
                $penlatUtility = Penlat_batch::updateOrCreate(
                    [
                        'batch' => $request->batch,
                        'penlat_id' => $request->penlat,
                    ],
                    [
                        'nama_program' => $request->program,
                        'date' => $request->date,
                        'filepath' => $imagePath,
                    ]
                );
            } else {
                // If the batch exists, fetch the existing Penlat_batch record
                $penlatUtility = Penlat_batch::where('batch', $request->batch)->first();

                // Check if usages for this batch already exist
                $checkIfExist = Penlat_utility_usage::where('penlat_batch_id', $penlatUtility->id)->exists();

                if ($checkIfExist) {
                    // Redirect with a warning message if usages already exist
                    DB::rollBack();
                    return redirect()->route('utility')->with('warning', "Usages for batch $penlatUtility->batch already exist...");
                }
                // Update the necessary fields
                if($imagePath){
                    $penlatUtility->filepath = $imagePath;  // Replace 'field_name' with the actual field and 'new_value' with the new value
                    // Save the changes to the database
                    $penlatUtility->save();
                }
            }

            // Loop through the request
            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'qty_') === 0) {
                    $id = substr($key, 4); // Extract the tool id from the key
                    $quantity = $value;

                    // Get the price and total inputs
                    $priceKey = 'price_' . $id;
                    $totalKey = 'total_' . $id;

                    // Sanitize inputs by removing currency symbols and thousand separators
                    $price = isset($request->$priceKey) ? preg_replace('/[^\d]/', '', preg_replace('/[^\d.,]/', '', $request->$priceKey)) : null;
                    $total = isset($request->$totalKey) ? preg_replace('/[^\d]/', '', preg_replace('/[^\d.,]/', '', $request->$totalKey)) : null;

                    // Convert to valid number format
                    $price = str_replace('.', '', $price);  // Remove thousand separator
                    $price = str_replace(',', '.', $price); // Convert decimal separator

                    $total = str_replace('.', '', $total);  // Remove thousand separator
                    $total = str_replace(',', '.', $total); // Convert decimal separator

                    // Only proceed if the quantity is not zero, null, or empty
                    if (!is_null($quantity) && $quantity !== '' && $quantity > 0) {

                        // Update or create usage record
                        Penlat_utility_usage::updateOrCreate(
                            [
                                'penlat_batch_id' => $penlatUtility->id,
                                'utility_id' => $id,
                            ],
                            [
                                'amount' => $quantity,
                                'price' => $price,   // Store the price
                                'total' => $total,   // Store the total
                            ]
                        );
                    }
                }
            }

            $this->updateUsageCost($penlatUtility->id);
            // Commit the transaction
            DB::commit();

            // Redirect back with a success message
            return redirect()->back()->with('success', 'Penlat utility data saved successfully!');

        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Log the error (optional)
            Log::error('Error storing penlat utility data: ' . $e->getMessage());

            // Redirect or return a response with an error message
            return redirect()->back()->with('error', 'Failed to save penlat utility data. Please try again.');
        }
    }

    public function utility_insert_new_item(Request $request, $batchId)
    {
        DB::beginTransaction();
        $findBatch = Penlat_batch::find($batchId);

        try {
            // Loop through the request
            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'qty_') === 0) {
                    $id = substr($key, 4); // Extract the tool id from the key
                    $quantity = $value;

                    // Get the price and total inputs
                    $priceKey = 'price_' . $id;
                    $totalKey = 'total_' . $id;

                    // Sanitize inputs by removing currency symbols and thousand separators
                    $price = isset($request->$priceKey) ? preg_replace('/[^\d]/', '', preg_replace('/[^\d.,]/', '', $request->$priceKey)) : null;
                    $total = isset($request->$totalKey) ? preg_replace('/[^\d]/', '', preg_replace('/[^\d.,]/', '', $request->$totalKey)) : null;

                    // Convert to valid number format
                    $price = str_replace('.', '', $price);  // Remove thousand separator
                    $price = str_replace(',', '.', $price); // Convert decimal separator

                    $total = str_replace('.', '', $total);  // Remove thousand separator
                    $total = str_replace(',', '.', $total); // Convert decimal separator

                    // Only proceed if the quantity is not zero, null, or empty
                    if (!is_null($quantity) && $quantity !== '' && $quantity > 0) {

                        // Update or create usage record
                        Penlat_utility_usage::updateOrCreate(
                            [
                                'penlat_batch_id' => $findBatch->id,
                                'utility_id' => $id,
                            ],
                            [
                                'amount' => $quantity,
                                'price' => $price,   // Store the price
                                'total' => $total,   // Store the total
                            ]
                        );
                    }
                }
            }
            $this->updateUsageCost($findBatch->id);
            // Commit the transaction
            DB::commit();

            // Redirect back with a success message
            return redirect()->back()->with('success', 'Penlat utility data saved successfully!');

        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Log the error (optional)
            Log::error('Error storing penlat utility data: ' . $e->getMessage());

            // Redirect or return a response with an error message
            return redirect()->back()->with('error', 'Failed to save penlat utility data. Please try again.');
        }
    }

    public function delete_batch_usage($id)
    {
        try {
            // Check if the Penlat is used in the Penlat_batch table
            $isExist = Penlat_utility_usage::where('penlat_batch_id', $id)->exists();

            if (!$isExist) {
                return response()->json(['status' => 'failed', 'message' => 'Cannot be deleted because it is not found!']);
            }

            $usages = Penlat_utility_usage::where('penlat_batch_id', $id);
            $usages->delete();

            // Get the associated batch before deleting the record
            $findBatch = Penlat_batch::find($id);

            // Call the updateUsageCost function to update the profit based on remaining items
            $this->updateUsageCost($findBatch->id);

            return response()->json(['status' => 'success', 'message' => 'Penlat utility data deleted successfully!']);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to delete Penlat: ' . $e->getMessage());
            return response()->json(['status' => 'failed', 'message' => 'Failed to delete record due to an unexpected error!']);
        }
    }

    public function delete_item_usage($id)
    {
        try {
            // Check if the Penlat is used in the Penlat_utility_usage table
            $usage = Penlat_utility_usage::find($id);

            if (!$usage) {
                return response()->json(['status' => 'failed', 'message' => 'Cannot be deleted because it is not found!']);
            }

            // Get the associated batch before deleting the record
            $findBatch = Penlat_batch::find($usage->penlat_batch_id);

            // Delete the usage record
            $usage->delete();

            // Call the updateUsageCost function to update the profit based on remaining items
            $this->updateUsageCost($findBatch->id);

            return response()->json(['status' => 'success', 'message' => 'Penlat utility data deleted successfully!']);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to delete Penlat: ' . $e->getMessage());
            return response()->json(['status' => 'failed', 'message' => 'Failed to delete record due to an unexpected error!']);
        }
    }

    public function room_inventory_store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'nama_ruangan' => 'required',
            'room_image' => 'nullable',
            'location' => 'required',
            'tool.*' => 'required',
            'amount.*' => 'required',
        ]);

        // Handle the image upload
        $imagePath = null;
        if ($request->hasFile('room_image')) {
            $image = $request->file('room_image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/room_images'), $filename);
            $imagePath = 'uploads/room_images/' . $filename;
        }

        // Create the inventory room entry
        $inventoryRoom = Room::create([
            'room_name' => $validated['nama_ruangan'],
            'location_id' => $validated['location'],
            'filepath' => $imagePath,
        ]);

         // Save the tools associated with this room
        foreach ($validated['tool'] as $key => $toolId) {
            Inventory_room::create([
                'room_id' => $inventoryRoom->id,
                'inventory_tool_id' => $toolId,
                'amount' => $validated['amount'][$key],
            ]);
        }

        return redirect()->route('room-inventory')->with('success', 'Room inventory saved successfully!');
    }

    public function room_inventory_insert_item(Request $request, $roomId)
    {
        // Validate the request
        $validated = $request->validate([
            'tool.*' => 'required',
            'amount.*' => 'required',
        ]);

         // Save the tools associated with this room
        foreach ($validated['tool'] as $key => $toolId) {
            Inventory_room::create([
                'room_id' => $roomId,
                'inventory_tool_id' => $toolId,
                'amount' => $validated['amount'][$key],
            ]);
        }

        return redirect()->route('preview-room', $roomId)->with('success', 'Room inventory saved successfully!');
    }

    public function tool_inventory(Request $request)
    {
        $locations = Location::all();

        $assetCondition = Asset_condition::all();
        $selectedLocation = '-1';

        if($request->locationFilter){
            $selectedLocation = $request->locationFilter;
        }

        if($selectedLocation != '-1'){
            $assets = Inventory_tools::where('location_id', $selectedLocation)->get();
        } else {
            $assets = Inventory_tools::all();
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
            'assets' => $assets,
            'selectedLocation' => $selectedLocation,
            'locations' => $locations,
            'assetCondition' => $assetCondition,
            'OutOfStockCount',
            'requiredMaintenanceCount'
        ]);
    }

    public function room_inventory(Request $request)
    {
        $assets = Inventory_tools::all();
        $locations = Location::all();

        $selectedLocation = '-1';

        if($request->locationFilter){
            $selectedLocation = $request->locationFilter;
        }

        if($selectedLocation != '-1'){
            $rooms = Room::where('location_id', $selectedLocation)->get();
        } else {
            $rooms = Room::all();
        }

        return view('operation.submenu.room_inventory', ['assets' => $assets, 'locations' => $locations,'selectedLocation' => $selectedLocation, 'rooms' => $rooms]);
    }

    public function utility(Request $request)
    {
        if ($request->ajax()) {

            $validateData = Penlat_utility_usage::groupBy('penlat_batch_id')->pluck('penlat_batch_id')->toArray();

            $data = Penlat_batch::whereIn('id', $validateData);

            // Apply filters based on the selected values from the dropdowns
            if ($request->namaPenlat && $request->namaPenlat != '-1') {
                $data->where('penlat_id', $request->namaPenlat);
            }

            if ($request->batch && $request->batch != '-1') {
                $data->where('batch', $request->batch);
            }

            $query = $data->with(['penlat', 'penlat_usage'])
            ->select('penlat_batch.*');

            $utilities = Utility::all();

            return DataTables::of($query)
                ->addColumn('image', function($item) {
                    $imageUrl = $item->filepath ? asset($item->filepath) : asset('img/default-img.png');
                    return '<a href="'.route('preview-utility', $item->id).'"><img src="'.$imageUrl.'" style="height: 100px; width: 100px;" alt="" class="img-fluid rounded mb-2 shadow animateBox"></a>';
                })
                ->addColumn('description', function($item) {
                    return $item->penlat->description;
                })
                ->addColumn('utilities', function($item) use ($utilities) {
                    $utilityData = [];
                    foreach ($utilities as $tool) {
                        $utility = $item->penlat_usage->firstWhere('utility_id', $tool->id);
                        $utilityData['utility_'.$tool->id] = $utility ? $utility->amount : '-';
                    }
                    return $utilityData;
                })
                ->addColumn('batch', function($item) {
                    return '<span class="font-weight-bold text-center">' . $item->batch . '</span>';
                })
                ->addColumn('date', function($item) {
                    return $item->date;
                })
                ->rawColumns(['image', 'batch'])
                ->make(true);
        }

        $penlatList = Penlat::all();
        $batchList = Penlat_batch::all();
        $utility = Utility::all();

        return view('operation.submenu.utility', [
            'penlatList' => $penlatList,
            'batchList' => $batchList,
            'utilities' => $utility
        ]);
    }

    public function preview_utility($id)
    {
        $utility = Penlat_batch::find($id);
        // Make sure to check if $utility exists to avoid errors
        if ($utility) {
            // Get utilities that are not used in the penlat_usage
            $utilities = Utility::whereNotIn('id', $utility->penlat_usage->pluck('utility_id'))->get();
        } else {
            $utilities = collect(); // Return an empty collection if batch not found
        }
        return view('operation.submenu.preview-utility', ['data' => $utility, 'utilities' => $utilities]);
    }

    public function update_utility_usage(Request $request, $id)
    {
        // Retrieve the penlat_usage record by ID
        $penlatUsage = Penlat_utility_usage::findOrFail($id);

        // Validate the input data
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'price' => 'required',
            'total' => 'required'
        ]);

        // Sanitize and format the price and total to remove currency symbols and formatting
        $price = preg_replace('/[^\d.,]/', '', $request->input('price')); // Remove currency symbols
        $price = str_replace('.', '', $price);  // Remove thousand separator
        $price = str_replace(',', '.', $price); // Convert decimal separator to dot

        $total = preg_replace('/[^\d.,]/', '', $request->input('total')); // Remove currency symbols
        $total = str_replace('.', '', $total);  // Remove thousand separator
        $total = str_replace(',', '.', $total); // Convert decimal separator to dot

        // Check if penlatUsage was found and proceed with update
        if ($penlatUsage) {
            $penlatUsage->amount = $request->input('amount');
            $penlatUsage->price = $price;
            $penlatUsage->total = $total;
            $penlatUsage->save();

            $findBatch = Penlat_batch::find($penlatUsage->penlat_batch_id);
            $this->updateUsageCost($findBatch->id);
            return response()->json(['message' => 'Utility usage updated successfully.'], 200);
        } else {
            return response()->json(['message' => 'Utility not found.'], 404);
        }
    }

    public function fetch_room_data($id)
    {
        // Fetch the room with its related tools and amount
        $inventoryRoom = Room::with('list.tools')->find($id);

        // Check if room exists
        if (!$inventoryRoom) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        // Prepare data to return
        $tools = [];
        $amounts = [];
        foreach ($inventoryRoom->list as $listItem) {
            foreach ($listItem->tools as $tool) {
                $tools[] = $tool->inventory_tool_id;
                $amounts[] = $tool->amount;
            }
        }

        // Return the data as JSON
        return response()->json([
            'room_name' => $inventoryRoom->room_name,
            'tools' => $tools,
            'amount' => $amounts
        ]);
    }

    public function delete_room($id)
    {
        try {
            // Find the room by ID
            $room = Room::with('list')->find($id);

            // Check if the room exists
            if (!$room) {
                return response()->json(['error' => 'Requirement cannot be deleted because it is not found!'], 400);
            }

            // Delete the related list items
            $room->list()->delete();

            // Delete the room itself
            $room->delete();

            return response()->json(['success' => 'Record deleted successfully.'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found.'], 404);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to delete room: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete record due to an unexpected error.'], 500);
        }
    }

    public function delete_item_room($id)
    {
        try {
            // Check if the Penlat is used in the Penlat_batch table
            $isItemExist = Inventory_room::where('id', $id)->exists();

            if (!$isItemExist) {
                return redirect()->back()->with('success', 'Failed to delete record due to unexist item.');
            }

            $item = Inventory_room::where('id', $id);
            $item->delete();

            return redirect()->back()->with('success', 'Deleted Successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('success', 'Deleted Successfully.');
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to delete Penlat: ' . $e->getMessage());
            return response()->json(['error' => 'Record not found.'], 404);
        }
    }

    public function update_room_data(Request $request, $roomId)
    {
        // Validate the request
        $validated = $request->validate([
            'nama_ruangan' => 'required',
            'room_image' => 'nullable',
            'location' => 'required',
        ]);

        try {
            // Find the existing room by ID
            $room = Room::findOrFail($roomId);

            // Handle the image upload
            $imagePath = $room->filepath; // Keep the existing image path

            if ($request->hasFile('room_image')) {
                // Unlink the previous image if it exists
                if ($room->filepath && file_exists(public_path($room->filepath))) {
                    unlink(public_path($room->filepath));
                }

                // Upload the new image
                $image = $request->file('room_image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/room_images'), $filename);
                $imagePath = 'uploads/room_images/' . $filename;
            }

            // Update the room entry
            $room->update([
                'room_name' => $validated['nama_ruangan'],
                'location_id' => $validated['location'],
                'filepath' => $imagePath,
            ]);

            // Redirect back with a success message
            return redirect()->route('preview-room', $roomId)->with('success', 'Room updated successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found.'], 404);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to update room: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update room. Please try again.');
        }
    }

    public function update_room_item(Request $request, $id)
    {
        // Retrieve the penlat_usage record by ID
        $inventoryItem = Inventory_room::findOrFail($id);

        // Validate and update the amount
        $request->validate([
            'amount' => 'required|numeric|min:0'
        ]);


        if ($inventoryItem) {
            $inventoryItem->amount = $request->input('amount');
            $inventoryItem->save();

            return response()->json(['message' => 'Utility usage updated successfully.'], 200);
        } else {
            return response()->json(['message' => 'Utility not found.'], 404);
        }
    }

    public function preview_room($roomId)
    {
        $data = Room::find($roomId);
        $assets = Inventory_tools::all();
        $locations = Location::all();
        return view('operation.submenu.preview-room-inventory', ['assets' => $assets, 'data' => $data, 'locations' => $locations]);
    }

    public function preview_room_user($roomId)
    {
        $data = Room::find($roomId);
        $assets = Inventory_tools::all();
        $locations = Location::all();
        return view('operation.submenu.preview-room-inventory-user', ['assets' => $assets, 'data' => $data, 'locations' => $locations]);
    }

    public function updateUsageCost($penlatBatchId)
    {
        // Get the sum of all 'total' values from Penlat_utility_usage for the given batch
        $totalUsageCost = Penlat_utility_usage::where('penlat_batch_id', $penlatBatchId)
            ->sum('total');

        $findBatch = Penlat_batch::find($penlatBatchId);
        // Find the related Profit record and update the 'penlat_usage' column
        Profit::where('pelaksanaan', $findBatch->batch)
            ->update(['penlat_usage' => $totalUsageCost]);
    }
}
