@extends('layouts.main')

@section('active-barcode_page')
active font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-qrcode"></i> Generate Qr Code</h1>
        <p class="mb-4">Generate Custom QR Code PMTC.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
    </div>
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
                    <h6 class="m-0 font-weight-bold" id="judul">Generate Barcoed</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white"><i class="fa fa-plus"></i> Assign Role to Access</a> --}}
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('generate.Qr') }}">
                        @csrf
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="qr_text">Text <small class="text-danger">(Max 255)</small>:</label>
                                        <textarea class="form-control" name="qr_text" id="qr_text" autocomplete="off" required></textarea>
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex justify-content-center align-items-end">
                                    <div class="form-group">
                                        <button type="submit" id="proceed-btn" class="btn btn-primary">Proceed</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <span class="text-dark font-weight-bold">Barcode Generation Guidelines</span>
                </div>
                <div class="card-body" style="background-color: rgb(247, 247, 247);">
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">Important Instructions</h6>
                    <ul class="ml-4">
                        <li>Ensure that the text input is accurate, formatted correctly, and does not exceed 255 characters.</li>
                        <li>For optimal readability, avoid text that is too short, as this may lead to an unreadable or overly simplified QR code.</li>
                        <li class="text-danger font-weight-bold">Note: Review the input carefully as the generated barcode will directly reflect the provided data.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
