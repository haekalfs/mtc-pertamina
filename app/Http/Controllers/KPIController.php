<?php

namespace App\Http\Controllers;

use App\Models\KPI;
use App\Models\PencapaianKPI;
use Illuminate\Http\Request;

class KpiController extends Controller
{
    public function index()
    {
        $indicators = KPI::all();
        return view('kpi-view.index', ['indicators' => $indicators]);
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

    public function report()
    {
        return view('kpi-view.admin.report');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kpi' => 'required|string|max:255',
            'target' => 'required|string|max:255',
            'period_start' => 'required|date',
            'period_end' => 'required|date',
        ]);

        $kpi = new KPI();
        $kpi->indicator = $request->input('kpi');
        $kpi->target = $request->input('target');
        $kpi->periode_start = $request->input('period_start');
        $kpi->periode_end = $request->input('period_end');
        $kpi->save();

        return redirect()->back()->with('success', 'KPI has been added successfully');
    }

    public function store_pencapaian(Request $request, $kpi_id)
    {
        // Validate the request data
        $request->validate([
            'pencapaian' => 'required|string|max:255',
            'score' => 'required|integer|between:0,100',
            'periode_start' => 'required|date',
            'periode_end' => 'required|date',
        ]);

        // Create a new record in the kpi_pencapaian table
        PencapaianKPI::create([
            'pencapaian' => $request->pencapaian,
            'score' => $request->score,
            'periode_start' => $request->periode_start,
            'periode_end' => $request->periode_end,
            'kpi_id' => $kpi_id,
            'user_id' => auth()->id(), // assuming the user is authenticated
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Pencapaian KPI berhasil ditambahkan!');
    }
}
