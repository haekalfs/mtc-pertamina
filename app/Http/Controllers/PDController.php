<?php

namespace App\Http\Controllers;

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
        return view('plan_dev.submenu.certificate');
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
}
