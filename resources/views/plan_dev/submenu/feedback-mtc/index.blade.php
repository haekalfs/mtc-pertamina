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
        <p class="mb-4">Feedback Report.</a></p>
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
                    <div class="d-flex">
                        {{-- <a id="addApproversBtn" class="btn btn-sm btn-primary shadow-sm text-white"><i class="fa fa-file-text fa-sm"></i> Filter</a>
                        <a id="hideApproversBtn" class="btn btn-sm btn-secondary shadow-sm text-white" style="display: none;"><i class="fa fa-backward fa-sm"></i> Cancel</a> --}}
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
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="position_id">Kelompok :</label>
                                        <select name="kelompok" class="form-control" id="kelompok">
                                            <option value="-1">Show All</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status">Tanggal :</label>
                                        <input type="text" class="form-control" name="daterange" id="daterange"/>
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
                            <thead>
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
<script>
$(document).ready(function() {
    var table = $('#listFeedbackMTC').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
        url: "{{ route('feedback-mtc') }}",
        data: function (d) {
            d.nama_pelatihan = $('#nama_pelatihan').val();
            d.periode = $('#periode').val();
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
        ],
    });

    // Re-draw the table when filters are changed
    $('#nama_pelatihan, #periode').change(function(){
        table.draw();
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
