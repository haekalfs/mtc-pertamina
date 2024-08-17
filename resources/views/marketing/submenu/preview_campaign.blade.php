@extends('layouts.main')

@section('active-marketing')
active font-weight-bold
@endsection

@section('show-marketing')
show
@endsection

@section('marketing-campaign')
font-weight-bold
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-bullhorn"></i> {{ $data->campaign_name }}</h1>
        <p class="mb-3">Kegiatan Marketing MTC.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('marketing-campaign') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
        {{-- <a href="{{ route('delete-campaign', $data->id) }}" class="btn btn-sm btn-danger shadow-sm text-white"><i class="fa fa-trash-o"></i> Delete</a> --}}
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
<style>
.alert-success-saving-mid {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 20px;
    border-radius: 5px;
    text-align: center;
    z-index: 10000;
}

.custom-card {
    border: none;
    color: white;
    border-radius: 15px;
}

.card-text {
    margin-bottom: 0;
    color: rgb(0, 0, 0);
}

.out-of-stock {
    background-color: #dc3545;
    color: white;
    font-weight: bold;
}

.card-icons {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-icons .badge {
    background-color: red;
    color: rgb(255, 255, 255);
}

.card-icons i {
    font-size: 20px;
    color: rgb(0, 0, 0);
}

.card-icons a {
    color: rgb(0, 0, 0);
    text-decoration: none;
}

.card-icons a:hover {
    color: lightgray;
}

.img-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
}

.img-container img {
    max-height: 100%;
    max-width: 100%;
    object-fit: cover;
}

</style>
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
                            <img src="{{ asset($data->img_filepath) }}" style="height: 150px; width: 250px; border-radius: 15px;" class="card-img" alt="...">
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th style="width: 200px;">Judul Kegiatan</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->campaign_name }}</td>
                                </tr>
                                <tr>
                                    <th>Informasi Kegiatan</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->campaign_details }}</td>
                                </tr>
                                <tr>
                                    <th>PIC</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->user_id }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Pelaksanaan</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->date }}</td>
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
                <div class="card-body">
                    {!! $data->campaign_result !!}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

