@extends('layouts.main')

@section('active-operation')
active font-weight-bold
@endsection

@section('show-operation')
show
@endsection

@section('tool-inventory')
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
            <a href="{{ route('tool-inventory') }}" class="clickable-card">
                <div class="card border-left-primary shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">Inventory Tools</div>
                                <div class="h6 mb-0 text-gray-800"><span style="font-size: 14px;">Inventory Tools PMTC</span></div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-fire-extinguisher fa-2x text-primary"></i>
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
            <a href="{{ route('audit-inventory') }}" class="clickable-card">
                <div class="card border-left-success shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">History</div>
                                    <div class="h6 mb-0 text-gray-800"><span style="font-size: 14px;">Audit Log of Inventory Tools</span></div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-exclamation-circle fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <span class="font-weight-bold">Guide to the Inventory Management Menu</span>
                </div>
                <div class="card-body" style="background-color: rgb(247, 247, 247);">
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">Overview</h6>
                    <p>The Inventory Management menu in PMTC consists of two main sections to facilitate inventory tracking and auditing:</p>
                    <ul class="ml-4">
                        <li><strong>Inventory Tools Management</strong>: This section is designed to manage and oversee the inventory of tools and equipment in PMTC. It allows users to track the status, availability, and usage of each inventory item.</li>
                        <li><strong>Audit Log for Inventory Management</strong>: This section provides a detailed log of all inventory-related activities, ensuring transparency and accountability. It records changes, updates, and actions taken within the inventory system.</li>
                    </ul>
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">Key Points</h6>
                    <ul class="ml-4">
                        <li>Each section is structured to streamline inventory tracking and auditing within PMTC.</li>
                        <li>Ensure all inventory data entered or modified is accurate and reflects the latest status of tools and equipment.</li>
                        <li>Access to this menu should be restricted to authorized personnel to maintain data integrity.</li>
                        <li>Regularly update inventory records to align with current stock levels and usage history.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
