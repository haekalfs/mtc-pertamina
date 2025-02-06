<?php

namespace App\Http\Controllers;

use App\Models\Infografis_peserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class RefractorController extends Controller
{
    public function index()
    {
        // Encrypt table names and pass them to the view
        $encryptedTables = [
            'profits' => Crypt::encryptString('profits'),
            'infografis_peserta' => Crypt::encryptString('infografis_peserta'),
            'feedback_reports' => Crypt::encryptString('feedback_reports'),
            'feedback_mtc' => Crypt::encryptString('feedback_mtc'),
        ];

        return view('refractor.index', compact('encryptedTables'));
    }

    public function data_deletion(Request $request)
    {
        // Split the date range into start and end dates
        $dateRange = explode(' - ', $request->daterange);
        $startDate = $dateRange[0];
        $endDate = $dateRange[1];

        try {
            // Decrypt the incoming encrypted table name
            $tableName = Crypt::decryptString($request->table_name);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return redirect()->back()->with('error', 'Invalid encrypted table name.');
        }

        // Validate table name
        $validTables = ['profits', 'infografis_peserta', 'feedback_reports', 'feedback_mtc'];
        if (!in_array($tableName, $validTables)) {
            return redirect()->back()->with('error', 'Invalid table name.');
        }

        // Special handling for infografis_peserta
        if ($tableName == 'infografis_peserta') {
            // Get IDs of participants that have a related certificateCheck
            $protectedIds = Infografis_peserta::has('certificateCheck')->pluck('id');

            // Delete only the records that are NOT linked to certificates
            $totalRows = DB::table($tableName)
                ->whereBetween('tgl_pelaksanaan', [$startDate, $endDate])
                ->whereNotIn('id', $protectedIds) // Exclude linked rows
                ->delete();
        } else {
            // Delete normally for other tables
            $totalRows = DB::table($tableName)
                ->whereBetween('tgl_pelaksanaan', [$startDate, $endDate])
                ->delete();
        }

        // Store the message in session and redirect back
        return redirect()->back()->with('success', "$totalRows rows have been deleted successfully!");
    }
}
