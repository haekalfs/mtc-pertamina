<?php

namespace App\Http\Controllers;

use App\Models\Penlat;
use App\Models\Penlat_batch;
use App\Models\Profit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FinanceController extends Controller
{
    public function dashboard($yearSelected = null)
    {
        $nowYear = date('Y');
        $yearsBefore = range($nowYear - 4, $nowYear);

        // Determine the year to filter by
        $currentYear = $yearSelected ? $yearSelected : $nowYear;

        // Filter records by the selected year
        $data = Profit::whereYear('tgl_pelaksanaan', $currentYear)->get();

        // Sum of multiple columns for total costs
        $totalCosts = $data->sum(function($item) {
            return (int) $item->biaya_instruktur +
                (int) $item->total_pnbp +
                (int) $item->penagihan_foto +
                (int) $item->biaya_transportasi_hari +
                (int) $item->penagihan_atk +
                (int) $item->penagihan_snack +
                (int) $item->penagihan_makan_siang +
                (int) $item->penagihan_laundry;
        });

        // Total revenue calculation
        $totalRevenue = $data->sum(function($item) {
            return (int) $item->total_biaya_pendaftaran_peserta;
        });

        // Nett income calculation
        $nettIncome = $totalRevenue - $totalCosts;

        // Return the view with the correct variables
        return view('finance.index', compact('yearsBefore', 'currentYear', 'totalRevenue', 'nettIncome', 'totalCosts'));
    }

    public function vendor_payment()
    {
        return view('finance.submenu.vendor_payment');
    }

    public function getChartDataProfits($year)
    {
        $dataByDate = Profit::select('tgl_pelaksanaan', DB::raw('sum(profit) as total'))
                    ->whereYear('tgl_pelaksanaan', $year) // Correctly filters by year
                    ->groupBy('tgl_pelaksanaan')
                    ->get();

        $dataPointsSpline = [];
        foreach ($dataByDate as $row) {
            $dataPointsSpline[] = [
                "x" => Carbon::parse($row->tgl_pelaksanaan)->timestamp * 1000, // Convert to JavaScript timestamp
                "y" => (float) $row->total // Ensure y-value is a float
            ];
        }

        return response()->json([
            'splineDataPoints' => $dataPointsSpline
        ]);
    }

    public function costs(Request $request)
    {
        if ($request->ajax()) {
            $query = Profit::with(['batch.penlat']);

            // Apply filters based on the selected values from the dropdowns
            if ($request->namaPenlat && $request->namaPenlat != '-1') {
                $query->whereHas('batch.penlat', function($q) use ($request) {
                    $q->where('id', $request->namaPenlat);
                });
            }

            if ($request->jenisPenlat && $request->jenisPenlat != '-1') {
                $query->whereMonth('tgl_pelaksanaan', $request->jenisPenlat);
            }

            if ($request->periode && $request->periode != '-1') {
                $query->whereYear('tgl_pelaksanaan', $request->periode);
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addColumn('description', function($item) {
                    return $item->batch->penlat->description ?:
                        '<a href="" data-toggle="modal" data-target="#createBatchModal" data-id="' . $item->id .
                        '" data-batch="' . $item->pelaksanaan . '" data-tgl="' . $item->tgl_pelaksanaan .
                        '" class="text-danger">Batch Not Found or Not Registered!</a>';
                })
                ->addColumn('pelaksanaan', function($item) {
                    return $item->pelaksanaan ?
                        '<a href="' . route('preview-costs', $item->batch->id) . '">' . $item->pelaksanaan . '</a>' : '-';
                })
                ->addColumn('total_biaya_pendaftaran_peserta', function($item) {
                    return $item->total_biaya_pendaftaran_peserta ? 'Rp ' . number_format($item->total_biaya_pendaftaran_peserta, 0, ',', '.') : '-';
                })
                ->addColumn('jumlah_biaya', function($item) {
                    return $item->jumlah_biaya ? 'Rp ' . number_format($item->jumlah_biaya, 0, ',', '.') : '-';
                })
                ->addColumn('profit', function($item) {
                    return $item->profit ? 'Rp ' . number_format($item->profit, 0, ',', '.') : '-';
                })
                ->rawColumns(['description', 'pelaksanaan'])
                ->make(true);
        }


        $data = Profit::all();
        // Sum of multiple columns
        $totalCosts = $data->sum(function($item) {
            return (int) $item->biaya_instruktur +
            (int) $item->total_pnbp +
            (int) $item->penagihan_foto +
            (int) $item->biaya_transportasi_hari +
            (int) $item->penagihan_atk +
            (int) $item->penagihan_snack +
            (int) $item->penagihan_makan_siang +
            (int) $item->penagihan_laundry;
        });

        // Total revenue
        $totalRevenue = $data->sum(function($item) {
            return (int) $item->total_biaya_pendaftaran_peserta;
        });

        // Nett income calculation
        $nettIncome = $totalRevenue - $totalCosts;

        $array = [
            'total_biaya_instruktur' => $data->sum(fn($item) => (int) $item->biaya_instruktur),
            'total_pnbp' => $data->sum(fn($item) => (int) $item->total_pnbp),
            'total_biaya_transportasi_hari' => $data->sum(fn($item) => (int) $item->biaya_transportasi_hari),
            'total_penagihan_foto' => $data->sum(fn($item) => (int) $item->penagihan_foto),
            'total_penagihan_atk' => $data->sum(fn($item) => (int) $item->penagihan_atk),
            'total_penagihan_snack' => $data->sum(fn($item) => (int) $item->penagihan_snack),
            'total_penagihan_makan_siang' => $data->sum(fn($item) => (int) $item->penagihan_makan_siang),
            'total_penagihan_laundry' => $data->sum(fn($item) => (int) $item->penagihan_laundry),
            'total_peserta' => $data->sum(fn($item) => (int) $item->jumlah_peserta),
            'revenue' => $data->sum(fn($item) => (int) $item->total_biaya_pendaftaran_peserta),
            'total_costs' => $totalCosts,
            'nett_income' => $nettIncome,
        ];

        $penlatList = Penlat::all();

        return view('finance.submenu.costs', ['data' => $data, 'penlatList' => $penlatList, 'arrayData' => $array]);
    }

    public function profits_import()
    {
        return view('finance.import.index');
    }

    public function preview_costs($id)
    {
        $utility = Penlat_batch::find($id);
        $item = Profit::where('pelaksanaan', $utility->batch)->first();

        $dataPoints = [
            ['label' => 'Profits', 'y' => (float) $item->profit],
            ['label' => 'Biaya Instruktur', 'y' => (float) $item->biaya_instruktur],
            ['label' => 'Total PNBP', 'y' => (float) $item->total_pnbp],
            ['label' => 'Biaya Transportasi', 'y' => (float) $item->biaya_transportasi_hari],
            ['label' => 'Penagihan Foto', 'y' => (float) $item->penagihan_foto],
            ['label' => 'Penagihan ATK', 'y' => (float) $item->penagihan_atk],
            ['label' => 'Penagihan Snack', 'y' => (float) $item->penagihan_snack],
            ['label' => 'Penagihan Makan Siang', 'y' => (float) $item->penagihan_makan_siang],
            ['label' => 'Penagihan Laundry', 'y' => (float) $item->penagihan_laundry],
        ];
        return view('finance.submenu.preview_costs', ['data' => $utility, 'item' => $item, 'dataPoints' => $dataPoints]);
    }
}
