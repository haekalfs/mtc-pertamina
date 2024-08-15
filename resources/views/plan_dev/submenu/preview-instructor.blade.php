@extends('layouts.main')

@section('active-pd')
active font-weight-bold
@endsection

@section('show-pd')
show
@endsection

@section('instructor')
font-weight-bold
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-bullhorn"></i> Preview Instructor Biodata</h1>
        <p class="mb-4">Kegiatan Marketing MTC.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('instructor') }}" class="btn btn-sm btn-secondary shadow-sm text-white mr-3"><i class="fa fa-backward"></i> Go Back</a>
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
                    <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Update Data</a>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md-12 mb-3">
                    <div class="row">
                        <div class="col-md-3 d-flex align-items-center justify-content-center" style="padding-left: 1em;">
                            <img src="{{ asset($data->imgFilepath) }}" style="height: 200px; width: 170px; border-radius: 15px;" class="card-img" alt="...">
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th style="width: 200px;">Nama Instruktur</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->instructor_name }}</td>
                                </tr>
                                <tr>
                                    <th>E-Mail Address</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->instructor_email }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Lahir / Umur</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->instructor_dob }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->instructor_address }}</td>
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
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> List Data</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Add Utilities</a> --}}
                    </div>
                </div>
                <div class="card-body mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Profil Diri</h6>
                            <div class="list-group mt-2">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Curriculum Vitae
                                    <a href="path_to_curriculum_vitae.pdf" download class="btn btn-primary btn-sm"><i class="fa fa-download fa-2x" style="font-size: 1.5em;"></i></a>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Ijazah
                                    <a href="path_to_ijazah.pdf" download class="btn btn-primary btn-sm"><i class="fa fa-download fa-2x" style="font-size: 1.5em;"></i></a>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Dokumen Pendukung
                                    <a href="path_to_dokumen_pendukung.pdf" download class="btn btn-primary btn-sm"><i class="fa fa-download fa-2x" style="font-size: 1.5em;"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4 class="card-title font-weight-bold">Qualified Certificates</h4>
                            <div class="ml-2">
                                <table class="table table-borderless table-sm">
                                    @php
                                        $badgeColors = ['bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'bg-primary', 'bg-secondary'];
                                    @endphp

                                    @foreach($certificateData as $index => $certificateItem)
                                        @foreach($certificateItem->catalog->relationOne as $relatedItem)
                                            <tr>
                                                <td class="mb-2">
                                                    <i class="ti-minus mr-2"></i>
                                                    <span class="badge text-white p-2 {{ $badgeColors[$index % count($badgeColors)] }}">
                                                        {{ $relatedItem->penlat->description }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

