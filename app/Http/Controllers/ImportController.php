<?php

namespace App\Http\Controllers;

use App\Jobs\ImportFeedback;
use App\Jobs\ImportParticipantInfographics;
use App\Jobs\ImportPenlat;
use App\Jobs\ImportVendorPayment;
use App\Jobs\ProfitsImport;
use App\Models\Infografis_peserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        // Validate and upload the file as before
        $validator = Validator::make($request->all(), [
            'file' => 'required|file',
        ]);

        if ($validator->fails()) {
            Session::flash('failed', "Error: Invalid file type.");
            return redirect('/operation/participant-infographics/import-page');
        }

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $destinationPath = public_path('uploads');
        $file->move($destinationPath, $filename);

        $filePath = $destinationPath . '/' . $filename;

        try {
            // Dispatch the job
            ImportParticipantInfographics::dispatch($filePath);

            return redirect()->back()->with('success', 'Data import started successfully, please wait until the data is all processed.');
        } catch (\Exception $e) {
            Session::flash('failed', 'Error dispatching the job: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function import_profits(Request $request)
    {
        // Validate and upload the file
        $validator = Validator::make($request->all(), [
            'file' => 'required',
        ]);

        if ($validator->fails()) {
            Session::flash('failed', "Error: Invalid file type.");
            return redirect('/dashboard');
        }

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $destinationPath = public_path('uploads');
        $file->move($destinationPath, $filename);

        $filePath = $destinationPath . '/' . $filename;

        try {
            // Dispatch the job
            ProfitsImport::dispatch($filePath);

            return redirect()->back()->with('success', 'Data import started successfully, please wait until the data is all processed.');
        } catch (\Exception $e) {
            Session::flash('failed', 'Error dispatching the job: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function import_penlat(Request $request)
    {
        // Validate and upload the file
        $validator = Validator::make($request->all(), [
            'file' => 'required',
        ]);

        if ($validator->fails()) {
            Session::flash('failed', "Error: Invalid file type.");
            return redirect()->route('penlat');
        }

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $destinationPath = public_path('uploads');
        $file->move($destinationPath, $filename);

        $filePath = $destinationPath . '/' . $filename;

        try {
            // Dispatch the job
            ImportPenlat::dispatch($filePath);

            return redirect()->back()->with('success', 'Data import started successfully, please wait until the data is all processed.');
        } catch (\Exception $e) {
            Session::flash('failed', 'Error dispatching the job: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function import_feedback(Request $request)
    {
        // Validate and upload the file
        $validator = Validator::make($request->all(), [
            'file' => 'required',
        ]);

        if ($validator->fails()) {
            Session::flash('failed', "Error: Invalid file type.");
            return redirect()->route('penlat');
        }

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $destinationPath = public_path('uploads');
        $file->move($destinationPath, $filename);

        $filePath = $destinationPath . '/' . $filename;

        try {
            // Dispatch the job
            ImportFeedback::dispatch($filePath);

            return redirect()->back()->with('success', 'Data import started successfully, please wait until the data is all processed.');
        } catch (\Exception $e) {
            Session::flash('failed', 'Error dispatching the job: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function import_vendor_payment(Request $request)
    {
        // Validate and upload the file
        $validator = Validator::make($request->all(), [
            'file' => 'required',
        ]);

        if ($validator->fails()) {
            Session::flash('failed', "Error: Invalid file type.");
            return redirect()->route('penlat');
        }

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $destinationPath = public_path('uploads');
        $file->move($destinationPath, $filename);

        $filePath = $destinationPath . '/' . $filename;

        try {
            // Dispatch the job
            ImportVendorPayment::dispatch($filePath);

            return redirect()->back()->with('success', 'Data import started successfully, please wait until the data is all processed.');
        } catch (\Exception $e) {
            Session::flash('failed', 'Error dispatching the job: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
