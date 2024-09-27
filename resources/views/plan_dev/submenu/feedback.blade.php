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
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="menu-icon fa fa-trophy"></i> Feedback Report to Instructor</h1>
        <p class="mb-4">Feedback Report from Participants to Instructors.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        {{-- <a href="{{ route('feedback-report-main') }}" class="btn btn-sm btn-secondary shadow-sm text-white mr-3"><i class="fa fa-backward"></i> Go Back</a> --}}
        <a href="{{ route('feedback-report-import-page') }}" class="btn btn-sm btn-primary shadow-sm text-white"><i class="fa fa-file-text fa-sm"></i> Import Data</a>
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
<div class="animated fadeIn zoom90">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" id="judul">List Data</h6>
                    <div class="d-flex">
                    </div>
                </div>
                <div class="card-body zoom80">
                    <div class="row d-flex justify-content-start mb-4">
                        <div class="col-md-12">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="email">Nama Penlat :</label>
                                        <select class="custom-select" id="nama_pelatihan" name="nama_pelatihan">
                                            <option value="-1" selected>Show All</option>
                                            @foreach ($filterPelatihan as $item)
                                                <option value="{{ $item->judul_pelatihan }}">{{ $item->judul_pelatihan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="position_id">Kelompok :</label>
                                        <select name="kelompok" class="form-control" id="kelompok">
                                            <option value="-1">Show All</option>
                                            @foreach ($filterKelompok as $item)
                                                <option value="{{ $item->kelompok }}">{{ $item->kelompok }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status">Tanggal :</label>
                                        <input type="text" class="form-control" name="daterange" id="daterange" autocomplete="off"/>
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex align-self-end justify-content-start">
                                    <div class="form-group">
                                        <div class="align-self-center">
                                            <button id="searchBtn" type="submit" class="btn btn-primary" style="padding-left: 1.2em; padding-right: 1.2em;"><i class="ti-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table id="listFeedback" class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Tgl Pelaksanaan</th>
                                <th>Judul Pelatihan</th>
                                <th>Instruktur</th>
                                @foreach($feedbackTemplates as $template)
                                    <th>{{ $template->questioner }}</th>
                                @endforeach
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be populated by Yajra DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editFeedbackModal" tabindex="-1" role="dialog" aria-labelledby="editFeedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 900px;" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="editFeedbackModalLabel">Edit Feedback Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editFeedbackForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-4 font-weight-bold text-secondary" id="judul">Basic Information</h6>
                            <!-- Basic Information Fields -->
                            <input type="hidden" id="editId" name="id">
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Nama Peserta:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="nama_peserta" name="nama_peserta" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Nama Program:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="nama_program" name="nama_program" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Tgl Pelaksanaan:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="date" class="form-control" id="tgl_pelaksanaan" name="tgl_pelaksanaan" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Tempat Pelaksanaan:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="tempat_pelaksanaan" name="tempat_pelaksanaan" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Instruktur:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="instruktur" name="instruktur" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-4 font-weight-bold text-secondary" id="judul">Keypoints</h6>
                            <!-- Keypoints Fields -->
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 370px;" class="mr-2">
                                    <p style="margin: 0;">Penguasaan Materi:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="penguasaan_materi" name="score_1" required>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 370px;" class="mr-2">
                                    <p style="margin: 0;">Pemaparan Materi:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="pemaparan_materi" name="score_2" required>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 370px;" class="mr-2">
                                    <p style="margin: 0;">Pengelolaan Waktu:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="pengelolaan_waktu" name="score_3" required>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 370px;" class="mr-2">
                                    <p style="margin: 0;">Interaktif:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="interaktif" name="score_4" required>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 370px;" class="mr-2">
                                    <p style="margin: 0;">Pengelolaan Usage Training Aids:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="pengelolaan_usage_training_aids" name="score_5" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-delete">Delete Data</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#listFeedback').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('feedback-report') }}",
            data: function(d) {
                d.nama_pelatihan = $('#nama_pelatihan').val();
                d.kelompok = $('#kelompok').val();

                // Pass startDate and endDate separately
                var dateRange = $('#daterange').val();
                if (dateRange) {
                    var dates = dateRange.split(' - ');
                    d.startDate = dates[0];
                    d.endDate = dates[1];
                }
            }
        },
        columns: [
            { data: 'tgl_pelaksanaan', name: 'tgl_pelaksanaan' },
            { data: 'judul_pelatihan', name: 'judul_pelatihan' },
            { data: 'instruktur', name: 'instruktur' },
            @foreach($feedbackTemplates as $template)
                { data: 'feedback_{{ $template->id }}', name: 'feedback_{{ $template->id }}' },
            @endforeach
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Redraw the DataTable when the search button is clicked
    $('#searchBtn').click(function() {
        $('#listFeedback').DataTable().draw();
    });

    $(document).on('click', '.edit-btn', function() {
        var feedbackId = $(this).data('id');

        // Make an AJAX request to fetch the data for the given feedback_id
        $.ajax({
            url: '/feedback-report/' + feedbackId + '/edit',
            method: 'GET',
            success: function(response) {
                // Assuming response is an array of feedback reports
                response.forEach(function(report, index) {
                    if (index === 0) {
                        $('#editId').val(report.feedback_id);
                        $('#tgl_pelaksanaan').val(report.tgl_pelaksanaan);
                        $('#tempat_pelaksanaan').val(report.tempat_pelaksanaan);
                        $('#nama_peserta').val(report.nama);
                        $('#nama_program').val(report.judul_pelatihan);
                        $('#instruktur').val(report.instruktur);
                    }
                    // Assign scores to their respective fields
                    $(`input[name="score_${index+1}"]`).val(report.score);
                });

                // Open the modal
                $('#editFeedbackModal').modal('show');
            }
        });
    });

    // Handle form submission for updating the data
    $('#editFeedbackForm').on('submit', function(e) {
        e.preventDefault();
        var feedbackId = $('#editId').val();

        // Send the updated data via AJAX
        $.ajax({
            url: '/feedback-report/' + feedbackId, // Use the feedbackId from the selected row
            method: 'PUT',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Close the modal and refresh the table
                $('#editFeedbackModal').modal('hide');
                $('#listFeedback').DataTable().ajax.reload();
            },
            error: function(xhr) {
                // Log the error response to see what's wrong
                console.log(xhr.responseJSON.errors); // This will show the validation errors
                alert('Error updating feedback report: ' + JSON.stringify(xhr.responseJSON.errors));
            }
        });
    });

    // Event listener for the Delete Data button
    $('#editFeedbackModal').on('click', '.btn-delete', function() {
        var id = $('#editId').val(); // Get the ID of the participant to delete from the hidden input field

        // Use SweetAlert to confirm the deletion
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this participant's data!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                // If the user confirms, proceed with the deletion
                $.ajax({
                    url: '/feedback-instructor-delete-data/' + id,  // Use the ID from the hidden input field
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}', // Add CSRF token
                    },
                    success: function(response) {
                        // Hide the modal
                        $('#editFeedbackModal').modal('hide');

                        // Show success alert with SweetAlert
                        swal("Success!", response.message, "success");

                        // Optionally, refresh the table or perform other actions
                        $('#listFeedback').DataTable().ajax.reload();
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
</script>
@endsection
