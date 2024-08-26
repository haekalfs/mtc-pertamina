<?php

namespace App\Http\Controllers;

use App\Models\Asset_condition;
use App\Models\Infografis_peserta;
use App\Models\Inventory_room;
use App\Models\Inventory_tools;
use App\Models\Location;
use App\Models\Penlat;
use App\Models\Penlat_batch;
use App\Models\Penlat_requirement;
use App\Models\Penlat_utility_usage;
use App\Models\Room;
use App\Models\Tool_img;
use App\Models\Utility;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        $getPesertaCount = Infografis_peserta::count();
        $getKebutuhanCount = Penlat_requirement::count();
        $getAssetCount = Inventory_tools::count();
        $getAssetStock = Inventory_tools::sum('initial_stock');

        return view('operation.index', compact('getPesertaCount', 'getKebutuhanCount', 'getAssetCount', 'getAssetStock', 'yearsBefore', 'yearSelected'));
    }

    public function getChartData($year)
    {
        // Filter data by the specified year in 'tgl_pelaksanaan' column
        $dataBatch = Penlat_batch::select('penlat_batch.id', 'penlat_batch.nama_program', 'penlat_batch.batch', DB::raw('SUM(penlat_utility_usage.amount) as total_usage'))
            ->join('penlat_utility_usage', 'penlat_batch.id', '=', 'penlat_utility_usage.penlat_batch_id')
            ->whereYear('penlat_batch.date', $year)
            ->groupBy('penlat_batch.id', 'penlat_batch.nama_program', 'penlat_batch.batch')
            ->orderBy('total_usage', 'DESC')
            ->get();

        $mostUsedUtility = [];
        foreach ($dataBatch as $row) {
            $mostUsedUtility[] = [
                "label" => $row->batch,
                "y" => $row->total_usage
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

        // Fetch data by date for the spline chart
        $dataByDate1 = Infografis_peserta::select('tgl_pelaksanaan', DB::raw('count(*) as total'))
            ->whereYear('tgl_pelaksanaan', $year)
            ->where('kategori_program', 'STCW')
            ->groupBy('tgl_pelaksanaan')
            ->get();

        $dataByDate2 = Infografis_peserta::select('tgl_pelaksanaan', DB::raw('count(*) as total'))
            ->whereYear('tgl_pelaksanaan', $year)
            ->where('kategori_program', 'NON STCW')
            ->groupBy('tgl_pelaksanaan')
            ->get();

        // Prepare data points for the spline chart
        $dataPointsSpline1 = [];
        $dataPointsSpline2 = [];
        foreach ($dataByDate1 as $row) {
            $dataPointsSpline1[] = [
                "x" => Carbon::parse($row->tgl_pelaksanaan)->timestamp * 1000,
                "y" => $row->total
            ];
        }

        foreach ($dataByDate2 as $row) {
            $dataPointsSpline2[] = [
                "x" => Carbon::parse($row->tgl_pelaksanaan)->timestamp * 1000,
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
            'mostUsedUtility' => $mostUsedUtility
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
            // Check if the Penlat is used in the Penlat_batch table
            $isExist = Infografis_peserta::where('id', $id)->exists();

            if (!$isExist) {
                return response()->json(['status' => 'failed', 'message' => 'Cannot be deleted because rows not found!']);
            }

            $usages = Infografis_peserta::where('id', $id);
            $usages->delete();

            return response()->json(['status' => 'success', 'message' => 'Peserta data deleted successfully!']);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to delete Penlat: ' . $e->getMessage());
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
            'image' => 'required|image',
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
            }

            // Loop through the tools and save the PenlatUtilityUsage entries
            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'qty_') === 0) {
                    $id = substr($key, 4); // Extract the tool id from the key
                    $quantity = $value;
                    $unit = $request->input('unit_' . $id);

                    Penlat_utility_usage::updateOrCreate(
                        [
                            'penlat_batch_id' => $penlatUtility->id,
                            'utility_id' => $id, // Assuming you want to store the tool's id as utility_id
                        ],
                        [
                            'amount' => $quantity,
                        ]
                    );
                }
            }

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

    public function tool_inventory()
    {
        $assets = Inventory_tools::all();
        $locations = Location::all();
        $assetCondition = Asset_condition::all();

        return view('operation.submenu.tool_inventory', ['assets' => $assets, 'locations' => $locations, 'assetCondition' => $assetCondition]);
    }

    public function tool_usage()
    {
        return view('operation.submenu.tool_usage');
    }

    public function room_inventory()
    {
        $assets = Inventory_tools::all();
        $locations = Location::all();
        $rooms = Room::all();
        return view('operation.submenu.room_inventory', ['assets' => $assets, 'locations' => $locations, 'rooms' => $rooms]);
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
                    $imageUrl = $item->filepath ? asset($item->filepath) : 'https://via.placeholder.com/50x50/5fa9f8/ffffff';
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
        return view('operation.submenu.preview-utility', ['data' => $utility]);
    }

    public function update_utility_usage(Request $request, $id)
    {
        // Retrieve the penlat_usage record by ID
        $penlatUsage = Penlat_utility_usage::findOrFail($id);

        // Validate and update the amount
        $request->validate([
            'amount' => 'required|numeric|min:0'
        ]);


        if ($penlatUsage) {
            $penlatUsage->amount = $request->input('amount');
            $penlatUsage->save();

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
            return response()->json(['error' => 'Record not found.'], 404);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to delete Penlat: ' . $e->getMessage());
            return redirect()->back()->with('success', 'Failed to delete record due to an unexpected error.');
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
}
