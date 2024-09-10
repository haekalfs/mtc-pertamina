<?php

namespace App\Http\Controllers;

use App\Models\Akhlak;
use App\Models\Nilai;
use App\Models\Quarter;
use App\Models\User;
use App\Models\User_pencapaian_akhlak;
use App\Models\User_pencapaian_akhlak_files;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AkhlakController extends Controller
{
    public function index(Request $request)
    {
        // Determine the current year and generate the range of years
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        // Capture the filter inputs
        $userId = $request->input('userId');
        $quarter = $request->input('quarter');
        $year = $request->input('year');

        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            'userId' => 'required',
            'quarter' => 'required',
            'year' => 'required'
        ]);

        // Query to get User Pencapaian Akhlak with filters
        $pencapaianQuery = User_pencapaian_akhlak::selectRaw('akhlak_id, quarter_id, periode, AVG(nilai_akhlak.score) as average_score')
            ->join('nilai_akhlak', 'user_pencapaian_akhlak.score', '=', 'nilai_akhlak.id')
            ->with(['akhlak', 'user', 'quarter'])
            ->groupBy('akhlak_id', 'quarter_id', 'periode');

        $userSelected = User::find($userId);
        // Apply filters
        if ($userId != -1) {
            $pencapaianQuery->where('user_pencapaian_akhlak.user_id', $userId);
        }

        if ($quarter != -1) {
            $pencapaianQuery->where('quarter_id', $quarter);
        }

        if ($year != -1) {
            $pencapaianQuery->where('periode', $year);
        }

        $pencapaianResults = $pencapaianQuery->get();

        // Fetch all Nilai Akhlak ranges
        $nilaiAkhlakRanges = Nilai::all();

        foreach ($pencapaianResults as $result) {
            $averageScore = $result->average_score;

            // Find the corresponding nilai_akhlak description based on the average score
            $nilai = $nilaiAkhlakRanges->filter(function ($range) use ($averageScore) {
                return $averageScore <= $range->score;
            })->first();

            $result->nilai_description = $nilai ? $nilai->description : 'Unknown';
        }

        // Query to group by akhlak_id and periode for the general summary with filters
        $generalPencapaianQuery = User_pencapaian_akhlak::selectRaw('akhlak_id, periode, AVG(nilai_akhlak.score) as average_score')
            ->join('nilai_akhlak', 'user_pencapaian_akhlak.score', '=', 'nilai_akhlak.id')
            ->with(['akhlak'])
            ->groupBy('akhlak_id', 'periode');

        // Apply filters to the general query
        if ($userId != -1) {
            $generalPencapaianQuery->where('user_pencapaian_akhlak.user_id', $userId);
        }

        if ($quarter != -1) {
            $generalPencapaianQuery->where('quarter_id', $quarter);
        }

        if ($year != -1) {
            $generalPencapaianQuery->where('periode', $year);
        }

        $generalPencapaianResults = $generalPencapaianQuery->get();

        // Fetch all Nilai Akhlak ranges for general results
        $nilaiAkhlakRangesGeneral = Nilai::all();

        foreach ($generalPencapaianResults as $result) {
            $averageScore = $result->average_score;

            // Find the corresponding nilai_akhlak description based on the average score
            $nilaiGeneral = $nilaiAkhlakRangesGeneral->filter(function ($range) use ($averageScore) {
                return $averageScore <= $range->score;
            })->first();

            $result->nilai_description = $nilaiGeneral ? $nilaiGeneral->description : 'Unknown';
        }

        // Get all users and akhlak points for the form
        $users = User::all();
        $akhlakPoin = Akhlak::all();
        $quarterList = Quarter::all();
        $nilaiList = Nilai::all();

        // Process generalPencapaianResults for radar chart
        $akhlakLabels = [];
        $averageScores = [];

        foreach ($generalPencapaianResults as $result) {
            $akhlakLabels[] = $result->akhlak->indicator;  // Assuming akhlak has a 'description' field for the label
            $averageScores[] = $result->average_score;
        }

        // Return the view with the necessary data and the selected filters
        return view('akhlak-view.index', [
            'akhlakPoin' => $akhlakPoin,
            'users' => $users,
            'nilaiList' => $nilaiList,
            'pencapaianResults' => $pencapaianResults,
            'generalPencapaianResults' => $generalPencapaianResults,
            'yearsBefore' => $yearsBefore,
            'userSelectedOpt' => $userId,
            'quarterList' => $quarterList,
            'userSelected' => $userSelected,
            'quarterSelected' => $quarter,
            'yearSelected' => $year,
            'akhlakLabels' => json_encode($akhlakLabels),  // Pass data as JSON
            'averageScores' => json_encode($averageScores)  // Pass data as JSON
        ]);
    }

    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'userId' => 'required',
            'activity_title' => 'required',
            'akhlak_points' => 'required|array',
            'akhlak_points.*' => 'required',
            'akhlak_value' => 'required|integer',
            'evidence' => 'sometimes|file|mimes:pdf,doc,docx',
            'quarter' => 'required|integer|min:1|max:4',
            'year' => 'required|integer',
        ]);

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
                'quarter_id' => $request->quarter,
                'periode' => $request->year,
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

    public function report(Request $request)
    {
        // Determine the current year and generate the range of years
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        // Capture the filter inputs
        $userId = $request->input('userId');
        $year = $request->input('year');

        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            'userId' => 'required',
            'year' => 'required'
        ]);

        // Query to get User Pencapaian Akhlak with filters
        $pencapaianQuery = User_pencapaian_akhlak::selectRaw('akhlak_id, quarter_id, periode, AVG(nilai_akhlak.score) as average_score')
        ->join('nilai_akhlak', 'user_pencapaian_akhlak.score', '=', 'nilai_akhlak.id')
        ->with(['akhlak', 'user', 'quarter'])
        ->groupBy('akhlak_id', 'quarter_id', 'periode');

        $userSelected = User::find($userId);

        // Apply filters
        if ($userId != -1) {
        $pencapaianQuery->where('user_pencapaian_akhlak.user_id', $userId);
        }

        if ($year != -1) {
        $pencapaianQuery->where('periode', $year);
        }

        $pencapaianResults = $pencapaianQuery->get();

        // Fetch all Nilai Akhlak ranges
        $nilaiAkhlakRanges = Nilai::all();

        foreach ($pencapaianResults as $result) {
        $averageScore = $result->average_score;

        // Find the corresponding nilai_akhlak description based on the average score
        $nilai = $nilaiAkhlakRanges->filter(function ($range) use ($averageScore) {
            return $averageScore <= $range->score;
        })->first();

        $result->nilai_description = $nilai ? $nilai->description : 'Unknown';
        }

        // Group by Akhlak to avoid duplicating the Indicator
        $pencapaianByAkhlak = $pencapaianResults->groupBy('akhlak_id');

        // Query to group by akhlak_id and periode for the general summary with filters
        $generalPencapaianQuery = User_pencapaian_akhlak::selectRaw('akhlak_id, periode, AVG(nilai_akhlak.score) as average_score')
            ->join('nilai_akhlak', 'user_pencapaian_akhlak.score', '=', 'nilai_akhlak.id')
            ->with(['akhlak'])
            ->groupBy('akhlak_id', 'periode');

        // Apply filters to the general query
        if ($userId != -1) {
            $generalPencapaianQuery->where('user_pencapaian_akhlak.user_id', $userId);
        }

        if ($year != -1) {
            $generalPencapaianQuery->where('periode', $year);
        }

        $generalPencapaianResults = $generalPencapaianQuery->get();

        // Fetch all Nilai Akhlak ranges for general results
        $nilaiAkhlakRangesGeneral = Nilai::all();

        foreach ($generalPencapaianResults as $result) {
            $averageScore = $result->average_score;

            // Find the corresponding nilai_akhlak description based on the average score
            $nilaiGeneral = $nilaiAkhlakRangesGeneral->filter(function ($range) use ($averageScore) {
                return $averageScore <= $range->score;
            })->first();

            $result->nilai_description = $nilaiGeneral ? $nilaiGeneral->description : 'Unknown';
        }

        // Get all users and akhlak points for the form
        $users = User::all();
        $akhlakPoin = Akhlak::all();
        $quarterList = Quarter::all();
        $nilaiList = Nilai::all();

        // Process generalPencapaianResults for radar chart
        $akhlakLabels = [];
        $averageScores = [];

        foreach ($generalPencapaianResults as $result) {
            $akhlakLabels[] = $result->akhlak->indicator;  // Assuming akhlak has a 'description' field for the label
            $averageScores[] = $result->average_score;
        }

        $allPencapaian = User_pencapaian_akhlak::where('periode', $year)->where('user_id', $userId)->get();
        // Return the view with the necessary data and the selected filters
        return view('akhlak-view.admin.report', [
            'akhlakPoin' => $akhlakPoin,
            'users' => $users,
            'nilaiList' => $nilaiList,
            'pencapaianByAkhlak' => $pencapaianByAkhlak,
            'generalPencapaianResults' => $generalPencapaianResults,
            'yearsBefore' => $yearsBefore,
            'userSelectedOpt' => $userId,
            'quarterList' => $quarterList,
            'userSelected' => $userSelected,
            'yearSelected' => $year,
            'allPencapaian' => $allPencapaian,
            'akhlakLabels' => json_encode($akhlakLabels),  // Pass data as JSON
            'averageScores' => json_encode($averageScores)  // Pass data as JSON
        ]);
    }

    public function downloadPdf(Request $request)
    {
        // Determine the current year and generate the range of years
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        // Capture the filter inputs
        $userId = $request->input('userId') ?? '-1';
        $year = $request->input('year') ?? $nowYear;
        $chartImage = $request->input('chartImage'); // Get the chart image data from the request

        // Query to get User Pencapaian Akhlak with filters
        $pencapaianQuery = User_pencapaian_akhlak::selectRaw('akhlak_id, quarter_id, periode, AVG(nilai_akhlak.score) as average_score')
        ->join('nilai_akhlak', 'user_pencapaian_akhlak.score', '=', 'nilai_akhlak.id')
        ->with(['akhlak', 'user', 'quarter'])
        ->groupBy('akhlak_id', 'quarter_id', 'periode');

        $userSelected = User::find($userId);

        // Apply filters
        if ($userId != -1) {
        $pencapaianQuery->where('user_pencapaian_akhlak.user_id', $userId);
        }

        if ($year != -1) {
        $pencapaianQuery->where('periode', $year);
        }

        $pencapaianResults = $pencapaianQuery->get();

        // Fetch all Nilai Akhlak ranges
        $nilaiAkhlakRanges = Nilai::all();

        foreach ($pencapaianResults as $result) {
        $averageScore = $result->average_score;

        // Find the corresponding nilai_akhlak description based on the average score
        $nilai = $nilaiAkhlakRanges->filter(function ($range) use ($averageScore) {
            return $averageScore >= $range->score;
        })->first();

        $result->nilai_description = $nilai ? $nilai->description : 'Unknown';
        }

        // Group by Akhlak to avoid duplicating the Indicator
        $pencapaianByAkhlak = $pencapaianResults->groupBy('akhlak_id');

        // Query to group by akhlak_id and periode for the general summary with filters
        $generalPencapaianQuery = User_pencapaian_akhlak::selectRaw('akhlak_id, periode, AVG(nilai_akhlak.score) as average_score')
            ->join('nilai_akhlak', 'user_pencapaian_akhlak.score', '=', 'nilai_akhlak.id')
            ->with(['akhlak'])
            ->groupBy('akhlak_id', 'periode');

        // Apply filters to the general query
        $generalPencapaianQuery->where('user_pencapaian_akhlak.user_id', 'haekals');
        $generalPencapaianQuery->where('periode', '2024');

        $generalPencapaianResults = $generalPencapaianQuery->get();

        // Fetch all Nilai Akhlak ranges for general results
        $nilaiAkhlakRangesGeneral = Nilai::all();

        foreach ($generalPencapaianResults as $result) {
            $averageScore = $result->average_score;

            // Find the corresponding nilai_akhlak description based on the average score
            $nilaiGeneral = $nilaiAkhlakRangesGeneral->filter(function ($range) use ($averageScore) {
                return $averageScore >= $range->score;
            })->first();

            $result->nilai_description = $nilaiGeneral ? $nilaiGeneral->description : 'Unknown';
        }

        $allPencapaian = User_pencapaian_akhlak::where('periode', $year)->where('user_id', $userId)->get();
        $userProfilePic = public_path($userSelected->users_detail->profile_pic);

        if (file_exists($userProfilePic)) {
            $userProfilePicBase64 = base64_encode(file_get_contents($userProfilePic));
            $mime = mime_content_type($userProfilePic);
            $imageSrc = 'data:' . $mime . ';base64,' . $userProfilePicBase64;
        } else {
            $imageSrc = '';  // Handle cases where the image file doesn't exist
        }
        // Create a view for the PDF using compact
        $pdfView = view('akhlak-view.laporan.pdf', compact(
            'pencapaianByAkhlak',
            'generalPencapaianResults',
            'yearsBefore',
            'userId',   // Rename 'userSelectedOpt' to 'userId' for compact
            'userSelected',
            'year',
            'chartImage',
            'allPencapaian',
            'imageSrc'
        ))->render();

        // Set options and create the PDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($pdfView);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('AKHLAK_Report.pdf');
    }
}
