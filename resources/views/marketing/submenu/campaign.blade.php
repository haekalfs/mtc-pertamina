@extends('layouts.main')

@section('active-marketing')
active font-weight-bold
@endsection

@section('show-marketing')
show
@endsection

@section('marketing-campaign')
font-weight-bold
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-bullhorn"></i> Marketing Campaign</h1>
        <p class="mb-4">Kegiatan Marketing MTC.</a></p>
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
        padding-bottom: 0;
        padding-top: 0;
        padding-left: 15px;
        padding-right: 15px;
    }

    #dataTable tbody td {
        padding-bottom: 0;
        padding-top: 0;
        padding-left: 15px;
        padding-right: 15px;
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
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Event</a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('marketing-campaign') }}">
                        @csrf
                        <div class="row d-flex justify-content-right mb-4">
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="year">Year:</label>
                                            <select class="custom-select" id="year" name="year">
                                                <option value="all" @if ($yearSelected == 'all') selected @endif>Show All</option>
                                                @for ($i = $currentYear; $i >= $currentYear - 5; $i--)
                                                    <option value="{{ $i }}" @if ($yearSelected == $i) selected @endif>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Month Dropdown -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="month">Month:</label>
                                            <select class="custom-select" id="month" name="month">
                                                <option value="all" @if ($monthSelected == 'all') selected @endif>Show All</option>
                                                @for ($m = 1; $m <= 12; $m++)
                                                    <option value="{{ $m }}" @if ($monthSelected == $m) selected @endif>
                                                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                                    </option>
                                                @endfor
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
                    <div>
                        <table id="dataTable" class="table table-borderless mt-4">
                            <thead class="text-center" style="display: none;">
                                <tr>
                                    <th>Kegiatan Marketing</th>
                                </tr>
                            </thead>
                            <tbody class="mt-4 zoom90">
                                @foreach($data as $item)
                                <tr>
                                    <td>
                                        <div class="card custom-card mb-3 bg-white shadow">
                                            <div class="row no-gutters">
                                                <div class="col-md-3 d-flex align-items-center justify-content-center" style="padding: 2em;">
                                                    <img src="{{ asset($item->img_filepath ? $item->img_filepath : 'https://via.placeholder.com/250x150/5fa9f8/ffffff') }}" style="height: 150px; width: 250px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
                                                </div>
                                                <div class="col-md-7 mt-2">
                                                    <div class="card-body text-secondary">
                                                        <div>
                                                            <h4 class="card-title font-weight-bold">{{ $item->campaign_name }}</h4>
                                                            <ul class="ml-3">
                                                                <li class="card-text"><span class="font-weight-bold">Informasi Kegiatan</span> : {{ $item->campaign_details }}</li>
                                                                <li class="card-text"><span class="font-weight-bold">PIC</span> : {{ $item->user_id }}</li>
                                                                <li class="card-text"><span class="font-weight-bold">Tgl Pelaksanaan</span> : {{ $item->date }}</li>
                                                                <li class="card-text">
                                                                    <span class="font-weight-bold">Hasil Kegiatan</span> : <br>
                                                                    {!! Str::limit($item->campaign_result, 300, '...') !!}
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-center justify-content-center">
                                                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('preview-campaign', $item->id) }}"><i class="menu-Logo fa fa-eye"></i> Preview Event</a>
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

<div class="modal fade zoom90" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 900px;" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('campaign.store') }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="file-upload" style="cursor: pointer;">
                                <img id="image-preview" src="https://via.placeholder.com/50x50/5fa9f8/ffffff"
                                     style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                     <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="file-upload" type="file" name="img" style="display: none;" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex align-items-center mb-3">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Nama Kegiatan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="activity_name" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Informasi Kegiatan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="activity_info">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">PIC :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <select class="custom-select" id="namaPenlat" name="namaPenlat">
                                                    @foreach($users as $user)
                                                    <option value="{{ $user->id }}" selected>{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Tanggal :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="date" class="form-control" name="activity_date">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="d-flex align-items-start mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Hasil Kegiatan :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <textarea id="summernote" name="activity_result"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
$('#summernote').summernote({
    placeholder: 'Hasil Kegiatan...',
    tabsize: 2,
    height: 220,
    toolbar: [
        ['style', ['style']],
        ['font', ['bold', 'underline', 'clear']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video']],
        ['view', ['fullscreen', 'codeview', 'help']]
    ]
});
</script>
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('image-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection

