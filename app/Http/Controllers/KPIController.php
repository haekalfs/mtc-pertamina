<?php

namespace App\Http\Controllers;

use App\Models\KPI;
use App\Models\PencapaianKPI;
use App\Models\Quarter;
use App\Models\User;
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
        // Determine the current year and generate the range of years
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        // Set the selected year
        $currentYear = $yearSelected ?? $nowYear;

        $quarters = Quarter::all();

        // Define the closure to be used within the with method
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

        // Fetch KPIs with their related 'pencapaian' data ordered by 'quarter_id' and filtered by 'periode'
        $kpis = Kpi::with(['pencapaian' => $pencapaianQuery])->get();

        // Calculate percentage and prepare data
        $data = $kpis->map(function ($kpi) {
            $kpiTarget = $kpi->target;
            $tercapai = $kpi->pencapaian->sum('score');
            return $kpiTarget > 0 ? min(($tercapai / $kpiTarget) * 100, 100) : 0;
        })->toArray();

        // Calculate the overall progress percentage
        $overallProgress = count($data) > 0 ? array_sum($data) / count($data) : 0;

        // Return view with data
        return view('kpi-view.index', [
            'kpis' => $kpis,
            'yearsBefore' => $yearsBefore,
            'selectedQuarter' => $quarterSelected,
            'yearSelected' => $currentYear,
            'overallProgress' => round($overallProgress, 2),
            'quarters' => $quarters
        ]);
    }

    public function pencapaian($kpi, $quarterSelected, $currentYear)
    {
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        $quarters = Quarter::all();

        // Define the closure to be used within the with method
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

        // Fetch a single KPI item with its related 'pencapaian' data
        $kpiItem = KPI::where('id', $kpi)
            ->with(['pencapaian' => $pencapaianQuery])
            ->first(); // Use first() instead of get() to fetch a single record

        // Calculate the percentage
        $kpiTarget = $kpiItem->target;
        $tercapai = $kpiItem->pencapaian->sum('score');
        $percentage = ($kpiTarget > 0) ? ($tercapai / $kpiTarget) * 100 : 0;

        // Prepare data for the chart
        $dataPoints = [];
        foreach ($kpiItem->pencapaian as $pencapaian) {
            $periodeStart = \Carbon\Carbon::parse($pencapaian->periode_start);
            $periodeEnd = \Carbon\Carbon::parse($pencapaian->periode_end);

            // Loop through each date from start to end
            for ($date = $periodeStart; $date->lte($periodeEnd); $date->addDay()) {
                $dataPoints[] = [
                    'x' => $date->timestamp * 1000, // CanvasJS uses timestamp in milliseconds
                    'y' => $pencapaian->score
                ];
            }
        }

        return view('kpi-view.pencapaian', [
            'kpiItem' => $kpiItem,
            'yearsBefore' => $yearsBefore,
            'percentage' => $percentage, // Pass the percentage to the view
            'dataPoints' => $dataPoints, // Pass the data points to the view
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
