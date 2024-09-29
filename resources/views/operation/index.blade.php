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
        <select class="form-control" id="yearSelected" name="yearSelected" required onchange="redirectToPage()" style="width: 100px;">
            @foreach (array_reverse($yearsBefore) as $year)
                <option value="{{ $year }}" {{ $year == $yearSelected ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="animated fadeIn" id="mainContainer">
    <div class="row">
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 animateBox">
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
        <div class="col-xl-4 col-md-6 animateBox">
            <a href="{{ route('tool-inventory') }}" class="clickable-card">
                <div class="card border-left-success shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">
                                    Inventaris Alat</div>
                                <div class="h6 mb-0 text-gray-800">
                                    @if($totalAttention != 0)
                                    <span style="font-size: 14px;">
                                        <span class="badge bg-warning text-white">{{ $totalAttention }} Assets Need Attention</span>
                                        {{-- <span class="badge out-of-stock">{{ $OutOfStockCount }} Assets is Out of Stock</span><br>
                                        <span class="badge out-of-stock">{{ $requiredMaintenanceCount }} Assets Require Maintenance</span> --}}
                                    </span>
                                    @else
                                    <div class="h6 mb-0 text-gray-800"><span style="font-size: 14px;">{{ $getAssetCount }} Assets Registered</span></div>
                                    @endif
                                </div>
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
        <div class="col-xl-4 col-md-6 animateBox">
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
        <div class="col-lg-12">
            <div class="card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <!-- <canvas id="TrafficChart"></canvas>   -->
                            <canvas id="lineChart"></canvas>
                        </div>
                        <div class="text-center mb-3">
                            <span id="CountSTCW"></span><span> & </span>
                            <span id="CountNonSTCW"></span>
                        </div>
                    </div>
                </div> <!-- /.row -->
            </div>
        </div><!-- /# column -->
        <div class="col-lg-7">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <h4 class="pt-3 pb-0 pl-3">{{ $monthName ? $monthName : '-' }} - {{ $yearSelected }}</h4>
                        <hr>
                        <div class="row mt-2">
                            <div class="col-lg-6">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div id='myDiv'><!-- Plotly chart will be drawn inside this DIV --></div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div id='myDiv2'><!-- Plotly chart will be drawn inside this DIV --></div>
                                </div>
                            </div>
                        </div>

                        <!-- Professional description with padding -->
                        <div style="padding: 10px;">
                            <small>
                                This chart illustrates the increase or decrease in participant realization for the latest month compared to the previous month.
                                The data reflects changes in both STCW and Non-STCW programs.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card zoom80">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card-body d-flex justify-content-center align-items-center">
                                    <!-- Make the table responsive and prevent overflow -->
                                    <div class="table-responsive w-100">
                                        <table class="table table-bordered mb-4" style="table-layout: fixed; width: 100%;">
                                            <thead class="text-white">
                                                <!-- Year and Quarter Headers -->
                                                <tr>
                                                    <th class="bg-secondary" rowspan="3" style="vertical-align: middle; text-align: center; width: 20%;">Category</th>
                                                    <th class="text-center bg-secondary" colspan="8">{{ $yearSelected }}</th>
                                                </tr>
                                                <tr class="bg-secondary">
                                                    @foreach(['TW-1', 'TW-2', 'TW-3', 'TW-4'] as $tw)
                                                        <th class="text-center" colspan="2">{{ $tw }}</th>
                                                    @endforeach
                                                </tr>
                                                <!-- Sub-headers for each quarter (Total, Percentage) -->
                                                <tr class="bg-secondary">
                                                    @foreach(['TW-1', 'TW-2', 'TW-3', 'TW-4'] as $tw)
                                                        <th class="text-center" style="width: 10%;">Total</th>
                                                        <th class="text-center" style="width: 10%;">%</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(empty($quarterlyData))
                                                    <tr class="text-center">
                                                        <td colspan="9">No Data Available</td>
                                                    </tr>
                                                @else
                                                    <!-- External Data Row -->
                                                    <tr>
                                                        <th>External</th>
                                                        @foreach($quarterlyData as $dataQuarter)
                                                            <td>{{ number_format($dataQuarter['external_count']) }}</td>
                                                            <td>{{ number_format($dataQuarter['external_percentage'], 2) }}%</td>
                                                        @endforeach
                                                    </tr>
                                                    <!-- Internal Data Row -->
                                                    <tr>
                                                        <th>Internal</th>
                                                        @foreach($quarterlyData as $dataQuarter)
                                                            <td>{{ number_format($dataQuarter['internal_count']) }}</td>
                                                            <td>{{ number_format($dataQuarter['internal_percentage'], 2) }}%</td>
                                                        @endforeach
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div> <!-- /.table-responsive -->
                                </div> <!-- /.card-body -->
                            </div> <!-- /.col-lg-12 -->
                        </div> <!-- /.row -->
                        <div class="pl-4 pr-4 pb-4">
                            <small>
                                This chart illustrates the increase or decrease in participant realization for the latest month compared to the previous month.
                                The data reflects changes in both STCW and Non-STCW programs.
                            </small>
                        </div>
                    </div> <!-- /.card -->
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <h4 class="pt-3 pb-0 pl-3">Monthly Realization</h4>
                <hr>
                <div class="card-body pt-0 d-flex justify-content-center align-items-center">
                    <table class="table table-bordered table-striped zoom90">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th>Peserta Eksternal</th>
                                <th>Peserta Internal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $index => $row)
                                <tr>
                                    <td>{{ $row['month'] }}</td>

                                    <!-- External Participants Count and Percentage Calculation -->
                                    <td>
                                        {{ $row['external_count'] }}
                                        @if($index > 0)
                                            @php
                                                // Calculate the difference between the current and previous month
                                                $previousExternalCount = $data[$index - 1]['external_count'];
                                                $percentageChange = 0;
                                                if($previousExternalCount > 0) {
                                                    $percentageChange = (($row['external_count'] - $previousExternalCount) / $previousExternalCount) * 100;
                                                }
                                            @endphp

                                            <!-- Display the percentage change -->
                                            @if($percentageChange > 0)
                                                <small class="badge bg-success text-white" style="font-size: 10px;">(+{{ number_format($percentageChange, 2) }}%)</small>
                                            @elseif($percentageChange < 0)
                                                <small class="badge bg-danger text-white" style="font-size: 10px;">({{ number_format($percentageChange, 2) }}%)</small>
                                            @else
                                                <small class="badge bg-secondary text-white" style="font-size: 10px;">(0%)</small>
                                            @endif
                                        @endif
                                    </td>

                                    <!-- Internal Participants Count -->
                                    <td>
                                        {{ $row['internal_count'] }}
                                        @if($index > 0)
                                            @php
                                                // Calculate the percentage change for internal participants
                                                $previousInternalCount = $data[$index - 1]['internal_count'];
                                                $internalPercentageChange = 0;
                                                if($previousInternalCount > 0) {
                                                    $internalPercentageChange = (($row['internal_count'] - $previousInternalCount) / $previousInternalCount) * 100;
                                                }
                                            @endphp

                                            <!-- Display the percentage change for internal participants -->
                                            @if($internalPercentageChange > 0)
                                                <small class="badge bg-success text-white" style="font-size: 10px;">(+{{ number_format($internalPercentageChange, 2) }}%)</small>
                                            @elseif($internalPercentageChange < 0)
                                                <small class="badge bg-danger text-white" style="font-size: 10px;">({{ number_format($internalPercentageChange, 2) }}%)</small>
                                            @else
                                                <small class="badge bg-secondary text-white" style="font-size: 10px;">(0%)</small>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="row">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="pl-4" id="pieChartNewParticipant" style="height: 370px; width: 100%;"></div>
                            </div>
                            <div class="col-lg-9">
                                <!-- <canvas id="TrafficChart"></canvas>   -->
                                <div class="pr-4" id="chartContainerBar" style="height: 400px; width: 100%;"></div>
                            </div>
                        </div>
                    </div>
                </div> <!-- /.row -->
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <div id="chartContainerColumn" style="height: 400px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="text-right mb-4">
        {{-- button or else --}}
        <small><i class="ti-fullscreen"></i>
            <a href="#" onclick="toggleFullScreen('mainContainer')">&nbsp;<i>Fullscreen</i></a>
        </small>
    </div>
</div>
<script>
window.onload = function() {
    // Data passed from PHP
    var countSTCW = {{ $countSTCWGauge }};
    var countNonSTCW = {{ $countNonSTCWGauge }};
    var stcwDelta = {{ $stcwDelta }};
    var nonStcwDelta = {{ $nonStcwDelta }};

    // STCW gauge
    var dataSTCW = [
        {
            domain: { x: [0, 1], y: [0, 1] },
            value: countSTCW,
            title: { text: "STCW" },
            type: "indicator",
            mode: "gauge+number+delta",
            delta: { reference: stcwDelta, valueformat: ".2f", relative: true }, // Use the delta here for percentage increase/decrease
            gauge: {
                axis: { range: [null, stcwDelta + 100] },
                steps: [
                    { range: [0, 250], color: "lightgray" },
                    { range: [250, 400], color: "gray" }
                ],
                threshold: {
                    line: { color: "red", width: 4 },
                    thickness: 0.75,
                    value: stcwDelta
                }
            }
        }
    ];

    // NON STCW gauge
    var dataNONSTCW = [
        {
            domain: { x: [0, 1], y: [0, 1] },
            value: countNonSTCW,
            title: { text: "NON STCW" },
            type: "indicator",
            mode: "gauge+number+delta",
            delta: { reference: nonStcwDelta, valueformat: ".2f", relative: true }, // Same for NON STCW
            gauge: {
                axis: { range: [null, nonStcwDelta + 100] },
                steps: [
                    { range: [0, 250], color: "lightgray" },
                    { range: [250, 400], color: "gray" }
                ],
                threshold: {
                    line: { color: "red", width: 4 },
                    thickness: 0.75,
                    value: nonStcwDelta
                }
            }
        }
    ];

    // Layout for both charts
    var layout = { width: 280, height: 170, margin: { l: 40, r: 40, t: 40, b: 0 } };

    // Plot the gauges
    Plotly.newPlot('myDiv', dataSTCW, layout);
    Plotly.newPlot('myDiv2', dataNONSTCW, layout);

    var selectedOption = document.getElementById("yearSelected").value;
    fetch('/api/chart-data/' + selectedOption)
        .then(response => response.json())
        .then(data => {
            // Map the labels to show month names and year
            const labels = data.dataPointsSpline1.map(dp => {
                const date = new Date(dp.x);
                return date.toLocaleString('default', { month: 'short', year: 'numeric' }); // Show Month and Year
            });

            const dataset1 = {
                label: "STCW Participants",
                borderColor: "rgba(101, 153, 255, 0.9)",
                borderWidth: 2,
                backgroundColor: "rgba(101, 153, 255, 0.5)",
                pointBorderColor: "rgba(101, 153, 255, 0.9)",
                pointBackgroundColor: "rgba(101, 153, 255, 0.9)",
                data: data.dataPointsSpline1.map(dp => dp.y),
                fill: true,
                tension: 0.4,
            };

            const dataset2 = {
                label: "NON STCW Participants",
                borderColor: "rgba(255, 99, 132, 0.9)",
                borderWidth: 2,
                backgroundColor: "rgba(255, 99, 132, 0.5)",
                pointBorderColor: "rgba(255, 99, 132, 0.9)",
                pointBackgroundColor: "rgba(255, 99, 132, 0.9)",
                data: data.dataPointsSpline2.map(dp => dp.y),
                fill: true,
                tension: 0.4,
            };

            const ctx = document.getElementById("lineChart").getContext("2d");
            const myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,  // Monthly labels
                    datasets: [dataset1, dataset2]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Month'  // Update the x-axis label to 'Month'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Data Peserta Training'
                            },
                            suggestedMax: 200
                        }
                    },
                    plugins: {
                        annotation: {
                            annotations: {
                                targetLine: {
                                    type: 'line',
                                    scaleID: 'y',
                                    value: 190,
                                    borderColor: 'rgba(255, 0, 0, 0.75)',
                                    borderWidth: 2,
                                    label: {
                                        content: 'Target 190 Peserta/Bulan',
                                        enabled: true,
                                        position: 'center',
                                        backgroundColor: 'rgba(255, 0, 0, 0.75)',
                                        color: '#fff'
                                    }
                                }
                            }
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    }
                }
            });

            var pieChart = new CanvasJS.Chart("chartContainerBar", {
                theme: "light2",
                animationEnabled: true,
                title: { text: "Top Penlat Based on Total Participants",
                margin: 20,
                fontSize: 16 },
                data: [{
                    type: "bar",
                    indexLabel: "{symbol} - {y}",
                    yValueFormatString: "#,##0\" Peserta\"",
                    dataPoints: data.barDataPoints
                }]
            });
            pieChart.render();

             // Column chart (New)
            var columnChart = new CanvasJS.Chart("chartContainerColumn", {
                theme: "light2",
                animationEnabled: true,
                title: {
                    text: "7 Years Prior of Total Participants",
                    margin: 20
                },
                axisY: {
                    title: "Jumlah Peserta",
                    labelFormatter: function(e) {
                        return CanvasJS.formatNumber(e.value, "#,##0");
                    }
                },
                data: [{
                    type: "column",
                    yValueFormatString: "#,##0\" Peserta\"",
                    dataPoints: data.columnDataPoints
                }]
            });
            columnChart.render();

            // Render the chart using CanvasJS
            var pieChartNewParticipant = new CanvasJS.Chart("pieChartNewParticipant", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "New vs Returning Participants " + selectedOption
                },
                data: [{
                    type: "pie",
                    indexLabel: "{label} - {y}",
                    dataPoints: data.chartData
                }]
            });
            pieChartNewParticipant.render();

            document.getElementById("CountSTCW").innerText = `STCW: ${data.countSTCW} Peserta`;
            document.getElementById("CountNonSTCW").innerText = `Non-STCW: ${data.countNonSTCW} Peserta`;
        });
}
function redirectToPage() {
    var selectedOption = document.getElementById("yearSelected").value;
    var url = "{{ url('/operation-dashboard') }}" + "/" + selectedOption;
    window.location.href = url; // Redirect to the desired page
}
</script>
@endsection
