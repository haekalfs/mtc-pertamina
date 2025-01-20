<?php

namespace App\Http\Controllers;

use App\Models\Asset_condition;
use App\Models\Error_log_import;
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

        // Set the selected year, default to the current year if null
        $yearSelected = $yearSelected ?? $nowYear;

        // Get counts for various items, handle null with default values
        $getPesertaCount = Infografis_peserta::whereYear('tgl_pelaksanaan', $yearSelected)->count() ?? 0;
        $getKebutuhanCount = Penlat_requirement::count() ?? 0;
        $getAssetCount = Inventory_tools::count() ?? 0;

        // Get count of assets that are out of stock
        $OutOfStockCount = Inventory_tools::where('asset_stock', '=', 0)->count() ?? 0;

        // Get count of assets requiring maintenance
        $requiredMaintenanceCount = Inventory_tools::where('next_maintenance', '<=', now())->count() ?? 0;

        $totalAttention = $OutOfStockCount + $requiredMaintenanceCount;

        // Initialize the year and months array
        $year = $yearSelected ?? Carbon::now()->year;

        $months = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4, 'Mei' => 5, 'Juni' => 6,
            'Juli' => 7, 'Agustus' => 8, 'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
        ];

        $data = [];

        // Loop through each month to get counts, handle null counts
        foreach ($months as $monthName => $monthNumber) {
            $externalCount = Infografis_peserta::whereYear('tgl_pelaksanaan', $year)
                ->whereMonth('tgl_pelaksanaan', $monthNumber)
                ->where('subholding', 'Eksternal')
                ->count() ?? 0;

            $internalCount = Infografis_peserta::whereYear('tgl_pelaksanaan', $year)
                ->whereMonth('tgl_pelaksanaan', $monthNumber)
                ->whereNotIn('subholding', ['Eksternal'])
                ->count() ?? 0;

            $data[] = [
                'month' => $monthName,
                'external_count' => $externalCount,
                'internal_count' => $internalCount,
            ];
        }

        // Gauge
        $latestMonthRecord = Infografis_peserta::whereYear('tgl_pelaksanaan', $year)
            ->orderBy('tgl_pelaksanaan', 'desc')
            ->first();

        // Safely parse the date and handle null
        $getDate = optional($latestMonthRecord)->tgl_pelaksanaan ? Carbon::parse($latestMonthRecord->tgl_pelaksanaan) : null;
        $monthName = optional($getDate)->format('F') ?? 'No data';

        $countSTCWGauge = 0;
        $countNonSTCWGauge = 0;

        if ($latestMonthRecord) {
            $latestMonth = Carbon::parse($latestMonthRecord->tgl_pelaksanaan)->month ?? null;

            // Get the count for STCW and NON STCW for the latest month
            $countSTCWGauge = Infografis_peserta::whereYear('tgl_pelaksanaan', $year)
                ->whereMonth('tgl_pelaksanaan', $latestMonth)
                ->where('kategori_program', 'STCW')
                ->count() ?? 0;

            $countNonSTCWGauge = Infografis_peserta::whereYear('tgl_pelaksanaan', $year)
                ->whereMonth('tgl_pelaksanaan', $latestMonth)
                ->where('kategori_program', 'NON STCW')
                ->count() ?? 0;

            // Previous month
            $previousMonth = ($latestMonth == 1) ? 12 : $latestMonth - 1;

            $previousCountSTCW = Infografis_peserta::whereYear('tgl_pelaksanaan', $year)
                ->whereMonth('tgl_pelaksanaan', $previousMonth)
                ->where('kategori_program', 'STCW')
                ->count() ?? 0;

            $previousCountNonSTCW = Infografis_peserta::whereYear('tgl_pelaksanaan', $year)
                ->whereMonth('tgl_pelaksanaan', $previousMonth)
                ->where('kategori_program', 'NON STCW')
                ->count() ?? 0;

            $stcwDelta = ($previousCountSTCW > 0) ? $previousCountSTCW : 0;
            $nonStcwDelta = ($previousCountNonSTCW > 0) ? $previousCountNonSTCW : 0;
        } else {
            // Set default counts and deltas if no data
            $stcwDelta = $nonStcwDelta = 0;
        }

        // Quarterly Data
        $quarters = [
            'TW-1' => [1, 2, 3],
            'TW-2' => [4, 5, 6],
            'TW-3' => [7, 8, 9],
            'TW-4' => [10, 11, 12],
        ];

        $quarterlyData = [];

        foreach ($quarters as $quarterName => $bulan) {
            $dataset1 = Infografis_peserta::whereYear('tgl_pelaksanaan', $year)
                ->whereIn(DB::raw('MONTH(tgl_pelaksanaan)'), $bulan)
                ->where('subholding', 'Eksternal')
                ->count() ?? 0;

            $dataset2 = Infografis_peserta::whereYear('tgl_pelaksanaan', $year)
                ->whereIn(DB::raw('MONTH(tgl_pelaksanaan)'), $bulan)
                ->whereNotIn('subholding', ['Eksternal'])
                ->count() ?? 0;

            $total = $dataset1 + $dataset2;
            $externalPercentage = $total ? ($dataset1 / $total) * 100 : 0;
            $internalPercentage = $total ? ($dataset2 / $total) * 100 : 0;

            $quarterlyData[$quarterName] = [
                'external_count' => $dataset1,
                'internal_count' => $dataset2,
                'external_percentage' => $externalPercentage,
                'internal_percentage' => $internalPercentage,
            ];
        }

        return view('operation.index', compact(
            'getPesertaCount',
            'totalAttention',
            'getKebutuhanCount',
            'getAssetCount',
            'yearsBefore',
            'yearSelected',
            'OutOfStockCount',
            'requiredMaintenanceCount',
            'data', 'year',
            'countSTCWGauge', 'countNonSTCWGauge', 'stcwDelta', 'nonStcwDelta',
            'quarterlyData',
            'monthName'
        ));
    }

    public function getChartData($year)
    {
        // Fetch and group the data by nama_program, counting the number of participants
        $data = Infografis_peserta::select('nama_program', DB::raw('count(*) as total'))
            ->whereYear('tgl_pelaksanaan', $year)
            ->groupBy('nama_program')
            ->orderBy('total', 'asc')
            ->having('total', '>', 450) // Only include categories with more than 500 participants
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
        $dataPointsBar = [];
        foreach ($data as $row) {
            $dataPointsBar[] = [
                "label" => $row->nama_program,
                "symbol" => substr($row->nama_program, 0, 2),
                "y" => $row->total
            ];
        }

        // Use the selected year to calculate the 7 years prior range
        $startYear = $year - 7;

        // Fetch total number of participants for each year, starting 7 years prior to the selected year
        $dataYearly = Infografis_peserta::select(DB::raw('YEAR(tgl_pelaksanaan) as year'), DB::raw('count(*) as total'))
            ->whereYear('tgl_pelaksanaan', '>=', $startYear)
            ->whereYear('tgl_pelaksanaan', '<=', $year) // Make sure to include up to the selected year
            ->groupBy(DB::raw('YEAR(tgl_pelaksanaan)'))
            ->orderBy('year', 'asc')
            ->get();

        // Prepare the data for the column chart
        $dataPointsColumn = [];
        foreach ($dataYearly as $row) {
            $dataPointsColumn[] = [
                "label" => $row->year,
                "y" => $row->total
            ];
        }

         // Retrieve the participants for the selected year
        $yearlyParticipants = Infografis_peserta::whereYear('tgl_pelaksanaan', $year)
            ->pluck('nama_peserta')
            ->toArray();

        // Retrieve all participants before the selected year
        $previousParticipants = Infografis_peserta::whereYear('tgl_pelaksanaan', '<', $year)
            ->pluck('nama_peserta')
            ->toArray();

        // Calculate the returning and new participants
        $returningParticipants = array_intersect($yearlyParticipants, $previousParticipants);
        $newParticipants = array_diff($yearlyParticipants, $previousParticipants);

        // Prepare data for the pie chart
        $chartData = [
            ['label' => 'New Participants', 'y' => count($newParticipants)],
            ['label' => 'Returning Participants', 'y' => count($returningParticipants)],
        ];

        // Return the data points in a JSON response
        return response()->json([
            'dataPointsSpline1' => $dataPointsSpline1,
            'dataPointsSpline2' => $dataPointsSpline2,
            'barDataPoints' => $dataPointsBar,
            'countSTCW' => $countSTCW,
            'countNonSTCW' => $countNonSTCW,
            'columnDataPoints' => $dataPointsColumn,
            'chartData' => $chartData // New column chart data
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

        $batches = Penlat_batch::all();
        $penlatList = Penlat::all();

        $checkIfAnyError = Error_log_import::where('import_id', 1)
            ->where('created_at', '>=', now()->subWeek())
            ->count();

        if ($checkIfAnyError) {
            Session::flash('error_log', "There are " . $checkIfAnyError . " errors since the last import! Click <a href='" . route('infographics.error.log') . "'>here</a> to view details.");
        }

        return view('operation.submenu.participant-infographics', [
            'yearsBefore' => $yearsBefore,
            'listPenlat' => $listPenlat,
            'listStcw' => $listStcw,
            'listJenisPenlat' => $listJenisPenlat,
            'listTw' => $listTw,
            'penlatList' => $penlatList,
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
            'person_id' => 'required|string|max:255',
            'nama_peserta' => 'required|string|max:255',
            'nama_program' => 'required|string|max:255',
            'batch' => 'required|string|max:145',
            'tgl_pelaksanaan' => 'required|date',
            'seafarer_code' => 'required|string|max:255',
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
        $infografisPeserta->participant_id = $request->input('person_id');
        $infografisPeserta->nama_peserta = $request->input('nama_peserta');
        $infografisPeserta->nama_program = $request->input('nama_program');
        $infografisPeserta->batch = $request->input('batch');
        $infografisPeserta->tgl_pelaksanaan = $request->input('tgl_pelaksanaan');
        $infografisPeserta->seafarer_code = $request->input('seafarer_code');
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
                    ],
                    [
                        'penlat_certificate_id' => $penlatCert->id,
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
            'person_id' => 'required|string|max:255',
            'nama_peserta' => 'required|string|max:255',
            'nama_program' => 'required|string|max:255',
            'tgl_pelaksanaan' => 'required|date',
            'tempat_pelaksanaan' => 'required|string|max:255',
            'jenis_pelatihan' => 'required|string|max:255',
            'keterangan' => 'required|string|max:255',
            'seafarer_code' => 'required|string|max:255',
            'subholding' => 'required|string|max:255',
            'perusahaan' => 'required|string|max:255',
            'kategori_program' => 'required|string|max:255',
            'realisasi' => 'required|string|max:255', // Ensure this matches the correct column name
        ]);

        $participant = Infografis_peserta::find($id);
        if ($participant) {
            $participant->participant_id = $request->input('person_id');
            $participant->nama_peserta = $request->input('nama_peserta');
            $participant->nama_program = $request->input('nama_program');
            $participant->tgl_pelaksanaan = $request->input('tgl_pelaksanaan');
            $participant->tempat_pelaksanaan = $request->input('tempat_pelaksanaan');
            $participant->jenis_pelatihan = $request->input('jenis_pelatihan');
            $participant->batch = $request->input('batch');
            $participant->seafarer_code = $request->input('seafarer_code');
            $participant->keterangan = $request->input('keterangan');
            $participant->subholding = $request->input('subholding');
            $participant->perusahaan = $request->input('perusahaan');
            $participant->kategori_program = $request->input('kategori_program');
            $participant->realisasi = $request->input('realisasi'); // Update this field as per your schema
            $participant->save();

            $findBatch = Penlat_batch::where('batch', $request->input('batch'))->first();

            if($findBatch){
                // Find the corresponding Penlat_certificate record
                $penlatCert = Penlat_certificate::where('penlat_batch_id', $findBatch->id)->first();

                if ($penlatCert) {
                    // Update or create the Receivables_participant_certificate record
                    Receivables_participant_certificate::updateOrCreate(
                        [
                            'infografis_peserta_id' => $participant->id,
                        ],
                        [
                            'penlat_certificate_id' => $penlatCert->id,
                            'updated_at' => now(),
                        ]
                    );
                }
            }

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
        $validator = Validator::make($request->all(), [
            'penlat' => [
                'required',
                'min:1', // Ensure at least 2 characters in length
            ],
            'batch' => [
                'required',
                'min:2', // Ensure at least 2 characters in length
            ],
            'date' => 'required|date', // Ensure valid date format
            'image' => 'sometimes|image', // Ensure file is an image if present
            'program' => 'sometimes' // Optional field
        ], [
            // Custom error messages for min length validation
            'penlat.min' => 'Penlat must contain at least 2 characters.',
            'batch.min' => 'Batch must contain at least 2 characters.',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withInput($request->input()) // Preserve form input
                ->with('failed', 'Penlat or Batch must contain at least 2 characters or is not in valid format!') // Set session message
                ->withErrors($validator); // Pass validation errors
        }

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
            ]);
        }

        return redirect()->route('room-inventory')->with('success', 'Room inventory saved successfully!');
    }

    public function room_inventory_insert_item(Request $request, $roomId)
    {
        // Validate the request
        $validated = $request->validate([
            'tool.*' => 'required',
        ]);

         // Save the tools associated with this room
        foreach ($validated['tool'] as $key => $toolId) {
            Inventory_room::create([
                'room_id' => $roomId,
                'inventory_tool_id' => $toolId,
            ]);
        }

        return redirect()->route('preview-room', $roomId)->with('success', 'Room inventory saved successfully!');
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
            // Check if the Inventory_room exists
            $item = Inventory_room::find($id);

            // If the item does not exist, return a not found response
            if (!$item) {
                return response()->json(['error' => 'Item not found.'], 404);
            }

            // Delete the item
            $item->delete();

            // Return a success response for AJAX
            return response()->json(['success' => 'Deleted Successfully.']);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to delete Inventory Room: ' . $e->getMessage());

            // Return a generic error message
            return response()->json(['error' => 'Failed to delete record. Please try again later.'], 500);
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

    public function preview_room(Request $request, $roomId)
    {
        $assets = Inventory_tools::all();
        $locations = Location::all();

        $selectedLocation = $request->locationFilter ?? '-1';
        $selectedCondition = $request->conditionFilter ?? '-1';
        $assetCondition = Asset_condition::all();

        $data = Room::with([
            'list.tools.location',
            'list.tools.condition',
            'list.tools.img',
            'list.tools.items.condition',
            'list.tools.rooms_inventory' // Include the rooms_inventory relationship
        ])->find($roomId);

        // Check for AJAX request
        if ($request->ajax()) {
            // Extract tools data through relationships
            $toolsQuery = $data->list->map->tools->filter(); // Collect all related tools

            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $toolsQuery = $toolsQuery->filter(function ($tool) use ($searchValue) {
                    return stripos($tool->asset_name, $searchValue) !== false ||
                        stripos($tool->asset_id, $searchValue) !== false;
                });
            }

            // Order the collection if requested
            if ($request->has('order') && isset($request->order[0]['column'])) {
                $columns = ['asset_name', 'asset_stock', 'used_amount'];
                $columnIndex = $request->order[0]['column'];
                $sortDirection = $request->order[0]['dir'] === 'asc' ? SORT_ASC : SORT_DESC;

                if (isset($columns[$columnIndex])) {
                    $toolsQuery = $toolsQuery->sortBy($columns[$columnIndex], SORT_REGULAR, $sortDirection === SORT_DESC);
                }
            }

            // Process DataTables response
            return DataTables::of($toolsQuery)
                ->addColumn('tool', function ($item) {
                    $hoursDifference = \Carbon\Carbon::now()->diffInHours(\Carbon\Carbon::parse($item->last_maintenance));

                    $html = '
                    <div class="row">
                        <div class="col-md-4 d-flex justify-content-center align-items-start mt-2">
                            <a class="animateBox" href="' . route('preview-asset', $item->id) . '">
                                <img src="' . asset($item->img->filepath) . '" style="height: 150px; width: 160px; border: 1px solid rgb(202, 202, 202);" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
                            </a>
                        </div>
                        <div class="col-md-8 text-left mt-sm-2">
                            <h5 class="card-title font-weight-bold">' . $item->asset_name . '</h5>
                            <div class="ml-2">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td style="width: 200px;" class="mb-2"><i class="fa fa-chevron-right mr-2"></i> Nomor Aset</td>
                                        <td style="text-align: start;">: ' . $item->asset_id . '</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 200px;" class="mb-2"><i class="fa fa-chevron-right mr-2"></i> Location</td>
                                        <td style="text-align: start;">: ' . $item->location->description . '</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 200px;" class="mb-2"><i class="fa fa-chevron-right mr-2"></i> Running Hour</td>
                                        <td style="text-align: start;">: ' . $hoursDifference . ' hours</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 200px;" class="mb-2"><i class="fa fa-chevron-right mr-2"></i> Next Maintenance</td>
                                        <td style="text-align: start;">: ' . \Carbon\Carbon::parse($item->next_maintenance)->format('d-M-Y') . '</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>';

                    return $html;
                })
                ->addColumn('stock', function ($item) {
                    return $item->asset_stock ? $item->asset_stock . ' Unit(s)' : 'Out of Stock';
                })
                ->addColumn('used', function ($item) {
                    return $item->used_amount ? $item->used_amount . ' Unit(s)' : '0 Unit';
                })
                ->addColumn('condition', function ($item) {
                    // Group asset items by condition and count them
                    $itemConditions = $item->items->groupBy('asset_condition_id')->map(function ($group) {
                        return [
                            'count' => $group->count(),
                            'condition' => $group->first()->condition->badge, // Assuming condition has a 'badge' field
                        ];
                    });

                    // Start building the table HTML for the conditions
                    $conditionHtml = '<table class="table table-borderless table-sm">';

                    // Iterate through the grouped conditions and add each to the table
                    foreach ($itemConditions as $condition) {
                        $conditionHtml .= '
                            <tr>
                                <td><i class="ti-minus mr-2"></i>' . $condition['count'] . ' Items are ' . $condition['condition'] . '</td>
                            </tr>';
                    }

                    // Check if maintenance is required and add it to the table
                    $nextMaintenanceDate = strtotime($item->next_maintenance);
                    $currentDate = strtotime(date('Y-m-d'));

                    if ($nextMaintenanceDate < $currentDate) {
                        $conditionHtml .= '
                            <tr>
                                <td><i class="ti-minus mr-2"></i><span class="badge out-of-stock">Maintenance Required</span></td>
                            </tr>';
                    }

                    $conditionHtml .= '</table>';

                    return $conditionHtml;
                })
                ->addColumn('action', function ($item) use ($roomId) {
                    // Filter the Inventory_room records for the specific room
                    $inventoryRoom = $item->rooms_inventory->where('room_id', $roomId)->first();

                    // Generate the delete button with a `data-id` attribute for AJAX
                    $deleteButton = $inventoryRoom
                        ? '<button class="btn btn-outline-danger btn-md text-danger delete-item" data-id="' . $inventoryRoom->id . '">
                                <i class="fa fa-trash-o"></i>
                           </button>'
                        : '<button class="btn btn-outline-danger btn-md text-danger" disabled>
                                <i class="fa fa-trash-o"></i>
                           </button>';

                    // Generate the view button using the `inventory_tool` id
                    $viewButton = '<button data-id="' . $item->id . '" class="btn btn-outline-secondary btn-md mr-2 view-tool">
                                        <i class="fa fa-info-circle"></i>
                                   </button>';

                    // Return both buttons in a container
                    return '
                    <div class="text-center">
                        ' . $viewButton . $deleteButton . '
                    </div>';
                })
                ->rawColumns(['tool', 'condition', 'action']) // Indicate which columns contain HTML
                ->make(true);
        }

        return view('operation.submenu.preview-room-inventory', [
            'assets' => $assets,
            'data' => $data,
            'locations' => $locations,
            'selectedLocation' => $selectedLocation,
            'locations' => $locations,
            'assetCondition' => $assetCondition,
        ]);
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
        try {
            // Get the sum of all 'total' values from Penlat_utility_usage for the given batch
            $totalUsageCost = Penlat_utility_usage::where('penlat_batch_id', $penlatBatchId)
                ->sum('total');

            // Find the batch
            $findBatch = Penlat_batch::find($penlatBatchId);
            if (!$findBatch) {
                throw new \Exception("Batch not found.");
            }

            // Find the related Profit record based on the batch
            $profit = Profit::where('pelaksanaan', $findBatch->batch)->first();

            // If Profit exists, update the 'penlat_usage' column
            if ($profit) {
                $profit->update(['penlat_usage' => $totalUsageCost]);
            } else {
                throw new \Exception("Profit record not found for batch: " . $findBatch->batch);
            }
        } catch (\Exception $e) {
            // Log the exception or handle the error
            Log::error('Error updating usage cost: ' . $e->getMessage());
        }
    }

    public function list_utilities()
    {
        $utilities = Utility::all();
        return view('master-data.utilities', [
            'utilities' => $utilities
        ]);
    }

    public function store_new_utility(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'utility_name' => 'required|string|max:255',
            'utility_unit' => 'required|string|max:255',
            'utility_field_name' => 'required|string|max:255',
            'display' => 'required|image|mimes:jpeg,png,jpg,gif', // Optional: validate file type and size
        ]);

        // Handle the image upload
        if ($request->hasFile('display')) {
            $image = $request->file('display');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/utility_data'), $filename);
            $imagePath = 'uploads/utility_data/' . $filename;
        } else {
            $imagePath = null; // In case no file is uploaded
        }

        // Create a new utility record
        Utility::create([
            'utility_name' => $validated['utility_name'],
            'utility_unit' => $validated['utility_unit'],
            'field_name' => $validated['utility_field_name'],
            'filepath' => $imagePath,
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Utility data saved successfully!');
    }

    public function deleteUtility(Request $request)
    {
        $utility = Utility::find($request->id);

        if (!$utility) {
            return response()->json(['error' => 'Utility not found.'], 404);
        }

        // Prohibit deletion for utility IDs 1 to 6
        if (in_array($utility->id, [1, 2, 3, 4, 5, 6])) {
            return response()->json(['error' => 'This utility cannot be deleted.'], 400);
        }

        // Check if utility has related records
        $isUtilityUsed = $utility->items()->exists();  // Check if there are related records in the penlat_utility_usage table

        if ($isUtilityUsed) {
            return response()->json(['error' => 'Utility cannot be deleted because it has related records.'], 400);
        }

        // Delete the utility if no related records exist
        $utility->delete();

        return response()->json(['success' => 'Utility deleted successfully.']);
    }

    public function getUtility($id)
    {
        $utility = Utility::find($id);

        if (!$utility) {
            return response()->json(['error' => 'Utility not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $utility
        ]);
    }

    public function updateUtility(Request $request)
    {
        // Validate input data
        $request->validate([
            'utility_name' => 'required|string|max:255',
            'utility_unit' => 'required|regex:/^[A-Za-z]+$/',
            'display' => 'nullable|image|mimes:jpeg,png,jpg,gif', // Limit to 2MB and specific formats
        ]);

        $utility = Utility::find($request->utility_id);

        // Handle image upload
        if ($request->hasFile('display')) {
            // Delete existing image if exists
            if ($utility->filepath && file_exists(public_path($utility->filepath))) {
                unlink(public_path($utility->filepath));
            }

            $image = $request->file('display');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/utility_data'), $filename);
            $utility->filepath = 'uploads/utility_data/' . $filename;
        }

        // Update utility fields with sanitized inputs
        $utility->utility_name = e($request->utility_name); // Escape HTML characters
        $utility->utility_unit = e($request->utility_unit);
        $utility->field_name = e($request->utility_field_name);

        $utility->save();

        return response()->json(['success' => 'Utility updated successfully.']);
    }

    public function error_log(Request $request)
    {
        // Delete rows older than 1 week
        Error_log_import::where('created_at', '<', now()->subWeek())->delete();

        $errors = Error_log_import::with('user') // Eager load the user relationship
            ->where('import_id', 1)
            ->where('created_at', '>=', now()->subWeek())
            ->orderBy('created_at', 'desc')
            ->get();

        if ($request->ajax()) {
            return DataTables::of($errors)
                ->addIndexColumn() // Automatically add index column
                ->addColumn('importer', function ($error) {
                    return $error->user ? $error->user->name : 'Unknown'; // Get user name or default
                })
                ->editColumn('created_at', function ($error) {
                    return $error->created_at->format('Y-m-d H:i:s'); // Format the date
                })
                ->make(true);
        }

        return view('operation.import.error_log');
    }
}
