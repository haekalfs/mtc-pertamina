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
use App\Models\Receivables_participant_certificate;
use App\Models\Regulation;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

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
        // Get start and end date from request
        $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
        $endDate = Carbon::parse($request->end_date)->format('Y-m-d');

        // Fetch only events within the selected range
        $batches = Penlat_batch::select('batch', 'date')
                            ->whereBetween('date', [$startDate, $endDate])
                            ->get();

        $events = $batches->map(function ($batch) {
            return [
                'title' => $batch->batch,
                'start' => Carbon::parse($batch->date)->format('Y-m-d'),
                'end' => Carbon::parse($batch->date)->format('Y-m-d'),
                'className' => 'bg-' . collect(['primary', 'success', 'danger', 'warning', 'info'])->random()
            ];
        });

        return response()->json($events);
    }

    public function getInfografisPeserta(Request $request)
    {
        $batch = $request->query('batch');

        $query = Infografis_peserta::where('batch', $batch);

        return DataTables::of($query)->make(true);
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
            $parts = explode("-", $month);
            $monthOnly = $parts[1]; // "01"
            $dateObj = DateTime::createFromFormat('!m', $monthOnly);
            $monthName = $dateObj->format('M'); // Full month name (e.g., January)

            $dataPoints1[] = [
                "label" => $monthName . ' ' . $parts[0],
                "month" => Carbon::createFromFormat('Y-m', $month)->format('m'),
                "year" => Carbon::createFromFormat('Y-m', $month)->year,
                "y" => $dataByMonthSTCW[$month] ?? 0
            ];
            $dataPoints2[] = [
                "label" => $monthName . ' ' . $parts[0],
                "month" => Carbon::createFromFormat('Y-m', $month)->format('m'),
                "year" => Carbon::createFromFormat('Y-m', $month)->year,
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
        [$startDate, $endDate] = explode(' - ', $periode);

        $profits = Infografis_peserta::select(
                'penlat.description',
                DB::raw('SUM(CAST(infografis_peserta.harga_pelatihan AS UNSIGNED)) as total_biaya')
            )
            ->join('penlat_batch', 'infografis_peserta.batch', '=', 'penlat_batch.batch')
            ->join('penlat', 'penlat_batch.penlat_id', '=', 'penlat.id')
            ->whereBetween('infografis_peserta.tgl_pelaksanaan', [$startDate, $endDate])
            ->when($type && $type != '-1', function ($query) use ($type) {
                $query->where('penlat.jenis_pelatihan', $type);
            })
            ->groupBy('penlat_batch.penlat_id', 'penlat.description')
            ->orderBy('total_biaya', 'desc')
            ->limit(10)
            ->get();

        $chartData = $profits->map(function ($item) {
            return [
                "label" => $item->description,
                "y" => (int) $item->total_biaya
            ];
        });

        return response()->json($chartData);
    }

    public function fetchDrilldownRevenueData(Request $request)
    {
        $periode = $request->input('periode', '2024-01-01 - 2024-12-31');
        $description = $request->input('description');
        [$startDate, $endDate] = explode(' - ', $periode);

        // Query the participants grouped by month and year for the selected location
        $drilldownData = Infografis_peserta::select(
                DB::raw("DATE_FORMAT(tgl_pelaksanaan, '%Y-%m') as ordering"), // Ordering format (YYYY-MM)
                DB::raw("DATE_FORMAT(tgl_pelaksanaan, '%b %Y') as month_year"), // Short month name (e.g., Jan 2024)
                DB::raw('count(*) as total'),
                DB::raw('SUM(CAST(harga_pelatihan AS UNSIGNED)) as total_biaya')
            )
            ->join('penlat_batch', 'infografis_peserta.batch', '=', 'penlat_batch.batch')
            ->join('penlat', 'penlat_batch.penlat_id', '=', 'penlat.id')
            ->whereBetween('tgl_pelaksanaan', [$startDate, $endDate])
            ->where('penlat.description', $description)
            ->groupBy('ordering', 'month_year')
            ->orderBy('ordering')
            ->get();

        // Map results for JSON response
        $chartData = $drilldownData->map(function ($item) {
            return [
                "label" => $item->month_year, // Month-Year
                "y" => (int) $item->total_biaya // Number of participants
            ];
        });

        return response()->json($chartData);
    }

    public function fetchParticipantsByRevenue(Request $request)
    {
        $periode = $request->input('periode', '2024-01-01 - 2024-12-31'); // Default date range
        $description = $request->input('description');
        $period = $request->input('period');

        $dateObj = DateTime::createFromFormat('M Y', $period);
        $period = $dateObj ? $dateObj->format('Y-m') : null;

        [$startDate, $endDate] = explode(' - ', $periode);

        // Query participants for the selected location and period
        $participants = Infografis_peserta::whereBetween('tgl_pelaksanaan', [$startDate, $endDate]) // Filter by date range
            ->join('penlat_batch', 'infografis_peserta.batch', '=', 'penlat_batch.batch')
            ->join('penlat', 'penlat_batch.penlat_id', '=', 'penlat.id')
            ->whereBetween('tgl_pelaksanaan', [$startDate, $endDate])
            ->where('penlat.description', $description)
            ->where(DB::raw("DATE_FORMAT(tgl_pelaksanaan, '%Y-%m')"), '=', $period) // Filter by period (Month-Year)
            ->get(); // Select relevant fields

        // Map results for JSON response
        $participantData = $participants->map(function ($item) {
            return [
                'tgl_pelaksanaan' => $item->tgl_pelaksanaan,
                'nama_peserta' => $item->nama_peserta,
                'batch' => $item->batch,
                'jenis_pelatihan' => $item->jenis_pelatihan,
                'kategori_program' => $item->kategori_program,
                'harga_pelatihan' => number_format($item->harga_pelatihan, 0, ',', '.'),
                'realisasi' => $item->realisasi,
            ];
        });

        return response()->json($participantData);
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

    public function fetchDrilldownChartData(Request $request)
    {
        $periode = $request->input('periode', '2024-01-01 - 2024-12-31');
        $location = $request->input('location');

        [$startDate, $endDate] = explode(' - ', $periode);

        // Query the participants grouped by month and year for the selected location
        $query = Infografis_peserta::select(
                DB::raw("DATE_FORMAT(tgl_pelaksanaan, '%Y-%m') as ordering"), // Ordering format (YYYY-MM)
                DB::raw("DATE_FORMAT(tgl_pelaksanaan, '%b %Y') as month_year"), // Short month name (e.g., Jan 2024)
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('tgl_pelaksanaan', [$startDate, $endDate])
            ->groupBy('ordering', 'month_year')
            ->orderBy('ordering');

        // Apply location filter only if provided
        if (!empty($location)) {
            $query->where('tempat_pelaksanaan', $location);
        }

        $drilldownData = $query->get();

        // Map results for JSON response
        $chartData = $drilldownData->map(function ($item) {
            return [
                "label" => $item->month_year, // Short Month-Year (e.g., Jan 2024)
                "y" => (int) $item->total // Number of participants
            ];
        });

        return response()->json($chartData);
    }

    public function fetchParticipantsByLocation(Request $request)
    {
        $periode = $request->input('periode', '2024-01-01 - 2024-12-31'); // Default date range
        $location = $request->input('location');
        $period = $request->input('period'); // Example: "Jan 2024"

        $dateObj = DateTime::createFromFormat('M Y', $period);
        $period = $dateObj ? $dateObj->format('Y-m') : null;

        [$startDate, $endDate] = explode(' - ', $periode);

        // Query participants for the selected location and period
        $participants = Infografis_peserta::whereBetween('tgl_pelaksanaan', [$startDate, $endDate]) // Filter by date range
            ->where('tempat_pelaksanaan', $location) // Filter by location
            ->where(DB::raw("DATE_FORMAT(tgl_pelaksanaan, '%Y-%m')"), '=', $period) // Filter by period (Month-Year)
            ->get(['tgl_pelaksanaan', 'nama_peserta', 'batch', 'jenis_pelatihan', 'kategori_program', 'harga_pelatihan', 'realisasi']); // Select relevant fields

        // Map results for JSON response
        $participantData = $participants->map(function ($item) {
            return [
                'tgl_pelaksanaan' => $item->tgl_pelaksanaan,
                'nama_peserta' => $item->nama_peserta,
                'batch' => $item->batch,
                'jenis_pelatihan' => $item->jenis_pelatihan,
                'kategori_program' => $item->kategori_program,
                'harga_pelatihan' => number_format($item->harga_pelatihan, 0, ',', '.'),
                'realisasi' => $item->realisasi,
            ];
        });

        return response()->json($participantData);
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

    public function fetchTrainingTypeDrilldownData(Request $request)
    {
        $periode = $request->input('periode', '2024-01-01 - 2024-12-31'); // Default date range
        $type = $request->input('type'); // Selected type
        [$startDate, $endDate] = explode(' - ', $periode); // Split into start and end dates

        // Query for drilldown data
        $drilldownData = Infografis_peserta::select(
                DB::raw("DATE_FORMAT(tgl_pelaksanaan, '%Y-%m') as ordering"), // Ordering format (YYYY-MM)
                DB::raw("DATE_FORMAT(tgl_pelaksanaan, '%b %Y') as period"), // Short month name (e.g., Jan 2024)
                DB::raw('count(*) as total') // Count participants
            )
            ->whereBetween('tgl_pelaksanaan', [$startDate, $endDate]) // Filter by date range
            ->where('jenis_pelatihan', $type) // Filter by type
            ->groupBy('ordering', 'period')
            ->orderBy('ordering')
            ->get();

        // Map results for JSON response
        $chartData = $drilldownData->map(function ($item) {
            return [
                "label" => $item->period, // Period (Month-Year)
                "y" => (int) $item->total // Total participants
            ];
        });

        return response()->json($chartData);
    }

    public function fetchParticipantsByTrainingType(Request $request)
    {
        $periode = $request->input('periode', '2024-01-01 - 2024-12-31'); // Default date range
        $type = $request->input('type'); // Selected type
        $period = $request->input('period'); // The selected period for drilldown (Month-Year)

        $dateObj = DateTime::createFromFormat('M Y', $period);
        $period = $dateObj ? $dateObj->format('Y-m') : null;

        [$startDate, $endDate] = explode(' - ', $periode); // Split into start and end dates

        // Query participants for the selected training type and period
        $participants = Infografis_peserta::whereBetween('tgl_pelaksanaan', [$startDate, $endDate]) // Filter by date range
            ->where('jenis_pelatihan', $type) // Filter by training type
            ->where(DB::raw('DATE_FORMAT(tgl_pelaksanaan, "%Y-%m")'), $period) // Filter by period (Month-Year)
            ->get(['tgl_pelaksanaan', 'nama_peserta', 'batch', 'jenis_pelatihan', 'kategori_program', 'harga_pelatihan', 'realisasi']); // Select relevant fields

        // Map results for JSON response
        $participantData = $participants->map(function ($item) {
            return [
                'tgl_pelaksanaan' => $item->tgl_pelaksanaan,
                'nama_peserta' => $item->nama_peserta,
                'batch' => $item->batch,
                'jenis_pelatihan' => $item->jenis_pelatihan,
                'kategori_program' => $item->kategori_program,
                'harga_pelatihan' => number_format($item->harga_pelatihan, 0, ',', '.'),
                'realisasi' => $item->realisasi,
            ];
        });

        return response()->json($participantData);
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
            $parts = explode("-", $month);
            $monthOnly = $parts[1]; // "01"
            $dateObj = DateTime::createFromFormat('!m', $monthOnly);
            $monthName = $dateObj->format('M'); // Full month name (e.g., January)

            $dataPoints[] = [
                "label" => $monthName . ' ' . $parts[0],
                "y" => $dataByMonth[$month] ?? 0
            ];
        }

        return response()->json([
            "dataPoints" => $dataPoints
        ]);
    }

    public function fetchParticipantsByOverall(Request $request)
    {
        $month = Carbon::parse($request->month)->month;
        $year = $request->year;
        $status = $request->status;

        // Base query filtered by month and year
        $query = Infografis_peserta::query()
            ->whereMonth('tgl_pelaksanaan', $month)
            ->whereYear('tgl_pelaksanaan', $year);

        // Return the DataTable response
        return DataTables::of($query)
            ->editColumn('tgl_pelaksanaan', function ($row) {
                return Carbon::parse($row->tgl_pelaksanaan)->format('d-m-Y');
            })
            ->addColumn('harga_pelatihan', function ($row) {
                return number_format($row->harga_pelatihan, 0, ',', '.');
            })
            ->make(true);
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

        // Build the query for Infografis_peserta with optional filters
        $infografisQuery = Infografis_peserta::select(DB::raw('DATE_FORMAT(tgl_pelaksanaan, "%Y-%m") as month'), 'id')
            ->whereBetween('tgl_pelaksanaan', [$startDate, $endDate]);

        if ($type && $type != '-1') {
            $infografisQuery->where('jenis_pelatihan', $type);
        }
        if ($stcw && $stcw != '-1') {
            $infografisQuery->where('kategori_program', $stcw);
        }

        // Fetch all relevant Infografis_peserta data
        $infografisData = $infografisQuery->get();

        // Get all `Receivables_participant_certificate` IDs
        $certificateData = Receivables_participant_certificate::select('infografis_peserta_id', 'status')->get();

        // Group the certificates by `infografis_peserta_id`
        $certificatesById = $certificateData->groupBy('infografis_peserta_id');

        // Calculate monthly statistics
        $dataByMonth = $infografisData->groupBy('month')->map(function ($items, $month) use ($certificatesById) {
            $registeredButNotYetIssued = 0;
            $issued = 0;
            $pending = 0;

            foreach ($items as $item) {
                $certificates = $certificatesById[$item->id] ?? collect();

                // Issued certificates (status = 1)
                $issued += $certificates->where('status', 1)->count();

                // Registered but not yet issued (status = 0 or null)
                $registeredButNotYetIssued += $certificates->whereIn('status', [0, null])->count();

                // Pending (Infografis_peserta IDs without any matching certificate)
                if ($certificates->isEmpty()) {
                    $pending++;
                }
            }

            return [
                'registeredButNotYetIssued' => $registeredButNotYetIssued,
                'issued' => $issued,
                'pending' => $pending,
            ];
        });

        // Map results into dynamic months
        $dataPointsRegisteredButNotYetIssued = [];
        $dataPointsPending = [];
        $dataPointsIssued = [];

        foreach ($months as $month) {
            $registeredButNotYetIssued = $dataByMonth[$month]['registeredButNotYetIssued'] ?? 0;
            $issued = $dataByMonth[$month]['issued'] ?? 0;
            $pending = $dataByMonth[$month]['pending'] ?? 0;

            $parts = explode("-", $month);
            $monthOnly = $parts[1]; // "01"
            $dateObj = DateTime::createFromFormat('!m', $monthOnly);
            $monthName = $dateObj->format('M'); // Full month name (e.g., January)

            $dataPointsRegisteredButNotYetIssued[] = [
                "label" => $monthName . ' ' . $parts[0],
                "y" => $registeredButNotYetIssued,
                "year" => Carbon::createFromFormat('Y-m', $month)->year,
            ];
            $dataPointsIssued[] = [
                "label" => $monthName . ' ' . $parts[0],
                "y" => $issued,
                "year" => Carbon::createFromFormat('Y-m', $month)->year,
            ];
            $dataPointsPending[] = [
                "label" => $monthName . ' ' . $parts[0],
                "y" => $pending,
                "year" => Carbon::createFromFormat('Y-m', $month)->year,
            ];
        }

        return response()->json([
            "dataPointsRegisteredButNotYetIssued" => $dataPointsRegisteredButNotYetIssued,
            "dataPointsIssued" => $dataPointsIssued,
            "dataPointsPending" => $dataPointsPending,
        ]);
    }

    public function getChartDetail(Request $request)
    {
        $month = Carbon::parse($request->month)->month;
        $year = $request->year;
        $status = $request->status;

        // Base query filtered by month and year
        $query = Infografis_peserta::query()
            ->whereMonth('tgl_pelaksanaan', $month)
            ->whereYear('tgl_pelaksanaan', $year);

        // Adjust query based on status using the relationship
        if ($status === 'Pending Certificates') {
            $query->whereHas('certificateCheck', function ($q) {
                $q->whereNull('status')->orWhere('status', 0);
            });
        } elseif ($status === 'Issued Certificates') {
            $query->whereHas('certificateCheck', function ($q) {
                $q->where('status', 1);
            });
        } elseif ($status === 'Not Registered') {
            $query->doesntHave('certificateCheck');
        }

        // Return data for Yajra DataTables
        return DataTables::of($query)
            ->addColumn('tgl_pelaksanaan', function ($row) {
                return Carbon::parse($row->tgl_pelaksanaan)->format('d-m-Y');
            })
            ->addColumn('harga_pelatihan', function ($row) {
                return number_format($row->harga_pelatihan, 0, ',', '.');
            })
            ->make(true);
    }

    public function getParticipants(Request $request)
    {
        $month = Carbon::parse($request->month)->month;
        $year = $request->year;
        $status = $request->status;

        // Base query filtered by month and year
        $query = Infografis_peserta::query()
            ->whereMonth('tgl_pelaksanaan', $month)
            ->whereYear('tgl_pelaksanaan', $year);

        // Apply filters based on status
        if ($status === 'STCW') {
            $query->where('kategori_program', 'STCW');
        } elseif ($status === 'NON STCW') {
            $query->where('kategori_program', 'NON STCW');
        }

        // Return the DataTable response
        return DataTables::of($query)
            ->editColumn('tgl_pelaksanaan', function ($row) {
                return Carbon::parse($row->tgl_pelaksanaan)->format('d-m-Y');
            })
            ->addColumn('harga_pelatihan', function ($row) {
                return number_format($row->harga_pelatihan, 0, ',', '.');
            })
            ->make(true);
    }

    public function fetchClickedParticipantonCalendar(Request $request)
    {
        try {
            // Ensure batch is correctly passed and sanitized
            $batch = urldecode($request->query('batch'));

            if (!$batch) {
                return response()->json(['error' => 'Batch parameter is missing'], 400);
            }

            // Fetch participants
            $participants = Infografis_peserta::where('batch', $batch)->get();

            return DataTables::of($participants)->toJson();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }
}
