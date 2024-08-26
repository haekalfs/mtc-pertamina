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
    /* Custom CSS to align the Select2 container */
    .select2-container--default .select2-selection--single {
        height: calc(2.25rem + 2px); /* Adjust this value to match your input height */
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: calc(2.25rem + 2px); /* Adjust this to vertically align the text */
    }

    .select2-container .select2-selection--single {
        height: 100% !important; /* Ensure the height is consistent */
    }

    .select2-container {
        width: 100% !important; /* Ensure the width matches the form control */
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
                                            <select id="penlatSelect" class="form-control" name="penlat">
                                                <option value="-1" {{ $penlatId == '-1' ? 'selected' : '' }}>Show All</option>
                                                @foreach ($penlatList as $item)
                                                    <option value="{{ $item->id }}" {{ $item->id == $penlatId ? 'selected' : '' }}>
                                                        {{ $item->description }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="email">Status :</label>
                                            <select class="form-control" name="status">
                                                <option value="-1" {{ $statusId == '-1' ? 'selected' : '' }}>Show All</option>
                                                <option value="1" {{ $statusId == '1' ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ $statusId == '0' ? 'selected' : '' }}>Non Active</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex align-self-end justify-content-start mb-1">
                                        <div class="form-group">
                                            <div class="align-self-center">
                                                <button type="submit" class="btn btn-primary" style="padding-left: 1.2em; padding-right: 1.2em;">
                                                    <i class="ti-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered mt-4 zoom90">
                            <thead>
                                <tr>
                                    <th>Instructors</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>
                                    <td data-th="Product">
                                        <div class="row">
                                            <div class="col-md-3 d-flex justify-content-center align-items-center text-center">
                                                <a href="{{ route('preview-instructor', ['id' => $item->id, 'penlatId' => $penlatId]) }}">
                                                    <img src="{{ $item->imgFilepath ? asset($item->imgFilepath) : 'https://via.placeholder.com/150x150/5fa9f8/ffffff' }}" style="height: 150px; width: 120px; border: 1px solid rgb(202, 202, 202);" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
                                                </a>
                                            </div>
                                            <div class="col-md-9 text-left mt-sm-2">
                                                <h5 class="card-title font-weight-bold">{{ $item->instructor_name }}</h5>
                                                <div class="ml-2">
                                                    <table class="table table-borderless table-sm">
                                                        <tr>
                                                            <td style="width: 180px;"><i class="ti-minus mr-2"></i> Email</td>
                                                            <td style="text-align: start;">: {{ $item->instructor_email }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><i class="ti-minus mr-2"></i> Umur</td>
                                                            <td style="text-align: start;">: {{ \Carbon\Carbon::parse($item->instructor_dob)->age }} Tahun</td>
                                                        </tr>
                                                        <tr>
                                                            <td><i class="ti-minus mr-2"></i> Jam Mengajar</td>
                                                            <td style="text-align: start;">: {{ $item->working_hours }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 180px;"><i class="ti-minus mr-2"></i> Avg Nilai Feedback</td>
                                                            <td style="text-align: start;">:
                                                                @php
                                                                    $roundedScore = round($item->average_feedback_score, 1); // Round to one decimal place
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
                                            <a class="btn btn-outline-secondary btn-sm mr-2" href="{{ route('preview-instructor', ['id' => $item->id, 'penlatId' => $penlatId]) }}"><i class="menu-Logo fa fa-eye"></i> Summary</a>
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
<script>
$(document).ready(function() {
    $('#penlatSelect').select2({
        placeholder: "Select Pelatihan...",
        width: '100%',
        height: '100%',
        allowClear: true,
    });
});
</script>
@endsection

