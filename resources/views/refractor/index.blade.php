@extends('layouts.main')

@section('active-refractor')
active font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-trash-o"></i> Data Cleansing</h1>
        <p class="mb-4">Data Cleansing.</a></p>
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
                    <h6 class="m-0 font-weight-bold" id="judul">Data Cleansing</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white"><i class="fa fa-plus"></i> Assign Role to Access</a> --}}
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('delete.data') }}" id="data-deletion-form">
                        @csrf
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="table_name">Database :</label>
                                        <select class="custom-select" id="table_name" name="table_name" required>
                                            <option selected disabled>Choose...</option>
                                            <option value="{{ $encryptedTables['profits'] }}">Profits & Loss</option>
                                            <option value="{{ $encryptedTables['infografis_peserta'] }}">Infografis Peserta</option>
                                            <option value="{{ $encryptedTables['feedback_reports'] }}">Feedback Instruktur</option>
                                            <option value="{{ $encryptedTables['feedback_mtc'] }}">Feedback Pelatihan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="daterange">Date Range :</label>
                                        <input type="text" class="form-control" name="daterange" id="daterange" autocomplete="off" required/>
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex justify-content-center align-items-end">
                                    <div class="form-group">
                                        <button type="button" id="proceed-btn" class="btn btn-primary">Proceed</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <span class="text-danger font-weight-bold">Data Deletion Guidelines</span>
                </div>
                <div class="card-body" style="background-color: rgb(247, 247, 247);">
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">Important Instructions</h6>
                    <ul class="ml-4">
                        <li>Ensure that the selected date range is accurate before proceeding.</li>
                        <li>Select the correct table for deletion based on your needs.</li>
                        <li class="text-danger font-weight-bold">Warning: This action is permanent and cannot be undone once executed.</li>
                        <li>Data will be deleted based on the `tgl_pelaksanaan` field for the selected date range.</li>
                        <li>Always double-check the date range and table before confirming the deletion.</li>
                        <li>If unsure, consult with the admin or your supervisor before proceeding.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    $('input[name="daterange"]').daterangepicker({
        // Setup options as needed
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
    });

    $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
});

document.getElementById('proceed-btn').addEventListener('click', function(e) {
    e.preventDefault();

    swal({
        title: "Are you sure?",
        text: "Do you want to delete data from the selected table?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            document.getElementById('data-deletion-form').submit();
        }
    });
});
</script>
@endsection
