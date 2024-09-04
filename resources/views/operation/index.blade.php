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
                                    <div class="h6 mb-0 text-gray-800"><span style="font-size: 14px;">{{ $getAssetCount }} Assets with Total {{ $getAssetStock }} Stocks</span></div>
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
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <!-- <canvas id="TrafficChart"></canvas>   -->
                            <canvas id="mostUsedUtility"></canvas>
                        </div>
                    </div>
                </div> <!-- /.row -->
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

            var pieChart = new CanvasJS.Chart("chartContainerPie", {
                theme: "light2",
                animationEnabled: true,
                title: { text: "Top Penlat Based on Jumlah Peserta" },
                data: [{
                    type: "doughnut",
                    indexLabel: "{symbol} - {y}",
                    yValueFormatString: "#,##0\" Peserta\"",
                    showInLegend: true,
                    legendText: "{label} : {y}",
                    dataPoints: data.pieDataPoints
                }]
            });
            pieChart.render();

            const pieCtx = document.getElementById("mostUsedUtility").getContext("2d");
            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: data.mostUsedUtility.map(dp => dp.label),
                    datasets: [{
                        label: "Jumlah Penggunaan",
                        data: data.mostUsedUtility.map(dp => dp.y),
                        backgroundColor: data.mostUsedUtility.map((dp, index) => {
                            const colors = ["#4F81BD", "#C0504D", "#9BBB59", "#8064A2", "#4BACC6", "#F79646"];
                            return colors[index % colors.length];
                        })
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: "Pelatihan dengan Penggunaan Utilitas Terbanyak"
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    let label = data.mostUsedUtility[context.dataIndex].batch || '';

                                    if (label) {
                                        label += '\n'; // Move batch to the next line
                                    }
                                    if (context.raw !== null) {
                                        label += context.raw.toLocaleString() + ' Items';
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
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
