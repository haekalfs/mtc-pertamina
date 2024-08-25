<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Penlat;
use App\Models\Penlat_batch;
use App\Models\Penlat_certificate;
use App\Models\Penlat_requirement;
use App\Models\Penlat_utility_usage;
use App\Models\Profit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
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
                $filePath = $item->filepath;
                if($filePath){
                    $imageUrl = asset($item->filepath);
                } else {
                    $imageUrl = asset('img/default-img.png');
                }

                return '<a href="'. route('preview-penlat', $item->id) .'"><img src="' . $imageUrl . '" style="height: 100px; width: 100px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow animateBox"></a>';
            })
            ->addColumn('action', function($item) {
                return '<div>
                            <a data-id="' . $item->id . '" href="#" class="btn btn-outline-secondary btn-md mb-2 mr-2 edit-tool" data-toggle="modal" data-target="#editDataModal">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button data-id="' . $item->id . '" class="btn btn-outline-danger btn-md mb-2">
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

    public function preview_penlat($penlatId)
    {
        $data = Penlat::find($penlatId);
        return view('penlat.preview-penlat', ['data' => $data]);
    }

    public function tool_requirement_penlat()
    {
        $penlatList = Penlat::all();
        $getData = Penlat_requirement::groupBy('penlat_id')->pluck('penlat_id')->toArray();
        $penlat = Penlat::whereIn('id', $getData)->get();
        $locations = Location::all();
        return view('operation.submenu.requirement_penlat', ['locations' => $locations, 'penlat' => $penlat, 'penlatList' => $penlatList]);
    }

    public function preview_requirement_penlat($penlatId)
    {
        $data = Penlat::findOrFail($penlatId);
        return view('operation.submenu.requirement_preview', ['data' => $data]);
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

    public function edit_requirement($id)
    {
        // Fetch the penlat with its related documents
        $penlat = Penlat::with('requirement')->find($id);

        // Check if penlat exists
        if (!$penlat) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        // Return the data as JSON
        return response()->json([
            'penlat_id' => $penlat->id,
            'documents' => $penlat->requirement->pluck('requirement')
        ]);
    }

    public function update_requirement(Request $request)
    {
        // Validate the request
        $request->validate([
            'edit_penlat' => 'required',
            'edit_documents' => 'required',
            'edit_documents.*' => 'required',
        ]);
        $penlatId = $request->input('edit_penlat');
        try {
            // Check if the Penlat is used in the Penlat_batch table
            $isRequirementExist = Penlat_requirement::where('penlat_id', $penlatId)->exists();

            if (!$isRequirementExist) {
                return response()->json(['error' => 'Requirement cannot be deleted because it is not found!'], 400);
            }

            $item = Penlat_requirement::where('penlat_id', $penlatId);
            $item->delete();

            $documents = $request->input('edit_documents');

            // Loop through the documents and create a new record for each
            foreach ($documents as $key => $document) {
                Penlat_requirement::create([
                    'penlat_id' => $request->edit_penlat,
                    'requirement' => $document,
                ]);
            }

            // Redirect back with a success message
            return redirect()->back()->with('success', 'Requirements submitted successfully!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found.'], 404);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to delete Penlat: ' . $e->getMessage());
        }
    }

    public function delete_requirement($id)
    {
        try {
            // Check if the Penlat is used in the Penlat_batch table
            $isRequirementExist = Penlat_requirement::where('penlat_id', $id)->exists();

            if (!$isRequirementExist) {
                return response()->json(['error' => 'Requirement cannot be deleted because it is not found!'], 400);
            }

            $item = Penlat_requirement::where('penlat_id', $id);
            $item->delete();

            return response()->json(['success' => 'Record deleted successfully.'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found.'], 404);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to delete Penlat: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete record due to an unexpected error.'], 500);
        }
    }

    public function delete_item_requirement($id)
    {
        try {
            // Check if the Penlat is used in the Penlat_batch table
            $isRequirementExist = Penlat_requirement::where('id', $id)->exists();

            if (!$isRequirementExist) {
                return response()->json(['error' => 'Requirement cannot be deleted because it is not found!'], 400);
            }

            $item = Penlat_requirement::where('id', $id);
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
            'display' => 'sometimes',
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

    public function delete($id)
    {
        try {
            // Check if the Penlat is used in the Penlat_batch table
            $isPenlatAssigned = Penlat_batch::where('penlat_id', $id)->exists();

            if ($isPenlatAssigned) {
                return response()->json(['error' => 'Penlat cannot be deleted because it is assigned to a batch!'], 400);
            }

            $penlat = Penlat::findOrFail($id);
            $penlat->delete();

            return response()->json(['success' => 'Record deleted successfully.'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found.'], 404);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to delete Penlat: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete record due to an unexpected error.'], 500);
        }
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
                    $imageUrl = $item->filepath ? asset($item->filepath) : 'https://via.placeholder.com/50x50/5fa9f8/ffffff';
                    return '<a href="' . route('preview-batch', $item->id) . '"><img src="' . $imageUrl . '" style="height: 100px; width: 100px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow animateBox"></a>';
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
                        <button data-id="' . $item->id . '" class="btn btn-outline-danger btn-md mb-2">
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

        $checkData = Penlat_batch::where('batch', $request->batch)->exists();

        if($checkData) {
            return redirect()->back()->with('failed', 'Batch is Already Exist!');
        }

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
            return redirect()->back()->with('failed', 'An unexpected error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function fetch_batch($id)
    {
        $penlat = Penlat_batch::findOrFail($id);

        $penlatData = [
            'id' => $penlat->id,
            'penlat_id' => $penlat->penlat_id,
            'nama_program' => $penlat->nama_program,
            'batch' => $penlat->batch,
            'date' => $penlat->date,
            'image' => $penlat->filepath ? asset($penlat->filepath) : null,
        ];

        return response()->json($penlatData);
    }

    public function update_batch(Request $request, $id)
    {
        $validatedData = $request->validate([
            'edit_penlat_id' => 'required',
            'edit_nama_program' => 'required|string|max:255',
            'edit_batch' => 'required',
            'edit_tgl_pelaksanaan' => 'required',
            'edit_display' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $penlat = Penlat_batch::findOrFail($id);
        $penlat->penlat_id = $request->input('edit_penlat_id');
        $penlat->nama_program = $request->input('edit_nama_program');
        $penlat->batch = $request->input('edit_batch');
        $penlat->date = $request->input('edit_tgl_pelaksanaan');

        if ($request->hasFile('edit_display')) {
            // Delete the old image if exists
            if ($penlat->filepath) {
                unlink(public_path($penlat->filepath));
            }

            $image = $request->file('edit_display');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/penlat_utility'), $filename);
            $penlat->filepath = 'uploads/penlat_utility/' . $filename;
        }

        $penlat->save();

        return redirect()->back()->with('success', 'Program data updated successfully.');
    }

    public function delete_batch($id)
    {
        try {
            // Check if the Penlat is used in the Penlat_batch table
            $isPenlatAssigned = Penlat_utility_usage::where('penlat_batch_id', $id)->exists();
            $isCertificatesExist = Penlat_certificate::where('penlat_batch_id', $id)->exists();

            if ($isPenlatAssigned || $isCertificatesExist) {
                return response()->json(['error' => 'Batch cannot be deleted because it is assigned to a functions!'], 400);
            }

            $penlat = Penlat_batch::findOrFail($id);
            $penlat->delete();

            return response()->json(['success' => 'Record deleted successfully.'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found.'], 404);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to delete Penlat: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete record due to an unexpected error.'], 500);
        }
    }

    public function preview_batch($id)
    {
        $data = Penlat_batch::find($id);

        $profits = Profit::where('pelaksanaan', $data->batch)->first();

        return view('penlat.preview-batch', ['data' => $data, 'profits' => $profits]);
    }

    public function penlat_import()
    {
        return view('penlat.import-page');
    }
}
