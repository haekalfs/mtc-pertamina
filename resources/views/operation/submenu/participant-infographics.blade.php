@extends('layouts.main')

@section('active-penlat')
active font-weight-bold
@endsection

@section('show-penlat')
show
@endsection

@section('participant-infographics')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="menu-icon fa fa-users"></i> Training Participants</h1>
        <p class="mb-4">Training Participants PMTC.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('participant-infographics-import-page') }}" class="btn btn-sm btn-primary shadow-sm text-white"><i class="fa fa-file-text fa-sm"></i> Import Data</a>
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

@if ($message = Session::get('error_log'))
<div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{!! $message !!}</strong>
</div>
@endif
<div class="animated fadeIn zoom90">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" id="judul">List Data</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Participant</a>
                    </div>
                </div>
                <div class="card-body zoom80">
                    <div class="row d-flex justify-content-start mb-4">
                        <div class="col-md-12">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <div class="form-group" id="penlatContainer">
                                        <label for="email">Nama Penlat :</label>
                                        <select class="custom-select" id="namaPenlat" name="namaPenlat">
                                            <option value="1" selected>Show All</option>
                                            @foreach($listPenlat as $participant)
                                                <option value="{{ $participant->nama_program }}" @if (in_array($participant->nama_program, $selectedArray)) selected @endif>{{ $participant->nama_program }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="position_id">STCW/Non :</label>
                                        <select name="stcw" class="form-control" id="stcw">
                                            <option value="1">Show All</option>
                                            @foreach($listStcw as $participant)
                                                <option value="{{ $participant->kategori_program }}" @if (in_array($participant->kategori_program, $selectedArray)) selected @endif>{{ $participant->kategori_program }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="status">Jenis Penlat :</label>
                                        <select class="form-control" id="jenisPenlat" name="jenisPenlat" required>
                                            <option value="1" selected>Show All</option>
                                            @foreach($listJenisPenlat as $participant)
                                                <option value="{{ $participant->jenis_pelatihan }}" @if (in_array($participant->jenis_pelatihan, $selectedArray)) selected @endif>{{ $participant->jenis_pelatihan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="position_id">TW :</label>
                                        <select name="tw" class="form-control" id="tw">
                                            <option value="1">Show All</option>
                                            @foreach($listTw as $participant)
                                                <option value="{{ $participant->realisasi }}" @if (in_array($participant->realisasi, $selectedArray)) selected @endif>{{ $participant->realisasi }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="position_id">Periode :</label>
                                        <select name="periode" class="form-control" id="periode">
                                            <option value="1" selected>Show All</option>
                                            @foreach (array_reverse($yearsBefore) as $year)
                                                <option value="{{ $year }}" @if (in_array($year, $selectedArray)) selected @endif>{{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex align-self-end justify-content-start">
                                    <div class="form-group">
                                        <div class="align-self-center">
                                            <button id="filterButton" class="btn btn-primary" style="padding-left: 1.2em; padding-right: 1.2em;"><i class="ti-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="listPeserta" class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama Peserta</th>
                                    <th>Nama Program</th>
                                    <th>Tgl Pelaksanaan</th>
                                    <th>Batch</th>
                                    <th>Tempat Pelaksanaan</th>
                                    <th>Jenis Pelatihan</th>
                                    <th>Keterangan</th>
                                    <th>Subholding</th>
                                    <th>Perusahaan</th>
                                    <th>Kategori Program</th>
                                    <th>Realisasi</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 1000px;" role="document">
        <div class="modal-content">
			<div class="modal-header d-flex flex-row align-items-center justify-content-between border-bottom-1">
                <h5 class="modal-title" id="inputDataModalLabel">Register Participant</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{ route('infografis.store') }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <input type="hidden" id="editId" name="id">
                    <div class="d-flex justify-content-end mb-4">
                        <!-- Small width for Tgl Pelaksanaan in the right top corner -->
                        <div class="d-flex align-items-center" style="width: 330px;">
                            <div style="width: 150px;" class="mr-2">
                                <p style="margin: 0;">Tgl Pelaksanaan:</p>
                            </div>
                            <div class="flex-grow-1">
                                <input type="date" class="form-control underline-input" name="tgl_pelaksanaan" style="width: 150px;" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Person ID:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" name="person_id" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Nama Peserta:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" name="nama_peserta" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 300px;" class="mr-2">
                                    <p style="margin: 0;">Nama Program:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <select id="infografis" class="form-control" name="nama_program">
                                        <option selected disabled>Select Pelatihan...</option>
                                        @foreach ($penlatList as $item)
                                            <option value="{{ $item->id }}">{{ $item->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Batch:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <select id="mySelect2" class="form-control" name="batch"></select>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Lokasi:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" name="tempat_pelaksanaan" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Seafarer Code:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" name="seafarer_code" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Jenis Pelatihan:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" name="jenis_pelatihan" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Keterangan:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" name="keterangan" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Subholding:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" name="subholding" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Perusahaan:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" name="perusahaan" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Kategori Program:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" name="kategori_program" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Realisasi:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" name="realisasi" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Insert Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade zoom90" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 1000px;" role="document">
        <div class="modal-content">
			<div class="modal-header d-flex flex-row align-items-center justify-content-between border-bottom-1">
                <h5 class="modal-title" id="editModalLabel">Edit Participant</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <input type="hidden" id="editId" name="id">
                    <div class="d-flex justify-content-end mb-4">
                        <!-- Small width for Tgl Pelaksanaan in the right top corner -->
                        <div class="d-flex align-items-center" style="width: 330px;">
                            <div style="width: 150px;" class="mr-2">
                                <p style="margin: 0;">Tgl Pelaksanaan:</p>
                            </div>
                            <div class="flex-grow-1">
                                <input type="date" class="form-control underline-input" id="editTglPelaksanaan" name="tgl_pelaksanaan" style="width: 150px;" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Person ID:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="editPersonId" name="person_id" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Nama Peserta:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="editNamaPeserta" name="nama_peserta" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Nama Program:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="editNamaProgram" name="nama_program" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Batch:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="editBatch" name="batch" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Lokasi:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="editTempatPelaksanaan" name="tempat_pelaksanaan" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Seafarer Code:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="editSeafarerCode" name="seafarer_code" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Jenis Pelatihan:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="editJenisPelatihan" name="jenis_pelatihan" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Keterangan:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="editKeterangan" name="keterangan" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Subholding:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="editSubholding" name="subholding" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Perusahaan:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="editPerusahaan" name="perusahaan" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Kategori Program:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="editKategoriProgram" name="kategori_program" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Realisasi:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="editRealisasi" name="realisasi" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger">Delete Data</button>
                    <button type="submit" class="btn btn-primary">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
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
<script>
$(document).ready(function() {
    $('#namaPenlat').select2({
        placeholder: "Select Pelatihan...",
        width: '100%',
        allowClear: true,
        dropdownParent: $('#penlatContainer'),
        language: {
            noResults: function() {
                return "No result match your request... Create new in Master Data Menu!"; // Customize this message as needed
            }
        }
    });

    var table = $('#listPeserta').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('participant-infographics') }}",
            data: function (d) {
                d.namaPenlat = $('#namaPenlat').val();
                d.stcw = $('#stcw').val();
                d.jenisPenlat = $('#jenisPenlat').val();
                d.tw = $('#tw').val();
                d.periode = $('#periode').val();
            }
        },
        columns: [
            { data: 'nama_peserta', name: 'nama_peserta' },
            { data: 'nama_program', name: 'nama_program' },
            { data: 'tgl_pelaksanaan', name: 'tgl_pelaksanaan' },
            { data: 'batch', name: 'batch' },
            { data: 'tempat_pelaksanaan', name: 'tempat_pelaksanaan' },
            { data: 'jenis_pelatihan', name: 'jenis_pelatihan' },
            { data: 'keterangan', name: 'keterangan' },
            { data: 'subholding', name: 'subholding' },
            { data: 'perusahaan', name: 'perusahaan' },
            { data: 'kategori_program', name: 'kategori_program' },
            { data: 'realisasi', name: 'realisasi' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
    order: [[2, 'desc']]
    });

    // Filter button click event
    $('#filterButton').click(function() {
        table.draw();
    });

    // Use event delegation to handle clicks on dynamically generated elements
    $('#listPeserta').on('click', '.edit-btn', function() {
        var id = $(this).data('item-id');
        $.ajax({
            url: '/infografis-peserta/' + id + '/edit',
            method: 'GET',
            success: function(data) {
                $('#editId').val(data.id);
                $('#editNamaPeserta').val(data.nama_peserta);
                $('#editNamaProgram').val(data.nama_program);
                $('#editTglPelaksanaan').val(data.tgl_pelaksanaan);
                $('#editTempatPelaksanaan').val(data.tempat_pelaksanaan);
                $('#editJenisPelatihan').val(data.jenis_pelatihan);
                $('#editKeterangan').val(data.keterangan);
                $('#editSubholding').val(data.subholding);
                $('#editBatch').val(data.batch);
                $('#editSeafarerCode').val(data.seafarer_code);
                $('#editPersonId').val(data.participant_id);
                $('#editPerusahaan').val(data.perusahaan);
                $('#editKategoriProgram').val(data.kategori_program);
                $('#editRealisasi').val(data.realisasi);
                $('#editModal').modal('show');
            }
        });
    });

    $('#editForm').submit(function(e) {
        e.preventDefault();
        var id = $('#editId').val();
        $.ajax({
            url: '/infografis-peserta/' + id,
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                table.draw(); // Redraw the table
                $('#editModal').modal('hide');
                $('.alert-success-saving-mid').show();
                $('.overlay-mid').show();
                $('.alert-success-saving-mid').text(response.message);
                setTimeout(function() {
                    $('.alert-success-saving-mid').fadeOut('slow');
                    $('.overlay-mid').fadeOut('slow');
                }, 1000);
            }
        });
    });

    // Event listener for the Delete Data button
    $('#editModal').on('click', '.btn-danger', function() {
        var id = $('#editId').val(); // Get the ID of the participant to delete from the hidden input field

        // Use SweetAlert to confirm the deletion
        swal({
            title: "Are you sure?",
            text: "This action will delete participant data & certificate, once deleted, you will not be able to recover this participant's data!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                // If the user confirms, proceed with the deletion
                $.ajax({
                    url: '/infografis-peserta-delete-data/' + id,  // Use the ID from the hidden input field
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}', // Add CSRF token
                    },
                    success: function(response) {
                        // Hide the modal
                        $('#editModal').modal('hide');

                        // Show success alert with SweetAlert
                        swal("Success!", response.message, "success");

                        // Optionally, refresh the table or perform other actions
                        table.draw();
                    },
                    error: function(xhr) {
                        // Handle errors (optional)
                        swal("Error!", "Something went wrong. Please try again.", "error");
                    }
                });
            }
        });
    });
});


$(document).ready(function() {
    // Update hidden input on Penlat change
    $('#infografis').on('change', function() {

        // Reinitialize the batch select dropdown, passing the selected penlat_id
        initSelect2WithAjax('mySelect2', '{{ route('batches.fetch') }}', 'Select or add a Batch', $(this).val());
    });

    // Initialize Select2 with AJAX for the batch dropdown (default initialization)
    initSelect2WithAjax('mySelect2', '{{ route('batches.fetch') }}', 'Select or add a Batch', null);
});

function initSelect2WithAjax(elementId, ajaxUrl, placeholderText, penlatId = null) {
    $('#' + elementId).select2({
        ajax: {
            url: ajaxUrl,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page || 1, // pagination
                    penlat_id: penlatId // pass penlat_id for filtering, if provided
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: $.map(data.items, function (item) {
                        return {
                            id: item.batch, // Use the 'batch' column for the option value
                            text: item.batch, // Use the 'batch' column for the option label
                            filepath: item.filepath, // Include filepath to use for image preview
                            date: item.date // Include date to prefill the date input
                        };
                    }),
                    pagination: {
                        more: data.total_count > (params.page * 10) // Check if more results are available
                    }
                };
            },
            cache: true
        },
        placeholder: placeholderText,
        minimumInputLength: 1, // Start searching after 1 character
        dropdownParent: $('#inputDataModal'),
        theme: 'classic',
        width: '100%',
        tags: true, // Allow adding new tags
        createTag: function (params) {
            var term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true // Mark as a new tag
            };
        },
        templateResult: function (data) {
            if (data.newTag) {
                return $('<span><em>Add new: "' + data.text + '"</em></span>');
            }
            return data.text;
        },
        templateSelection: function (data) {
            return data.text;
        }
    });
}
</script>
@endsection
