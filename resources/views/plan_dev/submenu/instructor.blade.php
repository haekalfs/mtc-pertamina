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
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-male"></i> Instruktur</h1>
        <p class="mb-4">Menu Instruktur.</a></p>
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
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Instructor</a>
                    </div>
                </div>
                <div class="row-toolbar mt-4 ml-2 zoom90">
                    <div class="col">
                        <select style="max-width: 18%;" class="form-control" id="rowsPerPage">
                            <option value="-1">Show All</option>
                        </select>
                    </div>
                    <div class="col-auto text-right mr-2">
                        <input class="form-control" type="text" id="searchInput" placeholder="Search...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="row zoom90">
                        <div class="col-md-12 mt-2">
                            <div class="card custom-card mb-3 bg-white shadow">
                                <div class="row no-gutters">
                                    <div class="col-md-3 d-flex align-items-center justify-content-center" style="padding: 2em;">
                                        <img src="https://via.placeholder.com/150x150/5fa9f8/ffffff" style="height: 150px; width: 150px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
                                    </div>
                                    <div class="col-md-7 mt-2">
                                        <div class="card-body text-secondary">
                                            <div>
                                                <h4 class="card-title font-weight-bold">Haekal Sastradilaga</h4>
                                                <ul class="ml-3">
                                                    <li class="card-text">Nilai Feedback</li>
                                                    <li class="card-text">Umur</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-center justify-content-center">
                                        <a class="btn btn-outline-secondary btn-sm" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-eye"></i> Review Instructor</a>
                                    </div>
                                    <div class="card-icons">
                                        <a href="#"><i class="fa fa-download fa-2x text-secondary" style="font-size: 1.5em;"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="card custom-card mb-3 bg-white shadow">
                                <div class="row no-gutters">
                                    <div class="col-md-3 d-flex align-items-center justify-content-center" style="padding: 2em;">
                                        <img src="https://via.placeholder.com/150x150/5fa9f8/ffffff" style="height: 150px; width: 150px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
                                    </div>
                                    <div class="col-md-7 mt-2">
                                        <div class="card-body text-secondary">
                                            <div>
                                                <h4 class="card-title font-weight-bold">Nuri Bayu Anggoro</h4>
                                                <ul class="ml-3">
                                                    <li class="card-text">Nilai Feedback</li>
                                                    <li class="card-text">Umur</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-center justify-content-center">
                                        <a class="btn btn-outline-secondary btn-sm" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-eye"></i> Review Instructor</a>
                                    </div>
                                    <div class="card-icons">
                                        <a href="#"><i class="fa fa-download fa-2x text-secondary" style="font-size: 1.5em;"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="card custom-card mb-3 bg-white shadow">
                                <div class="row no-gutters">
                                    <div class="col-md-3 d-flex align-items-center justify-content-center" style="padding: 2em;">
                                        <img src="https://via.placeholder.com/150x150/5fa9f8/ffffff" style="height: 150px; width: 150px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
                                    </div>
                                    <div class="col-md-7 mt-2">
                                        <div class="card-body text-secondary">
                                            <div>
                                                <h4 class="card-title font-weight-bold">Henry Ivan</h4>
                                                <ul class="ml-3">
                                                    <li class="card-text">Nilai Feedback</li>
                                                    <li class="card-text">Umur</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-center justify-content-center">
                                        <a class="btn btn-outline-secondary btn-sm" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-eye"></i> Review Instructor</a>
                                    </div>
                                    <div class="card-icons">
                                        <a href="#"><i class="fa fa-download fa-2x text-secondary" style="font-size: 1.5em;"></i></a>
                                    </div>
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

