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
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-male"></i> Preview Instructor Biodata</h1>
        <p class="mb-4">Biodata Instruktur.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
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
                <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> Biodata</h6>
                <div class="text-right">
                    <a class="btn btn-secondary btn-sm" href="{{ route('edit-instructor', $data->id) }}"><i class="menu-Logo fa fa-edit"></i></a>
                </div>
            </div>
            <div class="card-body text-secondary">
                <div class="col-md-12 mb-3">
                    <div class="row">
                        <div class="col-md-3 d-flex align-items-center justify-content-center" style="padding-left: 1em;">
                            <img src="{{ asset($data->imgFilepath) }}" style="height: 200px; width: 170px; border: 1px solid rgb(202, 202, 202);" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
                        </div>
                        <div class="col-md-9">
                            <h4 class="mb-2 font-weight-bold"><i class="fa fa-user"></i> Profile Instruktur</h4>
                            <hr>
                            <div class="row">
                                <div class="col-md-8">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <th style="width: 200px;">Nama Instruktur</th>
                                            <td style="text-align: start; font-weight:500">: <span class="ml-2">{{ $data->instructor_name }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>E-Mail Address</th>
                                            <td style="text-align: start; font-weight:500">: <span class="ml-2">{{ $data->instructor_email }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Lahir / Umur</th>
                                            <td style="text-align: start; font-weight:500">: <span class="ml-2">{{ $data->instructor_dob }} / {{ \Carbon\Carbon::parse($data->instructor_dob)->age}} Tahun</span></td>
                                        </tr>
                                        <tr>
                                            <th>Address</th>
                                            <td style="text-align: start; font-weight:500">: <span class="ml-2">{{ $data->instructor_address }}</span></td>
                                        </tr>
                                  </table>
                                </div>
                                <div class="col-md-4">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td style="text-align: end; font-size: 20px;">
                                                @php
                                                    $roundedScore = round($data->average_feedback_score, 1); // Round to one decimal place
                                                    $wholeStars = floor($roundedScore);
                                                    $halfStar = ($roundedScore - $wholeStars) >= 0.5;
                                                @endphp

                                                @for ($i = 0; $i < 5; $i++)
                                                    @if ($i < $wholeStars)
                                                        <i class="fa fa-star"></i>
                                                    @elseif ($halfStar && $i == $wholeStars)
                                                        <i class="fa fa-star-half-o"></i>
                                                    @else
                                                        <i class="fa fa-star-o"></i>
                                                    @endif
                                                @endfor
                                                {{ $roundedScore ?? '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: end; font-size: 20px;">
                                                {{ $data->working_hours }}
                                            </td>
                                        </tr>
                                  </table>
                                </div>
                            </div>
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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-certificate"></i> Attachments</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Add Utilities</a> --}}
                    </div>
                </div>
                <div class="card-body mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="mb-3 card-title font-weight-bold">Attachments</h4>
                            <div class="list-group mt-2">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Curriculum Vitae
                                    <a href="{{ asset($data->cvFilepath) }}" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-download fa-2x" style="font-size: 1.5em;"></i></a>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Ijazah
                                    <a href="{{ asset($data->ijazahFilepath) }}" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-download fa-2x" style="font-size: 1.5em;"></i></a>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    Dokumen Pendukung
                                    <a href="{{ asset($data->documentPendukungFilepath) }}" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-download fa-2x" style="font-size: 1.5em;"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4 class="card-title font-weight-bold">@if($penlatId == '-1') All @else Qualified @endif Certificates</h4>
                            <div class="ml-2">
                                <table class="table table-borderless table-sm">
                                    @php
                                        $badgeColors = ['bg-success'];
                                    @endphp

                                    @foreach($certificateData as $index => $certificateItem)
                                        <tr>
                                            <td class="mb-2">
                                                <i class="ti-minus mr-2"></i>
                                                <span class="badge text-white p-2 {{ $badgeColors[$index % count($badgeColors)] }}">
                                                    {{ $certificateItem->catalog->certificate_name }}
                                                </span>
                                                {{-- <span class="text-success"><i class="fa fa-check"></i></span> --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                            <div class="ml-2">
                                <table class="table table-borderless table-sm">
                                    @php
                                        $otherBadges = ['bg-secondary'];
                                    @endphp

                                    @foreach($remainingCerts as $index => $certs)
                                        <tr>
                                            <td class="mb-2">
                                                <i class="ti-minus mr-2"></i>
                                                <span class="badge text-white p-2 {{ $otherBadges[$index % count($otherBadges)] }}">
                                                    {{ $certs->catalog->certificate_name }}
                                                </span>
                                            </td>
                                        </tr>
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

