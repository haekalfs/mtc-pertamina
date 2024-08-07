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
        <p class="mb-4">Unduh Pencapaian Akhlak.</a></p>
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
                    <div class="col-md-12 text-center d-flex align-items-center justify-content-center mt-4">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination">
                              <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                              <li class="page-item"><a class="page-link" href="#">1</a></li>
                              <li class="page-item"><a class="page-link" href="#">2</a></li>
                              <li class="page-item"><a class="page-link" href="#">3</a></li>
                              <li class="page-item"><a class="page-link" href="#">Next</a></li>
                            </ul>
                          </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

