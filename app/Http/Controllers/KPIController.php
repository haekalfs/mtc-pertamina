<?php

namespace App\Http\Controllers;

use App\Models\KPI;
use App\Models\PencapaianKPI;
use App\Models\Quarter;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class KpiController extends Controller
{
    public function index($encryptedQuarter = null, $encryptedYear = null)
    {
        $quarterSelected = $encryptedQuarter ? Crypt::decryptString($encryptedQuarter) : -1;
        $yearSelected = $encryptedYear ? Crypt::decryptString($encryptedYear) : null;
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);
        $currentYear = $yearSelected ?? $nowYear;

        $quarters = Quarter::all();

        $pencapaianQuery = function ($query) use ($currentYear, $quarterSelected, $quarters) {
            $query->whereYear('periode_start', $currentYear)
                ->whereYear('periode_end', $currentYear)
                ->when($quarterSelected != -1, function ($query) use ($quarters, $quarterSelected) {
                    $getQuarter = $quarters->find($quarterSelected);
                    $query->where(function($query) use ($getQuarter) {
                        $query->whereMonth('periode_start', '>=', $getQuarter->month_start)
                            ->whereMonth('periode_start', '<=', $getQuarter->month_end)
                            ->orWhere(function($query) use ($getQuarter) {
                                $query->whereMonth('periode_end', '>=', $getQuarter->month_start)
                                    ->whereMonth('periode_end', '<=', $getQuarter->month_end);
                            });
                    });
                })
                ->orderBy('periode_start', 'asc');
        };

        $kpis = Kpi::with(['pencapaian' => $pencapaianQuery])->get();

        $data = $kpis->map(function ($kpi) use ($quarterSelected) {
            $target = $quarterSelected == '-1' ? $kpi->target : $kpi->target / 4;
            $kpiTarget = $target;
            $tercapai = $kpi->pencapaian->sum('score');
            return $kpiTarget > 0 ? min(($tercapai / $kpiTarget) * 100, 100) : 0;
        })->toArray();

        $overallProgress = count($data) > 0 ? array_sum($data) / count($data) : 0;

        $kpiChartsData = [];
        foreach ($kpis as $kpi) {
            $weeklyScores = [];

            foreach ($kpi->pencapaian as $pencapaian) {
                $periodeStart = \Carbon\Carbon::parse($pencapaian->periode_start)->startOfWeek();
                $periodeEnd = \Carbon\Carbon::parse($pencapaian->periode_end);

                for ($date = $periodeStart; $date->lte($periodeEnd); $date->addWeek()) {
                    $weekStart = $date->timestamp * 1000; // CanvasJS uses timestamp in milliseconds
                    if (!isset($weeklyScores[$weekStart])) {
                        $weeklyScores[$weekStart] = 0;
                    }
                    $weeklyScores[$weekStart] += $pencapaian->score;
                }
            }

            $dataPoints = [];
            foreach ($weeklyScores as $weekStart => $score) {
                $dataPoints[] = ['x' => $weekStart, 'y' => $score];
            }

            $target = $quarterSelected == '-1' ? $kpi->target : $kpi->target / 4;

            $kpiChartsData[] = [
                'kpi_id' => $kpi->id,
                'kpiName' => $kpi->indicator,
                'kpiTarget' => $target,
                'dataPoints' => $dataPoints
            ];
        }

        return view('kpi-view.index', [
            'kpis' => $kpis,
            'yearsBefore' => $yearsBefore,
            'selectedQuarter' => $quarterSelected,
            'yearSelected' => $currentYear,
            'overallProgress' => round($overallProgress, 2),
            'quarters' => $quarters,
            'kpiChartsData' => $kpiChartsData
        ]);
    }

    public function pencapaian($kpi, $quarterSelected, $currentYear)
    {
        $quarters = Quarter::all();

        $pencapaianQuery = function ($query) use ($currentYear, $quarterSelected, $quarters) {
            $query->whereYear('periode_start', $currentYear)
                ->whereYear('periode_end', $currentYear)
                ->when($quarterSelected != -1, function ($query) use ($quarters, $quarterSelected) {
                    $getQuarter = $quarters->find($quarterSelected);
                    $query->where(function($query) use ($getQuarter) {
                        $query->whereMonth('periode_start', '>=', $getQuarter->month_start)
                            ->whereMonth('periode_start', '<=', $getQuarter->month_end)
                            ->orWhere(function($query) use ($getQuarter) {
                                $query->whereMonth('periode_end', '>=', $getQuarter->month_start)
                                        ->whereMonth('periode_end', '<=', $getQuarter->month_end);
                            });
                    });
                })
                ->orderBy('periode_start', 'asc');
        };

        $kpiItem = KPI::where('id', $kpi)
            ->with(['pencapaian' => $pencapaianQuery])
            ->first();

        $kpiTarget = $kpiItem->target / 4;
        $tercapai = $kpiItem->pencapaian->sum('score');
        $percentage = ($kpiTarget > 0) ? min(($tercapai / $kpiTarget) * 100, 100) : 0;

        $weeklyScores = [];

        foreach ($kpiItem->pencapaian as $pencapaian) {
            $start = Carbon::parse($pencapaian->periode_start);
            $end = Carbon::parse($pencapaian->periode_end);
            $score = $pencapaian->score;

            // Move $start to the start of the week if it's not already
            $start = $start->startOfWeek();

            // Loop through each week in the period
            for ($date = $start; $date->lte($end); $date->addWeek()) {
                $weekStart = $date->timestamp * 1000; // CanvasJS uses timestamp in milliseconds
                if (!isset($weeklyScores[$weekStart])) {
                    $weeklyScores[$weekStart] = 0;
                }
                // Accumulate score for the week
                $weeklyScores[$weekStart] += $score;
            }
        }

        $dataPoints = [];
        foreach ($weeklyScores as $weekStart => $score) {
            $dataPoints[] = ['x' => $weekStart, 'y' => $score];
        }

        $quarterStart = 1;
        $quarterEnd = 12;
        if($quarterSelected != -1){
            $quarter = Quarter::find($quarterSelected);
            $quarterStart = $quarter->month_start;
            $quarterEnd = $quarter->month_end;
        }

        $startDate = Carbon::create($currentYear, $quarterStart, 1);
        $endDate = Carbon::create($currentYear, $quarterEnd, 1)->endOfMonth();

        return view('kpi-view.pencapaian', [
            'kpiItem' => $kpiItem,
            'percentage' => $percentage,
            'dataPoints' => $dataPoints,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'selectedQuarter' => $quarterSelected
        ]);
    }

    public function manage()
    {
        $indicators = KPI::all();
        return view('kpi-view.admin.manage', ['indicators' => $indicators]);
    }

    public function preview($kpi)
    {
        $kpiItem = KPI::find($kpi);
        return view('kpi-view.admin.preview', ['kpiItem' => $kpiItem]);
    }

    public function report(Request $request, $kpi = 7, $periode = 1)
    {
        // Determine the current year and generate the range of years
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        // Set the selected year
        $periode = $periode ?? $nowYear;

        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            'indicator' => 'required',
            'periode' => 'required'
        ]);

        // Extract period dates from the request
        $periode = $request->periode;
        $kpi = $request->nilai_akhlak;

        // Get all akhlak points
        $kpiPoin = KPI::all();

        $pencapaianQuery = PencapaianKPI::query();

        if ($periode != 1) {
            $pencapaianQuery->where('periode_start', $periode);
        }

        if ($kpi != 7) {
            $pencapaianQuery->where('kpi_id', $kpi);
        }

        $pencapaian = $pencapaianQuery->get();

        return view('kpi-view.admin.report', [
            'pencapaian' => $pencapaian,
            'yearsBefore' => $yearsBefore,
            'periode' => $periode,
            'kpiSelected' => $kpi,
            'kpiPoin' => $kpiPoin,
            'periode' => $periode
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kpi' => 'required',
            'target' => 'required',
            'periode' => 'required'
        ]);

        $kpi = new KPI();
        $kpi->indicator = $request->input('kpi');
        $kpi->target = $request->input('target');
        $kpi->periode = $request->input('periode');
        $kpi->save();

        return redirect()->back()->with('success', 'KPI has been added successfully');
    }

    public function store_pencapaian(Request $request, $kpi_id)
    {
        // Validate the request data
        $request->validate([
            'pencapaian' => 'required|string|max:255',
            'score' => 'required|integer',
            'daterange' => 'required|string',
        ]);

        // Split the daterange into start and end dates
        $daterange = explode(' - ', $request->daterange);
        $periode_start = Carbon::createFromFormat('m/d/Y', trim($daterange[0]))->format('Y-m-d');
        $periode_end = Carbon::createFromFormat('m/d/Y', trim($daterange[1]))->format('Y-m-d');

        // Create a new record in the kpi_pencapaian table
        PencapaianKPI::create([
            'pencapaian' => $request->pencapaian,
            'score' => $request->score,
            'periode_start' => $periode_start,
            'periode_end' => $periode_end,
            'kpi_id' => $kpi_id,
            'user_id' => auth()->id(), // assuming the user is authenticated
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Pencapaian KPI berhasil ditambahkan!');
    }

    // Other resource methods (index, show, etc.) can be here

    /**
     * Remove the specified KPI from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $kpi = Kpi::findOrFail($id);
        $kpi->delete();

        return redirect()->back()->with('success', 'KPI deleted successfully');
    }

    public function destroy_pencapaian($id)
    {
        $kpi = PencapaianKPI::findOrFail($id);
        $kpi_id = $kpi->kpi_id;
        $kpi->delete();

        return redirect()->back()->with('success', 'Pencapaian KPI deleted successfully');
    }
}
