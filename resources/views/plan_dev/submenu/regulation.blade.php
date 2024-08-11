@extends('layouts.main')

@section('active-pd')
active font-weight-bold
@endsection

@section('show-pd')
show
@endsection

@section('regulation')
font-weight-bold
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-building-o"></i> Regulation</h1>
        <p class="mb-4">Regulation in MTC.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
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
    #dataTable tbody tr {
        margin: 0;
        padding: 0;
    }

    #dataTable tbody td {
        padding: 0;
        border: none; /* Optional: removes the borders */
    }
</style>
<div class="animated fadeIn zoom90">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> List Data</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Regulation</a>
                    </div>
                </div>
                <div class="card-body">
                    <div>
                        <table id="dataTable" class="table table-borderless mt-4">
                            <thead class="text-center" style="display: none;">
                                <tr>
                                    <th>Regulations</th>
                                </tr>
                            </thead>
                            <tbody class="mt-2">
                                <tr>
                                    <td>
                                        <div class="card mb-3">
                                            <div class="card-body custom-card">
                                                <div class="row no-gutters">
                                                    <div class="col-md-10 mt-2">
                                                        <h5 class="card-title text-secondary font-weight-bold">Nama Regulasi</h5>
                                                        <a href="" class="card-text"><u>Lampiran Dokumen</u> <i class="fa fa-external-link fa-sm"></i></a>
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-center justify-content-center">
                                                        <a class="btn btn-outline-secondary btn-sm" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-eye"></i> Review Regulation</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="card mb-3">
                                            <div class="card-body custom-card">
                                                <div class="row no-gutters">
                                                    <div class="col-md-10 mt-2">
                                                        <h5 class="card-title text-secondary font-weight-bold">Testing</h5>
                                                        <a href="" class="card-text"><u>Lampiran Dokumen</u> <i class="fa fa-external-link fa-sm"></i></a>
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-center justify-content-center">
                                                        <a class="btn btn-outline-secondary btn-sm" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-eye"></i> Review Regulation</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

