<?php

namespace App\Http\Controllers;

use App\Models\Penlat;
use App\Models\Penlat_batch;
use App\Models\Receivables_participant_certificate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $penlatList = Penlat::all();
        $listBatch = Penlat_batch::all();

        if ($request->ajax()) {
            $query = Receivables_participant_certificate::with([
                'penlatCertificate.batch.penlat',
                'peserta'
            ]);

            // Apply filters
            if ($request->penlat && $request->penlat != -1) {
                $query->whereHas('penlatCertificate.batch.penlat', function ($q) use ($request) {
                    $q->where('id', $request->penlat);
                });
            }
            if ($request->kategori_pelatihan && $request->kategori_pelatihan != -1) {
                $query->whereHas('penlatCertificate.batch.penlat', function ($q) use ($request) {
                    $q->where('kategori_pelatihan', $request->kategori_pelatihan);
                });
            }
            if ($request->periode && $request->periode != -1) {
                $query->whereYear('issued_date', $request->periode);
            }

            $certificates = $query->get();

            return DataTables::of($certificates)
                ->addColumn('action', function ($certificate) {
                    return '<a class="btn btn-outline-secondary btn-md mb-2 mr-2 go-to-certificate" href="' . route('preview-certificate', ['id' => $certificate->penlat_certificate_id]) . '">
                                <i class="fa fa-external-link"></i>
                            </a>';
                })
                ->addColumn('penlatBatch', function ($certificate) {
                    return $certificate->penlatCertificate->batch->batch;
                })
                ->addColumn('penlatDescription', function ($certificate) {
                    return $certificate->penlatCertificate->batch->penlat->description;
                })
                ->addColumn('nama_peserta', function ($certificate) {
                    return $certificate->peserta->nama_peserta;
                })
                ->addColumn('created_by', function ($certificate) {
                    return $certificate->penlatCertificate->created_by;
                })
                ->addColumn('issued_date', function ($certificate) {
                    return $certificate->issued_date ? \Carbon\Carbon::parse($certificate->issued_date)->format('d-M-Y') : '-';
                })
                ->addColumn('certificate', function ($certificate) {
                    $batchInfo = $certificate->penlatCertificate->batch->batch ?? 'N/A';
                    $batchParts = explode('/', $batchInfo);
                    $formattedNumber = $certificate->certificate_number
                        ? $certificate->certificate_number
                        : '<span class="text-danger" style="font-style: italic;">Missing Number</span>';
                    return $formattedNumber . ' / ' . $batchParts[0] . ' / PMTC / ' . $batchParts[2] . ' / ' . $batchParts[3];
                })
                ->rawColumns(['action', 'certificate'])
                ->make(true);
        }

        return view('master-data.certificates_number', compact('penlatList', 'listBatch'));
    }

    public function export(Request $request)
    {
        $templatePath = public_path('uploads/template/template_certificate_export.xlsx');
        $spreadsheet = IOFactory::load($templatePath);

        $sheet = $spreadsheet->getSheet(0);
        $startRow = 5;

        // Apply filters
        $query = Receivables_participant_certificate::with([
            'penlatCertificate.batch.penlat',
            'peserta'
        ]);

        if ($request->penlat) {
            $query->whereHas('penlatCertificate.batch.penlat', function ($q) use ($request) {
                $q->where('id', $request->penlat);
            });
        }

        if ($request->kategori_pelatihan) {
            $query->whereHas('penlatCertificate.batch.penlat', function ($q) use ($request) {
                $q->where('kategori_pelatihan', $request->kategori_pelatihan);
            });
        }

        if ($request->periode && $request->periode != -1) {
            $query->whereYear('created_at', $request->periode);
        }

        $getForm = $query->orderBy('penlat_certificate_id', 'desc')->orderBy('certificate_number', 'desc')->get();

        foreach ($getForm as $row) {
            $sheet->setCellValueByColumnAndRow(1, $startRow, $row->certificate_number);
            $sheet->setCellValueByColumnAndRow(2, $startRow, Carbon::parse($row->issued_date)->format('d-M-Y'));
            $sheet->setCellValueByColumnAndRow(3, $startRow, $row->penlatCertificate->batches->penlat->description ?? 'N/A');
            $sheet->setCellValueByColumnAndRow(4, $startRow, $row->penlatCertificate->batches->batch ?? 'N/A');

            $batchInfo = $row->penlatCertificate->batches->batch ?? 'N/A';
            $batchParts = explode('/', $batchInfo);
            $part0 = $batchParts[0] ?? '-';
            $part2 = $batchParts[2] ?? '-';
            $part3 = $batchParts[3] ?? '-';

            $formattedNumber = $row->certificate_number ?: '-';
            $sheet->setCellValueByColumnAndRow(5, $startRow, "$formattedNumber / $part0 / PMTC / $part2 / $part3");
            $sheet->setCellValueByColumnAndRow(6, $startRow, $row->peserta->nama_peserta ?? 'N/A');
            $sheet->setCellValueByColumnAndRow(7, $startRow, Carbon::parse($row->expire_date)->format('d-M-Y') ?? '-');
            $sheet->setCellValueByColumnAndRow(8, $startRow, Carbon::parse($row->created_at)->format('d-M-Y'));
            $sheet->setCellValueByColumnAndRow(9, $startRow, $row->penlatCertificate->created_by);

            $startRow++;
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filePath = storage_path('app/public/output.xlsx');
        $writer->save($filePath);

        return response()->download($filePath, "Certificate_Master_Data.xlsx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
