@extends('layouts.main')

@section('active-finance')
active font-weight-bold
@endsection

@section('show-finance')
show
@endsection

@section('cost')
font-weight-bold
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="menu-icon ti-stats-down"></i> Preview Costs Training</h1>
        <p class="mb-3">Utilities Details Information.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
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

<div class="row zoom90">
    <div class="col-xl-12 col-lg-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> List Data</h6>
                <div class="text-right">
                    {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Update Data</a> --}}
                </div>
            </div>
            <div class="card-body">
                <div class="col-md-12 mb-3">
                    <div class="row">
                        <div class="col-md-3 d-flex align-items-center justify-content-center" style="padding-left: 1em;">
                            <img src="{{ $data->filepath ? asset($data->filepath) : 'https://via.placeholder.com/50x50/5fa9f8/ffffff' }}" style="height: 150px; width: 200px; border: 1px solid rgb(202, 202, 202);" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
                        </div>
                        <div class="col-md-9">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th style="width: 250px;">Nama Pelatihan</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->penlat->description }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 250px;">Nama Program</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->nama_program }}</td>
                                </tr>
                                <tr>
                                    <th>Batch</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->batch }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Pelaksanaan</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->date }}</td>
                                </tr>
                            </table>
                          <small class="font-weight-bold text-danger">
                            Editing batch is only available on the
                            <a href="{{ route('batch-penlat') }}" class="text-danger">
                                <u><i>Batch Program Page</i></u>
                            </a>.
                        </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="animated fadeIn zoom90">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">Pemakaian Utilitas : {{ $data->batch }}</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Add Utilities</a> --}}
                    </div>
                </div>
                <div class="card-body">
                    <table id="docLetter" class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:60%">Tool</th>
                                <th style="width:10%">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($data->penlat_usage->isNotEmpty())
                                @foreach($data->penlat_usage as $tool)
                                    <tr>
                                        <td data-th="Product">
                                            <div class="row">
                                                <div class="col-md-4 text-left">
                                                    <img src="{{ asset($tool->utility->filepath) }}" style="height: 100px; width: 100px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
                                                </div>
                                                <div class="col-md-8 text-left mt-sm-2">
                                                    <h5>{{ $tool->utility->utility_name }}</h5>
                                                    <p class="font-weight-light">Satuan Default ({{ $tool->utility->utility_unit }})</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center" style="width:7%">
                                            {{ $tool->amount }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="text-center">
                                    <td colspan="2">No Data Available</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-secondary" id="judul">List Costs : {{ $data->batch }}</h6>
                            <div class="text-right">
                                {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Edit Data</a> --}}
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title font-weight-bold">Profits</h5>
                            <div class="ml-2">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Jumlah Peserta</td>
                                        <td style="text-align: start;">: &nbsp; {{ $item->jumlah_peserta ? $item->jumlah_peserta : '-' }} Peserta</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Biaya Pendaftaran</td>
                                        <td style="text-align: start;">: &nbsp; {{ $item->biaya_pendaftaran_peserta ? 'Rp ' . number_format($item->biaya_pendaftaran_peserta, 0, ',', '.') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 250px;" class="mb-2 font-weight-bold text-success"><i class="ti-minus mr-2"></i> Total Biaya Pendaftaran</td>
                                        <td style="text-align: start;" class="font-weight-bold text-success">: &nbsp; {{ $item->total_biaya_pendaftaran_peserta ? 'Rp ' . number_format($item->total_biaya_pendaftaran_peserta, 0, ',', '.') : '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <h5 class="card-title font-weight-bold">Loss</h5>
                            <div class="ml-2">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Biaya Instruktur</td>
                                        <td style="text-align: start;">: &nbsp; {{ $item->biaya_instruktur ? 'Rp ' . number_format($item->biaya_instruktur, 0, ',', '.') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Total PNBP</td>
                                        <td style="text-align: start;">: &nbsp; {{ $item->total_pnbp ? 'Rp ' . number_format($item->total_pnbp, 0, ',', '.') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Honor + PNBP</td>
                                        <td style="text-align: start;">: &nbsp; {{ $item->total_pnbp ? 'Rp ' . number_format($item->honor_pnbp, 0, ',', '.') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Biaya Transportasi/Hari</td>
                                        <td style="text-align: start;">: &nbsp; {{ $item->biaya_transportasi_hari ? 'Rp ' . number_format($item->biaya_transportasi_hari, 0, ',', '.') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Penagihan Foto</td>
                                        <td style="text-align: start;">: &nbsp; {{ $item->penagihan_foto ? 'Rp ' . number_format($item->penagihan_foto, 0, ',', '.') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Penagihan ATK</td>
                                        <td style="text-align: start;">: &nbsp; {{ $item->penagihan_atk ? 'Rp ' . number_format($item->penagihan_atk, 0, ',', '.') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Penagihan Snacks</td>
                                        <td style="text-align: start;">: &nbsp; {{ $item->penagihan_snack ? 'Rp ' . number_format($item->snack, 0, ',', '.') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Penagihan Makan Siang</td>
                                        <td style="text-align: start;">: &nbsp; {{ $item->penagihan_makan_siang ? 'Rp ' . number_format($item->penagihan_makan_siang, 0, ',', '.') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Penagihan Laundry</td>
                                        <td style="text-align: start;">: &nbsp; {{ $item->penagihan_laundry ? 'Rp ' . number_format($item->penagihan_laundry, 0, ',', '.') : '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <hr>
                            <h5 class="card-title font-weight-bold">Total Profits</h5>
                            <div class="ml-2">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Total Biaya Pendaftaran</td>
                                        <td style="text-align: start;" class="">: &nbsp; {{ $item->total_biaya_pendaftaran_peserta ? 'Rp ' . number_format($item->total_biaya_pendaftaran_peserta, 0, ',', '.') : '-' }} </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 250px;" class="mb-2 "><i class="ti-minus mr-2"></i> Jumlah Biaya (COST) </td>
                                        <td style="text-align: start;" class="">: &nbsp; {{ $item->jumlah_biaya ? 'Rp ' . number_format($item->jumlah_biaya, 0, ',', '.') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 250px;" class="mb-2 font-weight-bold text-success"><i class="ti-minus mr-2"></i> Total Profits</td>
                                        <td style="text-align: start;" class="font-weight-bold text-success">: &nbsp; {{ $item->profit ? 'Rp ' . number_format($item->profit, 0, ',', '.') : '-' }} <i class="fa fa-plus"></i></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-secondary" id="judul">Grafik : {{ $data->batch }}</h6>
                        </div>
                        <div class="card-body">
                            <div id="chartContainer" style="height: 370px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.onload = function() {

        var chart = new CanvasJS.Chart("chartContainer", {
            animationEnabled: true,
            title: {
                text: "Perbanding Profit & Loss"
            },
            data: [{
                type: "pie",
                yValueFormatString: "Rp #,##0",
                indexLabel: "{label} ({y})",
                dataPoints: @json($dataPoints)
            }]
        });
        chart.render();

    }
</script>
@endsection

