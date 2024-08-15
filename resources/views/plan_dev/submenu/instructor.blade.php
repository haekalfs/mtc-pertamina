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
        {{-- <a href="{{ route('upload-certificate') }}" class="btn btn-sm btn-primary shadow-sm text-white"><i class="fa fa-file-text fa-sm"></i> Import Cerficate</a> --}}
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
                        <a class="btn btn-primary btn-sm text-white" href="{{ route('register-instructor') }}"><i class="menu-Logo fa fa-plus"></i> Register Instructor</a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('instructor') }}">
                        @csrf
                        <div class="row d-flex justify-content-start mb-1 p-1">
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="email">Nama Pelatihan :</label>
                                            <select class="form-control form-control" name="penlat">
                                                <option value="0" selected>Show All</option>
                                                @foreach ($penlatList as $item)
                                                <option value="{{ $item->id }}" @if ($item->id == $penlatId) selected @endif>{{ $item->description }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex align-self-end justify-content-start">
                                        <div class="form-group">
                                            <div class="align-self-center">
                                                <button type="submit" class="btn btn-primary" style="padding-left: 1.2em; padding-right: 1.2em;"><i class="ti-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive p-2">
                        <table id="dataTable" class="table table-borderless zoom90">
                            <thead class="text-center" style="display: none;">
                                <tr>
                                    <th>Instructors</th>
                                </tr>
                            </thead>
                            <tbody class="mt-2">
                                @foreach($data as $item)
                                <tr>
                                    <td>
                                        <div class="card custom-card mb-3 bg-white shadow-none" style="border: 3px solid rgb(228, 228, 228);">
                                            <div class="row no-gutters">
                                                <div class="col-md-2 d-flex align-items-center justify-content-center" style="padding: 1.2em;">
                                                    <img src="{{ asset($item->imgFilepath) }}" style="height: 150px; width: 120px;" alt="" class="img-fluid d-none d-md-block rounded">
                                                </div>
                                                <div class="col-md-8 mt-2">
                                                    <div class="card-body text-secondary p-2">
                                                        <h5 class="card-title font-weight-bold mb-1">{{ $item->instructor_name }}</h5>
                                                        <div class="ml-2">
                                                            <table class="table table-borderless table-sm mb-0">
                                                                <tr>
                                                                    <td style="width: 180px;"><i class="ti-minus mr-2"></i> Avg Nilai Feedback</td>
                                                                    <td style="text-align: start;">:</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width: 180px;"><i class="ti-minus mr-2"></i> Email</td>
                                                                    <td style="text-align: start;">: {{ $item->instructor_email }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><i class="ti-minus mr-2"></i> Umur</td>
                                                                    <td style="text-align: start;">: {{ $item->instructor_dob }}</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-center justify-content-center">
                                                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('preview-instructor', ['id' => $item->id, 'penlatId' => $penlatId]) }}"><i class="menu-Logo fa fa-eye"></i> Summary</a>
                                                </div>
                                                <div class="card-icons position-absolute" style="top: 10px; right: 10px;">
                                                    <a href="#"><i class="fa fa-download fa-2x text-secondary" style="font-size: 1.5em;"></i></a>
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

