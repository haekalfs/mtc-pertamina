<?php

namespace App\Http\Controllers;

use App\Models\KPI;
use App\Models\PencapaianKPI;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class KpiController extends Controller
{
    public function index($yearSelected = null)
    {
        // Determine the current year and generate the range of years
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        // Set the selected year
        $currentYear = $yearSelected ?? $nowYear;

        $pencapaian = PencapaianKPI::whereYear('created_at', $currentYear)->get();

        // Fetch all indicators
        $indicators = Kpi::where('periode', $currentYear)->get();

        //percentages
        $percentage = 0;

        // Define the closure to be used within the with method
        $pencapaianQuery = function ($query) use ($currentYear) {
            $query->where('periode', $currentYear)->orderBy('quarter_id', 'asc');
        };

        // Fetch KPIs with their related 'pencapaian' data ordered by 'quarter_id' and filtered by 'periode'
        $kpis = Kpi::with(['pencapaian' => $pencapaianQuery])->where('periode', $currentYear)->get();

        $scores = PencapaianKPI::where('periode', $currentYear)->pluck('score');
        $totalScore = $scores->sum();
        $numScores = $scores->count();
        $averageScore = $numScores > 0 ? $totalScore / $numScores : 0;
        $percentage = round($averageScore, 2);

       // Prepare chart data
        $chartData = $kpis->map(function($kpi) {
            $randomColor = 'rgba(' . rand(0, 255) . ', ' . rand(0, 255) . ', ' . rand(0, 255) . ', 0.2)';
            $borderColor = Str::replaceLast('0.2', '1', $randomColor);

            return [
                'id' => $kpi->id,
                'title' => $kpi->indicator,
                'type' => 'bar', // or any other type you prefer
                'labels' => $kpi->pencapaian->pluck('quarter.quarter_name')->toArray(),
                'data' => $kpi->pencapaian->pluck('score')->toArray(),
                'backgroundColor' => $randomColor,
                'borderColor' => $borderColor,
            ];
        });

        // Return view with data
        return view('kpi-view.index', [
            'kpis' => $kpis,
            'percentage' => $percentage,
            'indicators' => $indicators,
            'chartData' => $chartData,
            'yearsBefore' => $yearsBefore,
            'yearSelected' => $currentYear,
            'pencapaian' => $pencapaian
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
            $pencapaianQuery->where('periode', $periode);
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
            'quarter' => 'required',
            'year' => 'required',
        ]);

        // Create a new record in the kpi_pencapaian table
        PencapaianKPI::create([
            'pencapaian' => $request->pencapaian,
            'score' => $request->score,
            'quarter_id' => $request->quarter,
            'periode' => $request->year,
            'kpi_id' => $kpi_id,
            'user_id' => auth()->id(), // assuming the user is authenticated
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Pencapaian KPI berhasil ditambahkan!');
    }

    public function pencapaian($kpi)
    {
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        $kpiItem = KPI::find($kpi);
        $kpiCurrentPeriode = $kpiItem->periode;
        $previousYear1 = $kpiCurrentPeriode - 1;
        $previousYear2 = $kpiCurrentPeriode - 2;

        // Get data for the current period and the two previous years
        $kpiData = PencapaianKPI::whereIn('periode', [$kpiCurrentPeriode, $previousYear1, $previousYear2])->where('kpi_id', $kpi)->get();

        $scores = $kpiData->pluck('score');

        // Format the data as required
        $tableData = [];
        foreach ($kpiData as $data) {
            $year = $data->periode;

            $totalScore = $scores->sum();
            $numScores = $scores->count();
            $averageScore = $numScores > 0 ? $totalScore / $numScores : 0;
            $percentage = round($averageScore, 2);

            if (!isset($tableData[$year])) {
                $tableData[$year] = [];
            }
            $tableData[$year][] = [
                'no' => $data->id,
                'pencapaian' => $data->pencapaian,
                'score' => $data->score,
                'percentage' => $percentage,
                'periode' => $data->periode,
                'quarter_id' => $data->quarter->quarter_name,
            ];
        }

        return view('kpi-view.pencapaian', [
            'kpiItem' => $kpiItem,
            'yearsBefore' => $yearsBefore,
            'tableData' => $tableData
        ]);
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
