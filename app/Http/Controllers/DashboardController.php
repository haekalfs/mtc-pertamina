<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Feedback_report;
use App\Models\Infografis_peserta;
use App\Models\Instructor;
use App\Models\Inventory_tools;
use App\Models\MorningBriefing;
use App\Models\Penlat;
use App\Models\Penlat_batch;
use App\Models\Profit;
use App\Models\Regulation;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $roles = Auth::user()->role_id->pluck('role_name')->toArray();
            if (!session()->has('allowed_roles')) {
                session()->put('allowed_roles', $roles);
            }
        } catch (\Exception $e) {
            // Do nothing
        }

        $headline = Campaign::orderBy('updated_at', 'desc')->take(min(Campaign::count(), 12))->get();
        $queryCampaign = Campaign::query();
        $countCampaign = $queryCampaign->count();
        //Realisasi
        $getPesertaCount = Infografis_peserta::count();

        //sum profits
        $rawProfits = Profit::whereYear('tgl_pelaksanaan', date('Y'))->sum('total_biaya_pendaftaran_peserta');

        // Calculate the average feedback score
        $averageFeedbackScore = DB::table('feedback_mtc')
        ->select(DB::raw('
            avg(
                (
                    COALESCE(relevansi_materi, 0) +
                    COALESCE(manfaat_training, 0) +
                    COALESCE(durasi_training, 0) +
                    COALESCE(sistematika_penyajian, 0) +
                    COALESCE(tujuan_tercapai, 0) +
                    COALESCE(kedisiplinan_steward, 0) +
                    COALESCE(fasilitasi_steward, 0) +
                    COALESCE(layanan_pelaksana, 0) +
                    COALESCE(proses_administrasi, 0) +
                    COALESCE(kemudahan_registrasi, 0) +
                    COALESCE(kondisi_peralatan, 0) +
                    COALESCE(kualitas_boga, 0) +
                    COALESCE(media_online, 0) +
                    COALESCE(rekomendasi, 0)
                ) / 14
            ) as average_score
        '))
        ->value('average_score');

        $getAssetCount = Inventory_tools::count();

        // Count total instructors
        $instructorCount = Instructor::count();

        //Jumlah Pelatihan
        $penlatCount = Penlat::count();

        //Jumlah Batch
        $batchCount = Penlat_batch::count();
        $regulations = Regulation::orderBy('created_at', 'desc')->take(6)->get();
        $morningBriefing = MorningBriefing::orderBy('updated_at', 'desc')->take(1)->get();

        $infographicCategories = Infografis_peserta::select('subholding')->distinct()->get();
        $infographicTypes = Infografis_peserta::select('jenis_pelatihan')->distinct()->get();

        return view('dashboard', [
            'morningBriefing' => $morningBriefing,
            'regulations' => $regulations,
            'headline' => $headline,
            'getPesertaCount' => $getPesertaCount,
            'countCampaign' => $countCampaign,
            'rawProfits' => $rawProfits,
            'averageFeedbackScore' => $averageFeedbackScore,
            'getAssetCount' => $getAssetCount,
            'instructorCount' => $instructorCount,
            'penlatCount' => $penlatCount,
            'batchCount' => $batchCount,
            'infographicCategories' => $infographicCategories,
            'infographicTypes' => $infographicTypes

        ]);
    }

    public function getDashboardChartData(Request $request)
    {
        $day = $request->input('day');
        $month = $request->input('month');
        $year = $request->input('year');
        $category = $request->input('category');
        $type = $request->input('type');

        // 1. Location Chart Data
        $locationDataQuery = Infografis_peserta::select('tempat_pelaksanaan', DB::raw('count(*) as total'))
            ->whereYear('tgl_pelaksanaan', $year)
            ->groupBy('tempat_pelaksanaan');

        if ($day && $day != '-1') {
            $locationDataQuery->whereDay('tgl_pelaksanaan', $day);
        }
        if ($month && $month != '-1') {
            $locationDataQuery->whereMonth('tgl_pelaksanaan', $month);
        }
        if ($year && $year != '-1') {
            $locationDataQuery->whereYear('tgl_pelaksanaan', $year);
        }
        if ($category && $category != '-1') {
            $locationDataQuery->where('kategori_program', $category);
        }
        if ($type && $type != '-1') {
            $locationDataQuery->where('jenis_pelatihan', $type);
        }

        $locationData = $locationDataQuery->get();

        // Gauge Chart Data for STCW and NON STCW
        $latestMonthRecordQuery = Infografis_peserta::query();

        if ($year && $year != '-1') {
            $latestMonthRecordQuery->whereYear('tgl_pelaksanaan', $year);
        }
        $latestMonthRecord = $latestMonthRecordQuery->orderBy('tgl_pelaksanaan', 'desc')->first();

        $countSTCWGauge = 0;
        $countNonSTCWGauge = 0;
        $stcwDelta = 0;
        $nonStcwDelta = 0;

        if ($latestMonthRecord) {
            $latestMonth = Carbon::parse($latestMonthRecord->tgl_pelaksanaan)->month ?? null;

            // Count for STCW in the latest month
            $countSTCWGaugeQuery = Infografis_peserta::query();
            if ($year && $year != '-1') {
                $countSTCWGaugeQuery->whereYear('tgl_pelaksanaan', $year);
            }
            if ($latestMonth) {
                $countSTCWGaugeQuery->whereMonth('tgl_pelaksanaan', $latestMonth);
            }
            if ($category && $category != '-1') {
                $countSTCWGaugeQuery->where('kategori_program', $category);
            }
            $countSTCWGauge = $countSTCWGaugeQuery->where('kategori_program', 'STCW')->count() ?? 0;

            // Count for NON STCW in the latest month
            $countNonSTCWGaugeQuery = Infografis_peserta::query();
            if ($year && $year != '-1') {
                $countNonSTCWGaugeQuery->whereYear('tgl_pelaksanaan', $year);
            }
            if ($latestMonth) {
                $countNonSTCWGaugeQuery->whereMonth('tgl_pelaksanaan', $latestMonth);
            }
            if ($category && $category != '-1') {
                $countNonSTCWGaugeQuery->where('kategori_program', $category);
            }
            $countNonSTCWGauge = $countNonSTCWGaugeQuery->where('kategori_program', 'NON STCW')->count() ?? 0;

            // Calculate previous month (for delta)
            $previousMonth = ($latestMonth == 1) ? 12 : $latestMonth - 1;

            // STCW Delta in previous month
            $stcwDeltaQuery = Infografis_peserta::query();
            if ($year && $year != '-1') {
                $stcwDeltaQuery->whereYear('tgl_pelaksanaan', $year);
            }
            if ($previousMonth) {
                $stcwDeltaQuery->whereMonth('tgl_pelaksanaan', $previousMonth);
            }
            if ($category && $category != '-1') {
                $stcwDeltaQuery->where('kategori_program', $category);
            }
            $stcwDelta = $stcwDeltaQuery->where('kategori_program', 'STCW')->count() ?? 0;

            // NON STCW Delta in previous month
            $nonStcwDeltaQuery = Infografis_peserta::query();
            if ($year && $year != '-1') {
                $nonStcwDeltaQuery->whereYear('tgl_pelaksanaan', $year);
            }
            if ($previousMonth) {
                $nonStcwDeltaQuery->whereMonth('tgl_pelaksanaan', $previousMonth);
            }
            if ($category && $category != '-1') {
                $nonStcwDeltaQuery->where('kategori_program', $category);
            }
            $nonStcwDelta = $nonStcwDeltaQuery->where('kategori_program', 'NON STCW')->count() ?? 0;
        }

        // 3. Trend Revenue Chart Data
        $trendRevenueQuery = Profit::select(
                'penlat.description', // Get the description from Penlat
                DB::raw('SUM(CAST(profits.total_biaya_pendaftaran_peserta AS UNSIGNED)) as total_biaya')
            )
            ->join('penlat_batch', 'profits.pelaksanaan', '=', 'penlat_batch.batch') // Join with penlat_batch
            ->join('penlat', 'penlat_batch.penlat_id', '=', 'penlat.id') // Join with penlat
            ->groupBy('penlat_batch.penlat_id', 'penlat.description') // Group by penlat ID and description
            ->orderByDesc('total_biaya'); // Order by total_biaya

        // Apply filters based on request inputs
        if ($year && $year != '-1') {
            $trendRevenueQuery->whereYear('profits.tgl_pelaksanaan', $year);
        }
        if ($month && $month != '-1') {
            $trendRevenueQuery->whereMonth('profits.tgl_pelaksanaan', $month);
        }
        if ($day && $day != '-1') {
            $trendRevenueQuery->whereDay('profits.tgl_pelaksanaan', $day);
        }
        // Execute the query and get results
        $trendRevenueData = $trendRevenueQuery->get();

        // 1. Count of Peserta
        $getPesertaCountQuery = Infografis_peserta::query();

        if ($year && $year != '-1') {
            $getPesertaCountQuery->whereYear('tgl_pelaksanaan', $year);
        }
        if ($month && $month != '-1') {
            $getPesertaCountQuery->whereMonth('tgl_pelaksanaan', $month);
        }
        if ($day && $day != '-1') {
            $getPesertaCountQuery->whereDay('tgl_pelaksanaan', $day);
        }
        if ($category && $category != '-1') {
            $getPesertaCountQuery->where('kategori_program', $category);
        }
        if ($type && $type != '-1') {
            $getPesertaCountQuery->where('jenis_pelatihan', $type);
        }
        $getPesertaCount = $getPesertaCountQuery->count();

        // 2. Sum of Profits
        $rawProfitsQuery = Profit::query();

        if ($year && $year != '-1') {
            $rawProfitsQuery->whereYear('tgl_pelaksanaan', $year);
        }
        if ($month && $month != '-1') {
            $rawProfitsQuery->whereMonth('tgl_pelaksanaan', $month);
        }
        if ($day && $day != '-1') {
            $rawProfitsQuery->whereDay('tgl_pelaksanaan', $day);
        }
        // if ($category && $category != '-1') {
        //     $rawProfitsQuery->where('kategori_program', $category);
        // }
        // if ($type && $type != '-1') {
        //     $rawProfitsQuery->where('jenis_pelatihan', $type);
        // }
        $rawProfits = $rawProfitsQuery->sum('total_biaya_pendaftaran_peserta');

        // 3. Average Feedback Score
        $averageFeedbackScoreQuery = DB::table('feedback_mtc')->select(DB::raw('
            avg(
                (
                    COALESCE(relevansi_materi, 0) +
                    COALESCE(manfaat_training, 0) +
                    COALESCE(durasi_training, 0) +
                    COALESCE(sistematika_penyajian, 0) +
                    COALESCE(tujuan_tercapai, 0) +
                    COALESCE(kedisiplinan_steward, 0) +
                    COALESCE(fasilitasi_steward, 0) +
                    COALESCE(layanan_pelaksana, 0) +
                    COALESCE(proses_administrasi, 0) +
                    COALESCE(kemudahan_registrasi, 0) +
                    COALESCE(kondisi_peralatan, 0) +
                    COALESCE(kualitas_boga, 0) +
                    COALESCE(media_online, 0) +
                    COALESCE(rekomendasi, 0)
                ) / 14
            ) as average_score
        '));

        if ($year && $year != '-1') {
            $averageFeedbackScoreQuery->whereYear('tgl_pelaksanaan', $year);
        }
        if ($month && $month != '-1') {
            $averageFeedbackScoreQuery->whereMonth('tgl_pelaksanaan', $month);
        }
        if ($day && $day != '-1') {
            $averageFeedbackScoreQuery->whereDay('tgl_pelaksanaan', $day);
        }
        $averageFeedbackScore = $averageFeedbackScoreQuery->value('average_score');

        // 4. Average Feedback Training Score
        $averageFeedbackTrainingScoreQuery = Feedback_report::query();

        if ($year && $year != '-1') {
            $averageFeedbackTrainingScoreQuery->whereYear('tgl_pelaksanaan', $year);
        }
        if ($month && $month != '-1') {
            $averageFeedbackTrainingScoreQuery->whereMonth('tgl_pelaksanaan', $month);
        }
        if ($day && $day != '-1') {
            $averageFeedbackTrainingScoreQuery->whereDay('tgl_pelaksanaan', $day);
        }
        $averageFeedbackTrainingScore = $averageFeedbackTrainingScoreQuery->avg('score');

        return response()->json([
            'locationData' => $locationData,
            'countSTCWGauge' => $countSTCWGauge,
            'countNonSTCWGauge' => $countNonSTCWGauge,
            'stcwDelta' => $stcwDelta,
            'nonStcwDelta' => $nonStcwDelta,
            'trendRevenueData' => $trendRevenueData,
            'getPesertaCount' => $getPesertaCount,
            'rawProfits' => $rawProfits,
            'averageFeedbackScore' => round($averageFeedbackScore, 2),
            'averageFeedbackTrainingScore' => round($averageFeedbackTrainingScore, 2),
        ]);
    }

    public function getTrendData(Request $request)
    {
        // Optional year filter from the request
        $year = $request->input('year', date('Y'));

        $monthlyData = Infografis_peserta::select(
                DB::raw('MONTH(tgl_pelaksanaan) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('tgl_pelaksanaan', $year) // Filter by year if specified
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Prepare data for each month
        $dataPoints = [];
        for ($i = 1; $i <= 12; $i++) {
            $count = $monthlyData->firstWhere('month', $i)?->total ?? 0;
            $dataPoints[] = [
                "label" => DateTime::createFromFormat('!m', $i)->format('F'),
                "y" => $count
            ];
        }

        return response()->json([
            'dataPoints' => $dataPoints,
        ]);
    }

    public function getEvents(Request $request)
    {
        $currentYear = date('Y');
        // Fetch all records from Penlat_batch with batch and date
        $batches = Penlat_batch::select('batch', 'date')->whereYear('date', $currentYear)->get();

        // Create events array for FullCalendar
        $events = [];

        foreach ($batches as $batch) {
            $events[] = [
                'title' => $batch->batch,
                'start' => $batch->date, // Using the date from the database
                'end'   => $batch->date, // Assuming one-day events
                'className' => 'bg-' . collect(['primary', 'success', 'danger', 'warning', 'info'])->random() // Random class
            ];
        }

        return response()->json($events);
    }
}
