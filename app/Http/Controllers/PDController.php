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
use App\Models\Regulator;
use App\Models\Regulator_amendment;
use App\Models\Role;
use App\Models\Status;
use App\Models\Training_reference;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use ZipArchive;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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
        ->take(5)
        ->get();

        // Count total instructors
        $instructorCount = Instructor::count();

        // Get reference data
        $countIncompleteCert = Penlat_certificate::whereYear('created_at', $yearSelected)->where('status', 'On Process')->count();
        $countCert = Penlat_certificate::whereYear('created_at', $yearSelected)->count();

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
        $suggestions = Feedback_mtc::select('saran')
        ->whereNotNull('saran')
        ->orderByRaw('LENGTH(saran) DESC')
        ->take(min(Feedback_mtc::count(), 12))
        ->get();

        // Fetch the unique titles for the dropdown
        $trainingTitles = Feedback_mtc::select('judul_pelatihan')->whereYear('tgl_pelaksanaan', $yearSelected)
        ->distinct()
        ->orderBy('judul_pelatihan')
        ->pluck('judul_pelatihan');

        return view('plan_dev.index', [
            'instructorCount' => $instructorCount,
            'countIncompleteCert' => $countIncompleteCert,
            'countCert' => $countCert,
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
        $listRegulator = Regulator::all();
        $listAmendment = Regulator_amendment::all();

        if ($request->ajax()) {
            $query = Penlat_certificate::with(['batch.penlat']);

            if ($request->penlat) {
                $query->whereHas('batch.penlat', function($q) use ($request) {
                    $q->where('id', $request->penlat);
                });
            }

            if ($request->kategori_pelatihan) {
                $query->whereHas('batch.penlat', function($q) use ($request) {
                    $q->where('kategori_pelatihan', $request->kategori_pelatihan);
                });
            }

            if ($request->batch) {
                $query->whereHas('batch', function($q) use ($request) {
                    $q->where('batch', $request->batch);
                });
            }

            if ($request->periode && $request->periode != '-1') {
                $query->whereYear('start_date', $request->periode);
            }

            // Fetch the correct records
            $certificates = $query->get();

            // Manually build the DataTables response
            return DataTables::of($certificates)
                ->addColumn('jumlah_issued', function($item) {
                    $issuedCount = $item->participant->where('status', true)->count();
                    $totalIssued = $item->total_issued;

                    $class = $issuedCount == $totalIssued ? 'text-success' : '';

                    return '<span class="' . $class . '">' . $issuedCount . '/' . $totalIssued . '</span>';
                })
                ->addColumn('kategori_pelatihan', function($item) {
                    return $item->batch->penlat->kategori_pelatihan;
                })
                ->addColumn('tgl_pelaksanaan', function($item) {
                    return Carbon::parse($item->batch->date)->format('d-M-Y');
                })
                ->addColumn('created_by', function($item) {
                    return $item->created_by;
                })
                ->addColumn('created_at', function($item) {
                    return $item->created_at->format('d-M-Y');
                })
                ->editColumn('keterangan', function($item) {
                    return $item->keterangan ?? '-';
                })
                ->addColumn('action', function($item) {
                    $actionButtons = '
                        <a class="btn btn-outline-secondary mr-2 btn-sm" href="'.route('preview-certificate', $item->id).'">
                            <i class="menu-Logo fa fa-external-link"></i> Preview
                        </a>
                    ';

                    if ($item->status == 'On Process') {
                        $actionButtons .= '
                            <button class="btn btn-outline-danger btn-sm delete-certificate" data-id="'.$item->id.'">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        ';
                    }

                    return $actionButtons;
                })
                ->rawColumns(['jumlah_issued', 'action']) // Ensure the HTML is rendered
                ->make(true);
        }

        // If it's not an AJAX request, return the view with necessary data
        return view('plan_dev.submenu.certificate', [
            'penlatList' => $penlatList,
            'listBatch' => $listBatch,
            'listRegulator' => $listRegulator,
            'listAmendment' => $listAmendment
        ]);
    }

    public function certificate_update(Request $request, $certId)
    {
        // Validate input data
        $validatedData = $request->validate([
            'keterangan' => 'sometimes|max:255', // Optional, but validated if present
            'program' => 'sometimes',           // Optional
            'startDate' => 'sometimes|date',    // Optional
            'endDate' => 'sometimes|date',      // Optional
            'regulator' => 'sometimes',         // Optional
            'regulator_amendment' => 'sometimes', // Optional
        ]);

        // Find the certificate by ID
        $penlat = Penlat_certificate::findOrFail($certId);

        // Prepare the data to update, excluding empty values
        $updateData = [];
        $updateData['keterangan'] = $validatedData['keterangan'];

        if (!empty($validatedData['program'])) {
            $updateData['certificate_title'] = strtoupper($validatedData['program']);
        }
        if (!empty($validatedData['startDate'])) {
            $updateData['start_date'] = $validatedData['startDate'];
        }
        if (!empty($validatedData['endDate'])) {
            $updateData['end_date'] = $validatedData['endDate'];
        }
        if (empty($validatedData['regulator_amendment']) || empty($validatedData['regulator']) || $validatedData['regulator_amendment'] == -1 || $validatedData['regulator'] == -1) {
            $updateData['regulator_amendment'] = NULL;
            $updateData['regulator'] = NULL;
        } else {
            $updateData['regulator_amendment'] = $validatedData['regulator_amendment'];
            $updateData['regulator'] = $validatedData['regulator'];

        }

        // Update the certificate fields only if there are changes
        if (!empty($updateData)) {
            $penlat->update($updateData);
        }

        // Redirect back with success message
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
        $query = Instructor::with('feedbacks')
            ->withCount('feedbacks');  // Count feedbacks related to the instructor

        if ($request->ajax()) {
            // Apply the Penlat filter
            if ($request->filled('penlat') && $request->penlat != '-1') {
                $penlatId = $request->penlat;
                $certificatesCatalogIds = Certificates_to_penlat::where('penlat_id', $penlatId)
                    ->pluck('certificates_catalog_id');
                $instructorIds = Instructor_certificate::whereIn('certificates_catalog_id', $certificatesCatalogIds)
                    ->pluck('instructor_id');
                $query->whereIn('id', $instructorIds);
            }

            // Apply the Status filter
            if ($request->filled('status') && $request->status != '-1') {
                $query->where('status', $request->status);
            }

            // Apply the Age filter
            if ($request->filled('age') && $request->age != '-1') {
                $ageRange = $request->age;
                $query->where(function ($q) use ($ageRange) {
                    switch ($ageRange) {
                        case '1': $q->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, instructor_dob, CURDATE())'), [20, 30]); break;
                        case '2': $q->whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, instructor_dob, CURDATE())'), [30, 40]); break;
                        case '3': $q->where(DB::raw('TIMESTAMPDIFF(YEAR, instructor_dob, CURDATE())'), '>=', 40); break;
                    }
                });
            }

            return DataTables::of($query)
                // Handle the image with the fallback logic
                ->addColumn('avatar_img', function ($row) {
                    return $row->imgFilepath ? asset($row->imgFilepath) : asset('img/default-img.png');
                })
                ->addColumn('avatar_url', function ($row) {
                    return route('preview-instructor', ['id' => $row->id, 'penlatId' => request()->penlatId ?? '-1']);
                })
                ->addColumn('age', function ($row) {
                    return \Carbon\Carbon::parse($row->instructor_dob)->age . ' Tahun';
                })
                ->addColumn('working_hours', function ($row) {
                    return $row->working_hours ? $row->working_hours . ' Jam' : '-';
                })
                ->addColumn('rate', function ($row) {
                    $roundedScore = round($row->average_feedback_score, 1);
                    return '<span><i class="fa fa-star text-warning"></i> ' . ($roundedScore ?? '-') . '</span>';
                })
                ->addColumn('feedbacks_count', function ($row) {
                    return ($row->feedbacks_count / 5) . ' feedbacks'; // Fix feedback count by dividing by 5
                })
                ->addColumn('action', function ($row) use ($penlatId) {
                    $previewUrl = route('preview-instructor', ['id' => $row->id, 'penlatId' => $penlatId]);
                    $editUrl = route('edit-instructor', $row->id);
                    return '
                        <a class="btn btn-outline-secondary btn-sm mr-2" href="' . $editUrl . '"><i class="fa fa-edit"></i> Update</a>
                        <a class="btn btn-outline-secondary btn-sm" href="' . $previewUrl . '"><i class="fa fa-external-link"></i> Preview</a>
                    ';
                })
            ->rawColumns(['rate', 'action'])
            ->make(true);
        }

        $penlatList = Penlat::all();

        return view('plan_dev.submenu.instructor', [
            'penlatList' => $penlatList,
            'penlatId' => $penlatId,
            'statusId' => $statusId,
            'umur' => $request->age,
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
            'keterangan' => 'sometimes',
            'program' => 'sometimes',
            'startDate' => 'required',
            'endDate' => 'required',
            'regulator_amendment' => 'sometimes',
            'numbering' => 'required',
            'initial_number' => 'sometimes',
            'regulator' => 'sometimes',
            'photo_placeholder' => 'nullable|boolean',
        ]);

        // Store the current timestamp
        $currentTimestamp = now();

        DB::beginTransaction();

        try {
            // Check if the batch already exists
            $checkData = Penlat_batch::where('batch', $request->batch)->exists();
            $isInternal = true;

            if(!$checkData) {
                // Create or update the Penlat_batch entry
                $penlatBatch = Penlat_batch::updateOrCreate(
                    [
                        'batch' => $validated['batch'],
                        'penlat_id' => $validated['penlat'],
                    ],
                    [
                        'date' => $validated['startDate'],
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

            if($penlatBatch->penlat->kategori_pelatihan == 'NON STCW'){
                $isInternal = false;
            }

            // Retrieve all participants for the specified batch
            $participants = Infografis_peserta::where('batch', $validated['batch'])->orderBy('nama_peserta', 'asc')->get();

            $penlatCertificateData = [
                'certificate_title' => $validated['program'],
                'status' => $validated['status'],
                'start_date' => $validated['startDate'],
                'end_date' => $validated['endDate'],
                'keterangan' => $validated['keterangan'],
                'total_issued' => $participants->count(),
                'created_by' => Auth::id(),
                'updated_at' => $currentTimestamp,
                'photo_placeholder' => request()->has('photo_placeholder') ? 1 : 0,
            ];

            // Add regulator fields only if regulator_amendment is -1
            if ($validated['regulator_amendment'] != -1) {
                $penlatCertificateData['regulator_amendment'] = $validated['regulator_amendment'];
                $penlatCertificateData['regulator'] = $validated['regulator'];
            }

            $penlatCertificate = Penlat_certificate::updateOrCreate(
                ['penlat_batch_id' => $penlatBatch->id],
                $penlatCertificateData
            );

            $initialNumber = $validated['initial_number'];

            // Iterate over participants and update or create their certificates
            foreach ($participants as $participant) {
                $data = [
                    'isInternal' => $isInternal,
                    'updated_at' => $currentTimestamp,
                ];

                // Add certificate_number only if numbering is 1
                if ($validated['numbering'] == 1) {
                    $data['certificate_number'] = $this->getNumberCerticates($validated['penlat']);
                } elseif ($validated['numbering'] == 2 && $validated['initial_number']){
                    $data['certificate_number'] = $initialNumber;
                    $initialNumber++;
                }

                if($validated['status'] == 'Issued'){
                    $data['status'] = 1;
                }

                Receivables_participant_certificate::updateOrCreate(
                    [
                        'infografis_peserta_id' => $participant->id,
                        'penlat_certificate_id' => $penlatCertificate->id,
                    ],
                    $data
                );
            }

            // Commit the transaction
            DB::commit();

            // Redirect or return a response with a success message
            return redirect()->route('preview-certificate', $penlatCertificate->id)->with('success', 'Certificate data stored successfully.');

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
        $listAmendment = Regulator_amendment::all();
        $listRegulator = Regulator::all();

        return view('plan_dev.submenu.preview-certificate', ['data' => $data, 'listAmendment' => $listAmendment, 'listRegulator' => $listRegulator]);
    }

    public function getCertificates(Request $request, $id)
    {
        // Fetch the certificates with the required relationships
        $certificates = Receivables_participant_certificate::with(['peserta', 'penlatCertificate'])
            ->where('penlat_certificate_id', $id)
            ->get();

        return DataTables::of($certificates)
            ->addColumn('participant_registration_number', function ($row) {
                return $row->peserta ? $row->peserta->registration_number : '-';
            })
            ->addColumn('participant_name', function ($row) {
                return $row->peserta ? $row->peserta->nama_peserta : '-';
            })
            ->addColumn('status', function ($row) {
                return $row->status == 1
                    ? '<i class="fa fa-check"></i> <small>Issued</small>'
                    : '<small>Pending</small>';
            })
            ->addColumn('actions', function ($row) {
                return '<button class="btn btn-outline-secondary btn-md mb-2 mr-2 edit-button"
                            data-id="' . $row->id . '"
                            data-participant-name="' . ($row->peserta ? $row->peserta->nama_peserta : '-') . '"
                            data-expire-date="' . $row->expire_date . '"
                            data-received-date="' . $row->date_received . '"
                            data-issued-date="' . $row->issued_date . '"
                            data-certificate-status="' . $row->status . '"
                            data-certificate-number="' . $row->certificate_number . '">
                            <i class="fa fa-edit"></i>
                        </button>
                        <a class="btn btn-outline-success btn-md mb-2 mr-2 generateQR" href="javascript:void(0)"
                            data-id="' . $row->id . '">
                            <i class="fa fa-qrcode"></i>
                        </a>';
            })
            ->editColumn('certificate_number', function ($row) {
                return $row->certificate_number ?? '-';
            })
            ->editColumn('date_received', function ($row) {
                return $row->date_received
                    ? \Carbon\Carbon::parse($row->date_received)->format('d-M-Y')
                    : '-';
            })
            ->editColumn('expire_date', function ($row) {
                return $row->expire_date
                    ? \Carbon\Carbon::parse($row->expire_date)->format('d-M-Y')
                    : '-';
            })
            ->editColumn('issued_date', function ($row) {
                return $row->issued_date
                    ? \Carbon\Carbon::parse($row->issued_date)->format('d-M-Y')
                    : '-';
            })
            ->rawColumns(['status', 'actions'])
            ->make(true);
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
            'attachment' => 'sometimes|file|mimes:pdf,docx,xlsx,xls,jpeg,png,jpg,gif',
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
            'attachments.*' => 'sometimes|file|mimes:pdf,docx,xlsx,xls,jpeg,png,jpg,gif', // Adjust the allowed file types and size
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
            // Retrieve all references associated with the given penlat_id
            $references = Training_reference::where('penlat_id', $penlat_id)->get();

            // Loop through each reference and delete the associated file if it exists
            foreach ($references as $reference) {
                $filePath = public_path($reference->filepath); // Full path to the file

                if (file_exists($filePath)) {
                    // Delete the file from public_path
                    unlink($filePath);
                }
            }

            // Delete all references associated with the given penlat_id
            Training_reference::where('penlat_id', $penlat_id)->delete();

            return response()->json(['message' => 'All references and their files have been deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete references or files. Please try again.'], 500);
        }
    }

    public function destroy_reference($id)
    {
        try {
            // Find the training reference by ID
            $trainingReference = Training_reference::findOrFail($id);

            // Check if the file exists and delete it
            $filePath = public_path($trainingReference->filepath); // Full path to the file

            if (file_exists($filePath)) {
                unlink($filePath); // Delete the file
            }

            // Delete the database record
            $trainingReference->delete();

            return response()->json(['success' => 'Item and associated file deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete the item or file. Please try again.'], 500);
        }
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
        $ids = $request->input('ids'); // Array of IDs to delete

        if (empty($ids)) {
            return response()->json(['status' => 'error', 'message' => 'No IDs provided for deletion.']);
        }

        Receivables_participant_certificate::whereIn('id', $ids)->delete();

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

    public function markCertificateAsExpire(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'formIds' => 'required', // Ensure formIds is provided
            'dateReceived' => 'required|date', // Ensure the date is valid
        ]);

        try {
            // Retrieve the formIds and date from the request
            $formIds = $request->input('formIds');
            $dateReceived = $request->input('dateReceived');

            // Convert formIds to an array if it's a string
            if (!is_array($formIds)) {
                $formIds = explode(',', $formIds); // Split by commas into an array
            }

            // Update the expire_date for the selected IDs
            Receivables_participant_certificate::whereIn('id', $formIds)
                ->update(['expire_date' => $dateReceived]);

            // Return a JSON response
            return response()->json(['success' => true, 'message' => 'Certificates marked as expired successfully.']);
        } catch (\Exception $e) {
            // Handle errors and return a JSON error response
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function markCertificateAsReceived(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'formIds' => 'required', // Ensure formIds is provided
            'dateReceived' => 'required|date', // Ensure the date is valid
        ]);

        try {
            // Retrieve the formIds and date from the request
            $formIds = $request->input('formIds');
            $dateReceived = $request->input('dateReceived');

            // Convert formIds to an array if it's a string
            if (!is_array($formIds)) {
                $formIds = explode(',', $formIds); // Split by commas into an array
            }

            // Update the date_received column for the selected IDs
            Receivables_participant_certificate::whereIn('id', $formIds)
                ->update(['date_received' => $dateReceived]);

            // Return a JSON response
            return response()->json(['success' => true, 'message' => 'Certificates marked as received successfully.']);
        } catch (\Exception $e) {
            // Handle errors and return a JSON error response
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateCertificate(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'receivedDate' => 'sometimes',
            'expireDate' => 'sometimes',
            'certificateStatus' => 'sometimes',
            'issuedDate' => 'sometimes',
            'certificateNumber' => 'required',
        ]);

        try {
            // Find the record by ID
            $certificate = Receivables_participant_certificate::findOrFail($id);

            // Update the record
            $certificate->update([
                'date_received' => $request->receivedDate,
                'expire_date' => $request->expireDate,
                'certificate_number' => $request->certificateNumber,
                'status' => $request->certificateStatus,
                'issued_date' => $request->issuedDate,
            ]);

            // Check the related Penlat_certificate and update its status
            $penlatCertificate = Penlat_certificate::find($certificate->penlat_certificate_id); // Assuming relationship exists

            if ($penlatCertificate) {
                // Retrieve all participants' statuses
                $participantStatuses = $penlatCertificate->participants;

                // Check if all statuses are true
                $allIssued = true;
                foreach ($participantStatuses as $participant) {
                    if (!$participant->status) {
                        $allIssued = false;
                        break; // Exit loop early if any status is false
                    }
                }

                // Update the Penlat_certificate status if all are issued
                if ($allIssued) {
                    $penlatCertificate->update(['status' => 'Issued']);
                } else {
                    $penlatCertificate->update(['status' => 'On Process']);
                }
            }

            // Return a JSON response
            return response()->json(['success' => true, 'message' => 'Certificate data updated successfully.']);
        } catch (\Exception $e) {
            // Handle errors and return a JSON error response
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function validate_certificate($encryptedId)
    {
        try {
            // Trim and validate the input to ensure it's numeric
            $id = trim($encryptedId);

            if (!is_numeric($id)) {
                throw new \Exception("Invalid ID format.");
            }

            // Fetch data with relationships, ensuring the ID is numeric and sanitized
            $data = Receivables_participant_certificate::with(['peserta', 'penlatCertificate'])->findOrFail($id);

            return view('plan_dev.submenu.validate-certificate', ['data' => $data]);
        } catch (\Exception $e) {
            // Handle errors (e.g., invalid input or asset not found)
            return redirect()->route('certificate')->withErrors(['error' => 'Invalid QR code or certificate not found.']);
        }
    }

    public function generateQrCode($id)
    {
        $item = Receivables_participant_certificate::findOrFail($id);

        // Encrypt the asset ID
        $encryptedId = $item->id;

        // Generate the QR code for the validate-asset route with the encrypted ID
        $qrCodeData = QrCode::format('png')
            ->size(200)
            ->merge('/storage/app/MTC.png', 0.3) // Merge with a 30% size of the QR code
            ->errorCorrection('M') // Use high error correction level
            ->generate(route('validate-certificate', $encryptedId));

        // Encode the QR code as base64
        $base64QrCode = base64_encode($qrCodeData);

        return response()->json([
            'nama_peserta' => $item->peserta->nama_peserta,
            'link' => route('validate-certificate', $encryptedId),
            'qr_code' => 'data:image/png;base64,' . $base64QrCode
        ]);
    }

    public function export_selected(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'formIds' => 'required', // Ensure formIds is provided
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed. Ensure all required fields are filled.']);
        }
        $formIds = explode(',', $request->input('formIds'));

        // Create a unique name for the zip file
        $firstRecord = $formIds[0];
        $getPenlatDetail = Receivables_participant_certificate::find($firstRecord);

        $explodeBatch = explode('/', $getPenlatDetail->penlatCertificate->batch->batch);

        $zipFileName = 'certificates_' . str_replace('/', '_', $explodeBatch[0]) . '_' . $getPenlatDetail->penlatCertificate->batch->date . '.zip';
        $zipFilePath = public_path('uploads/certificates/' . $zipFileName);

        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($formIds as $formId) {
                $data = Receivables_participant_certificate::find($formId);

                // Get the description from related models
                $certificateRegulationName = $data->penlatCertificate->regulation->description;
                $certificateAmendmentName = $data->penlatCertificate->regulation_amendments->description;
                // Determine the appropriate font size based on text length
                $textLength = mb_strlen($certificateAmendmentName); // Use mb_strlen for multibyte safety

                $templateBase = $textLength > 20 ? 'template_certificate' : 'template_certificate_2';
                $placeholderSuffix = ($data->penlatCertificate->photo_placeholder == 1) ? '_placeholdered' : '';

                $templatePath = public_path("uploads/template/{$templateBase}{$placeholderSuffix}.xlsx");

                $spreadsheet = IOFactory::load($templatePath);
                $sheet = $spreadsheet->getActiveSheet();

                // Modify the spreadsheet
                $certificateNumber = $data->certificate_number;
                $batchParts = explode('/', $data->penlatCertificate->batch->batch);

                $formattedCertificate = sprintf(
                    '%s / %s / PMTC / %s / %s',
                    $certificateNumber,
                    $batchParts[0],
                    $batchParts[2],
                    $batchParts[3]
                );
                $sheet->setCellValue('B41', $batchParts[1]);

                $birthInfo = $data->peserta->birth_place . ', ' . Carbon::parse($data->peserta->birth_date)->format('d F Y');
                $sheet->setCellValueByColumnAndRow(16, 14, $birthInfo);
                $sheet->setCellValueByColumnAndRow(35, 9, $formattedCertificate);

                // Get the description
                $description = $data->penlatCertificate->certificate_title;

                // Define breakpoints for font sizes
                $fontSizes = [
                    43 => 28, // Up to 60 characters, font size 24
                    48 => 26, // Up to 60 characters, font size 24
                    49 => 24, // Up to 80 characters, font size 20
                    100 => 20, // Up to 100 characters, font size 16
                ];

                // Default font size for very long text
                $defaultFontSize = 28;

                // Determine the appropriate font size based on text length
                $fontSize = $defaultFontSize;
                foreach ($fontSizes as $charLimit => $size) {
                    if (strlen($description) <= $charLimit) {
                        $fontSize = $size;
                        break;
                    }
                }

                // Set the cell value
                $cellCoordinates = 'F19';
                $sheet->setCellValue($cellCoordinates, $description);

                // Apply the calculated font size
                $sheet->getStyle($cellCoordinates)->getFont()->setSize($fontSize);

                // Align text to wrap within the cell
                $sheet->getStyle($cellCoordinates)->getAlignment()->setWrapText(true);
                $sheet->getStyle($cellCoordinates)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($cellCoordinates)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                //Nama
                $participantName = $data->peserta->nama_peserta;

                // Define breakpoints for font sizes
                $fontSizeBreakpoints = [
                    30 => 22, // Up to 18 characters, font size 22
                    50 => 16, // Up to 28 characters, font size 16
                ];

                // Default font size for very long text
                $defaultFontSizeValue = 22;

                // Determine the appropriate font size based on text length
                $calculatedFontSize = $defaultFontSizeValue;
                foreach ($fontSizeBreakpoints as $characterLimit => $fontSizeValue) {
                    if (strlen($participantName) <= $characterLimit) {
                        $calculatedFontSize = $fontSizeValue;
                        break;
                    }
                }

                // Set the cell value
                $targetCell = 'P11';
                $sheet->setCellValue($targetCell, $participantName);

                // Apply the calculated font size
                $sheet->getStyle($targetCell)->getFont()->setSize($calculatedFontSize);

                // Align text to wrap within the cell
                $sheet->getStyle($targetCell)->getAlignment()->setWrapText(true);
                $sheet->getStyle($targetCell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle($targetCell)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);


                //periode
                $sheet->setCellValueByColumnAndRow(16, 23, Carbon::parse($data->penlatCertificate->start_date)->format('d F Y'));
                $sheet->setCellValueByColumnAndRow(25, 23, Carbon::parse($data->penlatCertificate->end_date)->format('d F Y'));

                //regulator
                $sheet->setCellValue('F26', $data->penlatCertificate->regulation_amendments->description ?? ' ');
                $sheet->setCellValue('F27', $data->penlatCertificate->regulation_amendments->translation ?? ' ');

                if(!$data->penlatCertificate->regulation_amendments->description || !$data->penlatCertificate->regulation->description){
                    $sheet->setCellValue('X26', ' ');
                    $sheet->setCellValue('O26', ' ');
                }

                if ($textLength > 20) {
                    if (strlen($certificateRegulationName) > 100) {
                        $fontSize = 9;
                    } else {
                        $fontSize = 14;
                    }

                    $excelCell = 'Y26';
                    $sheet->setCellValue($excelCell, $certificateRegulationName);
                    $sheet->getStyle($excelCell)->getFont()->setSize($fontSize);
                } else {
                    $excelCell = 'P26';
                    $sheet->setCellValue($excelCell, $certificateRegulationName);
                }

                // Generate the QR code
                $encryptedId = $data->id;
                $qrCodeData = QrCode::format('png')
                    ->size(200)
                    ->merge('/storage/app/MTC.png', 0.3) // Merge with a 30% size of the QR code
                    ->errorCorrection('M') // Use high error correction level
                    ->generate(route('validate-certificate', $encryptedId));

                // Embed the QR Code in the template
                $drawing = new MemoryDrawing();
                $drawing->setName('QR Code');
                $drawing->setDescription('QR Code');
                $drawing->setImageResource(imagecreatefromstring($qrCodeData)); // Load QR as an image resource
                $drawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
                $drawing->setMimeType(MemoryDrawing::MIMETYPE_PNG);
                $drawing->setHeight(100); // Set image height
                $drawing->setCoordinates('AO31'); // Specify the cell for placement
                $drawing->setWorksheet($sheet); // Attach to the worksheet

                // Set the date issued in the Excel sheet
                $sheet->setCellValueByColumnAndRow(27, 29, 'Jakarta, ' . Carbon::parse($data->issued_date)->format('d F Y'));

                // Generate a unique filename for the Excel file
                $namaPesertaFormatted = str_replace(' ', '_', strtolower($data->peserta->nama_peserta));
                $excelFileName = 'certificates_' . $namaPesertaFormatted . '_' . $certificateNumber . '.xlsx';

                $excelFilePath = public_path('uploads/certificates/' . $excelFileName);

                // Save the spreadsheet to a temporary file
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save($excelFilePath);

                // Add the Excel file to the zip archive
                $zip->addFile($excelFilePath, $excelFileName);
            }

            // Close the zip archive
            $zip->close();

            // Return the path to the ZIP file as a response
            return response()->json(['success' => true, 'fileUrl' => asset('uploads/certificates/' . $zipFileName)]);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to create zip file']);
        }
    }

    public function markAsIssued(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'formIds' => 'required', // Ensure formIds is provided
            'dateReceived' => 'sometimes', // Ensure the date is valid
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Ensure all required fields are filled.',
            ]);
        }

        $formIds = explode(',', $request->input('formIds'));

        try {
            foreach ($formIds as $formId) {
                $data = Receivables_participant_certificate::find($formId);

                if ($data) {
                    // Update status and optionally the issued date
                    $data->update([
                        'status' => 1,
                        'issued_date' => $request->input('dateReceived') ?: $data->issued_date,
                    ]);
                }
            }


            // Check the related Penlat_certificate and update its status
            $penlatCertificate = Penlat_certificate::find($data->penlat_certificate_id); // Assuming relationship exists

            if ($penlatCertificate) {
                // Retrieve all participants' statuses
                $participantStatuses = $penlatCertificate->participants;

                // Check if all statuses are true
                $allIssued = true;
                foreach ($participantStatuses as $participant) {
                    if (!$participant->status) {
                        $allIssued = false;
                        break; // Exit loop early if any status is false
                    }
                }

                // Update the Penlat_certificate status if all are issued
                if ($allIssued) {
                    $penlatCertificate->update(['status' => 'Issued']);
                } else {
                    $penlatCertificate->update(['status' => 'On Process']);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Status has been updated successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the status: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function fetchRegulators(Request $request)
    {
        $search = $request->input('q');
        $page = $request->input('page', 1);
        $perPage = 10;

        $query = Regulator::query();

        if ($search) {
            $query->where('description', 'like', '%' . $search . '%');
        }

        $regulators = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'items' => $regulators->map(function ($regulator) {
                return [
                    'id' => $regulator->id,
                    'description' => $regulator->description,
                ];
            }),
            'total_count' => $regulators->total(),
        ]);
    }

    public function storeRegulator(Request $request)
    {
        $request->validate([
            'description' => 'required',
        ]);

        $regulator = Regulator::create([
            'description' => $request->description,
        ]);

        return response()->json([
            'id' => $regulator->id,
            'description' => $regulator->description,
        ]);
    }

    public function refreshParticipants(Request $request)
    {
        $validated = $request->validate([
            'batch' => 'required',
            'penlatCertificateId' => 'required'
        ]);

        $currentTimestamp = now();
        $isInternal = true;

        // Fetch participants ordered by nama_peserta
        $participants = Infografis_peserta::where('batch', $validated['batch'])
            ->orderBy('nama_peserta', 'asc')
            ->pluck('id') // Get only IDs
            ->toArray();

        $penlatBatch = Penlat_batch::where('batch', $validated['batch'])->first();

        if (trim($penlatBatch->penlat->kategori_pelatihan) == trim('NON STCW')) {
            $isInternal = false;
        }

        // Fetch existing receivables
        $getReceivables = Receivables_participant_certificate::where('penlat_certificate_id', $request->penlatCertificateId)
            ->orderBy('id', 'asc') // Maintain order
            ->get();

        $participantIndex = 0; // Track the participant index

        if ($getReceivables->isEmpty()) {
            // If there are no receivables, create for all participants
            foreach ($participants as $participant) {
                Receivables_participant_certificate::create([
                    'infografis_peserta_id' => $participant,
                    'penlat_certificate_id' => $request->penlatCertificateId,
                    'isInternal' => $isInternal,
                    'updated_at' => $currentTimestamp,
                ]);
            }
        } else {
            // Assign existing receivables first
            foreach ($getReceivables as $receivable) {
                if (isset($participants[$participantIndex])) {
                    $receivable->update([
                        'infografis_peserta_id' => $participants[$participantIndex],
                        'isInternal' => $isInternal,
                        'updated_at' => $currentTimestamp,
                    ]);
                    $participantIndex++; // Move to the next participant
                }
            }

            // If there are more participants than receivables, create new ones
            while ($participantIndex < count($participants)) {
                Receivables_participant_certificate::create([
                    'infografis_peserta_id' => $participants[$participantIndex],
                    'penlat_certificate_id' => $request->penlatCertificateId,
                    'isInternal' => $isInternal,
                    'updated_at' => $currentTimestamp,
                ]);
                $participantIndex++;
            }
        }

        return response()->json([
            'message' => 'Participants refreshed successfully!',
        ]);
    }

    public function generateExcelWithQrCode($id)
    {
        $data = Receivables_participant_certificate::find($id);
        // Path to the template file
        $templatePath = public_path('uploads/template/template_certificate.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Generate the QR code
        $encryptedId = Crypt::encryptString($data->id);
        $qrCodeData = QrCode::format('png')
            ->size(200)
            ->merge('/storage/app/MTC.jpeg', 0.3) // Merge with a 30% size of the QR code
            ->errorCorrection('H') // Use high error correction level
            ->generate(route('validate-certificate', $encryptedId));

        // Embed the QR Code in the template
        $drawing = new MemoryDrawing();
        $drawing->setName('QR Code');
        $drawing->setDescription('QR Code');
        $drawing->setImageResource(imagecreatefromstring($qrCodeData)); // Load QR as an image resource
        $drawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
        $drawing->setMimeType(MemoryDrawing::MIMETYPE_PNG);
        $drawing->setHeight(100); // Set image height
        $drawing->setOffsetY(15); // Adjust the vertical offset as necessary (e.g., 5 points down)
        $drawing->setCoordinates('AO31'); // Specify the cell for placement
        $drawing->setWorksheet($sheet); // Attach to the worksheet

        // Save the modified file
        $outputPath = public_path('uploads/certificates/certificate_with_qr_' . $data->id . '.xlsx');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($outputPath);

        // Return the file for download
        return response()->download($outputPath);
    }

    public function getNumberCertificate($id)
    {
        date_default_timezone_set("Asia/Jakarta");
        $year = date('Y');
        $nextID = '1'; // Default next ID if no existing ID is found
        $existingID = null;

        try {
            $getStatus = Receivables_participant_certificate::find($id);

            if (!$getStatus) {
                // Handle the case where the record is not found
                return response()->json(['error' => 'Record not found'], 404);
            }

            // Get the related penlat_id
            $getPenlatId = $getStatus->penlatCertificate->batch->penlat->id;

            // Query to get matching records
            $query = Receivables_participant_certificate::whereYear('created_at', $year)
                ->whereHas('penlatCertificate.batch.penlat', function ($q) use ($getPenlatId) {
                    $q->where('id', $getPenlatId);
                });

            $existingID = $query->orderBy('certificate_number', 'desc')->lockForUpdate()->pluck('certificate_number')->first();

            if ($existingID !== null) {
                $nextID = sprintf('%05d', $existingID + 1);
            }
        } catch (\Exception $e) {
            // Log the error or handle the exception as needed
            // Log::error('Error fetching document number: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching the next ID'], 500);
        }

        return response()->json(['nextID' => $nextID, 'existingID' => $existingID]);
    }

    private function getNumberCerticates($getPenlatId)
    {
        date_default_timezone_set("Asia/Jakarta");
        $year = date('Y');
        $nextID = '1'; // Default next ID if no existing ID is found
        $existingID = null;

        try {
            // Query to get matching records
            $query = Receivables_participant_certificate::whereYear('created_at', $year)
                ->whereHas('penlatCertificate.batch.penlat', function ($q) use ($getPenlatId) {
                    $q->where('id', $getPenlatId);
                });

            $existingID = $query->orderBy('certificate_number', 'desc')->lockForUpdate()->pluck('certificate_number')->first();

            if ($existingID !== null) {
                $nextID = sprintf('%05d', $existingID + 1);
            }
        } catch (\Exception $e) {
            // Log the error or handle the exception as needed
            // Log::error('Error fetching document number: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching the next ID'], 500);
        }

        return $nextID;
    }
}
