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
                                        <label for="namaPenlat">Nama Pelatihan :</label>
                                        <select class="custom-select" id="namaPenlat" name="namaPenlat">
                                            <option value="-1" selected>Show All</option>
                                            @foreach($penlatList as $penlat)
                                                <option value="{{ $penlat->id }}">{{ $penlat->description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="jenisPenlat">Jenis Penlat :</label>
                                        <select class="form-control" id="jenisPenlat" name="jenisPenlat">
                                            <option value="-1" selected>Show All</option>
                                            @foreach($penlatList->unique('jenis_pelatihan') as $penlat)
                                                <option value="{{ $penlat->jenis_pelatihan }}">{{ $penlat->jenis_pelatihan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="stcw">STCW/Non :</label>
                                        <select name="stcw" class="form-control" id="stcw">
                                            <option value="-1">Show All</option>
                                            @foreach($penlatList->unique('kategori_pelatihan') as $penlat)
                                                <option value="{{ $penlat->kategori_pelatihan }}">{{ $penlat->kategori_pelatihan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
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
                    <table id="penlatTables" class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Display</th>
                                <th>Nama Pelatihan</th>
                                <th>Revenue &nbsp;<i class="fa fa-plus text-success"></i></th>
                                <th>Cost &nbsp;<i class="fa fa-minus text-danger"></i></th>
                                <th>Nett Income</th>
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
                        <h5 class="card-title font-weight-bold">Nett Income <span id="tahunIncome"></span></h5>
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
                                    <td style="width: 250px;" class="mb-2 font-weight-bold text-success"><i class="ti-minus mr-2"></i> Nett Income</td>
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
                        <li>Table diatas adalah List Pelatihan beserta Revenue, Cost & Nett Income.</li>
                        <li>Nominal dari table diatas, diambil dari semua batch yang dimiliki oleh pelatihan tersebut.</li>
                        <li>List Pelatihan adalah Induk Data dari Penlat, yang mana 1 Pelatihan bisa memiliki banyak batch.</li>
                        <li>Data di samping adalah summary dari semua revenue, cost & nett income data pelatihan.</li>
                        <li class="text-danger font-weight-bold">Untuk menampilkan summary & grafik, anda perlu memilih periode tahun.</li>
                    </ul>
                    <div class="alert alert-warning alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>Jika Import data berhasil namun data tidak muncul, artinya list pelatihan tidak ada/belum diimport.</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="row">
                    <div class="col-lg-12 d-flex justify-content-end align-items-end">
                        <select class="form-control mt-3 mr-4 zoom90" id="chartPeriode" name="chartPeriode" style="width: 150px;">
                            <option value="-1" selected disabled>Select Periode...</option>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <div id="chartContainerSpline" style="height: 370px; width: 100%;"></div>
                        </div>
                    </div>
                </div> <!-- /.row -->
            </div>
            <div class="card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <div id="stackedArea" style="height: 370px; width: 100%;"></div>
                        </div>
                    </div>
                </div> <!-- /.row -->
            </div>
        </div>
    </div>
</div>
<script>
window.onload = function () {
    // Get the current year
    var currentYear = new Date().getFullYear();

    // Populate the dropdowns with the current year as default
    var summaryPeriodeDropdown = document.getElementById("summaryPeriode");
    var chartPeriodeDropdown = document.getElementById("chartPeriode");

    // Populate both dropdowns with options from current year and previous years
    for (var year = currentYear; year >= currentYear - 4; year--) {
        var summaryOption = new Option(year, year);
        var chartOption = new Option(year, year);
        summaryPeriodeDropdown.add(summaryOption);
        chartPeriodeDropdown.add(chartOption);
    }

    // Set default selected value to current year
    summaryPeriodeDropdown.value = currentYear;
    chartPeriodeDropdown.value = currentYear;

    // Auto-load summary and chart data based on the default selected year
    loadSummaryData(currentYear);
    loadChartData(currentYear);

    // Add event listener to dropdowns for future changes
    summaryPeriodeDropdown.addEventListener("change", function() {
        var selectedOption = summaryPeriodeDropdown.value;
        loadSummaryData(selectedOption);
    });

    chartPeriodeDropdown.addEventListener("change", function() {
        var selectedOption = chartPeriodeDropdown.value;
        loadChartData(selectedOption); // Load chart data whenever the dropdown changes
    });
};
function loadChartData(value) {
    var selectedOption = value;
    if(selectedOption != '-1'){
        var selectedYear = selectedOption;
    } else {
        var selectedYear = '';
    }
    fetch('/api/chart-data-profits/' + selectedOption)
        .then(response => response.json())
        .then(data => {
            var chart = new CanvasJS.Chart("stackedArea", {
                animationEnabled: true,
                title: {
                    text: "Comparison of Profits: " + selectedYear + " vs Previous Year"
                },
                axisX: {
                    interval: 1,
                    labelFormatter: function(e) {
                        return e.label; // Use the label provided in the data points
                    }
                },
                axisY: {
                    includeZero: true,
                    title: "Profits",
                    prefix: "Rp",
                    labelFormatter: function(e) {
                        return CanvasJS.formatNumber(e.value, "#,##0");
                    }
                },
                toolTip: {
                    shared: true
                },
                legend: {
                    cursor: "pointer",
                    itemclick: toggleDataSeries
                },
                data: [
                    {
                        type: "stackedArea",
                        name: "Current Year",
                        showInLegend: true,
                        yValueFormatString: "#,##0 Rupiah",
                        dataPoints: data.dataPointsCurrentYear
                    },
                    {
                        type: "stackedArea",
                        name: "Previous Year",
                        showInLegend: true,
                        yValueFormatString: "#,##0 Rupiah",
                        dataPoints: data.dataPointsPreviousYear
                    }
                ]
            });
            chart.render();

            var chartProfit = new CanvasJS.Chart("chartContainerSpline", {
                animationEnabled: true,
                zoomEnabled: true,
                theme: "light2",
                title: { text: "Data Profits (Quarterly)" },
                axisX: {
                    interval: 1, // Ensure one label per point
                    labelAngle: -45 // Rotate labels if necessary to fit
                },
                axisY: {
                    includeZero: true
                },
                data: [{
                    type: "column",
                    color: "#6599FF",
                    yValueFormatString: "#,##0 Rupiah",
                    dataPoints: data.profitDataPoints // Use the updated data points with label and y values
                }]
            });
            chartProfit.render();
        });
    function toggleDataSeries(e) {
        e.dataSeries.visible = typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible;
        chart.render();
    }
}

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
                document.getElementById('tahun').innerText = selectedOption;
                document.getElementById('tahunSummary').innerText = selectedOption;
                document.getElementById('tahunRevenue').innerText = selectedOption;
                document.getElementById('tahunIncome').innerText = selectedOption;
            } else {
                document.getElementById('tahun').innerText = 'Overall';
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
$(document).ready(function() {
    var table = $('#penlatTables').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('profits') }}",
            data: function (d) {
                d.namaPenlat = $('#namaPenlat').val();
                d.jenisPenlat = $('#jenisPenlat').val();
                d.stcw = $('#stcw').val();
                d.periode = $('#periode').val();
                d.month = $('#month').val();
            }
        },
        columns: [
            { data: 'display', name: 'display', orderable: false, searchable: false },
            { data: 'description', name: 'description' },
            { data: 'revenue', name: 'revenue' },  // Updated column
            { data: 'cost', name: 'cost' },  // Updated column
            { data: 'nett_income', name: 'nett_income' }  // Updated column
        ]
    });
    // Redraw the table based on filter changes
    $('#namaPenlat, #jenisPenlat, #stcw, #periode, #month').change(function() {
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
</script>
@endsection
