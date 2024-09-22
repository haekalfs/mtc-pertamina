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
<style>
#revenue-tag {
    font-weight: inherit !important;
    border-radius: 0px !important;
}

#header2 {
    border-bottom: 5px solid rgb(109, 109, 109);
    color: rgb(109, 109, 109);
    margin-bottom: 1.5rem;
    padding: 1rem 0;
}
.card2 {
    border: 0rem;
    border-radius: 0rem;
}

.card-header2 {
    background-color: rgb(76, 132, 206);
    border-radius: 0 !important;
    color:	white;
    margin-bottom: 0;
    padding:	1rem;
}

.card-block2 {
    border: 1px solid #cccccc;
    margin-bottom: 30px;
}
#revenue-column-chart, #products-revenue-pie-chart, #orders-spline-chart {
    height: 300px;
    width: 100%;
}
</style>
<div class="d-sm-flex align-items-center justify-content-between">
    <div class="mb-3">
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="menu-icon fa fa-money"></i> Dashboard Finances</h1>
        <small class="text-muted">Jan {{ $currentYear }} - Dec {{ $currentYear }}</small>
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
                                    Revenue </div>
                                <div class="h6 mb-0 text-gray-800"> {{ $totalRevenue ? 'IDR ' . number_format($totalRevenue, 0, ',', '.') : '-' }}</div>
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
                                    Cost
                                </div>
                                <div class="h6 mb-0 text-gray-800"> {{ $totalCosts ? 'IDR ' . number_format($totalCosts, 0, ',', '.') : '-' }}</div>
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
                                    Nett Income
                                </div>
                                <div class="h6 mb-0 text-gray-800">{{ $nettIncome ? 'IDR ' . number_format($nettIncome, 0, ',', '.') : '-' }}</div>
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
        <div class="col-lg-3">
            <div class="card">
                <h4 class="pt-3 pb-0 pl-3">Yearly Revenue</h4>
                <hr>
                <div class="row mt-1">
                    @php
                        // Initialize $previousRevenue with null to compare with the first revenue
                        $previousRevenue = null;
                        $maxRevenue = max($revenuePerYear); // Calculate max revenue once
                    @endphp

                    @foreach($revenuePerYear as $year => $revenue)
                        @if ($year != $threeYearsAgo)  {{-- Skip rendering the year that is 3 years ago --}}
                            <div class="col-lg-12">
                                <div class="d-flex flex-column justify-content-center align-items-center">
                                    <span>{{ $year }}</span> <!-- Year above the progress bar -->

                                    <!-- Calculate percentage change if not the first year -->
                                    @php
                                        $percentageChange = 0;

                                        // Only calculate percentage change if previousRevenue exists and is > 0
                                        if ($previousRevenue !== null && $previousRevenue > 0) {
                                            $percentageChange = (($revenue - $previousRevenue) / $previousRevenue) * 100;
                                        }

                                        // Calculate progress percentage safely
                                        $progress = ($maxRevenue > 0) ? round($revenue / $maxRevenue * 100, 0) : 0;
                                    @endphp

                                    <!-- Progress circle for the revenue -->
                                    <a class="progress-circle-wrapper animateBox">
                                        <div class="progress-circle p{{ $progress }} @if($progress >= 50) over50 @endif">
                                            <span>
                                            <!-- Show percentage change if available -->
                                            @if($previousRevenue !== null)
                                                @if($percentageChange > 0)
                                                    <small class="text-success">(+{{ number_format($percentageChange, 2) }}%)</small>
                                                @elseif($percentageChange < 0)
                                                    <small class="text-danger">({{ number_format($percentageChange, 2) }}%)</small>
                                                @else
                                                    <small class="text-secondary">(0%)</small>
                                                @endif
                                            @endif
                                            </span>
                                            <div class="left-half-clipper">
                                                <div class="first50-bar"></div>
                                                <div class="value-bar"></div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endif

                        @php
                            // Update previousRevenue for the next iteration
                            $previousRevenue = $revenue;
                        @endphp
                    @endforeach
                </div>
                <!-- Professional description with padding -->
                <div style="padding: 10px;">
                    <small> This chart shows the revenue comparison over three years, highlighting percentage changes from the previous year. </small>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card2 shadow">
                <h4 class="card-header2">Monthly Revenue</h4>
                <div class="card-block2 bg-white">
                    <div class="p-3">
                        <table class="table table-bordered table-striped zoom90">
                            <thead>
                                <tr>
                                    <th>Bulan</th>
                                    <th>Revenue</th>
                                    <th>Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyData as $index => $data)
                                    <tr>
                                        <td>{{ DateTime::createFromFormat('!m', $data['month'])->format('F') }}</td>

                                        {{-- Revenue --}}
                                        <td>
                                            {{ number_format($data['totalRevenueMonthly'], 0, ',', '.') }}

                                            @if($index > 0)
                                                @php
                                                    $previousRevenue = $monthlyData[$index - 1]['totalRevenueMonthly'];
                                                    $revenueChange = 0;
                                                    if($previousRevenue > 0) {
                                                        $revenueChange = (($data['totalRevenueMonthly'] - $previousRevenue) / $previousRevenue) * 100;
                                                    }
                                                @endphp

                                                @if($revenueChange > 0)
                                                    <small class="badge bg-success text-white" style="font-size: 10px;">(+{{ number_format($revenueChange, 2) }}%)</small>
                                                @elseif($revenueChange < 0)
                                                    <small class="badge bg-danger text-white" style="font-size: 10px;">({{ number_format($revenueChange, 2) }}%)</small>
                                                @else
                                                    <small class="badge bg-secondary text-white" style="font-size: 10px;">(0%)</small>
                                                @endif
                                            @endif
                                        </td>

                                        {{-- Costs --}}
                                        <td>
                                            {{ number_format($data['totalCostsMonthly'], 0, ',', '.') }}

                                            @if($index > 0)
                                                @php
                                                    $previousCost = $monthlyData[$index - 1]['totalCostsMonthly'];
                                                    $costChange = 0;
                                                    if($previousCost > 0) {
                                                        $costChange = (($data['totalCostsMonthly'] - $previousCost) / $previousCost) * 100;
                                                    }
                                                @endphp

                                                @if($costChange > 0)
                                                    <small class="badge bg-success text-white" style="font-size: 10px;">(+{{ number_format($costChange, 2) }}%)</small>
                                                @elseif($costChange < 0)
                                                    <small class="badge bg-danger text-white" style="font-size: 10px;">({{ number_format($costChange, 2) }}%)</small>
                                                @else
                                                    <small class="badge bg-secondary text-white" style="font-size: 10px;">(0%)</small>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> <!-- /.row -->
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card2 shadow">
                <h4 class="card-header2">Penlat with Most Revenue</h4>
                <div class="card-block2 bg-white">
                    <div class="row">
                        <div class="col-lg-12 d-flex justify-content-end align-items-end">
                            <select class="form-control mt-3 mr-4 zoom90" id="revenueChartPeriode" name="revenueChartPeriode" style="width: 150px;">
                                <option value="-1" selected disabled>Select Periode...</option>
                                @foreach(range(date('Y'), date('Y') - 5) as $year)
                                    <option value="{{ $year }}" @if ($year == $currentYear) selected @endif>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-12">
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <!-- <canvas id="TrafficChart"></canvas>   -->
                            <div id="trendRevenueChart" style="height: 370px; width: 100%;"></div>
                            </div>
                        </div>
                    </div> <!-- /.row -->
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card2 shadow">
                <h4 class="card-header2">Revenue Per-Quarters</h4>
                <div class="card-block2 bg-white">
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
            </div>
        </div>
        <div class="col-md-6">
            <div class="card2 shadow">
                <h4 class="card-header2">Total Revenue <span class="badge bg-success" id="profitText"></span></h4>
                <div class="card-block2 bg-white">
                    <div class="row">
                        <div class="col-lg-12 d-flex justify-content-end align-items-end">
                            <select class="form-control mt-3 mr-4 zoom90" id="pieChartPeriode" name="pieChartPeriode" style="width: 150px;">
                                <option value="-1" selected disabled>Select Periode...</option>
                                @foreach(range(date('Y'), date('Y') - 5) as $year)
                                    <option value="{{ $year }}" @if ($year == $currentYear) selected @endif>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-12">
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <div id="pieContainer" style="height: 370px; width: 100%;"></div>
                            </div>
                        </div>
                    </div> <!-- /.row -->
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card2 shadow">
                <h4 class="card-header2">Revenue Comparison</h4>
                <div class="card-block2 bg-white">
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
                                <!-- <canvas id="TrafficChart"></canvas>   -->
                            <div id="stackedArea" style="height: 370px; width: 100%;"></div>
                            </div>
                        </div>
                    </div> <!-- /.row -->
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card2 shadow">
                <h4 class="card-header2">Summary <span id="tahunSummary"></span></h4>
                <div class="card-block2 bg-white">
                    <div class="p-4">
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

    var revenueChartPeriode = document.getElementById("revenueChartPeriode");
    loadTrendChartData();
    revenueChartPeriode.addEventListener("change", function() {
        loadTrendChartData();
    });

    var pieChartPeriode = document.getElementById("pieChartPeriode");
    loadPieChartData();
    pieChartPeriode.addEventListener("change", function() {
        loadPieChartData();
    });

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
                title: { text: "Data Profits (Quarterly)",
                margin: 50 },
                axisX: {
                    interval: 1, // Ensure one label per point
                    labelAngle: -45 // Rotate labels if necessary to fit
                },
                axisY: {
                    includeZero: true
                },
                data: [{
                    type: "column",
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
                theme: "light2",
                title: {
                    text: "Revenue Comparison: " + firstDataset + " vs " + secondDataset, margin: 20
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
                    prefix: "IDR",
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

function loadTrendChartData() {
    var selectedYear = document.getElementById("revenueChartPeriode").value;

    fetch('/api/chart-data-trend-revenue/' + selectedYear)
        .then(response => response.json())
        .then(data => {
            var chartData = data.map(item => ({
                label: item.description, // Use nama_pelatihan as label
                y: parseInt(item.total_biaya)
            }));

            var chart = new CanvasJS.Chart("trendRevenueChart", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "Trend Revenue by Pelatihan (" + selectedYear + ")",
                    margin: 20
                },
                axisY: {
                    title: "Trend Revenue by Pelatihan (in currency)"
                },
                data: [{
                    type: "column",
                    dataPoints: chartData
                }]
            });

            chart.render();
        })
        .catch(error => console.error('Error fetching data:', error));
}

function loadPieChartData() {
    var selectedYear = document.getElementById("pieChartPeriode").value;

    fetch('/api/pie-chart-data-profits/' + selectedYear)
        .then(response => response.json())
        .then(data => {
            var chart = new CanvasJS.Chart("pieContainer", {
                animationEnabled: true,
                title: {
                    text: "Revenue Consumption by Cost (in Percentage)"
                },
                data: [{
                    type: "pie",
                    yValueFormatString: "#,##0.##\"%\"",
                    indexLabel: "{label} ({y}%)",
                    dataPoints: data.dataPoints
                }]
            });
            chart.render();

            // Display profits below the chart
            var profitText = document.getElementById("profitText");
            profitText.innerHTML = "IDR " + data.profitsValue;
        })
        .catch(error => console.error('Error fetching data:', error));
}

function redirectToPage() {
    var selectedOption = document.getElementById("yearSelected").value;
    var url = "{{ url('/financial-dashboard') }}" + "/" + selectedOption;
    window.location.href = url; // Redirect to the desired page
}
// Function to format currency
function formatCurrency(value) {
    return 'IDR ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
</script>
@endsection
