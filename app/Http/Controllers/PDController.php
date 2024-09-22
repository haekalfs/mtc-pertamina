<?php

namespace App\Http\Controllers;

use App\Models\Certificates_catalog;
use App\Models\Certificates_to_penlat;
use App\Models\Department;
use App\Models\Feedback_mtc;
use App\Models\Feedback_report;
use App\Models\Feedback_template;
use App\Models\Infografis_peserta;
use App\Models\Instructor;
use App\Models\Instructor_certificate;
use App\Models\Penlat;
use App\Models\Penlat_batch;
use App\Models\Penlat_certificate;
use App\Models\Position;
use App\Models\Receivables_participant_certificate;
use App\Models\Regulation;
use App\Models\Role;
use App\Models\Status;
use App\Models\Training_reference;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PDController extends Controller
{
    public function index($yearSelected = null)
    {
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        // Set the selected year
        $yearSelected = $yearSelected ?? $nowYear;

        $getInstructors = Instructor::all();
        // Calculate average feedback score and order instructors by it
        $instructors = Instructor::withCount([
            'feedbacks as average_feedback_score' => function ($query) {
                $query->select(DB::raw('coalesce(avg(score), 0)'));
            },
            'feedbacks' // This will count the number of feedbacks
        ])
        ->orderByDesc('average_feedback_score')
        ->take(3)
        ->get();

        // Count total instructors
        $instructorCount = Instructor::count();

        // Get reference data
        $getData = Training_reference::groupBy('penlat_id')->pluck('penlat_id')->toArray();
        $referencesCount = Penlat::whereIn('id', $getData)->count();

        // Calculate the average feedback score
        $averageFeedbackScore = DB::table('feedback_mtc')->whereYear('tgl_pelaksanaan', $yearSelected)
        ->select(DB::raw('
            avg(
                (
                    COALESCE(relevansi_materi, 0) +
                    COALESCE(manfaat_training, 0) +
                    COALESCE(durasi_training, 0) +
                    COALESCE(sistematika_penyajian, 0) +
                    COALESCE(tujuan_tercapai, 0) +
                    COALESCE(kedisiplinan_steward, 0) +
                    COALESCE(fasilitasi_steward, 0) +
                    COALESCE(layanan_pelaksana, 0) +
                    COALESCE(proses_administrasi, 0) +
                    COALESCE(kemudahan_registrasi, 0) +
                    COALESCE(kondisi_peralatan, 0) +
                    COALESCE(kualitas_boga, 0) +
                    COALESCE(media_online, 0) +
                    COALESCE(rekomendasi, 0)
                ) / 14
            ) as average_score
        '))
        ->value('average_score');

        //3 Years Prior Avg Feedback
        // Set the selected year or default to the current year
        $selectedDate = Carbon::createFromDate($yearSelected ?? now()->year);

        // Now you can apply subYear() and subYears() based on this dynamically selected date
        $currentYearMtc = $selectedDate->year;
        $lastYearMtc = $selectedDate->copy()->subYear(1)->year;
        $twoYearsAgoMtc = $selectedDate->copy()->subYears(2)->year;

        // Calculate average feedback score for each year
        $feedbackScoresPerYear = [];
        $yearsMtc = [$currentYearMtc, $lastYearMtc, $twoYearsAgoMtc];

        foreach ($yearsMtc as $yearMtc) {
            $feedbackScoresPerYear[$yearMtc] = DB::table('feedback_mtc')
                ->whereYear('tgl_pelaksanaan', $yearMtc)
                ->select(DB::raw('
                    avg(
                        (
                            COALESCE(relevansi_materi, 0) +
                            COALESCE(manfaat_training, 0) +
                            COALESCE(durasi_training, 0) +
                            COALESCE(sistematika_penyajian, 0) +
                            COALESCE(tujuan_tercapai, 0) +
                            COALESCE(kedisiplinan_steward, 0) +
                            COALESCE(fasilitasi_steward, 0) +
                            COALESCE(layanan_pelaksana, 0) +
                            COALESCE(proses_administrasi, 0) +
                            COALESCE(kemudahan_registrasi, 0) +
                            COALESCE(kondisi_peralatan, 0) +
                            COALESCE(kualitas_boga, 0) +
                            COALESCE(media_online, 0) +
                            COALESCE(rekomendasi, 0)
                        ) / 14
                    ) as average_score_mtc
                '))
                ->value('average_score_mtc') ?? 0; // Default to 0 if no score
        }

        $regulations = Regulation::latest()->take(3)->get();

        // Fetch the suggestions with pagination (e.g., 5 per page)
        $suggestions = Feedback_mtc::select('saran')->whereNotNull('saran')->orderBy('updated_at', 'desc')->take(min(Feedback_mtc::count(), 12))->get();

        // Fetch the unique titles for the dropdown
        $trainingTitles = Feedback_mtc::select('judul_pelatihan')->whereYear('tgl_pelaksanaan', $yearSelected)
        ->distinct()
        ->orderBy('judul_pelatihan')
        ->pluck('judul_pelatihan');

        return view('plan_dev.index', [
            'instructorCount' => $instructorCount,
            'referencesCount' => $referencesCount,
            'instructors' => $instructors,
            'averageFeedbackScore' => $averageFeedbackScore,
            'regulations' => $regulations,
            'instructorsList' => $getInstructors,
            'yearsBefore' => $yearsBefore,
            'yearSelected'=> $yearSelected,
            'feedbackScoresPerYear' => $feedbackScoresPerYear,
            'suggestions' => $suggestions,
            'trainingTitles' => $trainingTitles
        ]);
    }

    public function getFeedbackMTCChartData($yearSelected, Request $request)
    {
        $instructorId = $request->query('ratingPelatihan', 'all');

        // List of columns to calculate averages for
        $columns = [
            'relevansi_materi',
            'manfaat_training',
            'durasi_training',
            'sistematika_penyajian',
            'tujuan_tercapai',
            'kedisiplinan_steward',
            'fasilitasi_steward',
            'layanan_pelaksana',
            'proses_administrasi',
            'kemudahan_registrasi',
            'kondisi_peralatan',
            'kualitas_boga',
            'media_online',
            'rekomendasi'
        ];

        // Initialize query
        $query = Feedback_mtc::whereYear('tgl_pelaksanaan', $yearSelected);

        // Apply filter if instructorId is not 'all'
        if ($instructorId !== 'all') {
            $query->where('judul_pelatihan', $instructorId);
        }

        // Fetch the average for each column
        $averages = [];
        foreach ($columns as $column) {
            $average = $query->avg(DB::raw("CAST($column AS DECIMAL(10,2))"));
            $averages[$column] = $average ?? 0;
        }

        // Return data in JSON format
        return response()->json($averages);
    }

    public function getFeedbackChartData(Request $request, $year)
    {
        $selectedInstructorId = $request->input('instructorId');

        $query = DB::table('feedback_reports as fr')
            ->join('feedback_template as ft', 'fr.feedback_template_id', '=', 'ft.id')
            ->select('ft.questioner', DB::raw('AVG(fr.score) as average_score'));

        // If an instructor is selected, filter by instructor; otherwise, return all
        if ($selectedInstructorId && $selectedInstructorId !== 'all') {
            $query->where('fr.instruktur', $selectedInstructorId);
        }

        $feedbackData = $query->whereYear('tgl_pelaksanaan', $year)->groupBy('ft.questioner')->get();

        return response()->json($feedbackData);
    }

    public function feedback_report_main()
    {
        return view('plan_dev.feedback-main');
    }

    public function feedback_report(Request $request)
    {
        if ($request->ajax()) {
            $feedbackTemplates = Feedback_template::all();

            // Start building the query for feedback reports
            $query = Feedback_report::query();

            // Apply filters based on the request parameters (if any)
            if ($request->nama_pelatihan != '-1') {
                $query->where('judul_pelatihan', $request->nama_pelatihan);
            }

            if ($request->kelompok != '-1') {
                $query->where('kelompok', $request->kelompok);
            }

            // Apply date range filter if provided
            if ($request->startDate && $request->endDate) {
                $query->whereBetween('tgl_pelaksanaan', [$request->startDate, $request->endDate]);
            }

            // Select necessary fields and perform a group by
            $query->select('nama', 'judul_pelatihan', 'instruktur', 'tgl_pelaksanaan', 'feedback_id')
                ->groupBy('nama', 'judul_pelatihan', 'instruktur', 'tgl_pelaksanaan', 'feedback_id');

            // Eager load feedback scores for all feedback templates in a single query
            $feedbackData = Feedback_report::whereIn('feedback_template_id', $feedbackTemplates->pluck('id'))
                ->get()
                ->groupBy(function($item) {
                    return $item->nama . '_' . $item->judul_pelatihan . '_' . $item->instruktur . '_' . $item->feedback_id;
                });

            // Transform the reports with feedback data
            $reports = $query->get()->map(function($item) use ($feedbackTemplates, $feedbackData) {
                $key = $item->nama . '_' . $item->judul_pelatihan . '_' . $item->instruktur . '_' . $item->feedback_id;
                $feedbackScores = $feedbackData->get($key, collect())->keyBy('feedback_template_id');

                $feedback = [];
                foreach ($feedbackTemplates as $template) {
                    $feedback['feedback_' . $template->id] = $feedbackScores->get($template->id)->score ?? '-';
                }

                // Add the feedback_id to the item
                $item->feedback_id_group = $item->feedback_id;

                return array_merge($item->toArray(), $feedback);
            });

            return DataTables::of($reports)
                ->addColumn('action', function($item) {
                    return '<a data-id="'. $item['feedback_id_group'] .'" class="btn btn-outline-secondary btn-sm mr-2 edit-btn"><i class="fa fa-edit"></i> Edit</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $feedbackTemplates = Feedback_template::orderBy('id', 'asc')->get();
        $queryFilter = Feedback_report::query();
        $filterPelatihan = $queryFilter->get()->unique('judul_pelatihan');
        $filterKelompok = $queryFilter->get()->unique('kelompok');

        return view('plan_dev.submenu.feedback', [
            'feedbackTemplates' => $feedbackTemplates,
            'filterPelatihan' => $filterPelatihan,
            'filterKelompok' => $filterKelompok
        ]);
    }

    public function feedback_report_import()
    {
        return view('plan_dev.submenu.feedback-import');
    }

    public function regulation()
    {
        $data = Regulation::all();
        $statuses = Status::all();
        return view('plan_dev.submenu.regulation', ['data' => $data ,'statuses' => $statuses]);
    }

    public function preview_regulation($itemId)
    {
        $data = Regulation::find($itemId);
        $statuses = Status::all();

        // Check if the file exists and is a PDF
        $fileExists = false;
        $isPdf = false;
        $filePath = null;

        if ($data && file_exists(public_path($data->filepath))) {
            $fileExists = true;
            $filePath = public_path($data->filepath);
            $isPdf = pathinfo($filePath, PATHINFO_EXTENSION) === 'pdf';
        }

        return view('plan_dev.submenu.preview-regulation', compact('data', 'fileExists', 'isPdf', 'filePath', 'statuses'));
    }

    public function main_certificate()
    {
        $data = Penlat_certificate::latest()->take(10)->get();
        $instructorData = Certificates_catalog::latest()->take(10)->get();

        return view('plan_dev.certification-main', ['data' => $data, 'instructorData' => $instructorData]);
    }

    public function certificate(Request $request)
    {
        $penlatList = Penlat::all();
        $listBatch = Penlat_batch::all();

        if ($request->ajax()) {
            $query = Penlat_certificate::with(['batch.penlat']);

            if ($request->penlat) {
                $query->whereHas('batch.penlat', function($q) use ($request) {
                    $q->where('id', $request->penlat);
                });
            }

            if ($request->batch) {
                $query->whereHas('batch', function($q) use ($request) {
                    $q->where('batch', $request->batch);
                });
            }

            // Fetch the correct records
            $certificates = $query->get();

            // Manually build the DataTables response
            return DataTables::of($certificates)
                ->addColumn('action', function($item) {
                    return '
                        <a class="btn btn-outline-secondary mr-2 btn-sm" href="'.route('preview-certificate', $item->id).'"><i class="menu-Logo fa fa-eye"></i> Preview</a>
                        <button class="btn btn-outline-danger btn-sm delete-certificate" data-id="'.$item->id.'"><i class="fa fa-trash"></i> Delete</button>
                    ';
                })
                ->make(true);
        }

        // If it's not an AJAX request, return the view with necessary data
        return view('plan_dev.submenu.certificate', [
            'penlatList' => $penlatList,
            'listBatch' => $listBatch
        ]);
    }

    public function certificate_update(Request $request, $certId)
    {
        $validatedData = $request->validate([
            'keterangan' => 'required',
            'status' => 'required',
        ]);

        $penlat = Penlat_certificate::findOrFail($certId);
        $penlat->keterangan = $request->input('keterangan');
        $penlat->status = $request->input('status');

        $penlat->save();

        return redirect()->back()->with('success', 'Program data updated successfully.');
    }

    public function certificate_instructor(Request $request)
    {
        // Eager load relationships to optimize queries
        $penlatList = Penlat::all();
        $listBatch = Penlat_batch::all();

        // Get the penlat filter value from the request, default to "-1" (Show All)
        $penlatFilter = $request->input('penlat', -1);

        // Apply filtering based on the selected penlat
        $dataQuery = Certificates_catalog::query();

        if ($penlatFilter != -1) {
            // Directly filter Certificates_catalog using a relationship (or use whereHas if necessary)
            $dataQuery->whereIn('id', function ($query) use ($penlatFilter) {
                $query->select('certificates_catalog_id')
                      ->from('certificates_to_penlats')
                      ->where('penlat_id', $penlatFilter);
            });
        }

        // Get the filtered or all data
        $data = $dataQuery->get();

        // Return view with filtered data
        return view('plan_dev.submenu.instructor-certificate', [
            'data' => $data,
            'penlatList' => $penlatList,
            'listBatch' => $listBatch
        ]);
    }

    public function instructor(Request $request, $penlatId = '-1', $statusId = '-1')
    {
        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            'penlat' => 'sometimes',
            'status' => 'sometimes',
            'age' => 'sometimes'
        ]);

        // Initialize the query with eager loading of the feedbacks relationship
        $query = Instructor::with('feedbacks');

        // Apply the Pelatihan (Training) filter
        if ($request->filled('penlat') && $request->penlat != '-1') {
            $penlatId = $request->penlat;

            // Search for related certificates
            $certificatesCatalogIds = Certificates_to_penlat::where('penlat_id', $penlatId)
                ->pluck('certificates_catalog_id');

            // Get instructor IDs related to the certificates
            $instructorIds = Instructor_certificate::whereIn('certificates_catalog_id', $certificatesCatalogIds)
                ->pluck('instructor_id');

            // Filter instructors by these IDs
            $query->whereIn('id', $instructorIds);
        }

        // Apply the Status filter
        if ($request->filled('status') && $request->status != '-1') {
            $statusId = $request->status;
            $query->where('status', $statusId);
        }

        // Apply the Age filter
        if ($request->filled('age') && $request->age != '-1') {
            $ageRange = $request->age;
            $query->where(function ($q) use ($ageRange) {
                switch ($ageRange) {
                    case '1': // 20 - 30 Years
                        $q->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, instructor_dob, CURDATE())'), [20, 30]);
                        break;
                    case '2': // 30 - 40 Years
                        $q->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, instructor_dob, CURDATE())'), [30, 40]);
                        break;
                    case '3': // >= 40 Years
                        $q->where(DB::raw('TIMESTAMPDIFF(YEAR, instructor_dob, CURDATE())'), '>=', 40);
                        break;
                }
            });
        }

        // Get the filtered data
        $data = $query->withCount([
            'feedbacks as average_feedback_score' => function ($query) {
                $query->select(DB::raw('coalesce(avg(score), 0)'));
            },
            'feedbacks' // This will count the number of feedbacks
        ])->get();

        // Get all Penlat data
        $penlatList = Penlat::all();

        // Pass the data to the view
        return view('plan_dev.submenu.instructor', [
            'data' => $data,
            'penlatList' => $penlatList,
            'penlatId' => $penlatId,
            'statusId' => $statusId,
            'umur' => $request->age
        ]);
    }

    public function training_reference(Request $request)
    {
        // Initialize the query with all Penlat data
        $query = Penlat::query();

        // Apply filters only if they are selected
        if ($request->filled('namaPenlat') && $request->namaPenlat != '-1') {
            $query->where('id', $request->namaPenlat);
        }

        if ($request->filled('stcw') && $request->stcw != '-1') {
            $query->where('kategori_pelatihan', $request->stcw);
        }

        // Get the penlat_ids that exist in the training references
        $getData = Training_reference::groupBy('penlat_id')->pluck('penlat_id')->toArray();
        $data = $query->whereIn('id', $getData)->get();

        // Fetch all Penlat data for the dropdown list
        $penlatList = Penlat::all();

        // Return view with data and penlatList
        return view('plan_dev.submenu.training-reference', [
            'penlatList' => $penlatList,
            'data' => $data,
            'selectedNamaPenlat' => $request->namaPenlat,
            'selectedStcw' => $request->stcw,
        ]);
    }

    public function regulation_store(Request $request)
    {
        // Validate the input
        $request->validate([
            'regulation_name' => 'required',
            'status' => 'required',
            'file' => 'required|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,odt,ods,odp,rtf'
        ]);

        // Create new regulation
        $regulation = new Regulation();
        $regulation->description = $request->input('regulation_name');
        $regulation->status = $request->input('status');
        $regulation->user_id = Auth::id();

        // If a file was uploaded, store it
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Get the file size in bytes before moving the file
            $fileSizeInBytes = $file->getSize();

            // Convert the size to KB
            $fileSizeInKb = $fileSizeInBytes / 1024;

            // Convert the size to MB
            $fileSizeInMb = $fileSizeInBytes / 1048576; // 1024 * 1024

            // Move the file to the desired location
            $file->move(public_path('uploads/regulation'), $filename);

            // Save the file path and size in KB or MB format with two decimal places
            $regulation->filepath = 'uploads/regulation/' . $filename;
            $regulation->filesize = number_format($fileSizeInKb, 2); // Save in KB
            // $regulation->filesize = number_format($fileSizeInMb, 2); // Save in MB
        }

        // Save to the database
        $regulation->save();

        // Redirect with a success message
        return redirect()->back()->with('success', 'Regulation created successfully.');
    }

    public function update_regulation(Request $request)
    {
        $request->validate([
            'regulation_name' => 'required',
            'status' => 'required',
            'file' => 'nullable|mimes:pdf,docx,doc'
        ]);

        $regulation = Regulation::find($request->input('regulation_id'));
        $regulation->description = $request->input('regulation_name');
        $regulation->status = $request->input('status');

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Optionally delete the old file if you want
            if ($regulation->filepath && file_exists(public_path($regulation->filepath))) {
                unlink(public_path($regulation->filepath));
            }

            $file->move(public_path('uploads/regulation'), $filename);
            $regulation->filepath = 'uploads/regulation/' . $filename;
        }

        $regulation->save();

        return redirect()->back()->with('success', 'Regulation updated successfully.');
    }

    public function delete_regulation($id)
    {
        $regulation = Regulation::find($id);

        if ($regulation) {
            // Delete the file from storage if it exists
            if (file_exists(public_path($regulation->filepath))) {
                unlink(public_path($regulation->filepath));
            }

            // Delete the record from the database
            $regulation->delete();

            return response()->json(['success' => 'Regulation deleted successfully.']);
        }

        return response()->json(['error' => 'Regulation not found.'], 404);
    }

    /**
     * Store the certificate data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function certificate_store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'penlat' => 'required',
            'batch' => 'required',
            'status' => 'required',
            'keterangan' => 'required',
            'program' => 'sometimes'
        ]);

        // Store the current timestamp
        $currentTimestamp = now();

        DB::beginTransaction();

        try {
            // Check if the batch already exists
            $checkData = Penlat_batch::where('batch', $request->batch)->exists();

            if(!$checkData) {
                // Create or update the Penlat_batch entry
                $penlatBatch = Penlat_batch::updateOrCreate(
                    [
                        'batch' => $validated['batch'],
                        'penlat_id' => $validated['penlat'],
                    ],
                    [
                        'nama_program' => $validated['program'],
                        'updated_at' => $currentTimestamp,
                    ]
                );
            } else {
                // If the batch exists, fetch the existing Penlat_batch record
                $penlatBatch = Penlat_batch::where('batch', $validated['batch'])->first();

                // Check if usages for this batch already exist
                $checkIfExist = Penlat_certificate::where('penlat_batch_id', $penlatBatch->id)->exists();

                if ($checkIfExist) {
                    // Redirect with a warning message if usages already exist
                    DB::rollBack();
                    return redirect()->route('certificate')->with('warning', "Certificate for batch $penlatBatch->batch already exist...");
                }
            }

            // Retrieve all participants for the specified batch
            $participants = Infografis_peserta::where('batch', $validated['batch'])->get();

            // Create or update the Penlat_certificate entry
            $penlatCertificate = Penlat_certificate::updateOrCreate(
                [
                    'penlat_batch_id' => $penlatBatch->id,
                ],
                [
                    'status' => $validated['status'],
                    'keterangan' => $validated['keterangan'],
                    'total_issued' => $participants->count(),
                    'updated_at' => $currentTimestamp,
                ]
            );

            // Iterate over participants and update or create their certificates
            foreach ($participants as $participant) {
                Receivables_participant_certificate::updateOrCreate(
                    [
                        'infografis_peserta_id' => $participant->id,
                        'penlat_certificate_id' => $penlatCertificate->id,
                    ],
                    [
                        'updated_at' => $currentTimestamp,
                    ]
                );
            }

            // Commit the transaction
            DB::commit();

            // Redirect or return a response with a success message
            return redirect()->back()->with('success', 'Certificate data stored successfully.');

        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Log the error (optional)
            Log::error('Error storing certificate data: ' . $e->getMessage());

            // Redirect or return a response with an error message
            return redirect()->back()->with('error', 'Failed to store certificate data. Please try again.');
        }
    }

    public function preview_certificate($id)
    {
        $data = Penlat_certificate::find($id);
        return view('plan_dev.submenu.preview-certificate', ['data' => $data]);
    }

    public function preview_certificate_catalog($id)
    {
        $data = Certificates_catalog::find($id);
        $data->total_issued = $data->holder->count();
        $data->save();

        return view('plan_dev.submenu.preview-certificate-catalog', ['data' => $data]);
    }

    public function register_instructor()
    {
        $certificate = Certificates_catalog::all();
        return view('plan_dev.submenu.register-instructor', ['certificate' => $certificate]);
    }

    public function certificate_catalog_store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'judulSertifikat' => 'required',
            'issued_by'       => 'required',
            'penlats'         => 'array', // Assuming 'penlats' is optional and can have multiple values
            'keterangan'      => 'nullable|string',
        ]);

        // Create a new Certificates_catalog entry
        $certificate = new Certificates_catalog();
        $certificate->certificate_name = $request->input('judulSertifikat');
        $certificate->issuedBy = $request->input('issued_by');
        $certificate->keterangan = $request->input('keterangan');

        // Save the model instance to the database
        $certificate->save();

        $penlats = $request->input('penlats');
        if ($penlats) {
            foreach ($penlats as $penlatId) {
                $certificateToPenlat = new Certificates_to_penlat();
                $certificateToPenlat->certificates_catalog_id = $certificate->id; // Assuming there's a certificate_id foreign key in Certificates_to_penlat
                $certificateToPenlat->penlat_id = $penlatId; // Assuming penlat_id is the foreign key related to penlats
                $certificateToPenlat->save();
            }
        }

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Certificate has been added successfully!');
    }

    public function preview_instructor($id, $penlatId)
    {
        $data = Instructor::find($id);

        if($penlatId != '-1'){
            $getData = Certificates_to_penlat::where('penlat_id', $penlatId)->pluck('certificates_catalog_id')->toArray();
        } else {
            $getData = Instructor_certificate::where('instructor_id', $id)->pluck('certificates_catalog_id')->toArray();
        }

        $certificateData = Instructor_certificate::where('instructor_id', $id)
            ->whereIn('certificates_catalog_id', $getData)
            ->with('catalog.relationOne.penlat')
            ->get();

        $allCerts = Instructor_certificate::where('instructor_id', $id)->whereNotIn('certificates_catalog_id', $getData)->get();

        return view('plan_dev.submenu.preview-instructor', ['penlatId' => $penlatId, 'data' => $data, 'certificateData' => $certificateData, 'remainingCerts' => $allCerts]);
    }

    public function references_store(Request $request)
    {
        // Validate the form data
        $request->validate([
            'penlat' => 'required',
            'documents.*' => 'required',
            'attachments.*' => 'sometimes|file|mimes:pdf,docx,xlsx,xls,jpeg,png,jpg,gif', // Adjust max size as needed
        ]);

        // Get the form data
        $documents = $request->input('documents');
        $penlatId = $request->input('penlat');

        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');

            foreach ($files as $file) {
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
                $upload_folder = public_path('uploads/references_attachment/');
                $filePath = 'uploads/references_attachment/' . $fileName;

                // Move the uploaded file to the storage folder
                $file->move($upload_folder, $fileName);

                $filePathArray[] = $filePath;
            }
        }

        $uniqueId = hexdec(substr(uniqid(), 0, 8));

        while (Training_reference::where('id', $uniqueId)->exists()) {
            $uniqueId = hexdec(substr(uniqid(), 0, 8));
        }

        // Example: Assuming you have a Document model, you can store each document in the database
        foreach ($documents as $key => $document) {

            try {
                DB::beginTransaction();

                $newDocument = new Training_reference([
                    'penlat_id' => $penlatId,
                    'references' => $document,
                    'filepath' => $filePathArray[$key] ?? NULL,
                ]);

                $newDocument->save();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                // Handle the exception or log the error
                return redirect()->back()->with('error', 'Failed to store receivable note. Please try again.');
            }
        }

        // Redirect back or to a specific route after processing
        return redirect()->back()->with('success', 'Form submitted successfully!');
    }

    public function preview_reference($penlatId)
    {
        $data = Penlat::find($penlatId);
        $penlatList = Penlat::all();
        return view('plan_dev.submenu.preview-training-reference', ['data' => $data, 'penlatList' => $penlatList]);
    }

    public function fetch_reference_data($id)
    {
        $reference = Training_reference::findOrFail($id);
        return response()->json($reference);
    }

    public function update_references(Request $request)
    {
        $request->validate([
            'document' => 'required',
            'attachment' => 'sometimes|file',
        ]);

        $reference = Training_reference::findOrFail($request->id);
        $reference->references = $request->document;

        // Handle file replacement
        if ($request->has('existing_file') && !$request->hasFile('attachment')) {
            // Do nothing; keep the existing file
        } elseif ($request->hasFile('attachment')) {
            // Unlink the existing file if it exists
            if ($reference->filepath && file_exists(public_path($reference->filepath))) {
                unlink(public_path($reference->filepath));
            }

            // Upload the new file
            $file = $request->file('attachment');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $upload_folder = public_path('uploads/references_attachment/');
            $filePath = 'uploads/references_attachment/' . $fileName;

            $file->move($upload_folder, $fileName);

            $reference->filepath = $filePath;
        } else {
            // If no file is uploaded and no existing file, set filepath to null
            $reference->filepath = null;
        }

        $reference->save();

        return redirect()->back()->with('success', 'Reference updated successfully!');
    }

    public function references_insert(Request $request, $penlatId)
    {
        // Validate the form data
        $request->validate([
            'documents.*' => 'required',
            'attachments.*' => 'sometimes|file', // Adjust the allowed file types and size
        ]);

        // Get the form data
        $documents = $request->input('documents');
        $penlatId = $penlatId;

        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');

            foreach ($files as $file) {
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
                $upload_folder = public_path('uploads/references_attachment/');
                $filePath = 'uploads/references_attachment/' . $fileName;

                // Move the uploaded file to the storage folder
                $file->move($upload_folder, $fileName);

                $filePathArray[] = $filePath;
            }
        }

        $uniqueId = hexdec(substr(uniqid(), 0, 8));

        while (Training_reference::where('id', $uniqueId)->exists()) {
            $uniqueId = hexdec(substr(uniqid(), 0, 8));
        }

        // Example: Assuming you have a Document model, you can store each document in the database
        foreach ($documents as $key => $document) {

            try {
                DB::beginTransaction();

                $newDocument = new Training_reference([
                    'penlat_id' => $penlatId,
                    'references' => $document,
                    'filepath' => $filePathArray[$key] ?? NULL,
                ]);

                $newDocument->save();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                // Handle the exception or log the error
                return redirect()->back()->with('error', 'Failed to store receivable note. Please try again.');
            }
        }

        // Redirect back or to a specific route after processing
        return redirect()->back()->with('success', 'Form submitted successfully!');
    }

    public function deleteTrainingReference($penlat_id)
    {
        try {
            // Delete all references associated with the given penlat_id
            Training_reference::where('penlat_id', $penlat_id)->delete();

            return response()->json(['message' => 'All references have been deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete references. Please try again.'], 500);
        }
    }

    public function destroy_reference($id)
    {
        $trainingReference = Training_reference::findOrFail($id);
        $trainingReference->delete();

        return response()->json(['success' => 'Item deleted successfully']);
    }

    public function save_receivable(Request $request)
    {
        $participant = Receivables_participant_certificate::findOrFail($request->id);
        $participant->update([
            'date_received' => $request->date_received,
            'status' => $request->status
        ]);

        $penlatCertificateId = $participant->penlat_certificate_id;

        // Check if there are any participants with status NULL or false
        $hasIncompleteStatus = Receivables_participant_certificate::where('penlat_certificate_id', $penlatCertificateId)
            ->where(function($query) {
                $query->whereNull('status')
                    ->orWhere('status', 'false');
            })->exists();

        // If all statuses are true, update the Penlat_certificate status
        if (!$hasIncompleteStatus) {
            Penlat_certificate::where('id', $penlatCertificateId)->update(['status' => 'Issued']);
        }else {
            Penlat_certificate::where('id', $penlatCertificateId)->update(['status' => 'On Process']);
        }

        return response()->json(['status' => 'success']);
    }

    public function delete_receivable(Request $request)
    {
        $participant = Receivables_participant_certificate::find($request->id);
        $participant->delete();

        return response()->json(['status' => 'success']);
    }

    public function saveAllReceivables(Request $request)
    {
        // Retrieve participants data from the request
        $participants = $request->input('participants');
        if (empty($participants)) {
            return response()->json(['status' => 'error', 'message' => 'No participants provided.'], 400);
        }

        // Loop through the participants and update each participant's status and date_received
        foreach ($participants as $participantData) {
            $participant = Receivables_participant_certificate::findOrFail($participantData['id']);
            $participant->update([
                'date_received' => $participantData['date_received'],
                'status' => $participantData['status']
            ]);

            // Capture penlatCertificateId from the last participant (assuming all participants have the same penlat_certificate_id)
            $penlatCertificateId = $participant->penlat_certificate_id;
        }

        // If penlatCertificateId is found, check if all participants have a true status
        if (isset($penlatCertificateId)) {
            // Check if there are any participants with status NULL or 'false'
            $hasIncompleteStatus = Receivables_participant_certificate::where('penlat_certificate_id', $penlatCertificateId)
                ->where(function($query) {
                    $query->whereNull('status')
                        ->orWhere('status', 'false');
                })->exists();

            // If no incomplete statuses exist, update the Penlat_certificate status to 'Issued'
            if (!$hasIncompleteStatus) {
                Penlat_certificate::where('id', $penlatCertificateId)->update(['status' => 'Issued']);
            }else {
                Penlat_certificate::where('id', $penlatCertificateId)->update(['status' => 'On Process']);
            }
        }

        return response()->json(['status' => 'success']);
    }

    public function delete_certificate(Request $request)
    {
        try {
            // Start a transaction
            DB::beginTransaction();

            // Find the certificate by ID
            $certificate = Penlat_certificate::find($request->id);

            if ($certificate) {
                // Delete related participants first
                $certificate->participant()->delete();

                // Delete the certificate
                $certificate->delete();

                // Commit the transaction
                DB::commit();

                return response()->json(['success' => true]);
            } else {
                // Rollback if certificate not found
                DB::rollBack();

                return response()->json(['success' => false], 404);
            }
        } catch (\Exception $e) {
            // Rollback in case of an error
            DB::rollBack();

            // Log the error for debugging
            Log::error('Error deleting certificate: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Error deleting certificate'], 500);
        }
    }

    public function deleteCertificate($id)
    {
        $certificate = Certificates_catalog::findOrFail($id);

        // Delete related records in the relationOne and holder relationships
        $certificate->relationOne()->delete();
        $certificate->holder()->delete();

        // Delete the certificate itself
        $certificate->delete();

        return response()->json(['success' => 'Certificate and related data deleted successfully.']);
    }
}
