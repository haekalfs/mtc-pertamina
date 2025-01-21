<?php

namespace App\Http\Controllers;

use App\Models\Receivables_participant_certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index()
    {
        $listCertificates = Receivables_participant_certificate::orderBy('certificate_number', 'desc')->orderBy('issued_date', 'desc')->get();
        return view('master-data.certificates_number', ['listCertificates' => $listCertificates]);
    }
}
