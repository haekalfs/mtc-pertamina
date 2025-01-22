<?php

namespace App\Http\Controllers;

use App\Models\Penlat;
use App\Models\Penlat_batch;
use App\Models\Receivables_participant_certificate;
use Illuminate\Http\Request;

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
            ])->orderBy('certificate_number', 'desc')->orderBy('issued_date', 'desc');

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

            return datatables()->eloquent($query)
                ->addColumn('action', function ($certificate) {
                    return '<a class="btn btn-outline-success btn-md mb-2 mr-2 generateQR" href="javascript:void(0)"
                                data-id="' . $certificate->id . '">
                                <i class="fa fa-qrcode"></i>
                            </a>';
                })
                ->editColumn('penlatBatch', function ($certificate) {
                    return $certificate->penlatCertificate->batch->batch;
                })
                ->editColumn('penlatDescription', function ($certificate) {
                    return $certificate->penlatCertificate->batch->penlat->description;
                })
                ->editColumn('nama_peserta', function ($certificate) {
                    return $certificate->peserta->nama_peserta;
                })
                ->editColumn('created_by', function ($certificate) {
                    return $certificate->penlatCertificate->created_by;
                })
                ->editColumn('issued_date', function ($certificate) {
                    return $certificate->issued_date ? \Carbon\Carbon::parse($certificate->issued_date)->format('d-M-Y') : '-';
                })
                ->editColumn('certificate', function ($certificate) {
                    $batchInfo = $certificate->penlatCertificate->batch->batch ?? 'N/A';
                    $batchParts = explode('/', $batchInfo);
                    $formattedNumber = $certificate->certificate_number
                        ? $certificate->certificate_number
                        : '<span class="text-danger" style="font-style: italic;">Missing Number</span>';
                    return $formattedNumber . ' / ' . $batchParts[0] . ' / PMTC / ' . $batchParts[2] . ' / ' . $batchParts[3];
                })
                ->make(true);
        }

        return view('master-data.certificates_number', compact('penlatList', 'listBatch'));
    }
}
