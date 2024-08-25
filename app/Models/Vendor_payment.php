<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor_payment extends Model
{
    use HasFactory;

    protected $table = 'vendor_payments';

    protected $fillable = [
        'tanggal_terima_dokumen_invoice',
        'jenis_vendor',
        'vendor',
        'nomor_vendor',
        'uraian',
        'wapu',
        'kode_pajak',
        'no_invoice',
        'nilai',
        'pajak',
        'management_fee',
        'no_req_id',
        'no_req_release',
        'no_pr',
        'no_po',
        'no_sa_gr',
        'no_req_payment_approval',
        'no_req_bmc',
        'tanggal_kirim_ke_edoc_ssc',
        'keterangan',
        'tanggal_terbayarkan'
    ];
}
