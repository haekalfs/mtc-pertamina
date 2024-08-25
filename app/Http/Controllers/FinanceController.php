<?php

namespace App\Http\Controllers;

use App\Models\Penlat;
use App\Models\Penlat_batch;
use App\Models\Profit;
use App\Models\Vendor_payment;
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

    public function vendor_payment(Request $request)
    {
        if ($request->ajax()) {
            $query = Vendor_payment::query();

            // Filter by vendor name if selected
            if ($request->namaPenlat != '-1') {
                $query->where('vendor', $request->namaPenlat);
            }

            // Filter by periode (tanggal_terbayarkan) if selected
            if ($request->periode != '-1') {
                $query->whereYear('tanggal_terbayarkan', $request->periode);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->make(true);
        }

        return view('finance.submenu.vendor_payment');
    }

    public function vendor_payment_import()
    {
        return view('finance.import.vendor_payment_import');
    }

    public function getChartDataProfits($year)
    {
        $dataByDate = Profit::select('tgl_pelaksanaan', DB::raw('sum(profit) as total'))
                    ->whereYear('tgl_pelaksanaan', $year)
                    ->groupBy('tgl_pelaksanaan')
                    ->get();

        $dataPointsSpline = [];
        foreach ($dataByDate as $row) {
            $dataPointsSpline[] = [
                "x" => Carbon::parse($row->tgl_pelaksanaan)->timestamp * 1000, // JavaScript timestamp
                "y" => (float) $row->total // Ensure y-value is a float
            ];
        }

        $data = Profit::whereYear('tgl_pelaksanaan', $year)->get();

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

        $totalRevenue = $data->sum(function($item) {
            return (int) $item->total_biaya_pendaftaran_peserta;
        });

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
            'revenue' => $totalRevenue,
            'total_costs' => $totalCosts,
            'nett_income' => $nettIncome,
        ];

        return response()->json([
            'profitDataPoints' => $dataPointsSpline,
            'array' => $array
        ]);
    }

    public function costs(Request $request, $penlat)
    {
        if ($request->ajax()) {
            $query = Profit::with(['batch.penlat']);

            // Apply filters based on the selected values from the dropdowns
            if ($request->has('namaPenlat')) {
                if ($request->namaPenlat == '-1') {
                    // Show all records, no filtering
                    $query->whereHas('batch.penlat');
                } elseif ($request->namaPenlat != $penlat) {
                    // Filter by `namaPenlat` if it's not equal to `penlat` and is not '-1'
                    $query->whereHas('batch.penlat', function($q) use ($request) {
                        $q->where('id', $request->namaPenlat);
                    });
                } else {
                    // Filter by `penlat` passed as a URL parameter (default case)
                    $query->whereHas('batch.penlat', function($q) use ($penlat) {
                        $q->where('id', $penlat);
                    });
                }
            } else {
                // If no `namaPenlat` is provided (default page load), use `$penlat` from URL
                $query->whereHas('batch.penlat', function($q) use ($penlat) {
                    $q->where('id', $penlat);
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
                        '<div class="animateBox"><a href="' . route('preview-costs', $item->batch->id) . '">' . $item->pelaksanaan . '</a></div>' : '-';
                })
                ->addColumn('revenue', function($item) {
                    return $item->total_biaya_pendaftaran_peserta ? 'Rp ' . number_format($item->total_biaya_pendaftaran_peserta, 0, ',', '.') : '-';
                })
                ->addColumn('cost', function($item) {
                    $totalCost = (int) $item->biaya_instruktur +
                                   (int) $item->total_pnbp +
                                   (int) $item->biaya_transportasi_hari +
                                   (int) $item->penagihan_foto +
                                   (int) $item->penagihan_atk +
                                   (int) $item->penagihan_snack +
                                   (int) $item->penagihan_makan_siang +
                                   (int) $item->penagihan_laundry;

                    return $totalCost ? 'Rp ' . number_format($totalCost, 0, ',', '.') : '-';
                })
                ->addColumn('nett_income', function($item) {
                    $totalCost = (int) $item->biaya_instruktur +
                                   (int) $item->total_pnbp +
                                   (int) $item->biaya_transportasi_hari +
                                   (int) $item->penagihan_foto +
                                   (int) $item->penagihan_atk +
                                   (int) $item->penagihan_snack +
                                   (int) $item->penagihan_makan_siang +
                                   (int) $item->penagihan_laundry;

                    $revenue = (int) $item->total_biaya_pendaftaran_peserta;

                    $nettIncome = $revenue - $totalCost;

                    return $nettIncome ? 'Rp ' . number_format($nettIncome, 0, ',', '.') : '-';
                })
                ->rawColumns(['description', 'pelaksanaan'])
                ->make(true);
        }

        $penlatList = Penlat::all();

        $selectedPenlat = Penlat::find($penlat);

        return view('finance.submenu.costs', ['penlatList' => $penlatList, 'selectedPenlat' => $selectedPenlat]);
    }

    public function profits(Request $request)
    {
        if ($request->ajax()) {
            $query = Penlat::query();

            // Apply filters based on the selected values from the dropdowns
            if ($request->namaPenlat && $request->namaPenlat != '-1') {
                $query->where('id', $request->namaPenlat);
            }

            if ($request->jenisPenlat && $request->jenisPenlat != '-1') {
                $query->where('jenis_pelatihan', $request->jenisPenlat);
            }

            if ($request->stcw && $request->stcw != '-1') {
                $query->where('kategori_pelatihan', $request->stcw);
            }

            // Apply the periode filter to the 'batch.tgl_pelaksanaan' field
            if ($request->periode && $request->periode != '-1') {
                $query->whereHas('batch', function ($query) use ($request) {
                    $query->whereYear('date', $request->periode);
                });
            }

            // Filter to include only Penlat that has related profits
            $query->whereHas('batch.profits');

            $data = $query->with(['batch' => function($query) {
                $query->with('profits');
            }])->get();

            return DataTables::of($data)
                ->addColumn('display', function($item) {
                    $filePath = $item->filepath;
                    $imageUrl = $filePath ? asset($item->filepath) : asset('img/default-img.png');
                    return '<a href="'.route('cost', $item->id).'"><img src="' . $imageUrl . '" style="height: 100px; width: 100px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow animateBox"></a>';
                })
                ->addColumn('cost', function($item) {
                    $totalCost = $item->batch->sum(function($batch) {
                        // Function to safely sum a field, ensuring it's an integer and not null
                        $safeSum = function ($field) use ($batch) {
                            return (int) $batch->profits->sum(function ($profit) use ($field) {
                                // Check if the value is numeric and not an empty string
                                return is_numeric($profit->$field) && $profit->$field !== '' ? $profit->$field : 0;
                            });
                        };

                        // Summing up the relevant income fields safely
                        $totalCost = $safeSum('biaya_instruktur') +
                                    $safeSum('total_pnbp') +
                                    $safeSum('biaya_transportasi_hari') +
                                    $safeSum('penagihan_foto') +
                                    $safeSum('penagihan_atk') +
                                    $safeSum('penagihan_snack') +
                                    $safeSum('penagihan_makan_siang') +
                                    $safeSum('penagihan_laundry');

                        return $totalCost;
                    });
                    return 'Rp ' . number_format($totalCost, 0, ',', '.');
                })
                ->addColumn('revenue', function($item) {
                    // Sum the 'profit' column from all related Profit models
                    $totalRevenue = $item->batch->sum(function($batch) {
                        return $batch->profits->sum('total_biaya_pendaftaran_peserta');
                    });
                    return 'Rp ' . number_format($totalRevenue, 0, ',', '.');
                })
                ->addColumn('nett_income', function($item) {
                    $totalNettIncome = $item->batch->sum(function($batch) {
                        // Function to safely sum a field, ensuring it's an integer and not null
                        $safeSum = function ($field) use ($batch) {
                            return (int) $batch->profits->sum(function ($profit) use ($field) {
                                // Check if the value is numeric and not an empty string
                                return is_numeric($profit->$field) && $profit->$field !== '' ? $profit->$field : 0;
                            });
                        };

                        // Summing up the relevant income fields safely
                        $totalIncome = $safeSum('biaya_instruktur') +
                                    $safeSum('total_pnbp') +
                                    $safeSum('biaya_transportasi_hari') +
                                    $safeSum('penagihan_foto') +
                                    $safeSum('penagihan_atk') +
                                    $safeSum('penagihan_snack') +
                                    $safeSum('penagihan_makan_siang') +
                                    $safeSum('penagihan_laundry');

                        // Summing up the cost-related field safely
                        $totalCost = $safeSum('total_biaya_pendaftaran_peserta');

                        // Calculate net income
                        $nettIncome = $totalIncome - $totalCost;

                        return str_replace('-', '', $nettIncome);
                    });
                    return 'Rp ' . number_format($totalNettIncome, 0, ',', '.');
                })
                ->rawColumns(['display'])
                ->make(true);
        }

        $penlatList = Penlat::all();

        return view('finance.submenu.profits', ['penlatList' => $penlatList]);
    }

    public function profits_import()
    {
        return view('finance.import.index');
    }

    public function preview_costs($id)
    {
        $utility = Penlat_batch::find($id);
        if(!$utility){
            return redirect()->back()->with('batch-registration', 'Batch is not registered yet. Register it by clicking red text below or <a href="' . route('batch-penlat') . '">Click Here</a>!');
        }
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
