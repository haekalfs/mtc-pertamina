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
                    <div class="d-flex">
                        {{-- <a id="addApproversBtn" class="btn btn-sm btn-primary shadow-sm text-white"><i class="fa fa-file-text fa-sm"></i> Filter</a>
                        <a id="hideApproversBtn" class="btn btn-sm btn-secondary shadow-sm text-white" style="display: none;"><i class="fa fa-backward fa-sm"></i> Cancel</a> --}}
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
                            <thead>
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
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
			<div class="modal-header d-flex flex-row align-items-center justify-content-between border-bottom-1">
                <h5 class="modal-title" id="editModalLabel">Edit Participant</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    @csrf
                    <input type="hidden" id="editId" name="id">
                    <div class="form-group">
                        <label for="editNamaPeserta">Nama Peserta</label>
                        <input type="text" class="form-control" id="editNamaPeserta" name="nama_peserta" required>
                    </div>
                    <div class="form-group">
                        <label for="editNamaProgram">Nama Program</label>
                        <input type="text" class="form-control" id="editNamaProgram" name="nama_program" required>
                    </div>
                    <div class="form-group">
                        <label for="editTglPelaksanaan">Tgl Pelaksanaan</label>
                        <input type="date" class="form-control" id="editTglPelaksanaan" name="tgl_pelaksanaan" required>
                    </div>
                    <div class="form-group">
                        <label for="editTempatPelaksanaan">Tempat Pelaksanaan</label>
                        <input type="text" class="form-control" id="editTempatPelaksanaan" name="tempat_pelaksanaan" required>
                    </div>
                    <div class="form-group">
                        <label for="editJenisPelatihan">Jenis Pelatihan</label>
                        <input type="text" class="form-control" id="editJenisPelatihan" name="jenis_pelatihan" required>
                    </div>
                    <div class="form-group">
                        <label for="editKeterangan">Keterangan</label>
                        <input type="text" class="form-control" id="editKeterangan" name="keterangan" required>
                    </div>
                    <div class="form-group">
                        <label for="editSubholding">Subholding</label>
                        <input type="text" class="form-control" id="editSubholding" name="subholding" required>
                    </div>
                    <div class="form-group">
                        <label for="editPerusahaan">Perusahaan</label>
                        <input type="text" class="form-control" id="editPerusahaan" name="perusahaan" required>
                    </div>
                    <div class="form-group">
                        <label for="editKategoriProgram">Kategori Program</label>
                        <input type="text" class="form-control" id="editKategoriProgram" name="kategori_program" required>
                    </div>
                    <div class="form-group">
                        <label for="editRealise">Realisasi</label>
                        <input type="text" class="form-control" id="editRealisasi" name="realisasi" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
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
                $('#participant-' + id + ' td:nth-child(1)').text(response.nama_peserta);
                $('#participant-' + id + ' td:nth-child(2)').text(response.nama_program);
                $('#participant-' + id + ' td:nth-child(3)').text(response.tgl_pelaksanaan);
                $('#participant-' + id + ' td:nth-child(4)').text(response.tempat_pelaksanaan);
                $('#participant-' + id + ' td:nth-child(5)').text(response.jenis_pelatihan);
                $('#participant-' + id + ' td:nth-child(6)').text(response.keterangan);
                $('#participant-' + id + ' td:nth-child(7)').text(response.subholding);
                $('#participant-' + id + ' td:nth-child(8)').text(response.perusahaan);
                $('#participant-' + id + ' td:nth-child(9)').text(response.kategori_program);
                $('#participant-' + id + ' td:nth-child(10)').text(response.realisasi);
                $('#editModal').modal('hide');
                $('.alert-success-saving-mid').show();
                $('.overlay-mid').show();
                $('.alert-success-saving-mid').text(response.message);
                setTimeout(function() {
                    $('.alert-success-saving-mid').fadeOut('slow');
                    $('.overlay-mid').fadeOut('slow');
                    window.location.reload();
                }, 1000);
            }
        });
    });
});
</script>
<script>
    document.getElementById('addApproversBtn').addEventListener('click', function() {
        // Hide the "Add Approvers" button
        document.getElementById('addApproversBtn').style.display = 'none';
        // Show the form
        document.getElementById('addApproverForm').style.display = 'block';
        document.getElementById('hideApproversBtn').style.display = 'block';
    });
    document.getElementById('hideApproversBtn').addEventListener('click', function() {
        // Hide the "Add Approvers" button
        document.getElementById('addApproversBtn').style.display = 'block';
        // Show the form
        document.getElementById('addApproverForm').style.display = 'none';
        document.getElementById('hideApproversBtn').style.display = 'none';
    });

    function displayFileName() {
        const input = document.getElementById('file');
        const label = document.getElementById('file-label');
        const file = input.files[0];
        if (file) {
            label.textContent = file.name;
        }
    }
</script>
@endsection
