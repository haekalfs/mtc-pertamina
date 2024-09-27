<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefractorController extends Controller
{
    public function index()
    {
        return view('refractor.index');
    }

    public function data_deletion(Request $request)
    {
        // Split the date range into start and end dates
        $dateRange = explode(' - ', $request->daterange);
        $startDate = $dateRange[0];
        $endDate = $dateRange[1];

        // Validate table name
        $validTables = ['profits', 'infografis_peserta', 'feedback_reports', 'feedback_mtc'];
        if (!in_array($request->table_name, $validTables)) {
            return redirect()->back()->with('error', 'Invalid table name.');
        }

        // Delete records based on date range and get the number of deleted rows
        $totalRows = DB::table($request->table_name)
            ->whereBetween('tgl_pelaksanaan', [$startDate, $endDate])
            ->delete();

        // Store the message in session and redirect back to the same page
        return redirect()->back()->with('success', "$totalRows rows have been deleted successfully!");
    }
}
