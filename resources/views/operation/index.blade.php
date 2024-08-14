@extends('layouts.main')

@section('active-operation')
active font-weight-bold
@endsection

@section('show-operation')
show
@endsection

@section('operation')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="menu-icon fa fa-cogs"></i> Dashboard Operation</h1>
        <p class="mb-4">Dashboard Operation.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
    </div>
</div>
<div class="animated fadeIn">
    <div class="row">
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 timesheet">
            <a href="{{ route('participant-infographics') }}" class="clickable-card">
                <div class="card border-left-primary shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">
                                    Infografis Peserta</div>
                                    <div class="h6 mb-0 text-gray-800">{{ $getPesertaCount }} Peserta</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-users fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 medical">
            <a href="{{ route('tool-inventory') }}" class="clickable-card">
                <div class="card border-left-success shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">
                                    Inventaris Alat</div>
                                <div class="h6 mb-0 text-gray-800"><span style="font-size: 14px;">{{ $getAssetCount }} Assets with Total {{ $getAssetStock }} Stocks</span></div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-fire-extinguisher fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 reimburse">
            <a href="{{ route('tool-requirement-penlat') }}" class="clickable-card">
                <div class="card border-left-info shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">
                                    Kebutuhan Alat</div>
                                <div class="h6 mb-0 text-gray-800">{{ $getKebutuhanCount }} Records</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-check-square-o fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
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
        <div class="col-lg-6">
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
        </div><!-- /# column -->
    </div>
</div>
<script>
window.onload = function () {
    fetch('/api/chart-data')
        .then(response => response.json())
        .then(data => {
            var chart = new CanvasJS.Chart("chartContainerSpline", {
                animationEnabled: true,
                theme: "light2",
                title: { text: "Data Peserta Training" },
                axisX: { valueFormatString: "DD MMM" },
                axisY: {
                    title: "Data Peserta Training MTC 2024",
                    includeZero: true,
                    maximum: 270
                },
                data: [{
                    type: "splineArea",
                    color: "#6599FF",
                    xValueType: "dateTime",
                    xValueFormatString: "DD MMM",
                    yValueFormatString: "#,##0 Peserta",
                    dataPoints: data.splineDataPoints
                }]
            });
            chart.render();

            var pieChart = new CanvasJS.Chart("chartContainerPie", {
                theme: "light2",
                animationEnabled: true,
                title: { text: "Data Based On Batch & Jumlah Peserta" },
                data: [{
                    type: "doughnut",
                    indexLabel: "{symbol} - {y}",
                    yValueFormatString: "#,##0.0\"%\"",
                    showInLegend: true,
                    legendText: "{label} : {y}",
                    dataPoints: data.pieDataPoints
                }]
            });
            pieChart.render();
        });
}
</script>

<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
@endsection
