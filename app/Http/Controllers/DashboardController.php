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

    public function fetchChartsData(Request $request)
    {
        $periode = $request->input('periode', '2024-01-01 - 2024-12-31');
        $type = $request->input('type', '-1');
        [$startDate, $endDate] = explode(' - ', $periode);

        $filtersType = function ($query) use ($startDate, $endDate, $type) {
            if ($type && $type != '-1') {
                $query->where('jenis_pelatihan', $type);
            }
        };

        $filtersType = function ($query) use ($startDate, $endDate, $type) {
            if ($type && $type != '-1') {
                $query->where('jenis_pelatihan', $type);
            }
        };

        // Parse the start and end dates
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Generate dynamic months between the range
        $months = collect();
        while ($start <= $end) {
            $months->push($start->format('Y-m'));
            $start->addMonth();
        }

        // Fetch and map STCW data
        $dataByMonthSTCW = Infografis_peserta::select(DB::raw('DATE_FORMAT(tgl_pelaksanaan, "%Y-%m") as month'), DB::raw('count(*) as total'))
            ->whereBetween('tgl_pelaksanaan', [$startDate, $endDate])
            ->where($filtersType)
            ->where('kategori_program', 'STCW')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Fetch and map NON STCW data
        $dataByMonthNonSTCW = Infografis_peserta::select(DB::raw('DATE_FORMAT(tgl_pelaksanaan, "%Y-%m") as month'), DB::raw('count(*) as total'))
            ->whereBetween('tgl_pelaksanaan', [$startDate, $endDate])
            ->where($filtersType)
            ->where('kategori_program', 'NON STCW')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Map data into dynamic months
        $dataPoints1 = [];
        $dataPoints2 = [];
        foreach ($months as $month) {
            $dataPoints1[] = [
                "label" => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                "y" => $dataByMonthSTCW[$month] ?? 0
            ];
            $dataPoints2[] = [
                "label" => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                "y" => $dataByMonthNonSTCW[$month] ?? 0
            ];
        }

        return response()->json([
            "dataPoints1" => $dataPoints1, // STCW Data
            "dataPoints2" => $dataPoints2  // NON STCW Data
        ]);
    }

    public function fetchTrendRevenueData(Request $request)
    {
        $periode = $request->input('periode', '2024-01-01 - 2024-12-31'); // Default range
        $type = $request->input('type');
        [$startDate, $endDate] = explode(' - ', $periode); // Split into start and end dates

        // Query profits within the selected date range
        $profits = Profit::select(
                'penlat.description', // Get the description from Penlat
                DB::raw('SUM(CAST(profits.total_biaya_pendaftaran_peserta AS UNSIGNED)) as total_biaya')
            )
            ->join('penlat_batch', 'profits.pelaksanaan', '=', 'penlat_batch.batch') // Join with penlat_batch
            ->join('penlat', 'penlat_batch.penlat_id', '=', 'penlat.id') // Join with penlat
            ->whereBetween('profits.tgl_pelaksanaan', [$startDate, $endDate]) // Filter by date range
            ->when($type && $type != '-1', function ($query) use ($type) {
                // Apply type filter if a valid type is selected
                $query->where('penlat.jenis_pelatihan', $type);
            })
            ->groupBy('penlat_batch.penlat_id', 'penlat.description') // Group by penlat_id and description
            ->orderByDesc('total_biaya') // Order by total_biaya
            ->get();

        // Map results for JSON response
        $chartData = $profits->map(function ($item) {
            return [
                "label" => $item->description,
                "y" => (int) $item->total_biaya
            ];
        });

        return response()->json($chartData);
    }

    public function fetchLocationChartData(Request $request)
    {
        $periode = $request->input('periode', '2024-01-01 - 2024-12-31'); // Default date range
        $type = $request->input('type');
        [$startDate, $endDate] = explode(' - ', $periode); // Split into start and end dates

        $filtersType = function ($query) use ($startDate, $endDate, $type) {
            if ($type && $type != '-1') {
                $query->where('jenis_pelatihan', $type);
            }
        };

        // Query the number of participants by location
        $locationData = Infografis_peserta::select(
                'tempat_pelaksanaan', // Location
                DB::raw('count(*) as total') // Count participants
            )
            ->whereBetween('tgl_pelaksanaan', [$startDate, $endDate]) // Filter by date range
            ->where($filtersType)
            ->groupBy('tempat_pelaksanaan') // Group by location
            ->orderByDesc('total') // Order by total in descending order
            ->get();

        // Map results for JSON response
        $chartData = $locationData->map(function ($item) {
            return [
                "label" => $item->tempat_pelaksanaan, // Location name
                "y" => (int) $item->total // Number of participants
            ];
        });

        return response()->json($chartData);
    }

    public function fetchTrainingTypeChartData(Request $request)
    {
        $periode = $request->input('periode', '2024-01-01 - 2024-12-31'); // Default date range
        $type = $request->input('type');
        [$startDate, $endDate] = explode(' - ', $periode); // Split into start and end dates

        $filtersType = function ($query) use ($startDate, $endDate, $type) {
            if ($type && $type != '-1') {
                $query->where('jenis_pelatihan', $type);
            }
        };

        // Query the number of participants grouped by training type
        $trainingTypeData = Infografis_peserta::select(
                'jenis_pelatihan', // Training type
                DB::raw('count(*) as total') // Count participants
            )
            ->whereBetween('tgl_pelaksanaan', [$startDate, $endDate]) // Filter by date range
            ->where($filtersType)
            ->groupBy('jenis_pelatihan') // Group by training type
            ->orderByDesc('total') // Order by total in descending order
            ->get();

        // Map results for JSON response
        $chartData = $trainingTypeData->map(function ($item) {
            return [
                "label" => $item->jenis_pelatihan, // Training type name
                "y" => (int) $item->total // Number of participants
            ];
        });

        return response()->json($chartData);
    }

    public function fetchOverallData(Request $request)
    {
        $periode = $request->input('periode', '2024-01-01 - 2024-12-31');
        $type = $request->input('type');
        [$startDate, $endDate] = explode(' - ', $periode);

        // Parse the start and end dates
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Generate dynamic months between the range
        $months = collect();
        while ($start <= $end) {
            $months->push($start->format('Y-m'));
            $start->addMonth();
        }

        $filtersType = function ($query) use ($startDate, $endDate, $type) {
            if ($type && $type != '-1') {
                $query->where('jenis_pelatihan', $type);
            }
        };

        // Fetch overall participant data
        $dataByMonth = Infografis_peserta::select(DB::raw('DATE_FORMAT(tgl_pelaksanaan, "%Y-%m") as month'), DB::raw('count(*) as total'))
            ->whereBetween('tgl_pelaksanaan', [$startDate, $endDate])
            ->where($filtersType)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Map results into dynamic months
        $dataPoints = [];
        foreach ($months as $month) {
            $dataPoints[] = [
                "label" => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                "y" => $dataByMonth[$month] ?? 0
            ];
        }

        return response()->json([
            "dataPoints" => $dataPoints
        ]);
    }

    public function fetchAmountData(Request $request)
    {
        $periode = $request->input('periode', "2024-01-03 - 2024-12-21");
        $type = $request->input('type');
        [$startDate, $endDate] = explode(' - ', $periode);

        // Common filters
        $filters = function ($query) use ($startDate, $endDate, $type) {
            $query->whereBetween('tgl_pelaksanaan', [$startDate, $endDate]);
        };
        $filterBatch = function ($query) use ($startDate, $endDate, $type) {
            $query->whereBetween('date', [$startDate, $endDate]);
        };
        $filtersType = function ($query) use ($startDate, $endDate, $type) {
            if ($type && $type != '-1') {
                $query->where('jenis_pelatihan', $type);
            }
        };

        // 1. Count of Peserta
        $getPesertaCount = Infografis_peserta::query()
            ->where($filters)->where($filtersType)
            ->count();

        // 2. Sum of Profits and Costs
        $filtersType2 = function ($query) use ($type, $filters) {
            // Apply filters for Penlat or other relationships dynamically
            $query->where($filters);
            if ($type && $type != '-1') {
                $query->whereHas('batch.penlat', function ($subQuery) use ($type) {
                    $subQuery->where('jenis_pelatihan', $type);
                });
            }
        };

        // Sum of Profits and Costs
        $profitQuery = Profit::query()->where($filtersType2);

        // Calculate raw profits
        $rawProfits = $profitQuery->sum('total_biaya_pendaftaran_peserta');

        // Calculate raw costs
        $rawCosts = $profitQuery->get()->sum(function ($item) {
            return (int) $item->biaya_instruktur +
                (int) $item->total_pnbp +
                (int) $item->penagihan_foto +
                (int) $item->biaya_transportasi_hari +
                (int) $item->penagihan_atk +
                (int) $item->penagihan_snack +
                (int) $item->penagihan_makan_siang +
                (int) $item->penlat_usage +
                (int) $item->penagihan_laundry;
        });

        // 3. Average Feedback Score
        $averageFeedbackScore = DB::table('feedback_mtc')
            ->whereBetween('tgl_pelaksanaan', [$startDate, $endDate])
            ->selectRaw('
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
            ')
            ->value('average_score');

        // 4. Average Feedback Training Score
        $averageFeedbackTrainingScore = Feedback_report::query()
            ->where($filters)
            ->avg('score');

        // 5. Total Training Count
        $filterDateBatch = function ($query) use ($startDate, $endDate, $type) {
            $query->whereBetween('date', [$startDate, $endDate]);
        };
        $filterBatch = function ($query) use ($type, $filterDateBatch) {
            // Apply the base filters
            $query->where($filterDateBatch);

            // Apply type filter if provided
            if ($type && $type != '-1') {
                $query->whereHas('penlat', function ($subQuery) use ($type) {
                    $subQuery->where('jenis_pelatihan', $type);
                });
            }
        };

        // Calculate total training with filters applied
        $totalTraining = Penlat_batch::where($filterBatch)->count();

        // Return data as JSON
        return response()->json([
            'peserta_count' => $getPesertaCount,
            'raw_profits' => $rawProfits,
            'raw_costs' => $rawCosts,
            'average_feedback_score' => $averageFeedbackScore,
            'average_feedback_training_score' => $averageFeedbackTrainingScore,
            'total_training' => $totalTraining,
        ]);
    }

    public function getIssuedCertificateData(Request $request)
    {
        $periode = $request->input('periode', '2024-01-01 - 2024-12-31');
        $type = $request->input('type');
        $stcw = $request->input('stcw');
        [$startDate, $endDate] = explode(' - ', $periode);

        // Parse the start and end dates
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Generate dynamic months between the range
        $months = collect();
        while ($start <= $end) {
            $months->push($start->format('Y-m'));
            $start->addMonth();
        }

        // Filter by type if provided
        $filtersType = function ($query) use ($type, $stcw) {
            if ($type && $type != '-1') {
                $query->where('jenis_pelatihan', $type);
            }
            if ($stcw && $stcw != '-1') {
                $query->where('kategori_program', $stcw);
            }
        };

        // Fetch data and group by month
        $dataByMonth = Infografis_peserta::with('certificateCheck')
            ->select(DB::raw('DATE_FORMAT(tgl_pelaksanaan, "%Y-%m") as month'))
            ->whereBetween('tgl_pelaksanaan', [$startDate, $endDate])
            ->where($filtersType)
            ->get()
            ->groupBy('month')
            ->map(function ($items, $month) {
                $registeredButNotYetIssued = $items->sum(function ($item) {
                    // Check if the certificateCheck relationship exists
                    return $item->certificateCheck ? 1 : 0;
                });

                $issued = $items->sum(function ($item) {
                    // Check if the certificateCheck relationship exists and status is "Issued"
                    return $item->certificateCheck && $item->certificateCheck->penlatCertificate->status == 'Issued' ? 1 : 0;
                });

                $pending = $items->count() - $registeredButNotYetIssued; // Remaining items are pending

                return [
                    'registeredButNotYetIssued' => $registeredButNotYetIssued,
                    'issued' => $issued,
                    'pending' => $pending,
                ];
            });

        // Map results into dynamic months
        $dataPointsRegisteredButNotYetIssued = [];
        $dataPointsPending = [];
        $dataPointsIssued = []; // New data points for truly issued certificates

        foreach ($months as $month) {
            $registeredButNotYetIssued = $dataByMonth[$month]['registeredButNotYetIssued'] ?? 0;
            $issued = $dataByMonth[$month]['issued'] ?? 0;
            $pending = $dataByMonth[$month]['pending'] ?? 0;

            $dataPointsRegisteredButNotYetIssued[] = [
                "label" => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                "y" => $registeredButNotYetIssued,
            ];
            $dataPointsIssued[] = [
                "label" => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                "y" => $issued,
            ];
            $dataPointsPending[] = [
                "label" => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                "y" => $pending,
            ];
        }

        return response()->json([
            "dataPointsRegisteredButNotYetIssued" => $dataPointsRegisteredButNotYetIssued,
            "dataPointsIssued" => $dataPointsIssued, // Include truly issued data
            "dataPointsPending" => $dataPointsPending,
        ]);
    }
}
