@extends('layouts.main')

@section('active-finance')
active font-weight-bold
@endsection

@section('show-finance')
show
@endsection

@section('cost')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="menu-icon ti-stats-down"></i> Profits & Loss</h1>
        <p class="mb-4">Costs Report.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('costs.import') }}" class="btn btn-sm btn-primary shadow-sm text-white"><i class="fa fa-file-text fa-sm"></i> Import Data</a>
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
                    <div class="row d-flex justify-content-start mb-4">
                        <div class="col-md-12">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email">Nama Penlat :</label>
                                        <select class="custom-select" id="namaPenlat" name="namaPenlat">
                                            <option value="-1" selected>Show All</option>
                                            @foreach($penlatList as $penlat)
                                                <option value="{{ $penlat->id }}">{{ $penlat->description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Month :</label>
                                        <select class="form-control" id="jenisPenlat" name="jenisPenlat" required>
                                            <option value="-1" selected>Show All</option>
                                            @foreach(range(1, 12) as $month)
                                                <option value="{{ $month }}">{{ date("F", mktime(0, 0, 0, $month, 1)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="position_id">Periode :</label>
                                        <select name="periode" class="form-control" id="periode">
                                            <option value="-1" selected>Show All</option>
                                            @foreach(range(date('Y'), date('Y') - 5) as $year)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table id="profitsTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tanggal Pelaksanaan</th>
                                <th>Nama Pelatihan</th>
                                <th>Batch</th>
                                <th>Jumlah Peserta</th>
                                <th>Total Biaya Pendaftaran</th>
                                <th>Jumlah Biaya</th>
                                <th>Profit</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" id="judul">List Data</h6>
                    <div class="d-flex">
                        {{-- <a id="addApproversBtn" class="btn btn-sm btn-primary shadow-sm text-white"><i class="fa fa-file-text fa-sm"></i> Filter</a>
                        <a id="hideApproversBtn" class="btn btn-sm btn-secondary shadow-sm text-white" style="display: none;"><i class="fa fa-backward fa-sm"></i> Cancel</a> --}}
                    </div>
                </div>
                <div class="card-body zoom80">
                    <div class="">
                        <h5 class="card-title font-weight-bold">Revenue & Operating Expenses</h5>
                        <div class="ml-2">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Revenue</td>
                                    <td style="text-align: start;" class="">: &nbsp; {{ $arrayData['revenue'] ? 'Rp ' . number_format($arrayData['revenue'], 0, ',', '.') : '-' }} </td>
                                </tr>
                                <tr>
                                    <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Biaya Instruktur</td>
                                    <td style="text-align: start;">: &nbsp; {{ $arrayData['total_biaya_instruktur'] ? 'Rp ' . number_format($arrayData['total_biaya_instruktur'], 0, ',', '.') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Total PNBP</td>
                                    <td style="text-align: start;">: &nbsp; {{ $arrayData['total_pnbp'] ? 'Rp ' . number_format($arrayData['total_pnbp'], 0, ',', '.') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Biaya Transportasi</td>
                                    <td style="text-align: start;">: &nbsp; {{ $arrayData['total_biaya_transportasi_hari'] ? 'Rp ' . number_format($arrayData['total_biaya_transportasi_hari'], 0, ',', '.') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Penagihan Foto</td>
                                    <td style="text-align: start;">: &nbsp; {{ $arrayData['total_penagihan_foto'] ? 'Rp ' . number_format($arrayData['total_penagihan_foto'], 0, ',', '.') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Penagihan ATK</td>
                                    <td style="text-align: start;">: &nbsp; {{ $arrayData['total_penagihan_atk'] ? 'Rp ' . number_format($arrayData['total_penagihan_atk'], 0, ',', '.') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Penagihan Snacks</td>
                                    <td style="text-align: start;">: &nbsp; {{ $arrayData['total_penagihan_snack'] ? 'Rp ' . number_format($arrayData['total_penagihan_snack'], 0, ',', '.') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Penagihan Makan Siang</td>
                                    <td style="text-align: start;">: &nbsp; {{ $arrayData['total_penagihan_makan_siang'] ? 'Rp ' . number_format($arrayData['total_penagihan_makan_siang'], 0, ',', '.') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Penagihan Laundry</td>
                                    <td style="text-align: start;">: &nbsp; {{ $arrayData['total_penagihan_laundry'] ? 'Rp ' . number_format($arrayData['total_penagihan_laundry'], 0, ',', '.') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Total Peserta</td>
                                    <td style="text-align: start;">: &nbsp; {{ $arrayData['total_peserta'] }} Peserta</td>
                                </tr>
                            </table>
                        </div>
                        <hr>
                        <h5 class="card-title font-weight-bold">Nett Income</h5>
                        <div class="ml-2">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td style="width: 270px;" class="mb-2 font-weight-bold text-success"><i class="ti-minus mr-2"></i> Revenue</td>
                                    <td style="text-align: start;" class="font-weight-bold text-success">: &nbsp; {{ $arrayData['revenue'] ? 'Rp ' . number_format($arrayData['revenue'], 0, ',', '.') : '-' }} <i class="fa fa-plus"></i></td>
                                </tr>
                                <tr>
                                    <td style="width: 270px;" class="mb-2 font-weight-bold text-danger"><i class="ti-minus mr-2"></i> Operating Expenses (COST) </td>
                                    <td style="text-align: start;" class="font-weight-bold text-danger">: &nbsp; {{ $arrayData['total_costs'] ? 'Rp ' . number_format($arrayData['total_costs'], 0, ',', '.') : '-' }} <i class="fa fa-minus"></i></td>
                                </tr>
                                <tr>
                                    <td style="width: 270px;" class="mb-2 font-weight-bold text-success"><i class="ti-minus mr-2"></i> Nett Income</td>
                                    <td style="text-align: start;" class="font-weight-bold text-success">: &nbsp; {{ $arrayData['nett_income'] ? 'Rp ' . number_format($arrayData['nett_income'], 0, ',', '.') : '-' }} <i class="fa fa-plus"></i></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" id="judul">Grafik Overall</h6>
                    <div class="d-flex">
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartContainerSpline" style="height: 370px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade zoom90" id="createBatchModal" tabindex="-1" role="dialog" aria-labelledby="createBatchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 900px;" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="createBatchModalLabel">Register Batch</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('batch.store') }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters mb-3">
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="file-upload" style="cursor: pointer;">
                                <img id="image-preview" src="https://via.placeholder.com/50x50/5fa9f8/ffffff"
                                     style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                     <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="file-upload" type="file" name="image" style="display: none;" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Nama Pelatihan :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <select id="penlatSelect" class="form-control" name="penlat">
                                        <option selected disabled>Select Pelatihan...</option>
                                        @foreach ($penlatList as $item)
                                        <option value="{{ $item->id }}">{{ $item->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Nama Program :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" id="programInput" class="form-control" name="program">
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Batch :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="batch" name="batch">
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Tgl Pelaksanaan :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="date" class="form-control" name="date">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
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
window.onload = function () {
    loadChartData();

    // Add event listener to dropdown
    document.getElementById("periode").addEventListener("change", function() {
        loadChartData(); // Load chart data whenever the dropdown changes
    });
};

function loadChartData() {
    var selectedOption = document.getElementById("periode").value;
    fetch('/api/chart-data-profits/' + selectedOption)
        .then(response => response.json())
        .then(data => {
            var chart = new CanvasJS.Chart("chartContainerSpline", {
                animationEnabled: true,
                zoomEnabled: true,
                theme: "light2",
                title: { text: "Data Profits" },
                axisX: { valueFormatString: "DD MMM" },
                axisY: {
                    includeZero: true
                },
                data: [{
                    type: "splineArea",
                    color: "#6599FF",
                    xValueType: "dateTime",
                    xValueFormatString: "DD MMM",
                    yValueFormatString: "#,##0 Rupiah",
                    dataPoints: data.splineDataPoints
                }]
            });
            chart.render();
        });
}
</script>

<script>
$('#createBatchModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var batch = button.data('batch'); // Extract batch info from data-* attributes
    var id = button.data('id'); // Extract id info
    var tgl = button.data('tgl'); // Extract tgl_pelaksanaan info

    var modal = $(this);
    modal.find('.modal-body #batch').val(batch);
    modal.find('.modal-body #item-id').val(id);
    modal.find('.modal-body input[name="date"]').val(tgl); // Pre-fill the date field
});
$(document).ready(function() {
    var table = $('#profitsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('cost') }}", // Ensure this route points to the controller's costs method
            data: function(d) {
                d.namaPenlat = $('#namaPenlat').val();
                d.jenisPenlat = $('#jenisPenlat').val();
                d.periode = $('#periode').val();
            }
        },
        columns: [
            { data: 'tgl_pelaksanaan', name: 'tgl_pelaksanaan' },
            { data: 'description', name: 'batch.penlat.description', orderable: false, searchable: false },
            { data: 'pelaksanaan', name: 'pelaksanaan', orderable: false, searchable: false },
            { data: 'jumlah_peserta', name: 'jumlah_peserta' },
            { data: 'total_biaya_pendaftaran_peserta', name: 'total_biaya_pendaftaran_peserta' },
            { data: 'jumlah_biaya', name: 'jumlah_biaya' },
            { data: 'profit', name: 'profit' }
        ]
    });

    $('#namaPenlat, #jenisPenlat, #periode').change(function() {
        table.draw();
    });
});
</script>
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('image-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('penlatSelect').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex].text;
            document.getElementById('programInput').value = selectedOption;
        });
    });
</script>
@endsection
