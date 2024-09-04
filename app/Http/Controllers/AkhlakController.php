<?php

namespace App\Http\Controllers;

use App\Models\Akhlak;
use App\Models\Nilai;
use App\Models\Quarter;
use App\Models\User;
use App\Models\User_pencapaian_akhlak;
use App\Models\User_pencapaian_akhlak_files;
use Carbon\Carbon;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AkhlakController extends Controller
{
    public function index(Request $request)
    {
        // Determine the current year and generate the range of years
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            'userId' => 'required|integer',
            'quarter' => 'required',
            'periode' => 'required'
        ]);

        // Initialize variables
        $userSelected = null;
        $userSelectedOpt = null;

        $pencapaianQuery = User_pencapaian_akhlak::query();
        $pencapaianChart = User_pencapaian_akhlak::query();

        // Extract period dates from the request
        $quarter = $request->quarter;
        $periode = $request->year;

        // Set the selected year
        $currentYear = $periode ?? 1;
        $quarterSelected = $quarter ?? 99;

        // If userId is not 1, find the user and filter pencapaian
        if ($request->userId != 1) {
            $userSelected = User::find($request->userId);
            if ($userSelected) {
                $userSelectedOpt = $userSelected->id;
                $pencapaianQuery->where('user_id', $request->userId);
                $pencapaianChart->where('user_id', $request->userId);

                if ($periode != 1) {
                    $pencapaianChart->where('periode', $currentYear);
                    $pencapaianQuery->where('periode', $currentYear);
                }

                if ($quarter != 99) {
                    $pencapaianChart->where('quarter_id', $quarter);
                    $pencapaianQuery->where('quarter_id', $quarter);
                }

                $results = $pencapaianChart->selectRaw('akhlak_id, AVG(score) as average_score')
                ->groupBy('akhlak_id')
                ->get()
                ->keyBy('akhlak_id')
                ->toArray();
            } else {
                // Add whereBetween condition if both periode_start and periode_end exist

                if ($periode != 1) {
                    $pencapaianChart->where('periode', $currentYear);
                    $pencapaianQuery->where('periode', $currentYear);
                }

                if ($quarter != 99) {
                    $pencapaianChart->where('quarter_id', $quarter);
                    $pencapaianQuery->where('quarter_id', $quarter);
                }

                $results = $pencapaianChart->selectRaw('akhlak_id, AVG(score) as average_score')
                ->groupBy('akhlak_id')
                ->get()
                ->keyBy('akhlak_id')
                ->toArray();
            }
        } else {

            if ($periode != 1) {
                $pencapaianChart->where('periode', $currentYear);
                $pencapaianQuery->where('periode', $currentYear);
            }

            if ($quarter != 99) {
                $pencapaianChart->where('quarter_id', $quarter);
                $pencapaianQuery->where('quarter_id', $quarter);
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
        $quarterList = Quarter::all();
        $nilai = Nilai::all();

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
            'quarterSelected' => $quarterSelected,
            'quarterList' => $quarterList,
            'yearsBefore' => $yearsBefore,
            'yearSelected' => $currentYear,
            'userSelected' => $userSelected,
            'userSelectedOpt' => $userSelectedOpt,
            'data' => $data,
            'nilai' => $nilai,
            'labels' => $labels
        ]);
    }

    public function report(Request $request, $userId = 1, $akhlak = 7, $periode = 1)
    {
        // Determine the current year and generate the range of years
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        // Set the selected year
        $periode = $periode ?? $nowYear;

        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            'userId' => 'required',
            'nilai_akhlak' => 'required',
            'periode' => 'required|date'
        ]);

        // Extract period dates from the request
        $userId = $request->userId;
        $periode = $request->periode;
        $akhlak = $request->nilai_akhlak;

        $userInfo = User::find($userId);

        // Get all users and akhlak points
        $users = User::all();
        $akhlakPoin = Akhlak::all();

        $pencapaianQuery = User_pencapaian_akhlak::query();

        if ($userId != 1) {
            $userSelected = User::find($userId);
            if ($userSelected) {
                $pencapaianQuery->where('user_id', $userId);
            }
        }

        if ($periode != 1) {
            $pencapaianQuery->where('periode', $periode);
        }

        if ($akhlak != 7) {
            $pencapaianQuery->where('akhlak_id', $akhlak);
        }

        // Execute the query
        $pencapaian = $pencapaianQuery->get();

        return view('akhlak-view.admin.report', [
            'yearsBefore' => $yearsBefore,
            'pencapaian' => $pencapaian,
            'periode' => $periode,
            'akhlakSelected' => $akhlak,
            'akhlakPoin' => $akhlakPoin,
            'userInfo' => $userInfo,
            'userSelected' => $userId,
            'users' => $users,
            'periode' => $periode
        ]);
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

    public function print($userId, $akhlak, $periode)
    {
        // Set the default time zone to Jakarta
        date_default_timezone_set("Asia/Jakarta");

        $userInfo = User::find($userId);
        $users = User::all();
        $akhlakPoin = Akhlak::all();

        $pencapaianQuery = User_pencapaian_akhlak::query();

        if ($userId != 1) {
            $userSelected = User::find($userId);
            if ($userSelected) {
                $pencapaianQuery->where('user_id', $userId);
            }
        }

        if ($periode != 1) {
            $pencapaianQuery->whereYear('periode_start', $periode)->whereYear('periode_end', $periode);
        }

        if ($akhlak != 7) {
            $pencapaianQuery->where('akhlak_id', $akhlak);
        }

        $pencapaian = $pencapaianQuery->get();

        $pdf = PDF::loadView('akhlak-view.laporan.pdf', [
            'pencapaian' => $pencapaian,
            'periode' => $periode,
            'akhlakSelected' => $akhlak,
            'akhlakPoin' => $akhlakPoin,
            'userInfo' => $userInfo,
            'userSelected' => $userId,
            'users' => $users,
        ]);

        return $pdf->download('laporan-akhlak.pdf');
    }
}
