<?php

namespace App\Http\Controllers;

use App\Models\Infografis_peserta;
use App\Models\Penlat;
use App\Models\Penlat_batch;
use App\Models\Penlat_certificate;
use App\Models\Receivables_participant_certificate;
use App\Models\Regulation;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PDController extends Controller
{
    public function index()
    {
        return view('plan_dev.index');
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

    public function certificate()
    {
        $penlatList = Penlat::all();
        //filtering list
        $listBatch = Penlat_batch::all();

        $data = Penlat_certificate::all();

        return view('plan_dev.submenu.certificate', ['data' => $data, 'penlatList' => $penlatList, 'listBatch' => $listBatch]);
    }

    public function instructor()
    {
        return view('plan_dev.submenu.instructor');
    }

    public function training_reference()
    {
        return view('plan_dev.submenu.training-reference');
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
                'penlat_id' => $validated['penlat'],
                'batch' => $validated['batch'],
            ],
            [
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
}
