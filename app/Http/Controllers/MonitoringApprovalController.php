<?php

namespace App\Http\Controllers;

use App\Models\Monitoring_approval;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringApprovalController extends Controller
{
    public function index()
    {
        $data = Monitoring_approval::all();
        return view('plan_dev.submenu.monitoring_approval', ['data' => $data]);
    }

    public function store(Request $request)
    {
        // Validate the input
        $request->validate([
            'document_name' => 'required',
            'approved_date' => 'required',
            'type' => 'required',
            'file' => 'required|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,odt,ods,odp,rtf'
        ]);

        // Create new document
        $document = new Monitoring_approval();
        $document->description = $request->input('document_name');
        $document->type = $request->input('type');
        $document->approval_date = $request->input('approved_date');
        $document->user_id = Auth::id();

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
            $file->move(public_path('uploads/monitoring_approval'), $filename);

            // Save the file path and size in KB or MB format with two decimal places
            $document->filepath = 'uploads/monitoring_approval/' . $filename;
            $document->filesize = number_format($fileSizeInKb, 2); // Save in KB
            // $document->filesize = number_format($fileSizeInMb, 2); // Save in MB
        }

        // Save to the database
        $document->save();

        // Redirect with a success message
        return redirect()->back()->with('success', 'document created successfully.');
    }

    public function preview($itemId)
    {
        $data = Monitoring_approval::find($itemId);

        // Initialize the variables for file and approval status
        $fileExists = false;
        $isPdf = false;
        $filePath = null;
        $statusBadge = null;

        if ($data) {
            // Check if the file exists and determine if it's a PDF
            if (file_exists(public_path($data->filepath))) {
                $fileExists = true;
                $filePath = public_path($data->filepath);
                $isPdf = pathinfo($filePath, PATHINFO_EXTENSION) === 'pdf';
            }

            // Parse the approval_date and calculate the next year's anniversary
            $approvalDate = Carbon::parse($data->approval_date);
            $nextApprovalDate = $approvalDate->copy()->addYear();
            $daysLeft = Carbon::now()->diffInDays($nextApprovalDate, false); // false to count negatives for past dates

            // Set the status badge or approval date
            if ($daysLeft <= 30 && $daysLeft > 0) {
                $statusBadge = "{$daysLeft} Days Left"; // Badge when less than 30 days left
            } elseif ($daysLeft <= 0) {
                $statusBadge = "0 Days Left"; // Show 0 if past the anniversary date
            } else {
                $statusBadge = null; // No badge if more than 30 days
            }
        }

        // Pass the status and other variables to the view
        return view('plan_dev.submenu.preview-monitoring-approval', compact('data', 'fileExists', 'isPdf', 'filePath', 'statusBadge'));
    }

    public function delete($id)
    {
        $document = Monitoring_approval::find($id);

        if ($document) {
            // Delete the file from storage if it exists
            if (file_exists(public_path($document->filepath))) {
                unlink(public_path($document->filepath));
            }

            // Delete the record from the database
            $document->delete();

            return response()->json(['success' => 'Regulation deleted successfully.']);
        }

        return response()->json(['error' => 'Regulation not found.'], 404);
    }

    public function update(Request $request)
    {
        $request->validate([
            'document_id' => 'required',
            'document_name' => 'required',
            'type' => 'required',
            'file' => 'nullable|mimes:pdf,docx,doc'
        ]);

        $document = Monitoring_approval::find($request->input('document_id'));
        $document->description = $request->input('document_name');
        $document->type = $request->input('type');
        $document->approval_date = $request->input('approved_date');

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Optionally delete the old file if you want
            if ($document->filepath && file_exists(public_path($document->filepath))) {
                unlink(public_path($document->filepath));
            }

            $file->move(public_path('uploads/monitoring_approval'), $filename);
            $document->filepath = 'uploads/monitoring_approval/' . $filename;
        }

        $document->save();

        return redirect()->back()->with('success', 'Document updated successfully.');
    }
}
