@extends('layouts.main')

@section('active-finance')
active font-weight-bold
@endsection

@section('show-finance')
show
@endsection

@section('finance')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="menu-icon fa fa-money"></i> Dashboard Finances</h1>
        <p class="mb-4">Dashboard Finances.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <select class="form-control" id="yearSelected" name="yearSelected" required onchange="redirectToPage()">
            @foreach (array_reverse($yearsBefore) as $year)
                <option value="{{ $year }}" @if ($year == $currentYear) selected @endif>{{ $year }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="animated fadeIn">
    <div class="row">
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 animateBox">
            <a href="{{ route('profits') }}" class="clickable-card">
                <div class="card border-left-primary shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">
                                    Revenue {{ $currentYear }}</div>
                                <div class="h6 mb-0 text-gray-800"> {{ $totalRevenue ? 'Rp ' . number_format($totalRevenue, 0, ',', '.') : '-' }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="ti-stats-up fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 animateBox">
            <a href="{{ route('profits') }}" class="clickable-card">
                <div class="card border-left-danger shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">
                                    Cost {{ $currentYear }}
                                </div>
                                <div class="h6 mb-0 text-gray-800"> {{ $totalCosts ? 'Rp ' . number_format($totalCosts, 0, ',', '.') : '-' }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="ti-stats-down fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 animateBox">
            <a href="{{ route('profits') }}" class="clickable-card">
                <div class="card border-left-success shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">
                                    Nett Income {{ $currentYear }}
                                </div>
                                <div class="h6 mb-0 text-gray-800">{{ $nettIncome ? 'Rp ' . number_format($nettIncome, 0, ',', '.') : '-' }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="ti-money fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" id="judul">Summary <span id="tahunSummary"></span></h6>
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
        <div class="col-lg-12">
            <div class="card">
                <div class="row">
                    <div class="col-lg-12 d-flex justify-content-end align-items-end">
                        <select class="form-control mt-3 mr-4 zoom90" id="chartPeriode" name="chartPeriode" style="width: 150px;">
                            <option value="-1" selected disabled>Select Periode...</option>
                            @foreach(range(date('Y'), date('Y') - 5) as $year)
                                <option value="{{ $year }}" @if ($year == $currentYear) selected @endif>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <!-- <canvas id="TrafficChart"></canvas>   -->
                            <div id="chartContainerSpline" style="height: 370px; width: 100%;"></div>
                        </div>
                    </div>
                </div> <!-- /.row -->
            </div>
            <div class="card">
                <div class="row">
                    <div class="col-lg-12 d-flex justify-content-end align-items-end">
                        <select class="form-control mt-3 mr-4 zoom90" id="firstDataset" name="firstDataset" style="width: 150px;">
                            <option value="-1" selected disabled>First Dataset...</option>
                            @foreach(range(date('Y'), date('Y') - 5) as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                        <select class="form-control mt-3 mr-4 zoom90" id="secondDataset" name="secondDataset" style="width: 150px;">
                            <option value="-1" selected disabled>Second Dataset...</option>
                            @foreach(range(date('Y'), date('Y') - 5) as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
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
    loadChartData();
    var chartPeriodeDropdown = document.getElementById("chartPeriode");
    chartPeriodeDropdown.addEventListener("change", function() {
        loadChartData(); // Load chart data whenever the dropdown changes
    });
    loadSummaryData();

    loadComparisonChartData();
    var firstDataset = document.getElementById("firstDataset");
    var secondDataset = document.getElementById("secondDataset");

    firstDataset.addEventListener("change", function() {
        loadComparisonChartData();
    });
    secondDataset.addEventListener("change", function() {
        loadComparisonChartData();
    });
};

function loadSummaryData() {
    var selectedOption = document.getElementById("yearSelected").value;
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
function loadChartData() {
    var selectedOption = document.getElementById("chartPeriode").value;
    fetch('/api/chart-data-profits/' + selectedOption) // Fixed concatenation
        .then(response => response.json())
        .then(data => {

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


function loadComparisonChartData() {
    var firstDataset = document.getElementById("firstDataset").value;
    var secondDataset = document.getElementById("secondDataset").value;
    fetch('/api/comparison-chart-data-profits/' + firstDataset + '/' + secondDataset)
        .then(response => response.json())
        .then(data => {
            var chart = new CanvasJS.Chart("stackedArea", {
                animationEnabled: true,
                title: {
                    text: "Comparison of Profits: " + firstDataset + " vs " + secondDataset
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
        });
    function toggleDataSeries(e) {
        e.dataSeries.visible = typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible;
        chart.render();
    }
}

function redirectToPage() {
    var selectedOption = document.getElementById("yearSelected").value;
    var url = "{{ url('/financial-dashboard') }}" + "/" + selectedOption;
    window.location.href = url; // Redirect to the desired page
}
// Function to format currency
function formatCurrency(value) {
    return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
</script>
@endsection
