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
use Dompdf\Dompdf;
use Dompdf\Options;

class KpiController extends Controller
{
    public function index($encryptedQuarter = null, $encryptedYear = null)
    {
        //decrypting
        $quarterSelected = $encryptedQuarter ? Crypt::decryptString($encryptedQuarter) : -1;
        $yearSelected = $encryptedYear ? Crypt::decryptString($encryptedYear) : null;

        //date
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

        $kpis = Kpi::with(['pencapaian' => $pencapaianQuery])->where('periode', $currentYear)->get();

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

        $quarterRow = Quarter::find($quarterSelected);

        return view('kpi-view.index', [
            'kpis' => $kpis,
            'yearsBefore' => $yearsBefore,
            'selectedQuarter' => $quarterSelected,
            'yearSelected' => $currentYear,
            'overallProgress' => round($overallProgress, 2),
            'quarters' => $quarters,
            'kpiChartsData' => $kpiChartsData,
            'quarterRow' => $quarterRow
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

        if($quarterSelected == '-1'){
            $target = $kpiItem->target;
        } else {
            $target = $kpiItem->target / 4;
        }

        $kpiTarget = $target;
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

    public function manage($yearSelected = null)
    {
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);
        $yearSelected = $yearSelected ?? $nowYear;

        $indicators = KPI::where('periode', $yearSelected)->get();

        return view('kpi-view.admin.manage', ['indicators' => $indicators, 'yearsBefore' => $yearsBefore, 'yearSelected' => $yearSelected]);
    }

    public function preview($kpi)
    {
        $kpiItem = KPI::find($kpi);
        return view('kpi-view.admin.preview', ['kpiItem' => $kpiItem]);
    }

    public function report(Request $request)
    {
        // Determine the current year and generate the range of years
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        // Get the selected values from the request or use default
        $periode = $request->periode ?? $nowYear;
        $indicator = $request->indicator ?? '-1';

        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            'indicator' => 'required',
            'periode' => 'required'
        ]);

        // Prepare the query with conditional filtering
        $query = KPI::query();

        // Apply indicator filter if a specific indicator is selected
        if ($indicator != '-1') {
            $query->where('id', $indicator);
        }

        // Apply periode filter if a specific period is selected
        if ($periode) {
            $query->where('periode', $periode);
        }

        // Fetch the filtered KPIs
        $kpis = $query->get();

        $allKPI = KPI::where('periode', $periode)->get();

        $data = [];
        $overallKPI = [
            'Q1' => 0,
            'Q2' => 0,
            'Q3' => 0,
            'Q4' => 0,
        ];

        $indicatorCount = 0;

        foreach ($kpis as $kpi) {
            $pencapaianData = PencapaianKPI::where('kpi_id', $kpi->id)->orderBy('periode_start', 'asc')->get();

            // Initialize quarters with zero values
            $quarters = [
                'Q1' => ['Tercapai' => 0, '%' => 0],
                'Q2' => ['Tercapai' => 0, '%' => 0],
                'Q3' => ['Tercapai' => 0, '%' => 0],
                'Q4' => ['Tercapai' => 0, '%' => 0],
            ];

            // Group scores by quarters
            foreach ($pencapaianData as $pencapaian) {
                switch ($pencapaian->quarter_id) {
                    case 1:
                        $quarters['Q1']['Tercapai'] += $pencapaian->score;
                        break;
                    case 2:
                        $quarters['Q2']['Tercapai'] += $pencapaian->score;
                        break;
                    case 3:
                        $quarters['Q3']['Tercapai'] += $pencapaian->score;
                        break;
                    case 4:
                        $quarters['Q4']['Tercapai'] += $pencapaian->score;
                        break;
                }
            }

            // Calculate percentages based on the target
            foreach ($quarters as $key => $quarter) {
                $quarters[$key]['%'] = $kpi->target > 0 ? min(($quarter['Tercapai'] / ($kpi->target / 4)) * 100, 100) : 0;
            }

            // Store the data in the $data array
            $data[$kpi->indicator] = $quarters;

            // Sum up percentages for overall KPI calculation
            $overallKPI['Q1'] += $quarters['Q1']['%'];
            $overallKPI['Q2'] += $quarters['Q2']['%'];
            $overallKPI['Q3'] += $quarters['Q3']['%'];
            $overallKPI['Q4'] += $quarters['Q4']['%'];
            $indicatorCount++;
        }

        // Calculate average percentages for overall KPI
        if ($indicatorCount > 0) {
            $overallKPI['Q1'] /= $indicatorCount;
            $overallKPI['Q2'] /= $indicatorCount;
            $overallKPI['Q3'] /= $indicatorCount;
            $overallKPI['Q4'] /= $indicatorCount;
        }

        $kpiChartsData = [];
        foreach ($kpis as $kpi) {
            $weeklyScores = [];

            foreach ($kpi->pencapaian()->orderBy('periode_start', 'asc')->get() as $pencapaian) {
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

            $target = $kpi->target;

            $kpiChartsData[] = [
                'kpi_id' => $kpi->id,
                'kpiName' => $kpi->indicator,
                'kpiTarget' => $target,
                'dataPoints' => $dataPoints
            ];
        }


        $dataProgress = $kpis->map(function ($kpi){
            $target = $kpi->target;
            $kpiTarget = $target;
            $tercapai = $kpi->pencapaian->sum('score');
            return $kpiTarget > 0 ? min(($tercapai / $kpiTarget) * 100, 100) : 0;
        })->toArray();

        $overallProgress = count($dataProgress) > 0 ? array_sum($dataProgress) / count($dataProgress) : 0;

        // Render the view as HTML for preview
        return view('kpi-view.admin.report', [
            'yearsBefore' => $yearsBefore,
            'periode' => $periode,
            'kpis' => $allKPI,
            'data' => $data,
            'overallKPI' => $overallKPI,
            'kpiChartsData' => $kpiChartsData,
            'kpiSelected' => $indicator,
            'overallProgress' => round($overallProgress, 2)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kpi' => 'required',
            'target' => 'required',
            'goal' => 'required',
            'periode' => 'required'
        ]);

        $kpi = new KPI();
        $kpi->indicator = $request->input('kpi');
        $kpi->goal = $request->input('goal');
        $kpi->target = preg_replace('/[^0-9]/', '', $request->input('target'));
        $kpi->periode = $request->input('periode');
        $kpi->save();

        return redirect()->back()->with('success', 'KPI has been added successfully');
    }

    public function store_pencapaian(Request $request, $kpi_id)
    {
        // Validate the request data
        $request->validate([
            'pencapaian' => 'required|string|max:255',
            'score' => 'required',
            'daterange' => 'required|string',
        ]);

        // Split the daterange into start and end dates
        $daterange = explode(' - ', $request->daterange);
        $periode_start = Carbon::createFromFormat('m/d/Y', trim($daterange[0]))->format('Y-m-d');
        $periode_end = Carbon::createFromFormat('m/d/Y', trim($daterange[1]))->format('Y-m-d');

        $Quarter = $this->getQuarter($periode_start);

        // Create a new record in the kpi_pencapaian table
        PencapaianKPI::create([
            'pencapaian' => $request->pencapaian,
            'score' => preg_replace('/[^0-9]/', '', $request->score),
            'periode_start' => $periode_start,
            'periode_end' => $periode_end,
            'quarter_id' => $Quarter,
            'kpi_id' => $kpi_id,
            'user_id' => auth()->id(), // assuming the user is authenticated
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Pencapaian KPI berhasil ditambahkan!');
    }

    public function update_pencapaian(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'edit_pencapaian' => 'required|string|max:255',
            'edit_score' => 'required',
            'edit_daterange' => 'required',
        ]);

        // Split the daterange into start and end dates
        $daterange = explode(' - ', $request->edit_daterange);
        $periode_start = Carbon::createFromFormat('m/d/Y', trim($daterange[0]))->format('Y-m-d');
        $periode_end = Carbon::createFromFormat('m/d/Y', trim($daterange[1]))->format('Y-m-d');

        $Quarter = $this->getQuarter($periode_start);

        // Find the existing KPI achievement record by its ID and update it
        $pencapaianKPI = PencapaianKPI::findOrFail($id);
        $pencapaianKPI->update([
            'pencapaian' => $request->edit_pencapaian,
            'score' => preg_replace('/[^0-9]/', '', $request->edit_score),
            'periode_start' => $periode_start,
            'periode_end' => $periode_end,
            'quarter_id' => $Quarter,
            'user_id' => auth()->id(), // assuming the user is authenticated
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Pencapaian KPI berhasil diperbarui!');
    }

    public function edit_pencapaian($id)
    {
        $pencapaian = PencapaianKPI::findOrFail($id);
        return response()->json($pencapaian);
    }

    // Delete KPI function
    public function delete_pencapaian($id)
    {
        $kpi = PencapaianKPI::find($id);

        if (!$kpi) {
            return response()->json(['message' => 'KPI not found'], 404);
        }

        $kpi->delete();

        return response()->json(['message' => 'KPI deleted successfully'], 200);
    }

    private function getQuarter($date)
    {
        $month = Carbon::parse($date)->month;

        if ($month >= 1 && $month <= 3) {
            return 1; // Q1: January - March
        } elseif ($month >= 4 && $month <= 6) {
            return 2; // Q2: April - June
        } elseif ($month >= 7 && $month <= 9) {
            return 3; // Q3: July - September
        } else {
            return 4; // Q4: October - December
        }
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

    public function downloadPdf(Request $request)
    {
        // Determine the current year and generate the range of years
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        // Get the selected values from the request or use default
        $periode = $request->input('periodeInput') ?? $nowYear;
        $indicator = $request->input('kpiInput') ?? '-1';

        // Prepare the query with conditional filtering
        $query = KPI::query();

        // Apply indicator filter if a specific indicator is selected
        if ($indicator != '-1') {
            $query->where('id', $indicator);
        }

        // Apply periode filter if a specific period is selected
        if ($periode) {
            $query->where('periode', $periode);
        }

        // Fetch the filtered KPIs
        $kpis = $query->get();

        $dataProgress = $kpis->map(function ($kpi){
            $target = $kpi->target;
            $kpiTarget = $target;
            $tercapai = $kpi->pencapaian->sum('score');
            return $kpiTarget > 0 ? min(($tercapai / $kpiTarget) * 100, 100) : 0;
        })->toArray();

        $overallProgress = count($dataProgress) > 0 ? array_sum($dataProgress) / count($dataProgress) : 0;

        $chartImage = $request->input('chartImage'); // Get the chart image data from the request

        // Your table data and other necessary data for the report

        $data = [];
        $overallKPI = [
            'Q1' => 0,
            'Q2' => 0,
            'Q3' => 0,
            'Q4' => 0,
        ];

        $indicatorCount = 0;

        foreach ($kpis as $kpi) {
            $pencapaianData = PencapaianKPI::where('kpi_id', $kpi->id)->orderBy('periode_start', 'asc')->get();

            // Initialize quarters with zero values
            $quarters = [
                'Q1' => ['Tercapai' => 0, '%' => 0],
                'Q2' => ['Tercapai' => 0, '%' => 0],
                'Q3' => ['Tercapai' => 0, '%' => 0],
                'Q4' => ['Tercapai' => 0, '%' => 0],
            ];

            // Group scores by quarters
            foreach ($pencapaianData as $pencapaian) {
                switch ($pencapaian->quarter_id) {
                    case 1:
                        $quarters['Q1']['Tercapai'] += $pencapaian->score;
                        break;
                    case 2:
                        $quarters['Q2']['Tercapai'] += $pencapaian->score;
                        break;
                    case 3:
                        $quarters['Q3']['Tercapai'] += $pencapaian->score;
                        break;
                    case 4:
                        $quarters['Q4']['Tercapai'] += $pencapaian->score;
                        break;
                }
            }

            // Calculate percentages based on the target
            foreach ($quarters as $key => $quarter) {
                $quarters[$key]['%'] = $kpi->target > 0 ? min(($quarter['Tercapai'] / ($kpi->target / 4)) * 100, 100) : 0;
            }

            // Store the data in the $data array
            $data[$kpi->indicator] = $quarters;

            // Sum up percentages for overall KPI calculation
            $overallKPI['Q1'] += $quarters['Q1']['%'];
            $overallKPI['Q2'] += $quarters['Q2']['%'];
            $overallKPI['Q3'] += $quarters['Q3']['%'];
            $overallKPI['Q4'] += $quarters['Q4']['%'];
            $indicatorCount++;
        }

        // Calculate average percentages for overall KPI
        if ($indicatorCount > 0) {
            $overallKPI['Q1'] /= $indicatorCount;
            $overallKPI['Q2'] /= $indicatorCount;
            $overallKPI['Q3'] /= $indicatorCount;
            $overallKPI['Q4'] /= $indicatorCount;
        }

        $kpiChartsData = [];
        foreach ($kpis as $kpi) {
            $weeklyScores = [];

            foreach ($kpi->pencapaian()->orderBy('periode_start', 'asc')->get() as $pencapaian) {
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

            $target = $kpi->target;

            $kpiChartsData[] = [
                'kpi_id' => $kpi->id,
                'kpiName' => $kpi->indicator,
                'kpiTarget' => $target,
                'dataPoints' => $dataPoints
            ];
        }

        // Create a view for the PDF
        $pdfView = view('kpi-view.admin.report-pdf', compact('data', 'chartImage', 'overallProgress', 'overallKPI'))->render();

        // Set options and create the PDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($pdfView);
        $dompdf->setPaper('A4', 'potrait');
        $dompdf->render();

        return $dompdf->stream('KPI_Report.pdf');
    }

    public function duplicateKpis($newYear)
    {
        // Find the closest year to $newYear in the database
        $closestYearKpi = Kpi::select('periode')
            ->orderByRaw('ABS(periode - ?) ASC', [$newYear])
            ->first();

        // Check if a KPI for the closest year exists
        if (!$closestYearKpi) {
            // If no KPI is found, redirect to 'manage-kpi' route with a failure message
            return redirect()->route('manage-kpi')
                ->with('failed', 'Failure to find existing KPI for the closest year. You have to create it manually here...');
        }

        // Fetch all the KPIs for the closest year
        $closestYearKpis = Kpi::where('periode', $closestYearKpi->periode)->get();

        foreach ($closestYearKpis as $kpi) {
            // Duplicate the KPI with the new year
            Kpi::create([
                'indicator' => $kpi->indicator,
                'goal' => $kpi->goal,
                'target' => $kpi->target,
                'periode' => $newYear, // Assign the new year
                'isAvg' => $kpi->isAvg,
            ]);
        }

        // Redirect with success message after duplication
        return redirect()->back()->with('success', 'KPIs duplicated for the year ' . $newYear);
    }
}
