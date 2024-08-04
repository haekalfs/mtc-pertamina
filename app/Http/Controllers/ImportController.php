<?php

namespace App\Http\Controllers;

use App\Models\Infografis_peserta;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $path = $request->file('file')->getRealPath();
        $data = Excel::toArray([], $path)[0];

        // Remove the header row
        $header = array_shift($data);

        foreach ($data as $row) {
            Infografis_peserta::create([
                'nama_peserta' => $row[0],
                'nama_program' => $row[1],
                'tgl_pelaksanaan' => $row[2],
                'tempat_pelaksanaan' => $row[3],
                'jenis_pelatihan' => $row[4],
                'keterangan' => $row[5],
                'subholding' => $row[6],
                'perusahaan' => $row[7],
                'kategori_program' => $row[8],
                'realisasi' => $row[9],
            ]);
        }

        return redirect()->back()->with('success', 'Data imported successfully.');
    }
}
