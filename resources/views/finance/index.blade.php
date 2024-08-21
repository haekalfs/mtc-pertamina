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
            <a href="{{ route('cost') }}" class="clickable-card">
                <div class="card border-left-primary shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">
                                    Revenue</div>
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
            <a href="{{ route('cost') }}" class="clickable-card">
                <div class="card border-left-danger shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">
                                    Cost
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
            <a href="{{ route('cost') }}" class="clickable-card">
                <div class="card border-left-success shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">
                                    Nett Income
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
        <div class="col-lg-12">
            <div class="card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <!-- <canvas id="TrafficChart"></canvas>   -->
                            <div id="chartContainerSpline" style="height: 370px; width: 100%;"></div>
                        </div>
                    </div>
                </div> <!-- /.row -->
            </div>
        </div><!-- /# column -->
        {{-- <div class="col-lg-6">
            <div class="card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <!-- <canvas id="TrafficChart"></canvas>   -->
                            <div id="chartContainerPie" style="height: 370px; width: 100%;"></div>
                        </div>
                    </div>
                </div> <!-- /.row -->
            </div>
        </div><!-- /# column --> --}}
    </div>
</div>
<script>
window.onload = function () {
    loadChartData();
};

function loadChartData() {
    var selectedOption = document.getElementById("yearSelected").value;
    fetch('/api/chart-data-profits/' + selectedOption) // Fixed concatenation
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
                    dataPoints: data.profitDataPoints
                }]
            });
            chart.render();
        });
}

function redirectToPage() {
    var selectedOption = document.getElementById("yearSelected").value;
    var url = "{{ url('/financial-dashboard') }}" + "/" + selectedOption;
    window.location.href = url; // Redirect to the desired page
}
</script>
@endsection
