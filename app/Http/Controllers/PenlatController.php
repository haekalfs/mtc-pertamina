<?php

namespace App\Http\Controllers;

use App\Jobs\RefreshBatchData;
use App\Jobs\RefreshExistingBatch;
use App\Models\Inventory_tools;
use App\Models\Location;
use App\Models\Penlat;
use App\Models\Penlat_alias;
use App\Models\Penlat_batch;
use App\Models\Penlat_certificate;
use App\Models\Penlat_requirement;
use App\Models\Penlat_utility_usage;
use App\Models\Profit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;

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
            ->addColumn('alias', function($item) {
                // Join aliases into a comma-delimited string
                return $item->aliases->pluck('alias')->implode(', ');
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

    public function preview_penlat(Request $request, $penlatId)
    {
        $data = Penlat::find($penlatId);

        if ($request->ajax()) {
            $query = Penlat_batch::with('penlat');

            $query->whereHas('penlat', function($q) use ($penlatId) {
                $q->where('id', $penlatId);
            });

            $dataBatch = $query->get();

            return DataTables::of($dataBatch)
                ->addColumn('display', function($item) {
                    $imageUrl = $item->filepath ? asset($item->filepath) : asset('img/default-img.png');
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
                ->rawColumns(['display'])  // Enable raw HTML for the 'display' and 'action' columns
                ->make(true);
        }

        $penlatList = Penlat::all();
        return view('penlat.preview-penlat', ['data' => $data, 'penlatList' => $penlatList]);
    }

    public function tool_requirement_penlat()
    {
        $penlatList = Penlat::all();
        $getData = Penlat_requirement::groupBy('penlat_id')->pluck('penlat_id')->toArray();
        $penlat = Penlat::whereIn('id', $getData)->get();
        $assets = Inventory_tools::all();

        return view('operation.submenu.requirement_penlat', ['assets' => $assets, 'penlat' => $penlat, 'penlatList' => $penlatList]);
    }

    public function preview_requirement($penlatId)
    {
        $data = Penlat::find($penlatId);
        $assets = Inventory_tools::all();
        return view('operation.submenu.preview-requirement', ['assets' => $assets, 'data' => $data]);
    }

    public function requirement_insert_item(Request $request, $roomId)
    {
        // Validate the request
        $validated = $request->validate([
            'tool.*' => 'required',
            'amount.*' => 'required',
        ]);

         // Save the tools associated with this room
        foreach ($validated['tool'] as $key => $toolId) {
            Penlat_requirement::create([
                'penlat_id' => $roomId,
                'inventory_tool_id' => $toolId,
                'amount' => $validated['amount'][$key],
            ]);
        }

        return redirect()->route('preview-requirement', $roomId)->with('success', 'Requirement saved successfully!');
    }

    public function store(Request $request)
    {
        // Validate the form inputs
        $validatedData = $request->validate([
            'nama_program' => 'required|string|max:255',
            'alias' => 'nullable', // Comma-separated tags
            'jenis_pelatihan' => 'nullable|string|max:255',
            'kategori_program' => 'nullable|string|max:255',
            'display' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Start database transaction
        DB::beginTransaction();

        try {
            // Check if any of the aliases already exist (ignoring case sensitivity)
            $aliasArray = explode(',', $request->alias);
            foreach ($aliasArray as $alias) {
                if (Penlat_alias::where('alias', trim($alias))->exists()) {
                    return redirect()->back()->with('failed', 'One of the aliases already exists. Please choose different aliases.');
                }
            }

            // Create a new Penlat record
            $penlat = new Penlat();
            $penlat->description = $request->input('nama_program');
            $penlat->jenis_pelatihan = $request->input('jenis_pelatihan');
            $penlat->kategori_pelatihan = $request->input('kategori_program');

            // Handle file upload if an image was uploaded
            if ($request->hasFile('display')) {
                $image = $request->file('display');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/penlat'), $filename);
                $penlat->filepath = 'uploads/penlat/' . $filename;
            }

            // Save the Penlat data
            $penlat->save();

            // Save aliases in Penlat_alias model
            foreach ($aliasArray as $alias) {
                $penlatAlias = new Penlat_alias();
                $penlatAlias->penlat_id = $penlat->id;  // Save the newly created Penlat's ID
                $penlatAlias->alias = trim($alias);  // Trim to remove any extra spaces
                $penlatAlias->save();
            }

            // Commit the transaction if everything went well
            DB::commit();

            return redirect()->back()->with('success', 'Program data and image uploaded successfully.');

        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();

            // Return with an error message
            return redirect()->back()->with('error', 'Failed to save program data. Please try again.')->withInput();
        }
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

    public function update_requirement(Request $request, $toolId)
    {
        // Retrieve the penlat_usage record by ID
        $inventoryItem = Penlat_requirement::findOrFail($toolId);

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
            return response()->json(['error' => 'Error 500.'], 404);
        }
    }

    public function delete_item_requirement($id)
    {
        try {
            // Check if the Penlat is used in the Penlat_batch table
            $isItemExist = Penlat_requirement::where('id', $id)->exists();

            if (!$isItemExist) {
                return redirect()->back()->with('success', 'Failed to delete record due to unexist item.');
            }

            $item = Penlat_requirement::where('id', $id);
            $item->delete();

            return redirect()->back()->with('success', 'Deleted Successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found.'], 404);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to delete Penlat: ' . $e->getMessage());
            return response()->json(['error' => 'Record not found.'], 404);
        }
    }

    public function edit($id)
    {
        $penlat = Penlat::findOrFail($id);

        // Get aliases as a comma-separated string
        $aliases = $penlat->aliases->pluck('alias')->implode(',');

        $penlatData = [
            'id' => $penlat->id,
            'description' => $penlat->description,
            'alias' => $aliases, // Add comma-delimited aliases
            'jenis_pelatihan' => $penlat->jenis_pelatihan,
            'kategori_pelatihan' => $penlat->kategori_pelatihan,
            'image' => $penlat->filepath ? asset($penlat->filepath) : null,
        ];

        return response()->json($penlatData);
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'nama_program' => 'required|string|max:255',
            'alias_hidden' => 'sometimes',  // Alias can be a comma-separated string
            'jenis_pelatihan' => 'nullable|string|max:255',
            'kategori_program' => 'nullable|string|max:255',
            'display' => 'sometimes',
        ]);

        // Find the Penlat record
        $penlat = Penlat::findOrFail($id);
        $penlat->description = $request->input('nama_program');
        $penlat->jenis_pelatihan = $request->input('jenis_pelatihan');
        $penlat->kategori_pelatihan = $request->input('kategori_pelatihan');

        // Handle file upload (display image)
        if ($request->hasFile('display')) {
            // Delete the old image if it exists
            if ($penlat->filepath && file_exists(public_path($penlat->filepath))) {
                unlink(public_path($penlat->filepath));
            }

            // Upload the new image
            $image = $request->file('display');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/penlat'), $filename);
            $penlat->filepath = 'uploads/penlat/' . $filename;
        }

        // Update the Penlat entry in the database
        $penlat->save();

        // Handling alias updates
        if ($request->input('alias_hidden')) {
            // Split the alias string into an array, trim whitespace, and remove duplicates
            $aliasArray = array_unique(array_map('trim', explode(',', $request->alias_hidden)));

            // Fetch existing Penlat_alias entries for this Penlat
            $existingAliases = Penlat_alias::where('penlat_id', $penlat->id)->pluck('alias')->toArray();

            // Fetch existing batch aliases (first word of 'batch' column in Penlat_batch) for this Penlat
            $batchAliases = Penlat_batch::where('penlat_id', $penlat->id)->get()->map(function ($row) {
                $parts = explode('/', $row->batch);
                return $parts[0];  // Get the first word of the batch
            })->toArray();

            // Filter out aliases that are in the batch (don't delete them)
            $aliasesToDelete = array_diff($existingAliases, $batchAliases, $aliasArray);

            // Delete aliases that are no longer needed and not in batches
            Penlat_alias::where('penlat_id', $penlat->id)->whereIn('alias', $aliasesToDelete)->delete();

            // Save the new aliases in the Penlat_alias model
            foreach ($aliasArray as $alias) {
                // Check if alias already exists for this penlat_id, if not, create a new entry
                if (!in_array($alias, $existingAliases)) {
                    $penlatAlias = new Penlat_alias();
                    $penlatAlias->penlat_id = $penlat->id;
                    $penlatAlias->alias = $alias;
                    $penlatAlias->save();
                }
            }
        }

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Program data updated successfully.');
    }

    public function delete($id)
    {
        try {
            // Find the Penlat record
            $penlat = Penlat::findOrFail($id);

            // Check if the Penlat is associated with Penlat_batch, Penlat_requirement, Training_reference, or Certificates_to_penlat
            $isPenlatAssignedToBatch = $penlat->batch()->exists();
            $hasRequirements = $penlat->requirement()->exists();
            $hasReferences = $penlat->references()->exists();

            // If any of the relationships have associated records, prevent deletion
            if ($isPenlatAssignedToBatch || $hasRequirements || $hasReferences) {
                return response()->json(['error' => 'Penlat cannot be deleted because it has related records in other tables!'], 400);
            }

            // If no related records exist, proceed with deletion
            $penlat->penlat_to_certificate()->delete();
            $penlat->aliases()->delete();
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
        $validated = $request->validate([
            'penlat' => 'required',
            'tool.*' => 'required',
            'amount.*' => 'required',
        ]);

        // Save the tools associated with this room
        foreach ($validated['tool'] as $key => $toolId) {
            Penlat_requirement::create([
                'penlat_id' => $request->penlat,
                'inventory_tool_id' => $toolId,
                'amount' => $validated['amount'][$key],
            ]);
        }

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Requirements submitted successfully!');
    }

    public function batch(Request $request)
    {
        if ($request->ajax()) {
            $query = Penlat_batch::with(['penlat', 'infografis_peserta']);

            // Apply filter based on selected values from dropdowns
            if ($request->namaPenlat && $request->namaPenlat != '-1') {
                $query->whereHas('penlat', function($q) use ($request) {
                    $q->where('id', $request->namaPenlat);
                });
            }

            if ($request->kategoriPenlat && $request->kategoriPenlat != '-1') {
                $query->whereHas('penlat', function($q) use ($request) {
                    $q->where('kategori_pelatihan', $request->kategoriPenlat);
                });
            }

            if ($request->jenisPenlat && $request->jenisPenlat != '-1') {
                $query->whereHas('penlat', function($q) use ($request) {
                    $q->where('jenis_pelatihan', $request->jenisPenlat);
                });
            }

            if ($request->periode && $request->periode != '-1') {
                $query->whereYear('date', $request->periode);
            }

            $data = $query->orderBy('date', 'desc')->get();

            return DataTables::of($data)
                ->addColumn('display', function($item) {
                    $imageUrl = $item->filepath ? asset($item->filepath) : asset('img/default-img.png');
                    return '<a href="' . route('preview-batch', $item->id) . '"><img src="' . $imageUrl . '" style="height: 100px; width: 100px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow animateBox"></a>';
                })
                ->addColumn('nama_pelatihan', function($item) {
                    return $item->penlat->description;
                })
                ->addColumn('batch', function($item) {
                    return $item->batch;
                })
                ->addColumn('harga_pelatihan', function ($item) {
                    $totalHarga = $item->infografis_peserta->sum('harga_pelatihan');
                    return 'Rp ' . number_format($totalHarga, 0, ',', '.');
                })
                ->addColumn('jenis_pelatihan', function($item) {
                    return $item->penlat->jenis_pelatihan;
                })
                ->addColumn('tgl_pelaksanaan', function($item) {
                    return Carbon::parse($item->date)->format('d-M-Y');
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
            'image' => 'sometimes',
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
                    'batch' => trim($request->batch),
                ],
                [
                    'penlat_id' => $request->penlat,
                    'nama_program' => $request->program,
                    'date' => $request->date,
                    'filepath' => $imagePath,
                ]
            );

            // Update related profits
            $profits = $penlatUtility->profits;
            foreach ($profits as $profit) {
                $profit->tgl_pelaksanaan = $request->date;
                $profit->save();
            }

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
            if ($penlat->filepath && file_exists(public_path($penlat->filepath))) {
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
            // Find the Penlat_batch by ID
            $batch = Penlat_batch::findOrFail($id);

            // Check if there are related records in the hasMany or hasOne relationships
            $isPenlatAssigned = $batch->penlat_usage()->exists();
            $isCertificatesExist = $batch->certificate()->exists();
            $isBatchHasParticipants = $batch->infografis_peserta()->exists();
            $isProfitsExist = $batch->profits()->exists();

            // If any related records exist, prevent deletion
            if ($isPenlatAssigned || $isCertificatesExist || $isBatchHasParticipants || $isProfitsExist) {
                return response()->json(['error' => 'Batch cannot be deleted because it has related records in other tables.'], 400);
            }

            // If no related records exist, proceed with deletion
            $batch->delete();

            return response()->json(['success' => 'Record deleted successfully.'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found.'], 404);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to delete Penlat batch: ' . $e->getMessage());
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

    public function fetchBatches(Request $request)
    {
        // Get the search query (if any) and the pagination page
        $search = $request->input('q');
        $penlat_id = $request->input('penlat_id'); // Get the selected penlat_id
        $page = $request->input('page', 1);
        $perPage = 10;

        // Query to get the batches, filtered by the search term and penlat_id if provided
        $query = Penlat_batch::query();

        if ($penlat_id) {
            // Filter by penlat_id if selected
            $query->where('penlat_id', $penlat_id);
        }

        if ($search) {
            // Search by batch name
            $query->where('batch', 'like', '%' . $search . '%');
        }

        // Paginate the results
        $batches = $query->paginate($perPage, ['*'], 'page', $page);

        // Return the results in the format expected by Select2
        return response()->json([
            'items' => $batches->items(),
            'total_count' => $batches->total()
        ]);
    }

    public function fetchBatchesCertificates(Request $request)
    {
        $search = $request->input('q');
        $penlat_id = $request->input('penlat_id');
        $page = $request->input('page', 1);
        $perPage = 10;

        $query = Penlat_batch::query();

        if ($penlat_id) {
            $query->where('penlat_id', $penlat_id);
        }

        if ($search) {
            $query->where('batch', 'like', '%' . $search . '%');
        }

        $batches = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'items' => $batches->map(function ($batch) {
                return [
                    'id' => $batch->batch,
                    'text' => $batch->batch,
                    'date' => $batch->date, // Include the 'date' column
                ];
            }),
            'total_count' => $batches->total()
        ]);
    }

    public function refresh_batch_data(Request $request)
    {
        try {
            // Validate the year input to ensure it's a 4-digit number
            $request->validate([
                'year' => ['required', 'digits:4', 'integer', 'min:1900', 'max:2500'],
            ]);

            $year = $request->input('year'); // Get the sanitized year input

            // Dispatch the job to refresh the batch data with the given year
            RefreshBatchData::dispatch($year); // Pass the year to the job
            RefreshExistingBatch::dispatch(); // Pass the year to the job

            // Cache to indicate job is processing
            if (!Cache::has('jobs_processing')) {
                Cache::put('jobs_processing', true, now()->addMinutes(3));
            }

            return response()->json([
                'success' => true,
                'message' => 'Batch data refresh initiated successfully for year ' . $year,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate batch data refresh.',
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $templatePath = public_path('uploads/template/template_batch_export.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $headerRow = 5; // Header row position
        $startRow = $headerRow + 1; // Start writing data below the header

        // Define headers explicitly
        $headers = [
            'A' => 'Date',
            'B' => 'Pelatihan',
            'C' => 'Batch',
            'D' => 'Jenis Pelatihan',
            'E' => 'Kategori Pelatihan',
            'F' => 'Jumlah Peserta',
            'G' => 'Total Harga Pelatihan',
            'H' => 'Created At'
        ];

        foreach ($headers as $col => $text) {
            $sheet->setCellValue("{$col}{$headerRow}", $text);
        }

        // **Apply AutoFilter to Header Row**
        $sheet->setAutoFilter("A{$headerRow}:H{$headerRow}");

        // Apply filters
        $query = Penlat_batch::with(['penlat', 'infografis_peserta']);

        if ($request->penlat && $request->penlat != -1) {
            $query->whereHas('penlat', function ($q) use ($request) {
                $q->where('id', $request->penlat);
            });
        }

        if ($request->jenis_pelatihan && $request->jenis_pelatihan != -1) {
            $query->whereHas('penlat', function ($q) use ($request) {
                $q->where('jenis_pelatihan', $request->jenis_pelatihan);
            });
        }

        if ($request->kategori_pelatihan && $request->kategori_pelatihan != -1) {
            $query->whereHas('penlat', function ($q) use ($request) {
                $q->where('kategori_pelatihan', $request->kategori_pelatihan);
            });
        }

        if ($request->periode && $request->periode != -1) {
            $query->whereYear('date', $request->periode);
        }

        $getForm = $query->orderBy('date', 'asc')->orderBy('batch', 'asc')->get();

        foreach ($getForm as $row) {
            $sheet->setCellValue("A{$startRow}", Carbon::parse($row->date)->format('d-M-Y'));
            $sheet->setCellValue("B{$startRow}", $row->penlat->description ?? 'N/A');
            $sheet->setCellValue("C{$startRow}", $row->batch ?? 'N/A');
            $sheet->setCellValue("D{$startRow}", $row->penlat->jenis_pelatihan ?? 'N/A');
            $sheet->setCellValue("E{$startRow}", $row->penlat->kategori_pelatihan ?? 'N/A');

            // Column F: Count of participants
            $totalParticipants = $row->infografis_peserta->count();
            $sheet->setCellValue("F{$startRow}", $totalParticipants);

            // Column G: Sum of harga_pelatihan
            $totalHargaPelatihan = $row->infografis_peserta->sum('harga_pelatihan');
            $sheet->setCellValue("G{$startRow}", $totalHargaPelatihan);

            $sheet->setCellValue("H{$startRow}", Carbon::parse($row->created_at)->format('d-M-Y'));

            $startRow++;
        }

        $lastRow = $startRow - 1; // Last row of data

        // **Apply Styling to Header Row**
        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'], // White text
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'cf3e3e'], // Yellow header
            ],
        ];
        $sheet->getStyle("A{$headerRow}:H{$headerRow}")->applyFromArray($headerStyle);

        // **Apply Borders to Data Rows**
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle("A{$headerRow}:H{$lastRow}")->applyFromArray($borderStyle);

        // **Apply Zebra Striping for Readability**
        for ($row = $headerRow + 1; $row <= $lastRow; $row++) {
            if ($row % 2 == 0) { // Alternate row color (light gray)
                $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F2F2F2'],
                    ],
                ]);
            }
        }

        // **Auto-size Columns**
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // **Set Document Properties**
        $spreadsheet->getProperties()
            ->setCreator("Your App Name")
            ->setLastModifiedBy("Your App Name")
            ->setTitle("Batches Master Data")
            ->setSubject("Batch Data Export")
            ->setDescription("Generated batch data report");

        // **Save the File**
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->setPreCalculateFormulas(false); // Prevent recalculation issues
        $filePath = storage_path('app/public/output.xlsx');
        $writer->save($filePath);

        return response()->download($filePath, "Batches_Master_Data.xlsx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function getAmendments(Request $request)
    {
        $penlat = Penlat::with('amendments.regulators')->find($request->penlat_id);

        if (!$penlat || $penlat->amendments->isEmpty()) {
            return response()->json([
                'amendment' => null // Return null instead of an error
            ]);
        }

        // Get the first amendment (or modify this logic if multiple amendments are needed)
        $amendment = $penlat->amendments->first();

        return response()->json([
            'amendment' => [
                'id' => $amendment->id,
                'description' => $amendment->description,
                'regulator' => $amendment->regulators ? [
                    'id' => $amendment->regulators->id,
                    'description' => $amendment->regulators->description
                ] : null
            ]
        ]);
    }

}
