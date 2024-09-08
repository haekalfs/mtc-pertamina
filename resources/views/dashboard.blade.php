@extends('layouts.main')

@section('active-dashboard')
active font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-4">
    <h1 class="h4 mb-0 font-weight-bold text-gray-800 text-secondary"><i class="far fa-smile-beam"></i> Welcome onboard, {{ Auth::user()->name }}!</h1>
    {{-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-smile-beam fa-sm text-white-50"></i> Show Details</a> --}}
</div>
<div class="animated fadeIn">
    <!-- Widgets  -->
    <div class="row">
        <div class="col-lg-3 col-md-6 animateBox">
            <a href="{{ route('operation') }}" class="clickable-card">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-widget-five">
                            <div class="stat-icon dib flat-color-1">
                                <i class="ti-cup"></i>
                            </div>
                            <div class="stat-content">
                                <div class="text-left dib">
                                    <div class="stat-text"><span class="count">{{ $getPesertaCount }}</span></div>
                                    <div class="stat-heading">Realisasi Peserta</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 animateBox">
            <a href="{{ route('marketing') }}" class="clickable-card">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-widget-five">
                            <div class="stat-icon dib flat-color-2">
                                <i class="ti-camera"></i>
                            </div>
                            <div class="stat-content">
                                <div class="text-left dib">
                                    <div class="stat-text"><span class="count">{{ $countCampaign }}</span></div>
                                    <div class="stat-heading">Event Campaign</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 animateBox">
            <a href="{{ route('plan-dev') }}" class="clickable-card">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-widget-five">
                            <div class="stat-icon dib flat-color-3">
                                <i class="pe-7s-browser"></i>
                            </div>
                            <div class="stat-content">
                                <div class="text-left dib">
                                    <div class="stat-text"><span><i class="fa fa-star text-warning"></i> {{ round($averageFeedbackScore, 2) ?? '-' }}</span></div>
                                    <div class="stat-heading">Rekap Feedback</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 animateBox">
            <a href="{{ route('finance') }}" class="clickable-card">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-widget-five">
                            <div class="stat-icon dib flat-color-4">
                                <i class="pe-7s-cash"></i>
                            </div>
                            <div class="stat-content">
                                <div class="text-left dib">
                                    <div class="h6"><span>{{ number_format($rawProfits, 0, ',', '.') }}</span></div>
                                    <div class="stat-heading">Raw Profits MTC</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 animateBox">
            <a href="{{ route('tool-inventory') }}" class="clickable-card">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-widget-five">
                            <div class="stat-icon dib flat-color-1">
                                <i class="fa fa-fire-extinguisher"></i>
                            </div>
                            <div class="stat-content">
                                <div class="text-left dib">
                                    <div class="h6">
                                        <div class="h6 mb-0 text-gray-800"><span style="font-size: 14px;">{{ $getAssetCount }} Assets with Total {{ $getAssetStock }} Stocks</span></div>
                                    </div>
                                    <div class="stat-heading">Inventaris Alat</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 animateBox">
            <a href="{{ route('instructor') }}" class="clickable-card">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-widget-five">
                            <div class="stat-icon dib flat-color-5">
                                <i class="fa fa-male"></i>
                            </div>
                            <div class="stat-content">
                                <div class="text-left dib">
                                    <div class="h6"><span>{{ $instructorCount }}</span></div>
                                    <div class="stat-heading">Jumlah Instruktur</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 animateBox">
            <a href="{{ route('penlat') }}" class="clickable-card">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-widget-five">
                            <div class="stat-icon dib flat-color-6">
                                <i class="fa fa-list-alt"></i>
                            </div>
                            <div class="stat-content">
                                <div class="text-left dib">
                                    <div class="h6"><span>{{ number_format($penlatCount, 0, ',', '.') }}</span></div>
                                    <div class="stat-heading">Jumlah Pelatihan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 animateBox">
            <a href="{{ route('batch-penlat') }}" class="clickable-card">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-widget-five">
                            <div class="stat-icon dib flat-color-7">
                                <i class="fa fa-folder"></i>
                            </div>
                            <div class="stat-content">
                                <div class="text-left dib">
                                    <div class="h6"><span>{{ number_format($batchCount, 0, ',', '.') }}</span></div>
                                    <div class="stat-heading">Jumlah Batches Pelatihan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <!-- /Widgets -->
    <!--  Traffic  -->
    <div class="row">
        <div class="col-xl-12 col-md-12 zoom90 mb-3">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold">Marketing Event</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            @if(!$headline || $headline->isEmpty())
                                No Data Available
                            @else
                            <div id="carouselExampleIndicators2" class="carousel slide" data-ride="carousel">

                                <div class="carousel-inner">
                                    @foreach($headline->chunk(3) as $index => $chunk)
                                    <div class="carousel-item{{ $index === 0 ? ' active' : '' }}">
                                        <div class="row">
                                            @foreach($chunk as $hl)
                                            <div class="col-md-4 mb-1">
                                                <div class="card" style=" border: 1px solid #e1e1e1;">
                                                    <img class="img-fluid" alt="100%x280" style="max-height: 200px;" src="{{ asset($hl->img_filepath) }}">
                                                    <div class="card-body">
                                                        <h4 class="card-title">{{ $hl->campaign_name }}</h4>
                                                        <div class="card-text short-news mb-3">{!! Str::limit($hl->campaign_result, 300, '...') !!}</div>
                                                        <a class="btn btn-secondary btn-sm read-more-button" href="{{ route('preview-campaign', $hl->id) }}">Read More</a>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div class="col-12 text-right p-0">
                                <a class="btn btn-primary mr-1" href="#carouselExampleIndicators2" role="button" data-slide="prev">
                                    <i class="fa fa-arrow-left"></i>
                                </a>
                                <a class="btn btn-primary " href="#carouselExampleIndicators2" role="button" data-slide="next">
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <div id="chartContainerSpline" style="height: 370px; width: 100%;"></div>
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
        <div class="col-lg-12">
            <div class="card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <div id="chartContainerSpline2" style="height: 370px; width: 100%;"></div>
                        </div>
                    </div>
                </div> <!-- /.row -->
            </div>
        </div>
    </div>
</div>

<script>
window.onload = function() {
    loadChartData();
    var selectedOption = "{{ date('Y') }}";
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

            document.getElementById("CountSTCW").innerText = `STCW: ${data.countSTCW} Peserta`;
            document.getElementById("CountNonSTCW").innerText = `Non-STCW: ${data.countNonSTCW} Peserta`;
        });
}

function loadChartData() {
    var selectedOption = "{{ date('Y') }}";
    fetch('/api/chart-data-profits/' + selectedOption) // Fixed concatenation
        .then(response => response.json())
        .then(data => {
            var chartProfit = new CanvasJS.Chart("chartContainerSpline2", {
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
                    type: "splineArea",
                    color: "#6599FF",
                    yValueFormatString: "#,##0 Rupiah",
                    dataPoints: data.profitDataPoints // Use the updated data points with label and y values
                }]
            });
            chartProfit.render();
        });
}
</script>
@endsection
