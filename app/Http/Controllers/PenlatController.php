<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Penlat;
use App\Models\Penlat_batch;
use App\Models\Penlat_requirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PenlatController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Penlat::query();

            // Apply filters based on the selected values from the dropdowns
            if ($request->namaPenlat && $request->namaPenlat != '-1') {
                $query->where('id', $request->namaPenlat);
            }

            if ($request->jenisPenlat && $request->jenisPenlat != '-1') {
                $query->where('jenis_pelatihan', $request->jenisPenlat);
            }

            if ($request->stcw && $request->stcw != '-1') {
                $query->where('kategori_pelatihan', $request->stcw);
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addColumn('display', function($item) {
                    return '<a href="#"><img src="' . asset($item->filepath) . '" style="height: 100px; width: 100px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow"></a>';
                })
                ->addColumn('action', function($item) {
                    return '<div>
                                <a data-id="' . $item->id . '" href="#" class="btn btn-outline-secondary btn-md mb-2 mr-2 edit-tool" data-toggle="modal" data-target="#editDataModal">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button class="btn btn-outline-danger btn-md mb-2">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </div>';
                })
                ->rawColumns(['display', 'action'])
                ->make(true);
        }

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

    public function batch(Request $request)
    {
        if ($request->ajax()) {
            $query = Penlat_batch::with('penlat');

            // Apply filter based on selected values from dropdowns
            if ($request->namaPenlat && $request->namaPenlat != '-1') {
                $query->whereHas('penlat', function($q) use ($request) {
                    $q->where('id', $request->namaPenlat);
                });
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addColumn('display', function($item) {
                    return '<a href="' . route('preview-batch', $item->id) . '"><img src="' . asset($item->filepath) . '" style="height: 100px; width: 100px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow "></a>';
                })
                ->addColumn('nama_pelatihan', function($item) {
                    return $item->penlat->description;
                })
                ->addColumn('batch', function($item) {
                    return $item->batch;
                })
                ->addColumn('jenis_pelatihan', function($item) {
                    return $item->penlat->jenis_pelatihan;
                })
                ->addColumn('tgl_pelaksanaan', function($item) {
                    return $item->date;
                })
                ->addColumn('action', function($item) {
                    return '
                        <a data-id="' . $item->id . '" href="#" class="btn btn-outline-secondary btn-md mb-2 mr-2 edit-tool" data-toggle="modal" data-target="#editDataModal">
                            <i class="fa fa-edit"></i>
                        </a>
                        <button class="btn btn-outline-danger btn-md mb-2">
                            <i class="fa fa-trash-o"></i>
                        </button>
                    ';
                })
                ->rawColumns(['display', 'action'])  // Enable raw HTML for the 'display' and 'action' columns
                ->make(true);
        }

        $penlatList = Penlat::all();
        return view('penlat.batch', ['penlatList' => $penlatList]);
    }

    public function batch_store(Request $request)
    {
        // Validate the request
        $request->validate([
            'penlat' => 'required',
            'batch' => 'required',
            'date' => 'required',
            'image' => 'required',
            'program' => 'sometimes',
        ]);

        DB::beginTransaction(); // Start the transaction

        try {
            // Handle the image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/penlat_utility'), $filename);
                $imagePath = 'uploads/penlat_utility/' . $filename;
            }

            // Create or update the entry
            $penlatUtility = Penlat_batch::updateOrCreate(
                [
                    'batch' => $request->batch,
                ],
                [
                    'penlat_id' => $request->penlat,
                    'nama_program' => $request->program,
                    'date' => $request->date,
                    'filepath' => $imagePath,
                ]
            );

            DB::commit(); // Commit the transaction

            // Redirect back with a success message
            return redirect()->back()->with('success', 'Penlat Batch data saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction for any other exceptions
            return redirect()->back()->with('error', 'An unexpected error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function preview_batch($id)
    {
        $data = Penlat_batch::find($id);
        return view('penlat.preview-batch', ['data' => $data]);
    }
}
