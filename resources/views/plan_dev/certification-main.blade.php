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
        <div class="col-xl-4 col-md-6 animateBox">
            <a href="{{ route('certificate') }}" class="clickable-card">
                <div class="card border-left-primary shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">Certificate Peserta</div>
                                <div class="h6 mb-0 text-gray-800"><span style="font-size: 14px;">Status Sertifikasi Peserta</span></div>
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

        <div class="col-xl-4 col-md-6 animateBox">
            <a href="{{ route('certificate-instructor') }}" class="clickable-card">
                <div class="card border-left-success shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">
                                    Certificate Instruktur</div>
                                    <div class="h6 mb-0 text-gray-800"><span style="font-size: 14px;">Sertifikasi Trainer/Instruktur</span></div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-male fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <span class="text-primary font-weight-bold">Certificate Menu Guidelines</span>
                </div>
                <div class="card-body" style="background-color: rgb(247, 247, 247);">
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">Overview</h6>
                    <p>The Certificate menu provides two essential sub-menus for managing certifications within the MTC system:</p>
                    <ul class="ml-4">
                        <li><strong>Sertifikasi Peserta</strong>: This menu is dedicated to managing the certification status of participants who have undergone training at the MTC. It provides details on their certification progress and outcomes.</li>
                        <li><strong>Instructor Certificate</strong>: This menu is used to manage certifications for instructors or trainers within the MTC. It includes information on the certification status and other relevant details for instructors.</li>
                    </ul>
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">Key Points</h6>
                    <ul class="ml-4">
                        <li>Each sub-menu is tailored to manage and view certification details specific to either participants or instructors.</li>
                        <li>Ensure that all certification data entered or modified is accurate and reflects the current status of the individuals involved.</li>
                        <li>Access to these menus should be restricted to authorized personnel only to maintain data integrity.</li>
                        <li>Regularly update the certification information to ensure it aligns with the latest training outcomes and instructor qualifications.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
