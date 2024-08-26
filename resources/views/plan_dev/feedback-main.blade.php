@extends('layouts.main')

@section('active-pd')
active font-weight-bold
@endsection

@section('show-pd')
show
@endsection

@section('feedback-report')
font-weight-bold
@endsection

@section('content')

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
<div class="animated fadeIn">
    <div class="row">
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 animateBox">
            <a href="{{ route('feedback-mtc') }}" class="clickable-card">
                <div class="card border-left-primary shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">Feedback MTC</div>
                                <div class="h6 mb-0 text-gray-800"><span style="font-size: 14px;">Feedback Report</span></div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-trophy fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="mb-4">
            <div class="outer">
                <div class="inner"></div>
            </div>
        </div>
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 animateBox">
            <a href="{{ route('feedback-report') }}" class="clickable-card">
                <div class="card border-left-success shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">Feedback Instruktur</div>
                                <div class="h6 mb-0 text-gray-800"><span style="font-size: 14px;">Feedback Peserta ke Instruktur</span></div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-male fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>


        <div class="col-xl-12 col-md-12">

            <div class="card mb-4">
                <div class="card-header">
                    <span class="text-danger font-weight-bold">Data Import Guidelines</span>
                </div>
                <div class="card-body" style="background-color: rgb(247, 247, 247);">
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">Import Rules</h6>
                    <ul class="ml-4">
                        <li>Ensure all import files are in the correct format (e.g., CSV, XLSX) as specified in the guidelines.</li>
                        <li>Verify the data in the files for accuracy and completeness before importing.</li>
                        <li>Check for and resolve any data inconsistencies or errors in the file to prevent import issues.</li>
                        <li>Unauthorized changes to import files or procedures are strictly prohibited.</li>
                        <li>Double-check the mapping of data fields to ensure they align with the database schema.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
