@extends('layouts.main')

@section('active-finance')
active font-weight-bold
@endsection

@section('show-finance')
show
@endsection

@section('vendor-payment')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="menu-icon fa fa-list-alt"></i> Vendor Payment</h1>
        <p class="mb-4">Vendor Payment.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('vendor-payment-importer') }}" class="btn btn-sm btn-primary shadow-sm text-white"><i class="fa fa-file-text fa-sm"></i> Import Data</a>
    </div>
</div>
<div class="overlay overlay-mid" style="display: none;"></div>

<div class="alert alert-danger alert-success-delete-mid" role="alert" style="display: none;">
</div>

<div class="alert alert-success alert-success-saving-mid" role="alert" style="display: none;">
    Your entry has been saved successfully.
</div>
@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('failed'))
<div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif
<div class="animated fadeIn zoom90">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" id="judul">List Data</h6>
                </div>
                <div class="card-body zoom80">
                    <div class="row d-flex justify-content-start mb-4">
                        <div class="col-md-12">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Vendor Name :</label>
                                        <select class="custom-select" id="namaPenlat" name="namaPenlat">
                                            <option value="-1" selected>Show All</option>
                                            <!-- Populate vendor names dynamically -->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="position_id">Periode :</label>
                                        <select name="periode" class="form-control" id="periode">
                                            <option value="-1" selected>Show All</option>
                                            @foreach(range(date('Y'), date('Y') - 5) as $year)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex align-self-end justify-content-start">
                                    <div class="form-group">
                                        <div class="align-self-center">
                                            <button type="button" id="filterButton" class="btn btn-primary" style="padding-left: 1.2em; padding-right: 1.2em;"><i class="ti-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table id="listPayments" class="table table-bordered">
                        <thead class="text-center">
                            <tr>
                                <th>No</th>
                                <th>Vendor</th>
                                <th>Nomor Vendor</th>
                                <th>Uraian</th>
                                <th>WAPU</th>
                                <th>Kode Pajak</th>
                                <th>No Invoice</th>
                                <th>Nilai</th>
                                <th>Pajak</th>
                                <th>Management Fee</th>
                                <th>No Req ID</th>
                                <th>No Req Release</th>
                                <th>No PR</th>
                                <th>No PO</th>
                                <th>No SA GR</th>
                                <th>No Req Payment Approval</th>
                                <th>No Req BMC</th>
                                <th>Tanggal Kirim ke e-Doc SSC</th>
                                <th>Keterangan</th>
                                <th>Tanggal Terbayarkan</th>
                                <!-- Add any other relevant columns -->
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    var table = $('#listPayments').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("vendor-payment") }}',
            data: function(d) {
                d.namaPenlat = $('#namaPenlat').val();
                d.periode = $('#periode').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'vendor', name: 'vendor' },
            { data: 'nomor_vendor', name: 'nomor_vendor' },
            { data: 'uraian', name: 'uraian' },
            { data: 'wapu', name: 'wapu' },
            { data: 'kode_pajak', name: 'kode_pajak' },
            { data: 'no_invoice', name: 'no_invoice' },
            { data: 'nilai', name: 'nilai' },
            { data: 'pajak', name: 'pajak' },
            { data: 'management_fee', name: 'management_fee' },
            { data: 'no_req_id', name: 'no_req_id' },
            { data: 'no_req_release', name: 'no_req_release' },
            { data: 'no_pr', name: 'no_pr' },
            { data: 'no_po', name: 'no_po' },
            { data: 'no_sa_gr', name: 'no_sa_gr' },
            { data: 'no_req_payment_approval', name: 'no_req_payment_approval' },
            { data: 'no_req_bmc', name: 'no_req_bmc' },
            { data: 'tanggal_kirim_ke_edoc_ssc', name: 'tanggal_kirim_ke_edoc_ssc' },
            { data: 'keterangan', name: 'keterangan' },
            { data: 'tanggal_terbayarkan', name: 'tanggal_terbayarkan' },
        ]
    });

    $('#filterButton').click(function() {
        table.draw();
    });
});
</script>
@endsection
