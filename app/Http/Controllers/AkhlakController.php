<?php

namespace App\Http\Controllers;

use App\Models\Akhlak;
use App\Models\User;
use App\Models\User_pencapaian_akhlak;
use App\Models\User_pencapaian_akhlak_files;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AkhlakController extends Controller
{
    public function index(Request $request, $yearSelected = 1)
    {
        // Determine the current year and generate the range of years
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        // Set the selected year
        $currentYear = $yearSelected ?? $nowYear;

        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            'userId' => 'required|integer',
            'periode_start' => 'required|date',
            'periode_end' => 'required|date'
        ]);

        // Initialize variables
        $userSelected = null;
        $userSelectedOpt = null;

        $pencapaianQuery = User_pencapaian_akhlak::query();
        $pencapaianChart = User_pencapaian_akhlak::query();

        // Extract period dates from the request
        $periode_start = $request->periode_start;
        $periode_end = $request->periode_end;

        // If userId is not 1, find the user and filter pencapaian
        if ($request->userId != 1) {
            $userSelected = User::find($request->userId);
            if ($userSelected) {
                $userSelectedOpt = $userSelected->id;
                $pencapaianQuery->where('user_id', $request->userId);
                $pencapaianChart->where('user_id', $request->userId);

                // Add whereBetween condition if both periode_start and periode_end exist
                if ($periode_start && $periode_end) {
                    $startDate = Carbon::createFromFormat('Y-m-d', $periode_start);
                    $endDate = Carbon::createFromFormat('Y-m-d', $periode_end);
                    $pencapaianQuery->whereBetween('periode_start', [$startDate, $endDate])->whereBetween('periode_end', [$startDate, $endDate]);
                    $pencapaianChart->whereBetween('periode_start', [$startDate, $endDate])->whereBetween('periode_end', [$startDate, $endDate]);
                }

                if ($yearSelected != 1) {
                    $pencapaianChart->whereYear('periode_start', $currentYear)->whereYear('periode_end', $currentYear);
                    $pencapaianQuery->whereYear('periode_start', $currentYear)->whereYear('periode_end', $currentYear);
                }

                $results = $pencapaianChart->selectRaw('akhlak_id, AVG(score) as average_score')
                ->groupBy('akhlak_id')
                ->get()
                ->keyBy('akhlak_id')
                ->toArray();
            } else {
                // Add whereBetween condition if both periode_start and periode_end exist

                if ($yearSelected != 1) {
                    $pencapaianChart->whereYear('periode_start', $currentYear)->whereYear('periode_end', $currentYear);
                    $pencapaianQuery->whereYear('periode_start', $currentYear)->whereYear('periode_end', $currentYear);
                }

                if ($periode_start && $periode_end) {
                    $startDate = Carbon::createFromFormat('Y-m-d', $periode_start);
                    $endDate = Carbon::createFromFormat('Y-m-d', $periode_end);
                    $pencapaianQuery->whereBetween('periode_start', [$startDate, $endDate])->whereBetween('periode_end', [$startDate, $endDate]);
                    $pencapaianChart->whereBetween('periode_start', [$startDate, $endDate])->whereBetween('periode_end', [$startDate, $endDate]);
                }

                $results = $pencapaianChart->selectRaw('akhlak_id, AVG(score) as average_score')
                ->groupBy('akhlak_id')
                ->get()
                ->keyBy('akhlak_id')
                ->toArray();
            }
        } else {

            if ($yearSelected != 1) {
                $pencapaianChart->whereYear('periode_start', $currentYear)->whereYear('periode_end', $currentYear);
                $pencapaianQuery->whereYear('periode_start', $currentYear)->whereYear('periode_end', $currentYear);
            }

            // Add whereBetween condition if both periode_start and periode_end exist
            if ($periode_start && $periode_end) {
                $startDate = Carbon::createFromFormat('Y-m-d', $periode_start);
                $endDate = Carbon::createFromFormat('Y-m-d', $periode_end);
                $pencapaianQuery->whereBetween('periode_start', [$startDate, $endDate])->whereBetween('periode_end', [$startDate, $endDate]);
                $pencapaianChart->whereBetween('periode_start', [$startDate, $endDate])->whereBetween('periode_end', [$startDate, $endDate]);
            }

            $results = $pencapaianChart->selectRaw('akhlak_id, AVG(score) as average_score')
            ->groupBy('akhlak_id')
            ->get()
            ->keyBy('akhlak_id')
            ->toArray();
        }

        // Execute the query
        $pencapaian = $pencapaianQuery->get();

        // Get all users and akhlak points
        $users = User::all();
        $akhlakPoin = Akhlak::all();

        // Map data for the radar chart
        $labels = ['Kompeten', 'Harmonis', 'Loyal', 'Adaptif', 'Kolaboratif', 'Amanah'];
        $dataMap = [
            1 => 'Amanah',
            2 => 'Kompeten',
            3 => 'Harmonis',
            4 => 'Loyal',
            5 => 'Adaptif',
            6 => 'Kolaboratif'
        ];
        $data = array_fill(0, count($labels), 0);

        foreach ($results as $akhlak_id => $result) {
            $index = array_search($dataMap[$akhlak_id], $labels);
            if ($index !== false) {
                $data[$index] = $result['average_score'];
            }
        }

        // Return the view with the necessary data
        return view('akhlak-view.index', [
            'akhlakPoin' => $akhlakPoin,
            'pencapaian' => $pencapaian,
            'users' => $users,
            'yearsBefore' => $yearsBefore,
            'yearSelected' => $currentYear,
            'userSelected' => $userSelected,
            'periode_start' => $periode_start,
            'periode_end' => $periode_end,
            'userSelectedOpt' => $userSelectedOpt,
            'data' => $data,
            'labels' => $labels
        ]);
    }

    public function report()
    {
        return view('akhlak-view.admin.report');
    }

    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'userId' => 'required',
            'activity_title' => 'required',
            'akhlak_points' => 'required',
            'akhlak_points.*' => 'required',
            'akhlak_value' => 'required|string|max:10',
            'evidence' => 'required|file|mimes:pdf,doc,docx',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        // // Handle the file upload
        // $evidenceFile = $request->file('evidence');
        // $fileName = time() . '_' . $evidenceFile->getClientOriginalName();
        // $filePath = $evidenceFile->storeAs('akhlak_evidences', $fileName, 'public');
        // Handle the file upload
        $evidenceFile = $request->file('evidence');
        $fileName = time() . '_' . $evidenceFile->getClientOriginalName();
        $filePath = public_path('akhlak_evidences/' . $fileName);

        // Move the file to the public path
        $evidenceFile->move(public_path('akhlak_evidences'), $fileName);

        // Create the main record
        foreach ($request->akhlak_points as $akhlakId) {
            $userPencapaianAkhlak = User_pencapaian_akhlak::create([
                'user_id' => $request->userId,
                'judul_kegiatan' => $request->activity_title,
                'akhlak_id' => $akhlakId,
                'score' => $request->akhlak_value,
                'periode_start' => $request->period_start,
                'periode_end' => $request->period_end,
            ]);

            // Save the file information in the User_pencapaian_akhlak_files model
            User_pencapaian_akhlak_files::create([
                'filename' => $fileName,
                'filepath' => $filePath,
                'user_pencapaian_akhlak_id' => $userPencapaianAkhlak->id,
            ]);
        }

        return redirect()->back()->with('success', 'Data Pencapaian Akhlak has been successfully saved.');
    }
}
