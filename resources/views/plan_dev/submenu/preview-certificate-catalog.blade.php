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
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa fa-certificate"></i> Preview Certificate Holder</h1>
        <p class="mb-3">{{ $data->certificate_name }}</a></p>
    </div>
    <div class="d-sm-flex mb-2"> <!-- Add this div to wrap the buttons -->
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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> List Holder</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Add Participant</a> --}}
                    </div>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered mt-4 zoom90">
                        <thead>
                            <tr>
                                <th>Instructors</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data->holder as $item)
                            <tr>
                                <td data-th="Product">
                                    <div class="row">
                                        <div class="col-md-3 d-flex justify-content-center align-items-center text-center">
                                            <img src="{{ $item->instructor->imgFilepath ? asset($item->instructor->imgFilepath) : 'https://via.placeholder.com/150x150/5fa9f8/ffffff' }}" style="height: 150px; width: 120px; border: 1px solid rgb(202, 202, 202);" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
                                        </div>
                                        <div class="col-md-9 text-left mt-sm-2">
                                            <h5 class="card-title font-weight-bold">{{ $item->instructor->instructor_name }}</h5>
                                            <div class="ml-2">
                                                <table class="table table-borderless table-sm">
                                                    <tr>
                                                        <td style="width: 180px;"><i class="ti-minus mr-2"></i> Email</td>
                                                        <td style="text-align: start;">: {{ $item->instructor->instructor_email }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><i class="ti-minus mr-2"></i> Umur</td>
                                                        <td style="text-align: start;">: {{ \Carbon\Carbon::parse($item->instructor->instructor_dob)->age }} Tahun</td>
                                                    </tr>
                                                    <tr>
                                                        <td><i class="ti-minus mr-2"></i> Jam Mengajar</td>
                                                        <td style="text-align: start;">: {{ $item->instructor->working_hours }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 180px;"><i class="ti-minus mr-2"></i> Avg Nilai Feedback</td>
                                                        <td style="text-align: start;">:
                                                            @php
                                                                $roundedScore = round($item->instructor->average_feedback_score, 1); // Round to one decimal place
                                                                $wholeStars = floor($roundedScore);
                                                                $halfStar = ($roundedScore - $wholeStars) >= 0.5;
                                                            @endphp

                                                            @for ($i = 0; $i < 5; $i++)
                                                                @if ($i < $wholeStars)
                                                                    <i class="fa fa-star text-warning"></i>
                                                                @elseif ($halfStar && $i == $wholeStars)
                                                                    <i class="fa fa-star-half-o text-warning"></i>
                                                                @else
                                                                    <i class="fa fa-star-o text-warning"></i>
                                                                @endif
                                                            @endfor
                                                            {{ $roundedScore ?? '-' }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="actions text-center">
                                    <div>
                                        <a class="btn btn-outline-secondary btn-sm mr-2" href="{{ route('preview-instructor', ['id' => $item->instructor->id, 'penlatId' => '0']) }}"><i class="menu-Logo fa fa-eye"></i> Summary</a>
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

