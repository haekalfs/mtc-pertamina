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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">List Holder</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Add Participant</a> --}}
                    </div>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-striped mt-4" style="border: 1px solid rgb(229, 229, 229);">
                        <thead class="text-secondary" style="background-color: #ecedee;">
                            <tr>
                                <th>Avatar</th>
                                <th>Name</th>
                                <th>Umur</th>
                                <th>Gender</th>
                                <th>Jam Mengajar</th>
                                <th>Total Feedback</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data->holder as $item)
                            <tr>
                                <td>
                                    <div class="round-img"><a href=""><img class="rounded-circle" src="{{ $item->instructor->imgFilepath ? asset($item->instructor->imgFilepath) : asset('img/default-img.png') }}" style="height: 70px; width: 70px; border: 1px solid rgb(202, 202, 202);"></a></div>
                                </td>
                                <td>
                                    @php
                                        $roundedScore = round($item->instructor->average_feedback_score, 1); // Round to one decimal place
                                        $wholeStars = floor($roundedScore);
                                        $halfStar = ($roundedScore - $wholeStars) >= 0.5;
                                    @endphp
                                    <div>{{ $item->instructor->instructor_name }}<div style="margin-top: 3px;"><span><i class="fa fa-star text-warning"></i> {{ $roundedScore ?? '-' }}</span></div></div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($item->instructor->instructor_dob)->age }} Tahun</td>
                                <td>{{ $item->instructor->instructor_gender }}</td>
                                <td>{{ $item->instructor->working_hours ? $item->instructor->working_hours : '-' }}</td>
                                <td>{{ $item->instructor->feedbacks->count() / 5 }} Feedbacks</td>
                                <td class="actions text-center">
                                    <div>
                                        <a class="btn btn-outline-secondary btn-sm mr-2" href="{{ route('preview-instructor', ['id' => $item->instructor->id, 'penlatId' => '0']) }}"><i class="menu-Logo fa fa-eye"></i> Preview</a>
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

