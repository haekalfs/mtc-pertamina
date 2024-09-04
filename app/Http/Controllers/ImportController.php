<?php

namespace App\Http\Controllers;

use App\Jobs\ConvertXlsxToCsv;
use App\Jobs\ConvertXlsxToCsvInfografis;
use App\Jobs\ImportFeedback;
use App\Jobs\ImportFeedbackMTC;
use App\Jobs\ImportParticipantInfographics;
use App\Jobs\ImportPenlat;
use App\Jobs\ImportVendorPayment;
use App\Jobs\ProfitsImport;
use App\Models\Infografis_peserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        // Validate and upload the file
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx|max:40960', // Accept only XLSX files and limit size to 50 MB (40960 KB)
        ], [
            'file.required' => 'Error: The file is required.',
            'file.file' => 'Error: The uploaded file must be a valid file.',
            'file.mimes' => 'Error: The file must be an XLSX file.',
            'file.max' => 'Error: The file size must be less than 50 MB.',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all(); // Get all error messages
            Session::flash('failed', implode(' ', $messages)); // Combine them into a single string
            return redirect()->route('participant-infographics-import-page')->withInput();
        }

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $destinationPath = public_path('uploads');
        $file->move($destinationPath, $filename);

        $filePath = $destinationPath . '/' . $filename;

        try {
            // Dispatch the job
            ConvertXlsxToCsvInfografis::dispatch($filePath);

            // Set a cache indicating the job is processing, if it doesn't already exist
            if (!Cache::has('jobs_processing')) {
                Cache::put('jobs_processing', true, now()->addMinutes(5)); // Cache for 10 minutes
            }

            return redirect()->back()->with('success', 'Data import started successfully, please wait until the data is all processed.');
        } catch (\Exception $e) {
            Session::flash('failed', 'Error dispatching the job: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function import_profits(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx|max:40960', // Accept only XLSX files and limit size to 50 MB (40960 KB)
        ], [
            'file.required' => 'Error: The file is required.',
            'file.file' => 'Error: The uploaded file must be a valid file.',
            'file.mimes' => 'Error: The file must be an XLSX file.',
            'file.max' => 'Error: The file size must be less than 50 MB.',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all(); // Get all error messages
            Session::flash('failed', implode(' ', $messages)); // Combine them into a single string
            return redirect()->route('costs.import')->withInput();
        }

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $destinationPath = public_path('uploads');
        $file->move($destinationPath, $filename);

        $filePath = $destinationPath . '/' . $filename;

        try {
            // Dispatch the job
            ProfitsImport::dispatch($filePath);
            // Set a cache indicating the job is processing, if it doesn't already exist
            if (!Cache::has('jobs_processing')) {
                Cache::put('jobs_processing', true, now()->addMinutes(5)); // Cache for 10 minutes
            }

            return redirect()->back()->with('success', 'Data import started successfully, please wait until the data is all processed.');
        } catch (\Exception $e) {
            Session::flash('failed', 'Error dispatching the job: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function import_penlat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx|max:40960',
        ], [
            'file.required' => 'Error: The file is required.',
            'file.file' => 'Error: The uploaded file must be a valid file.',
            'file.mimes' => 'Error: The file must be an XLSX file.',
            'file.max' => 'Error: The file size must be less than 50 MB.',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            Session::flash('failed', implode(' ', $messages));
            return redirect()->route('penlat-import')->withInput();
        }

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $destinationPath = public_path('uploads');
        $file->move($destinationPath, $filename);

        $filePath = $destinationPath . '/' . $filename;

        try {
            // Dispatch the ConvertXlsxToCsv job
            ConvertXlsxToCsv::dispatch($filePath);
            if (!Cache::has('jobs_processing')) {
                Cache::put('jobs_processing', true, now()->addMinutes(5));
            }

            return redirect()->back()->with('success', 'Data conversion started successfully, please wait until the data is processed.');
        } catch (\Exception $e) {
            Session::flash('failed', 'Error dispatching the job: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function import_feedback(Request $request)
    {
        // Validate and upload the file
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx|max:40960', // Accept only XLSX files and limit size to 50 MB (40960 KB)
        ], [
            'file.required' => 'Error: The file is required.',
            'file.file' => 'Error: The uploaded file must be a valid file.',
            'file.mimes' => 'Error: The file must be an XLSX file.',
            'file.max' => 'Error: The file size must be less than 50 MB.',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all(); // Get all error messages
            Session::flash('failed', implode(' ', $messages)); // Combine them into a single string
            return redirect()->route('feedback-report-import-page')->withInput();
        }

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $destinationPath = public_path('uploads');
        $file->move($destinationPath, $filename);

        $filePath = $destinationPath . '/' . $filename;

        try {
            // Dispatch the job
            ImportFeedback::dispatch($filePath);
            // Set a cache indicating the job is processing, if it doesn't already exist
            if (!Cache::has('jobs_processing')) {
                Cache::put('jobs_processing', true, now()->addMinutes(5)); // Cache for 10 minutes
            }

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
            'file' => 'required|file|mimes:xlsx|max:40960', // Accept only XLSX files and limit size to 50 MB (40960 KB)
        ], [
            'file.required' => 'Error: The file is required.',
            'file.file' => 'Error: The uploaded file must be a valid file.',
            'file.mimes' => 'Error: The file must be an XLSX file.',
            'file.max' => 'Error: The file size must be less than 50 MB.',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all(); // Get all error messages
            Session::flash('failed', implode(' ', $messages)); // Combine them into a single string
            return redirect()->route('vendor-payment-importer')->withInput();
        }

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $destinationPath = public_path('uploads');
        $file->move($destinationPath, $filename);

        $filePath = $destinationPath . '/' . $filename;

        try {
            // Dispatch the job
            ImportVendorPayment::dispatch($filePath);
            // Set a cache indicating the job is processing, if it doesn't already exist
            if (!Cache::has('jobs_processing')) {
                Cache::put('jobs_processing', true, now()->addMinutes(5)); // Cache for 10 minutes
            }

            return redirect()->back()->with('success', 'Data import started successfully, please wait until the data is all processed.');
        } catch (\Exception $e) {
            Session::flash('failed', 'Error dispatching the job: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function import_feedback_mtc(Request $request)
    {
        // Validate and upload the file
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx|max:40960', // Accept only XLSX files and limit size to 50 MB (40960 KB)
        ], [
            'file.required' => 'Error: The file is required.',
            'file.file' => 'Error: The uploaded file must be a valid file.',
            'file.mimes' => 'Error: The file must be an XLSX file.',
            'file.max' => 'Error: The file size must be less than 50 MB.',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all(); // Get all error messages
            Session::flash('failed', implode(' ', $messages)); // Combine them into a single string
            return redirect()->route('feedback-mtc-import-page')->withInput();
        }

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $destinationPath = public_path('uploads');
        $file->move($destinationPath, $filename);

        $filePath = $destinationPath . '/' . $filename;

        try {
            // Dispatch the job
            ImportFeedbackMTC::dispatch($filePath);
            // Set a cache indicating the job is processing, if it doesn't already exist
            if (!Cache::has('jobs_processing')) {
                Cache::put('jobs_processing', true, now()->addMinutes(5)); // Cache for 10 minutes
            }

            return redirect()->back()->with('success', 'Data import started successfully, please wait until the data is all processed.');
        } catch (\Exception $e) {
            Session::flash('failed', 'Error dispatching the job: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
