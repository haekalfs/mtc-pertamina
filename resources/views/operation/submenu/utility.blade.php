@extends('layouts.main')

@section('active-operation')
active font-weight-bold
@endsection

@section('show-operation')
show
@endsection

@section('utility')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-cogs"></i> Utility Usage</h1>
        <p class="mb-4">Managing Access based on roles.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        {{-- <a href="{{ route('participant-infographics-import-page') }}" class="btn btn-sm btn-primary shadow-sm text-white"><i class="fa fa-file-text fa-sm"></i> Import Data</a> --}}
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
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> New Penlat Usage</a>
                    </div>
                </div>
                <div class="card-body zoom80">
                    <form method="GET" action="{{ route('utility') }}">
                        @csrf
                        <div class="row d-flex justify-content-start mb-4">
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Nama Penlat :</label>
                                            <select class="custom-select" id="namaPenlat" name="namaPenlat">
                                                <option value="1" selected>Show All</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="position_id">Batch :</label>
                                            <select name="stcw" class="form-control" id="stcw">
                                                <option value="1">Show All</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex align-self-end justify-content-start">
                                        <div class="form-group">
                                            <div class="align-self-center">
                                                <button type="submit" class="btn btn-primary" style="padding-left: 1.2em; padding-right: 1.2em;"><i class="ti-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table id="docLetter" class="table table-bordered">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Display</th>
                                <th>Penlat</th>
                                @foreach($utilities as $tool)
                                    <th>{{ $tool->utility_name }} ({{$tool->utility_unit}})</th>
                                @endforeach
                                <th>Batch</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                            <tr>
                                <td class="text-center  d-flex flex-row align-items-center justify-content-center">
                                    <a href="{{ route('preview-utility', $item->id) }}"><img src="https://via.placeholder.com/100x100/5fa9f8/ffffff" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow "></a>
                                </td>
                                <td>{{ $item->penlat->description }}</td>
                                @foreach($utilities as $tool)
                                    @php
                                        // Find the matching utility record for the current tool
                                        $utility = $item->penlat_usage->firstWhere('utility_id', $tool->id);
                                    @endphp
                                    <td class="text-center">{{ $utility ? $utility->amount : '-' }}</td> <!-- Assuming 'value' column in penlat_utilities -->
                                @endforeach
                                <td class="text-center font-weight-bold">{{ $item->batch }}</td>
                                <td>{{ $item->date }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
@endsection
