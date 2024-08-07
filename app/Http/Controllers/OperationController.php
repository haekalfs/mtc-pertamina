<?php

namespace App\Http\Controllers;

use App\Models\Infografis_peserta;
use App\Models\Inventory_tools;
use App\Models\Location;
use App\Models\Penlat_utility;
use App\Models\Penlat_utility_usage;
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

    public function tool_inventory()
    {
        $assets = Inventory_tools::all();
        $locations = Location::all();

        return view('operation.submenu.tool_inventory', ['assets' => $assets, 'locations' => $locations]);
    }

    public function room_inventory()
    {
        $locations = Location::all();
        return view('operation.submenu.room_inventory', ['locations' => $locations]);
    }

    public function tool_requirement_penlat()
    {
        $locations = Location::all();
        return view('operation.submenu.requirement_penlat', ['locations' => $locations]);
    }

    public function utility()
    {
        $data = Penlat_utility::all();
        $utility = Utility::all();
        return view('operation.submenu.utility', ['data' => $data, 'utilities' => $utility]);
    }

    public function preview_utility($id)
    {
        return view('operation.submenu.preview-utility', []);
    }
}
