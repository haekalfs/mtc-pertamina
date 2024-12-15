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

@if ($message = Session::get('batch-registration'))
<div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{!! $message !!}</strong>
</div>
@endif

@if ($message = Session::get('failed'))
<div class="alert alert-danger alert-block">
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

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif
<div class="animated fadeIn">
    <div class="row">
        <div class="col-md-12 zoom90">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" id="judul">List Data <span id="tahun"></span></h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Profit</a>
                    </div>
                </div>
                <div class="card-body zoom80">
                    <div class="row d-flex justify-content-start mb-4">
                        <div class="col-md-12">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="namaPenlat">Nama Pelatihan :</label>
                                        <select class="custom-select" id="namaPenlat" name="namaPenlat">
                                            <option value="-1" selected>Show All</option>
                                            @foreach($penlatList as $penlat)
                                                <option value="{{ $penlat->id }}">{{ $penlat->description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="jenisPenlat">Jenis Pelatihan :</label>
                                        <select class="form-control" id="jenisPenlat" name="jenisPenlat">
                                            <option value="-1" selected>Show All</option>
                                            @foreach($penlatList->unique('jenis_pelatihan') as $penlat)
                                                <option value="{{ $penlat->jenis_pelatihan }}">{{ $penlat->jenis_pelatihan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status">Month :</label>
                                        <select class="form-control" id="month" name="month" required>
                                            <option value="-1" selected>Show All</option>
                                            @foreach(range(1, 12) as $month)
                                                <option value="{{ $month }}">{{ date("F", mktime(0, 0, 0, $month, 1)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="position_id">Year :</label>
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
                        <thead class="thead-light">
                            <tr>
                                <th>Tanggal Pelaksanaan</th>
                                <th>Nama Pelatihan</th>
                                <th>Batch</th>
                                <th>Jumlah Peserta</th>
                                <th>Jumlah Peserta (Master Data)</th>
                                <th>Revenue &nbsp;<i class="fa fa-plus text-success"></i></th>
                                <th>Cost &nbsp;<i class="fa fa-minus text-danger"></i></th>
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
                    <h6 class="m-0 font-weight-bold" id="judul">Summary <span id="tahunSummary"></span></h6>
                    <div class="d-flex zoom90">
                        <select name="summaryPeriode" class="form-control" id="summaryPeriode">
                            <option value="-1" selected disabled>Select Periode...</option>
                        </select>
                    </div>
                </div>
                <div class="card-body zoom80">
                    <div class="">
                        <h5 class="card-title font-weight-bold">Revenue & Operating Expenses <span id="tahunRevenue"></span></h5>
                        <div class="ml-2">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Biaya Instruktur</td>
                                    <td style="text-align: start;">: &nbsp; <span id="total_biaya_instruktur">-</span></td>
                                </tr>
                                <tr>
                                    <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Total PNBP</td>
                                    <td style="text-align: start;">: &nbsp; <span id="total_pnbp">-</span></td>
                                </tr>
                                <tr>
                                    <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Biaya Transportasi</td>
                                    <td style="text-align: start;">: &nbsp; <span id="total_biaya_transportasi_hari">-</span></td>
                                </tr>
                                <tr>
                                    <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Penagihan Foto</td>
                                    <td style="text-align: start;">: &nbsp; <span id="total_penagihan_foto">-</span></td>
                                </tr>
                                <tr>
                                    <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Penagihan ATK</td>
                                    <td style="text-align: start;">: &nbsp; <span id="total_penagihan_atk">-</span></td>
                                </tr>
                                <tr>
                                    <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Penagihan Snacks</td>
                                    <td style="text-align: start;">: &nbsp; <span id="total_penagihan_snack">-</span></td>
                                </tr>
                                <tr>
                                    <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Penagihan Makan Siang</td>
                                    <td style="text-align: start;">: &nbsp; <span id="total_penagihan_makan_siang">-</span></td>
                                </tr>
                                <tr>
                                    <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Penagihan Laundry</td>
                                    <td style="text-align: start;">: &nbsp; <span id="total_penagihan_laundry">-</span></td>
                                </tr>
                                <tr>
                                    <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Penggunaan Alat</td>
                                    <td style="text-align: start;">: &nbsp; <span id="total_penggunaan_alat">-</span></td>
                                </tr>
                                <tr>
                                    <td style="width: 250px;" class="mb-2"><i class="ti-minus mr-2"></i> Total Peserta</td>
                                    <td style="text-align: start;">: &nbsp; <span id="total_peserta">-</span> Peserta</td>
                                </tr>
                            </table>
                        </div>
                        <hr>
                        <h5 class="card-title font-weight-bold">Profit <span id="tahunIncome"></span></h5>
                        <div class="ml-2">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td style="width: 250px;" class="mb-2 font-weight-bold text-success"><i class="ti-minus mr-2"></i> Revenue</td>
                                    <td style="text-align: start;" class="font-weight-bold text-success">: &nbsp; <span id="revenue">-</span> <i class="fa fa-plus"></i></td>
                                </tr>
                                <tr>
                                    <td style="width: 250px;" class="mb-2 font-weight-bold text-danger"><i class="ti-minus mr-2"></i> Operating Cost</td>
                                    <td style="text-align: start;" class="font-weight-bold text-danger">: &nbsp; <span id="totalCosts">-</span> <i class="fa fa-minus"></i></td>
                                </tr>
                                <tr>
                                    <td style="width: 250px;" class="mb-2 font-weight-bold text-success"><i class="ti-minus mr-2"></i> Profit</td>
                                    <td style="text-align: start;" class="font-weight-bold text-success">: &nbsp; <span id="nett_income">-</span> <i class="fa fa-plus"></i></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 zoom90">
            <div class="card mb-4">
                <div class="card-header">
                    <span class="text-danger font-weight-bold">User Manual</span>
                </div>
                <div class="card-body" style="background-color: rgb(247, 247, 247);">
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">General Guidelines</h6>
                    <ul class="ml-4" style="line-height:200%">
                        <li>Table diatas adalah List Pelatihan beserta Revenue, Cost & Profit.</li>
                        <li>Nominal dari table diatas, diambil dari semua batch yang dimiliki oleh pelatihan tersebut.</li>
                        <li>List Pelatihan adalah Induk Data dari Penlat, yang mana 1 Pelatihan bisa memiliki banyak batch.</li>
                        <li>Data di samping adalah summary dari semua revenue, cost & profit data pelatihan.</li>
                        <li class="text-danger font-weight-bold">Untuk menampilkan summary & grafik, anda perlu memilih periode tahun.</li>
                    </ul>
                    <div class="alert alert-warning alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>Jika Import data berhasil namun data tidak muncul, artinya list pelatihan tidak ada/belum diimport.</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createBatchModal" tabindex="-1" role="dialog" aria-labelledby="createBatchModalLabel" aria-hidden="true">
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
                                <img id="image-preview" src="{{ asset('img/default-img.png') }}"
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
                                    <select id="penlatSelect" class="form-control select2" name="penlat">
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
<script>
window.onload = function () {
    // Get the current year
    var currentYear = new Date().getFullYear();

    // Populate the dropdowns with the current year as default
    var summaryPeriodeDropdown = document.getElementById("summaryPeriode");

    // Populate both dropdowns with options from current year and previous years
    for (var year = currentYear; year >= currentYear - 4; year--) {
        var summaryOption = new Option(year, year);
        summaryPeriodeDropdown.add(summaryOption);
    }

    // Set default selected value to current year
    summaryPeriodeDropdown.value = currentYear;

    // Auto-load summary and chart data based on the default selected year
    loadSummaryData(currentYear);

    // Add event listener to dropdowns for future changes
    summaryPeriodeDropdown.addEventListener("change", function() {
        var selectedOption = summaryPeriodeDropdown.value;
        loadSummaryData(selectedOption);
    });
};

function loadSummaryData(value) {
    var selectedOption = value;
    if(selectedOption != '-1'){
        var selectedYear = selectedOption;
    } else {
        var selectedYear = '';
    }
    fetch('/api/summary-data-profits/' + selectedOption)
        .then(response => response.json())
        .then(data => {
            // Update financial data on the page
            if(selectedOption != '-1'){
                document.getElementById('tahunSummary').innerText = selectedOption;
                document.getElementById('tahunRevenue').innerText = selectedOption;
                document.getElementById('tahunIncome').innerText = selectedOption;
            } else {
                document.getElementById('tahunSummary').innerText = '';
                document.getElementById('tahunRevenue').innerText = '';
                document.getElementById('tahunIncome').innerText = '';
            }
            document.getElementById('revenue').innerText = formatCurrency(data.array.revenue);
            document.getElementById('total_biaya_instruktur').innerText = formatCurrency(data.array.total_biaya_instruktur);
            document.getElementById('total_pnbp').innerText = formatCurrency(data.array.total_pnbp);
            document.getElementById('total_biaya_transportasi_hari').innerText = formatCurrency(data.array.total_biaya_transportasi_hari);
            document.getElementById('total_penagihan_foto').innerText = formatCurrency(data.array.total_penagihan_foto);
            document.getElementById('total_penagihan_atk').innerText = formatCurrency(data.array.total_penagihan_atk);
            document.getElementById('total_penagihan_snack').innerText = formatCurrency(data.array.total_penagihan_snack);
            document.getElementById('total_penagihan_makan_siang').innerText = formatCurrency(data.array.total_penagihan_makan_siang);
            document.getElementById('total_penagihan_laundry').innerText = formatCurrency(data.array.total_penagihan_laundry);
            document.getElementById('total_penggunaan_alat').innerText = formatCurrency(data.array.total_penggunaan_alat);
            document.getElementById('total_peserta').innerText = data.array.total_peserta;
            document.getElementById('nett_income').innerText = formatCurrency(data.array.nett_income);
            document.getElementById('totalCosts').innerText = formatCurrency(data.array.total_costs);
        });
}
// Function to format currency
function formatCurrency(value) {
    return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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
                url: "{{ route('profits') }}",
                data: function(d) {
                    d.namaPenlat = $('#namaPenlat').val();
                    d.jenisPenlat = $('#jenisPenlat').val();
                    d.month = $('#month').val();
                    d.periode = $('#periode').val();
                }
            },
            lengthMenu: [
                [10, 25, 50, 100, -1], // Values for page length
                [10, 25, 50, 100, "All"] // Labels displayed in the dropdown
            ],
            pageLength: 10, // Default page length
            columns: [
                { data: 'tgl_pelaksanaan', name: 'tgl_pelaksanaan' },
                { data: 'description', name: 'batch.penlat.description', orderable: false, searchable: false },
                { data: 'pelaksanaan', name: 'pelaksanaan' },
                { data: 'jumlah_peserta_1', name: 'jumlah_peserta_1' },
                { data: 'jumlah_actual_peserta', name: 'jumlah_actual_peserta' },
                { data: 'revenue', name: 'revenue' },
                { data: 'cost', name: 'cost' },
                { data: 'nett_income', name: 'nett_income' }
            ]
        });

        $('#namaPenlat, #jenisPenlat, #periode, #month').change(function() {
            table.draw();
        });

        // Initialize Select2
        $('#penlatSelect').select2({
            dropdownParent: $('#createBatchModal'),
            theme: "classic",
            placeholder: "Select Pelatihan...",
            width: '100%',
            tags: true,
        });

        // Event listener for change event
        $('#penlatSelect').on('change', function() {
            var selectedOption = $(this).find('option:selected').text();
            $('#programInput').val(selectedOption);
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
</script>
@endsection
