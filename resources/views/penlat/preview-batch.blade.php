@extends('layouts.main')

@section('active-penlat')
active font-weight-bold
@endsection

@section('show-penlat')
show
@endsection

@section('batch-penlat')
font-weight-bold
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-folder"></i> Preview Batch Detail</h1>
        <p class="mb-3">Batch Details Information.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('batch-penlat') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
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
                        <div class="col-md-2 d-flex align-items-center justify-content-center" style="padding-left: 1em;">
                            <img src="{{ $data->filepath ? asset($data->filepath) : 'https://via.placeholder.com/50x50/5fa9f8/ffffff' }}" style="height: 150px; width: 150px; border-radius: 15px;" class="card-img" alt="...">
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th style="width: 200px;">Nama Pelatihan</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->penlat->description }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 200px;">Nama Program</th>
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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> List Participants</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Add Utilities</a> --}}
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title font-weight-bold">Peserta</h5>
                    <ul class="ml-4">
                        @if(!$data->certificate || !$data->certificate->participant || $data->certificate->participant->isEmpty())
                            No Data Available
                        @else
                            @foreach ($data->certificate->participant as $item)
                                <li class="card-text">{{ $item->peserta->nama_peserta }}</li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> List Penggunaan Utilities</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Add Utilities</a> --}}
                    </div>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered mt-4">
                        <thead>
                            <tr>
                                <th style="width:60%">Tool</th>
                                <th style="width:10%">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data->penlat_usage as $tool)
                                <tr>
                                    <td data-th="Product">
                                        <div class="row">
                                            <div class="col-md-4 text-left">
                                                <img src="{{ asset($tool->utility->filepath) }}" style="height: 100px; width: 100px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow ">
                                            </div>
                                            <div class="col-md-8 text-left mt-sm-2">
                                                <h5>{{ $tool->utility->utility_name }}</h5>
                                                <p class="font-weight-light">Satuan Default ({{$tool->utility->utility_unit}})</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center" style="width:7%">
                                        {{ $tool->amount }}
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

@endsection

