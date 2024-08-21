@extends('layouts.main')

@section('active-pd')
active font-weight-bold
@endsection

@section('show-pd')
show
@endsection

@section('certificate')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa fa-certificate"></i> Preview Certificate Catalog</h1>
        <p class="mb-3">Certificate Details Information.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('certificate-instructor') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> List Participants</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Add Participant</a> --}}
                    </div>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-borderless zoom90">
                        <thead class="text-center" style="display: none;">
                            <tr>
                                <th>Instructors</th>
                            </tr>
                        </thead>
                        <tbody class="mt-2">
                            @foreach ($data->holder as $item)
                            <tr>
                                <td>
                                    <div class="card custom-card mb-3 bg-white shadow-none" style="border: 3px solid rgb(228, 228, 228);">
                                        <div class="row no-gutters">
                                            <div class="col-md-2 d-flex align-items-center justify-content-center animateBox" style="padding: 1.2em;">
                                                <img src="{{ asset($item->instructor->imgFilepath) }}" style="height: 150px; width: 120px; border: 1px solid rgb(202, 202, 202);" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
                                            </div>
                                            <div class="col-md-8 mt-2">
                                                <div class="card-body text-secondary p-2">
                                                    <h5 class="card-title font-weight-bold mb-1 mt-2">{{ $item->instructor->instructor_name }}</h5>
                                                    <div class="ml-2">
                                                        <table class="table table-borderless table-sm mb-0 mt-2">
                                                            <tr>
                                                                <td style="width: 180px;"><i class="ti-minus mr-2"></i> Email</td>
                                                                <td style="text-align: start;">: {{ $item->instructor->instructor_email }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><i class="ti-minus mr-2"></i> Umur</td>
                                                                <td style="text-align: start;">: {{ \Carbon\Carbon::parse($item->instructor->instructor_dob)->age}} Tahun</td>
                                                            </tr>
                                                            <tr>
                                                                <td><i class="ti-minus mr-2"></i> Jam Mengajar</td>
                                                                <td style="text-align: start;">: {{ $item->instructor->working_hours }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="width: 180px;"><i class="ti-minus mr-2"></i> Avg Nilai Feedback</td>
                                                                <td style="text-align: start;">:</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
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

@endsection

