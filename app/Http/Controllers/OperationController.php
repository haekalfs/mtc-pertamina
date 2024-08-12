<?php

namespace App\Http\Controllers;

use App\Models\Asset_condition;
use App\Models\Infografis_peserta;
use App\Models\Inventory_tools;
use App\Models\Location;
use App\Models\Penlat;
use App\Models\Penlat_requirement;
use App\Models\Penlat_utility;
use App\Models\Penlat_utility_usage;
use App\Models\Tool_img;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OperationController extends Controller
{
    public function index()
    {
        return view('operation.index');
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

        $query = Infografis_peserta::query();

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
        // Execute the query
        $data = $query->get();

        $filter = Infografis_peserta::all();
        //filtering list
        $listPenlat = $filter->unique('nama_program');
        $listStcw = $filter->unique('kategori_program');
        $listJenisPenlat = $filter->unique('jenis_pelatihan');
        $listTw = $filter->unique('realisasi');

        return view('operation.submenu.participant-infographics', [
            'data' => $data,
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
        ]);

        // Handle the image upload
        $imagePath = null;
        if ($request->hasFile('image')) {

            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/penlat_utility'), $filename);
            $imagePath = 'uploads/penlat_utility/' . $filename;
        }

        // Create a new PenlatUtility entry
        $penlatUtility = Penlat_utility::create([
            'penlat_id' => $request->penlat,
            'batch' => $request->batch,
            'date' => $request->date,
            'filepath' => $imagePath,
        ]);

        // Loop through the tools and save the PenlatUtilityUsage entries
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'qty_') === 0) {
                $id = substr($key, 4); // Extract the tool id from the key
                $quantity = $value;
                $unit = $request->input('unit_' . $id);

                Penlat_utility_usage::create([
                    'penlat_utility_id' => $penlatUtility->id,
                    'utility_id' => $id, // Assuming you want to store the tool's id as utility_id
                    'amount' => $quantity,
                ]);
            }
        }

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Penlat utility data saved successfully!');
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
        $locations = Location::all();
        return view('operation.submenu.room_inventory', ['locations' => $locations]);
    }

    public function utility()
    {
        $penlatList = Penlat::all();
        $data = Penlat_utility::all();
        $utility = Utility::all();
        return view('operation.submenu.utility', ['data' => $data, 'utilities' => $utility, 'penlatList' => $penlatList]);
    }

    public function preview_utility($id)
    {
        $utility = Penlat_utility::find($id);
        return view('operation.submenu.preview-utility', ['data' => $utility]);
    }
}
