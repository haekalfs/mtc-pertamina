<?php

namespace App\Http\Controllers;

use App\Models\Certificates_catalog;
use App\Models\Certificates_to_penlat;
use App\Models\Department;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PDController extends Controller
{
    public function index()
    {
        $query = Instructor::query();
        $instructorCount = $query->count();
        $getData = Training_reference::groupBy('penlat_id')->pluck('penlat_id')->toArray();
        $referencesCount = Penlat::whereIn('id', $getData)->count();
        $instructors = $query->limit(5)->get();

        return view('plan_dev.index', ['instructorCount' => $instructorCount, 'referencesCount' => $referencesCount, 'instructors' => $instructors]);
    }

    public function feedback_report()
    {
        return view('plan_dev.submenu.feedback');
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

    public function main_certificate()
    {
        $penlatList = Penlat::all();
        //filtering list
        $listBatch = Penlat_batch::all();

        $data = Penlat_certificate::all();
        $instructorData = Certificates_catalog::all();

        return view('plan_dev.certification-main', ['data' => $data, 'penlatList' => $penlatList, 'listBatch' => $listBatch, 'instructorData' => $instructorData]);
    }

    public function certificate()
    {
        $penlatList = Penlat::all();
        //filtering list
        $listBatch = Penlat_batch::all();

        $data = Penlat_certificate::all();

        return view('plan_dev.submenu.certificate', ['data' => $data, 'penlatList' => $penlatList, 'listBatch' => $listBatch]);
    }

    public function certificate_instructor()
    {
        $penlatList = Penlat::all();
        //filtering list
        $listBatch = Penlat_batch::all();

        $data = Certificates_catalog::all();

        return view('plan_dev.submenu.instructor-certificate', ['data' => $data, 'penlatList' => $penlatList, 'listBatch' => $listBatch]);
    }

    public function instructor(Request $request, $penlatId = 0)
    {
        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            'penlat' => 'sometimes'
        ]);

        $query = Instructor::query();

        // Apply filters based on the request
        if($request->penlat != 0){
            $penlatId = $request->penlat;
            //search data
            $getData = Certificates_to_penlat::where('penlat_id', $penlatId)->pluck('certificates_catalog_id')->toArray();
            $validateData = Instructor_certificate::whereIn('certificates_catalog_id', $getData)->pluck('instructor_id')->toArray();
            $query->whereIn('id', $validateData);
        }

        $data = $query->get();
        $penlatList = Penlat::all();
        return view('plan_dev.submenu.instructor', ['data' => $data, 'penlatList' => $penlatList, 'penlatId' => $penlatId]);
    }

    public function training_reference()
    {
        $penlatList = Penlat::all();
        $getData = Training_reference::groupBy('penlat_id')->pluck('penlat_id')->toArray();

        $data = Penlat::whereIn('id', $getData)->get();

        return view('plan_dev.submenu.training-reference', ['penlatList' => $penlatList, 'data' => $data]);
    }

    public function upload_certificate()
    {
        return view('plan_dev.submenu.upload_certificate');
    }

    public function regulation_store(Request $request)
    {
        // Validate the input
        $request->validate([
            'regulation_name' => 'required',
            'status' => 'required',
            'file' => 'required|mimes:pdf,docx,doc'
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

            // Get the file size before moving the file
            $fileSize = $file->getSize();

            $file->move(public_path('uploads/regulation'), $filename);
            $regulation->filepath = 'uploads/regulation/' . $filename;
            $regulation->filesize = $fileSize;
        }

        // Save to the database
        $regulation->save();

        // Redirect with a success message
        return redirect()->back()->with('success', 'Regulation created successfully.');
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

        // Create or update the Penlat_batch entry
        $penlatBatch = Penlat_batch::updateOrCreate(
            [
                'batch' => $validated['batch'],
            ],
            [
                'penlat_id' => $validated['penlat'],
                'nama_program' => $validated['program'],
                'updated_at' => $currentTimestamp,
            ]
        );

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

        // Redirect or return a response with a success message
        return redirect()->back()->with('success', 'Certificate data stored successfully.');
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

        if($penlatId != 0){
            $getData = Certificates_to_penlat::where('penlat_id', $penlatId)->pluck('certificates_catalog_id')->toArray();
        } else {
            $getData = Instructor_certificate::where('instructor_id', $id)->pluck('certificates_catalog_id')->toArray();
        }

        $certificateData = Instructor_certificate::where('instructor_id', $id)
            ->whereIn('certificates_catalog_id', $getData)
            ->with('catalog.relationOne.penlat')
            ->get();

        $allCerts = Instructor_certificate::where('instructor_id', $id)->whereNotIn('certificates_catalog_id', $getData)->get();

        return view('plan_dev.submenu.preview-instructor', ['data' => $data, 'certificateData' => $certificateData, 'remainingCerts' => $allCerts]);
    }

    public function references_store(Request $request)
    {
        // Validate the form data
        $request->validate([
            'penlat' => 'required',
            'documents.*' => 'required',
            'attachments.*' => 'sometimes|file', // Adjust the allowed file types and size
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
}
