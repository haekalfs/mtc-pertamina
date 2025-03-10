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
                                <div class="stat-heading mb-1 font-weight-bold">Training Certifications</div>
                                <div class="h6 mb-0 text-gray-800"><span style="font-size: 14px;">Training Certifications Status</span></div>
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
                                <div class="stat-heading mb-1 font-weight-bold">Instructors Certificates</div>
                                    <div class="h6 mb-0 text-gray-800"><span style="font-size: 14px;">List Certificates for Instructor</span></div>
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
                    <span class="font-weight-bold">Certificate Menu Guide</span>
                </div>
                <div class="card-body" style="background-color: rgb(247, 247, 247);">
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">Overview</h6>
                    <p>The Certificate menu provides two main sub-menus for managing certifications in the MTC system:</p>
                    <ul class="ml-4">
                        <li><strong>Participant Certification</strong>: This menu is used to manage the certification status of participants who have completed training at MTC. It provides details on their progress and certification results.</li>
                        <li><strong>Instructor Certification</strong>: This menu is used to manage certifications for instructors or trainers at MTC. It includes information on their certification status and other relevant details.</li>
                    </ul>
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">Key Points</h6>
                    <ul class="ml-4">
                        <li>Each sub-menu is designed to manage and view specific certification details for both participants and instructors.</li>
                        <li>Ensure that all certification data entered or modified is accurate and reflects the current status of the respective individual.</li>
                        <li>Access to this menu should be restricted to authorized personnel to maintain data integrity.</li>
                        <li>Update certification information regularly to align with the latest training outcomes and instructor qualifications.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
