@extends('layouts.main')

@section('active-penlat')
active font-weight-bold
@endsection

@section('show-penlat')
show
@endsection

@section('penlat')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-list-alt"></i> Preview Pelatihan</h1>
        <p class="mb-3">Pelatihan Details Information.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('penlat') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
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
                <h6 class="m-0 font-weight-bold text-secondary" id="judul">Detail Penlat</h6>
                <div class="text-right">
                    {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Update Data</a> --}}
                </div>
            </div>
            <div class="card-body">
                <div class="col-md-12 mb-3">
                    <div class="row">
                        <div class="col-md-3 d-flex align-items-center justify-content-center" style="padding-left: 1em;">
                            <img src="{{ $data->filepath ? asset($data->filepath) : 'https://via.placeholder.com/50x50/5fa9f8/ffffff' }}" style="height: 150px; width: 200px; border: 1px solid rgb(202, 202, 202);" class="img-fluid d-none d-md-block rounded mb-2 shadow ">
                        </div>
                        <div class="col-md-9">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th style="width: 200px;">Nama Pelatihan</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->description }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 200px;">Alias</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->alias }}</td>
                                </tr>
                                <tr>
                                    <th>Jenis Pelatihan</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->jenis_pelatihan }}</td>
                                </tr>
                                <tr>
                                    <th>Kategori Pelatihan</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->kategori_pelatihan }}</td>
                                </tr>
                            </table>
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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">Referensi Pelatihan</h6>
                </div>
                <div class="card-body">
                    <table id="listReferensi" class="table table-bordered mt-4 zoom90">
                        <thead>
                            <tr>
                                <th>Referensi</th>
                            </tr>
                        </thead>
                        <tbody>@php $no = 1; @endphp
                            @foreach ($data->references as $item)
                            <tr>
                                <td>
                                    <div class="row">
                                        <div class="col-md-12 text-left mt-sm-2">
                                            <h5 class="card-title font-weight-bold">{{ $no++ }}. {{ $item->references }}</h5>
                                            <div class="ml-2">
                                                <i class="ti-minus mr-2"></i> <a href="{{ asset($item->filepath) }}" target="_blank">{{ $item->filepath }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> Kebutuhan Alat Pelatihan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="listUtilities" class="table table-bordered mt-4">
                            <thead>
                                <tr>
                                    <th>Tool</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data->requirement as $tool)
                                    <tr>
                                        <td data-th="Product">
                                            <div class="row">
                                                <div class="col-md-4 text-left">
                                                    <img src="{{ asset($tool->tools->img->filepath) }}" style="height: 100px; width: 100px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow ">
                                                </div>
                                                <div class="col-md-8 text-left mt-sm-2">
                                                    <h5>{{ $tool->tools->asset_name }}</h5>
                                                    {{-- <p class="font-weight-light">Satuan Default ({{$tool->tools->utility_unit}})</p> --}}
                                                </div>
                                            </div>
                                        </td>
                                        <td data-th="Quantity" style="width:10%">
                                            <input type="number" class="form-control form-control-md text-center noline-input" name="amount_{{ $tool->id }}" value="{{ $tool->amount }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="animated fadeIn zoom90">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" id="judul">List of Batches Already Conducted</h6>
                </div>
                <div class="card-body zoom90">
                    <table id="batchTables" class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Display</th>
                                <th>Nama Pelatihan</th>
                                <th>Batch</th>
                                <th>Jenis Pelatihan</th>
                                <th>Tgl Pelaksanaan</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    var table = $('#batchTables').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('preview-penlat', $data->id) }}",
            data: function (d) {
                d.namaPenlat = $('#namaPenlat').val();
            }
        },
        columns: [
            { data: 'display', name: 'display', orderable: false, searchable: false },
            { data: 'nama_pelatihan', name: 'penlat.description' },
            { data: 'batch', name: 'batch' },
            { data: 'jenis_pelatihan', name: 'penlat.jenis_pelatihan' },
            { data: 'tgl_pelaksanaan', name: 'date' }
        ]
    });

    $('#namaPenlat').on('click', function() {
        table.draw();
    });
});
</script>
@endsection

