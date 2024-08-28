@extends('layouts.main')

@section('active-operation')
active font-weight-bold
@endsection

@section('show-operation')
show
@endsection

@section('participant-infographics')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="menu-icon fa fa-users"></i> Participants Infographics</h1>
        <p class="mb-4">Infographics about participant.</a></p>
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
<style>

.alert-success-saving-mid {
  display: none;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  padding: 20px;
  border-radius: 5px;
  text-align: center;
  z-index: 10000;
}
</style>
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
                    <form method="GET" action="{{ route('participant-infographics') }}">
                        @csrf
                        <div class="row d-flex justify-content-start mb-4">
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="form-group">
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
                                    {{-- <div class="col-md-1 d-flex align-self-end justify-content-start">
                                        <div class="form-group">
                                            <div class="align-self-center">
                                                <button type="submit" class="btn btn-primary" style="padding-left: 1.2em; padding-right: 1.2em;"><i class="ti-search"></i></button>
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table id="listPeserta" class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama Peserta</th>
                                    <th>Nama Program</th>
                                    <th>Tgl Pelaksanaan</th>
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
<div class="modal fade zoom90" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Nama Peserta:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" name="nama_peserta" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Nama Program:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" name="nama_program" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Batch:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" name="batch" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Tgl Pelaksanaan:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="date" class="form-control" name="tgl_pelaksanaan" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Tempat Pelaksanaan:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" name="tempat_pelaksanaan" required>
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

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-4">
                                        <div style="width: 140px;" class="mr-2">
                                            <p style="margin: 0;">Kategori Program:</p>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control" name="kategori_program" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
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
                    <div class="row">
                        <div class="col-md-6">
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
                                    <p style="margin: 0;">Tgl Pelaksanaan:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="date" class="form-control" id="editTglPelaksanaan" name="tgl_pelaksanaan" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Tempat Pelaksanaan:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="editTempatPelaksanaan" name="tempat_pelaksanaan" required>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Jenis Pelatihan:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="editJenisPelatihan" name="jenis_pelatihan" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
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
<script>
$(document).ready(function() {

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
            { data: 'tempat_pelaksanaan', name: 'tempat_pelaksanaan' },
            { data: 'jenis_pelatihan', name: 'jenis_pelatihan' },
            { data: 'keterangan', name: 'keterangan' },
            { data: 'subholding', name: 'subholding' },
            { data: 'perusahaan', name: 'perusahaan' },
            { data: 'kategori_program', name: 'kategori_program' },
            { data: 'realisasi', name: 'realisasi' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ]
    });

    // Re-draw the table when filters are changed
    $('#namaPenlat, #stcw, #jenisPenlat, #tw, #periode').change(function(){
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
</script>
@endsection
