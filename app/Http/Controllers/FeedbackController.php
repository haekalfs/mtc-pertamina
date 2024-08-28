<?php

namespace App\Http\Controllers;

use App\Models\Feedback_mtc;
use App\Models\Feedback_report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class FeedbackController extends Controller
{
    public function feedback_mtc(Request $request)
    {
        if ($request->ajax()) {
            $query = Feedback_mtc::query();

            return DataTables::of($query)
                ->addColumn('action', function($row){
                    return '<button class="btn btn-outline-secondary btn-sm edit-feedback" data-id="'.$row->id.'"><i class="fa fa-edit"></i> Edit</button>';
                })
                ->rawColumns(['action']) // Make the action column HTML-safe
                ->make(true);
        }

        return view('plan_dev.submenu.feedback-mtc.index');
    }

    public function feedback_mtc_import()
    {
        return view('plan_dev.submenu.feedback-mtc.feedback-mtc-import');
    }

    public function edit_feeedback_mtc($id)
    {
        $feedback = Feedback_mtc::find($id);

        if (!$feedback) {
            return response()->json(['status' => 'failed', 'message' => 'Record not found!'], 404);
        }

        return response()->json($feedback);
    }

    public function update_feeedback_mtc(Request $request, $id)
    {
        try {
            $feedback = Feedback_mtc::find($id);

            if (!$feedback) {
                return redirect()->route('feedback-mtc')->with('error', 'Record not found!');
            }

            $feedback->update($request->all());

            return response()->json(['message' => 'Feedback report updated successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to update feedback: ' . $e->getMessage());

            return response()->json(['message' => 'Failed to update feedback due to an unexpected error!']);
        }
    }

    public function edit_feedback($feedbackId)
    {
        $feedbackReport = Feedback_report::where('feedback_id', $feedbackId)->get();
        return response()->json($feedbackReport);
    }

    public function update_feedback(Request $request, $feedbackId)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'tgl_pelaksanaan' => 'required|date',
            'tempat_pelaksanaan' => 'required|string|max:255',
            'nama_peserta' => 'required|string|max:145',
            'nama_program' => 'required|string|max:255',
            'instruktur' => 'required|string|max:255',
            'score_1' => 'required|string|max:255',
            'score_2' => 'required|string|max:255',
            'score_3' => 'required|string|max:255',
            'score_4' => 'required|string|max:255',
            'score_5' => 'required|string|max:255',
        ]);

        // Update each feedback report record with the same feedback_id
        $feedbackReports = Feedback_report::where('feedback_id', $feedbackId)->get();

        foreach ($feedbackReports as $index => $report) {
            $report->update([
                'tgl_pelaksanaan' => $validatedData['tgl_pelaksanaan'],
                'tempat_pelaksanaan' => $validatedData['tempat_pelaksanaan'],
                'nama' => $validatedData['nama_peserta'],
                'judul_pelatihan' => $validatedData['nama_program'],
                'instruktur' => $validatedData['instruktur'],
                'score' => $validatedData['score_' . ($index + 1)],
            ]);
        }

        return response()->json(['message' => 'Feedback report updated successfully']);
    }

    public function delete_feedback_instructor($id)
    {
        try {
            // Retrieve all Feedback_report records with the given feedback_id
            $feedbackInstructor = Feedback_report::where('feedback_id', $id)->get();

            // Check if any records were found
            if ($feedbackInstructor->isEmpty()) {
                return response()->json(['status' => 'failed', 'message' => 'Record not found!']);
            }

            // Delete all found records
            $feedbackInstructor->each(function ($record) {
                $record->delete();
            });

            return response()->json(['status' => 'success', 'message' => 'All matching records deleted successfully!']);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to delete Feedback_report records: ' . $e->getMessage());

            return response()->json(['status' => 'failed', 'message' => 'Failed to delete records due to an unexpected error!']);
        }
    }

    public function delete_feedback_mtc($id)
    {
        try {
            // Retrieve all Feedback_mtc records with the given feedback_id
            $feedbackMTC = Feedback_mtc::where('id', $id)->get();

            // Check if any records were found
            if ($feedbackMTC->isEmpty()) {
                return response()->json(['status' => 'failed', 'message' => 'Record not found!']);
            }

            // Delete all found records
            $feedbackMTC->each(function ($record) {
                $record->delete();
            });

            return response()->json(['status' => 'success', 'message' => 'All matching records deleted successfully!']);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to delete Feedback_mtc records: ' . $e->getMessage());

            return response()->json(['status' => 'failed', 'message' => 'Failed to delete records due to an unexpected error!']);
        }
    }
}
