@extends('layouts.main')

@section('active-pd')
active font-weight-bold
@endsection

@section('show-pd')
show
@endsection

@section('monitoring-approval')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-file-text"></i> Monitoring Approvals</h1>
        <p class="mb-4">Monitoring Approval in MTC.</a></p>
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
        padding: 0;
    }

    #dataTable tbody td {
        padding: 0;
        border: none; /* Optional: removes the borders */
    }
</style>
<div class="animated fadeIn zoom90">
    <div class="row mb-4">
        <div class="col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">List Data</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Approval</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive p-2">
                        <table id="dataTable" class="table table-borderless">
                            <thead class="text-center" style="display: none;">
                                <tr>
                                    <th>Documents</th>
                                </tr>
                            </thead>
                            <tbody class="mt-2">
                                @foreach($data as $item)
                                <tr>
                                    <td class="">
                                        <div class="card mb-3">
                                            <div class="card-body custom-card" style="position: relative;">
                                                @php
                                                    // Parse the approval_date and calculate the next year's anniversary
                                                    $approvalDate = Carbon\Carbon::parse($item->approval_date);
                                                    $nextApprovalDate = $approvalDate->copy()->addYear();
                                                    $currentDate = Carbon\Carbon::now();

                                                    // Calculate the number of days left
                                                    $daysLeft = $currentDate->diffInDays($nextApprovalDate, false); // false to count negatives for past dates
                                                @endphp

                                                <!-- Conditionally show the badge -->
                                                <a href="{{ route('preview-monitoring-approval', $item->id) }}" class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                                                    @if($daysLeft <= 30 && $daysLeft > 0)
                                                        <!-- Show the actual number of days left -->
                                                        <span class="badge badge-danger ml-3">{{ $daysLeft }} Days Left</span>
                                                    @elseif($daysLeft <= 0)
                                                        <!-- If the next approval date has passed, show "0 Days Left" -->
                                                        <span class="badge badge-danger ml-3">0 Days Left</span>
                                                    @endif
                                                    <i class="fa fa-edit fa-lg text-secondary ml-2"></i>
                                                </a>
                                                <div class="row no-gutters">
                                                    <div class="col-md-2 d-flex justify-content-center align-items-center text-center">
                                                        <a href="">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-pdf" viewBox="0 0 16 16" style="height: 120px; width: 120px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow animateBox">
                                                                <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1"/>
                                                                <path d="M4.603 12.087a.8.8 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.7 7.7 0 0 1 1.482-.645 20 20 0 0 0 1.062-2.227 7.3 7.3 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.187-.012.395-.047.614-.084.51-.27 1.134-.52 1.794a11 11 0 0 0 .98 1.686 5.8 5.8 0 0 1 1.334.05c.364.065.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.86.86 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.7 5.7 0 0 1-.911-.95 11.6 11.6 0 0 0-1.997.406 11.3 11.3 0 0 1-1.021 1.51c-.29.35-.608.655-.926.787a.8.8 0 0 1-.58.029m1.379-1.901q-.25.115-.459.238c-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361q.016.032.026.044l.035-.012c.137-.056.355-.235.635-.572a8 8 0 0 0 .45-.606m1.64-1.33a13 13 0 0 1 1.01-.193 12 12 0 0 1-.51-.858 21 21 0 0 1-.5 1.05zm2.446.45q.226.244.435.41c.24.19.407.253.498.256a.1.1 0 0 0 .07-.015.3.3 0 0 0 .094-.125.44.44 0 0 0 .059-.2.1.1 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a4 4 0 0 0-.612-.053zM8.078 5.8a7 7 0 0 0 .2-.828q.046-.282.038-.465a.6.6 0 0 0-.032-.198.5.5 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822q.036.167.09.346z"/>
                                                            </svg>
                                                        </a>
                                                    </div>
                                                    <div class="col-md-10 text-left">
                                                        <div class="col-md-12">
                                                            <h5 class="card-title text-secondary font-weight-bold">{{ $item->description }}</h5>

                                                            <!-- Stack Type and Status vertically -->
                                                            <p class="mb-1">Type : {{ $item->type }}</p>
                                                            <p class="mb-2">Status : {{ $item->approval_date }}</p>

                                                            <a href="{{ asset($item->filepath) }}" class="card-text"><u>Lampiran Dokumen</u> <i class="fa fa-external-link fa-sm"></i> <small>{{ $item->filesize }} Kb</small></a>
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
</div>

<div class="modal fade" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('monitoring-approval.store') }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-12">
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex align-items-start mb-4">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Nama Documents :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <textarea class="form-control" rows="3" name="document_name"></textarea>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Document Type :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="type" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Approved Date :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="date" class="form-control" name="approved_date" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Dokumen :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="file" class="form-control" name="file" required>
                                            </div>
                                        </div>
                                    </div>
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
@endsection

