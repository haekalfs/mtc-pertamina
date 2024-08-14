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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class OperationController extends Controller
{
    public function index()
    {
        $getPesertaCount = Infografis_peserta::count();
        $getKebutuhanCount = Penlat_requirement::count();
        $getAssetCount = Inventory_tools::count();
        $getAssetStock = Inventory_tools::sum('initial_stock');

        return view('operation.index', compact('getPesertaCount', 'getKebutuhanCount', 'getAssetCount', 'getAssetStock'));
    }

    public function getChartData()
    {
        $data = Infografis_peserta::select('nama_program', DB::raw('count(*) as total'))
                                ->groupBy('batch', 'nama_program')
                                ->get();

        $dataByDate = Infografis_peserta::select('tgl_pelaksanaan', DB::raw('count(*) as total'))
                                ->groupBy('tgl_pelaksanaan')
                                ->get();

        $dataPointsPie = [];
        foreach ($data as $row) {
            $dataPointsPie[] = [
                "label" => $row->nama_program,
                "symbol" => substr($row->nama_program, 0, 2),
                "y" => $row->total
            ];
        }

        $dataPointsSpline = [];
        foreach ($dataByDate as $row) {
            $dataPointsSpline[] = [
                "x" => Carbon::parse($row->tgl_pelaksanaan)->timestamp * 1000,
                "y" => $row->total
            ];
        }

        return response()->json([
            'splineDataPoints' => $dataPointsSpline,
            'pieDataPoints' => $dataPointsPie
        ]);
    }

    public function participant_infographics(Request $request)
    {
        // Determine the current year and generate the range of years
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        // Set the selected year
        $currentYear = $periodeSelected ?? $nowYear;

        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            'namaPenlat' => 'required',
            'stcw' => 'required',
            'jenisPenlat' => 'required',
            'tw' => 'required',
            'periode' => 'required|digits:4' // Assuming 'periode' is a year
        ]);

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
                    return '<a data-item-id="' . $row->id . '" class="btn btn-outline-secondary btn-sm mr-2 edit-btn"  href="#" data-toggle="modal" data-target="#editModal"><i class="ti-eye"></i> Edit</a>';
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

    public function utility_store(Request $request)
    {
        // Validate the request
        $request->validate([
            'penlat' => 'required',
            'batch' => 'required',
            'date' => 'required',
            'image' => 'required',
            'program' => 'sometimes'
        ]);

        // Handle the image upload
        $imagePath = null;
        if ($request->hasFile('image')) {

            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/penlat_utility'), $filename);
            $imagePath = 'uploads/penlat_utility/' . $filename;
        }

        // Create a new entry
        $penlatUtility = Penlat_batch::updateOrCreate(
            [
                'penlat_id' => $request->penlat,
                'batch' => $request->batch,
            ],
            [
                'nama_program' => $request->program,
                'date' => $request->date,
                'filepath' => $imagePath,
            ]
        );

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

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Penlat utility data saved successfully!');
    }

    public function room_inventory_store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'nama_ruangan' => 'required',
            'room_image' => 'nullable',
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

    public function utility()
    {
        $penlatList = Penlat::all();

        $validateData = Penlat_utility_usage::groupBy('penlat_batch_id')->pluck('penlat_batch_id')->toArray();
        $data = Penlat_batch::whereIn('id', $validateData)->get();

        $batchList = Penlat_batch::all();

        $utility = Utility::all();
        return view('operation.submenu.utility', ['data' => $data, 'utilities' => $utility, 'penlatList' => $penlatList, 'batchList' => $batchList]);
    }

    public function preview_utility($id)
    {
        $utility = Penlat_batch::find($id);
        return view('operation.submenu.preview-utility', ['data' => $utility]);
    }
}
