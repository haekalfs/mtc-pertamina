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
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="menu-icon fa fa-trophy"></i> Feedback Report to MTC</h1>
        <p class="mb-4">Feedback Report from Participants to Pelatihan.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('feedback-mtc-import-page') }}" class="btn btn-sm btn-primary shadow-sm text-white"><i class="fa fa-file-text fa-sm"></i> Import Data</a>
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
            <div class="alert alert-warning alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>Not all columns are showed due to big data...</strong>
            </div>
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" id="judul">List Data</h6>
                </div>
                <div class="card-body zoom80">
                    <div class="row d-flex justify-content-start mb-4">
                        <div class="col-md-12">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="nama_pelatihan">Nama Penlat:</label>
                                        <select class="custom-select select2" id="nama_pelatihan" name="nama_pelatihan" style="width: 100%;">
                                            <option value="-1" selected>Show All</option>
                                            @foreach ($listFeedback as $id => $judul)
                                                <option value="{{ $judul }}">{{ $judul }}</option>
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
                    <div class="table-responsive">
                        <table id="listFeedbackMTC" class="table table-bordered">
                            <thead class="thead-light">
                              <tr>
                                <th>Nama Peserta</th>
                                <th>Judul Pelatihan</th>
                                <th>Tanggal Pelaksanaan</th>
                                <th>Relevansi Materi</th>
                                <th>Manfaat Training</th>
                                <th>Durasi Training</th>
                                <th>Sistematika Penyajian</th>
                                <th>Tujuan Tercapai</th>
                                <th>Kedisiplinan Steward</th>
                                <th>Saran</th>
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
<div class="modal fade" id="editFeedbackModal" tabindex="-1" aria-labelledby="editFeedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 1280px;" role="document">
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
                        <div class="col-md-4">
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
                                    <p style="margin: 0;">Judul Pelatihan:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="judul_pelatihan" name="judul_pelatihan" required>
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
                                    <p style="margin: 0;">Tgl Pelaksanaan:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="date" class="form-control" id="tgl_pelaksanaan" name="tgl_pelaksanaan" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Email Peserta:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="email" class="form-control" id="email_peserta" name="email_peserta" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <h6 class="mb-4 font-weight-bold text-secondary" id="judul">Training Details</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <!-- Training Details Fields -->
                                    <div class="d-flex align-items-center mb-4">
                                        <div style="width: 140px;" class="mr-2">
                                            <p style="margin: 0;">Relevansi Materi:</p>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control" id="relevansi_materi" name="relevansi_materi" required>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-4">
                                        <div style="width: 140px;" class="mr-2">
                                            <p style="margin: 0;">Durasi Training:</p>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control" id="durasi_training" name="durasi_training" required>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-4">
                                        <div style="width: 140px;" class="mr-2">
                                            <p style="margin: 0;">Sistematika Penyajian:</p>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control" id="sistematika_penyajian" name="sistematika_penyajian" required>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-4">
                                        <div style="width: 140px;" class="mr-2">
                                            <p style="margin: 0;">Tujuan Tercapai:</p>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control" id="tujuan_tercapai" name="tujuan_tercapai" required>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-4">
                                        <div style="width: 140px;" class="mr-2">
                                            <p style="margin: 0;">Kedisiplinan Steward:</p>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control" id="kedisiplinan_steward" name="kedisiplinan_steward" required>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-4">
                                    <!-- Training Details Fields -->
                                    <div class="d-flex align-items-center mb-4">
                                        <div style="width: 140px;" class="mr-2">
                                            <p style="margin: 0;">Fasilitasi Steward:</p>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control" id="fasilitasi_steward" name="fasilitasi_steward" required>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-4">
                                        <div style="width: 140px;" class="mr-2">
                                            <p style="margin: 0;">Layanan Pelaksana:</p>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control" id="layanan_pelaksana" name="layanan_pelaksana" required>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-4">
                                        <div style="width: 140px;" class="mr-2">
                                            <p style="margin: 0;">Proses Administrasi:</p>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control" id="proses_administrasi" name="proses_administrasi" required>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-4">
                                        <div style="width: 140px;" class="mr-2">
                                            <p style="margin: 0;">Kemudahan Registrasi:</p>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control" id="kemudahan_registrasi" name="kemudahan_registrasi" required>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-4">
                                        <div style="width: 140px;" class="mr-2">
                                            <p style="margin: 0;">Kondisi Peralatan:</p>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control" id="kondisi_peralatan" name="kondisi_peralatan" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <!-- Training Details Fields -->
                                    <div class="d-flex align-items-center mb-4">
                                        <div style="width: 140px;" class="mr-2">
                                            <p style="margin: 0;">Kualitas Boga:</p>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control" id="kualitas_boga" name="kualitas_boga" required>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-4">
                                        <div style="width: 140px;" class="mr-2">
                                            <p style="margin: 0;">Media Online:</p>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control" id="media_online" name="media_online" required>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-4">
                                        <div style="width: 140px;" class="mr-2">
                                            <p style="margin: 0;">Rekomendasi:</p>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control" id="rekomendasi" name="rekomendasi" required>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-4">
                                        <div style="width: 140px;" class="mr-2">
                                            <p style="margin: 0;">Saran:</p>
                                        </div>
                                        <div class="flex-grow-1">
                                            <textarea class="form-control" id="saran" name="saran" rows="3" required></textarea>
                                        </div>
                                    </div>
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

    $('#nama_pelatihan').select2({
        placeholder: "Select a training program",
        allowClear: true
    });

    // DataTable Initialization
    var table = $('#listFeedbackMTC').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('feedback-mtc') }}",
            data: function (d) {
                d.nama_pelatihan = $('#nama_pelatihan').val();
                d.daterange = $('#daterange').val(); // Pass daterange directly
            }
        },
        columns: [
            { data: 'nama_peserta', name: 'nama_peserta' },
            { data: 'judul_pelatihan', name: 'judul_pelatihan' },
            { data: 'tgl_pelaksanaan', name: 'tgl_pelaksanaan' },
            { data: 'relevansi_materi', name: 'relevansi_materi' },
            { data: 'manfaat_training', name: 'manfaat_training' },
            { data: 'durasi_training', name: 'durasi_training' },
            { data: 'sistematika_penyajian', name: 'sistematika_penyajian' },
            { data: 'tujuan_tercapai', name: 'tujuan_tercapai' },
            { data: 'kedisiplinan_steward', name: 'kedisiplinan_steward' },
            { data: 'saran', name: 'saran' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
    });

    // Re-draw the table when the search button is clicked
    $('#searchBtn').on('click', function (e) {
        e.preventDefault(); // Prevent default form submission if within a form
        table.draw();
    });

    // Open modal and prefill data when Edit button is clicked
    $('#listFeedbackMTC').on('click', '.edit-feedback', function() {
        var id = $(this).data('id');

        $.ajax({
            url: "{{ route('feedback-mtc.edit', '') }}/" + id,
            method: 'GET',
            success: function(data) {
                // Fill the modal with data
                $('#editId').val(data.id);
                $('#editFeedbackModal input[name="nama_peserta"]').val(data.nama_peserta);
                $('#editFeedbackModal input[name="judul_pelatihan"]').val(data.judul_pelatihan);
                $('#editFeedbackModal input[name="tgl_pelaksanaan"]').val(data.tgl_pelaksanaan);
                $('#editFeedbackModal input[name="tempat_pelaksanaan"]').val(data.tempat_pelaksanaan);
                $('#editFeedbackModal input[name="email_peserta"]').val(data.email_peserta);
                $('#editFeedbackModal input[name="relevansi_materi"]').val(data.relevansi_materi);
                $('#editFeedbackModal input[name="durasi_training"]').val(data.durasi_training);
                $('#editFeedbackModal input[name="sistematika_penyajian"]').val(data.sistematika_penyajian);
                $('#editFeedbackModal input[name="tujuan_tercapai"]').val(data.tujuan_tercapai);
                $('#editFeedbackModal input[name="kedisiplinan_steward"]').val(data.kedisiplinan_steward);
                $('#editFeedbackModal input[name="fasilitasi_steward"]').val(data.fasilitasi_steward);
                $('#editFeedbackModal input[name="layanan_pelaksana"]').val(data.layanan_pelaksana);
                $('#editFeedbackModal input[name="proses_administrasi"]').val(data.proses_administrasi);
                $('#editFeedbackModal input[name="kemudahan_registrasi"]').val(data.kemudahan_registrasi);
                $('#editFeedbackModal input[name="kondisi_peralatan"]').val(data.kondisi_peralatan);
                $('#editFeedbackModal input[name="kualitas_boga"]').val(data.kualitas_boga);
                $('#editFeedbackModal input[name="media_online"]').val(data.media_online);
                $('#editFeedbackModal input[name="rekomendasi"]').val(data.rekomendasi);
                $('#editFeedbackModal textarea[name="saran"]').val(data.saran);

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
            url: '/feedback-mtc-update/' + feedbackId, // Use the feedbackId from the selected row
            method: 'PUT',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Close the modal and refresh the table
                $('#editFeedbackModal').modal('hide');
                $('#listFeedbackMTC').DataTable().ajax.reload();
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
                    url: '/feedback-mtc-delete-data/' + id,  // Use the ID from the hidden input field
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
                        $('#listFeedbackMTC').DataTable().ajax.reload();
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
