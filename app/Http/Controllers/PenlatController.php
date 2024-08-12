<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Penlat;
use App\Models\Penlat_requirement;
use Illuminate\Http\Request;

class PenlatController extends Controller
{
    public function index()
    {
        $data = Penlat::all();
        return view('penlat.index', ['data' => $data]);
    }

    public function tool_requirement_penlat()
    {
        $penlatList = Penlat::all();
        $getData = Penlat_requirement::groupBy('penlat_id')->pluck('penlat_id')->toArray();
        $penlat = Penlat::whereIn('id', $getData)->get();
        $locations = Location::all();
        return view('operation.submenu.requirement_penlat', ['locations' => $locations, 'penlat' => $penlat, 'penlatList' => $penlatList]);
    }

    public function store(Request $request)
    {
        // Validate the form inputs
        $validatedData = $request->validate([
            'nama_program' => 'required|string|max:255',
            'alias' => 'nullable|string|max:100',
            'jenis_pelatihan' => 'nullable|string|max:255',
            'kategori_program' => 'nullable|string|max:255',
            'display' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Store the main data in the Penlat table
        $penlat = new Penlat();
        $penlat->description = $request->input('nama_program');
        $penlat->alias = $request->input('alias');
        $penlat->jenis_pelatihan = $request->input('jenis_pelatihan');
        $penlat->kategori_pelatihan = $request->input('kategori_program');

        // If an image was uploaded, save the file path
        if ($request->hasFile('display')) {
            $image = $request->file('display');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/penlat'), $filename);
            $penlat->filepath = 'uploads/penlat/' . $filename;
        }

        $penlat->save();

        return redirect()->back()->with('success', 'Program data and image uploaded successfully.');
    }

    public function edit($id)
    {
        $penlat = Penlat::findOrFail($id);

        $penlatData = [
            'id' => $penlat->id,
            'description' => $penlat->description,
            'alias' => $penlat->alias,
            'jenis_pelatihan' => $penlat->jenis_pelatihan,
            'kategori_pelatihan' => $penlat->kategori_pelatihan,
            'image' => $penlat->filepath ? asset($penlat->filepath) : null,
        ];

        return response()->json($penlatData);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama_program' => 'required|string|max:255',
            'alias' => 'nullable|string|max:100',
            'jenis_pelatihan' => 'nullable|string|max:255',
            'kategori_program' => 'nullable|string|max:255',
            'display' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $penlat = Penlat::findOrFail($id);
        $penlat->description = $request->input('nama_program');
        $penlat->alias = $request->input('alias');
        $penlat->jenis_pelatihan = $request->input('jenis_pelatihan');
        $penlat->kategori_pelatihan = $request->input('kategori_pelatihan');

        if ($request->hasFile('display')) {
            // Delete the old image if exists
            if ($penlat->filepath) {
                unlink(public_path($penlat->filepath));
            }

            $image = $request->file('display');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/penlat'), $filename);
            $penlat->filepath = 'uploads/penlat/' . $filename;
        }

        $penlat->save();

        return redirect()->back()->with('success', 'Program data updated successfully.');
    }

    public function requirement_store(Request $request)
    {
        // Validate the request
        $request->validate([
            'penlat' => 'required',
            'documents' => 'required',
            'documents.*' => 'required',
        ]);

        $documents = $request->input('documents');

        // Loop through the documents and create a new record for each
        foreach ($documents as $key => $document) {
            Penlat_requirement::create([
                'penlat_id' => $request->penlat,
                'requirement' => $document,
            ]);
        }

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Requirements submitted successfully!');
    }
}
