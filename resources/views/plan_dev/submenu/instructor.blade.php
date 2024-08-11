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
        <a href="{{ route('upload-certificate') }}" class="btn btn-sm btn-primary shadow-sm text-white"><i class="fa fa-file-text fa-sm"></i> Import Cerficate</a>
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
    .badge-custom {
        font-size: 0.9rem; /* Adjust the font size */
        padding: 0.5rem 1rem; /* Adjust the padding for height and width */
        border-radius: 0.5rem; /* Optional: Adjust the border radius */
    }
</style>
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
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Instructor</a>
                    </div>
                </div>
                <div class="card-body">
                    <div>
                        <table id="dataTable" class="table table-borderless mt-4">
                            <thead class="text-center" style="display: none;">
                                <tr>
                                    <th>Instructors</th>
                                </tr>
                            </thead>
                            <tbody class="mt-2">
                                <tr>
                                    <td>
                                        <div class="card custom-card mb-3 bg-white shadow">
                                            <div class="row no-gutters">
                                                <div class="col-md-3 d-flex align-items-center justify-content-center" style="padding: 2em;">
                                                    <img src="https://via.placeholder.com/250x150/5fa9f8/ffffff" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
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
                                                    <a class="btn btn-outline-secondary btn-sm" href="#" data-toggle="modal" data-target="#customModal"><i class="menu-Logo fa fa-eye"></i> Review Pelatihan</a>
                                                </div>
                                                <div class="card-icons">
                                                    <a href="#"><i class="fa fa-download fa-2x text-secondary" style="font-size: 1.5em;"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="card custom-card mb-3 bg-white shadow">
                                            <div class="row no-gutters">
                                                <div class="col-md-3 d-flex align-items-center justify-content-center" style="padding: 2em;">
                                                    <img src="https://via.placeholder.com/250x150/5fa9f8/ffffff" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
                                                </div>
                                                <div class="col-md-7 mt-2">
                                                    <div class="card-body text-secondary">
                                                        <div>
                                                            <h4 class="card-title font-weight-bold">Henry</h4>
                                                            <ul class="ml-3">
                                                                <li class="card-text">Nilai Feedback</li>
                                                                <li class="card-text">Umur</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-center justify-content-center">
                                                    <a class="btn btn-outline-secondary btn-sm" href="#" data-toggle="modal" data-target="#customModal"><i class="menu-Logo fa fa-eye"></i> Review Pelatihan</a>
                                                </div>
                                                <div class="card-icons">
                                                    <a href="#"><i class="fa fa-download fa-2x text-secondary" style="font-size: 1.5em;"></i></a>
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
<div class="modal fade" id="customModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content custom-modal-content">
        <div class="modal-header d-flex flex-row align-items-center justify-content-between border-bottom-1">
          <h5 class="modal-title" id="exampleModalLabel">Instructor Detail</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <h6>Serfifikat</h6>
          <div class="badge-container mt-2 mb-4">
            <span class="badge badge-success badge-custom mb-2">Certificate 1 <i class="fa fa-check-square-o"></i></span>
            <span class="badge badge-success badge-custom">Certificate 3 <i class="fa fa-check-square-o"></i></span>
            <span class="badge badge-success badge-custom">Certificate 4 <i class="fa fa-check-square-o"></i></span>
            <span class="badge badge-secondary badge-custom">Badge 2</span>
            <span class="badge badge-secondary badge-custom">Badge 2</span>
          </div>
          <hr>
          <h6>Profil Diri</h6>
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
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
@endsection

