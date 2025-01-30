@extends('layouts.main')

@section('active-dashboard')
active font-weight-bold
@endsection

@section('content')
<style>
.welcome-container {
    position: fixed;
    bottom: 10px;
    right: 5px;
    z-index: 9999; /* Ensure it's above all other content */
    display: flex;
    align-items: flex-end;
    padding-right: 20px; /* Add padding to prevent overlap */
}

.box3 {
    width: 300px;
    border-radius: 15px;
    background: rgb(76, 132, 206);
    color: #fff;
    padding: 20px;
    margin-bottom: 30px;
    text-align: center;
    font-weight: 900;
    font-family: Arial;
    position: relative;
}

.sb13:before {
    content: "";
    width: 0;
    height: 0;
    position: absolute;
    border-left: 15px solid rgb(76, 132, 206);
    border-right: 15px solid transparent;
    border-top: 15px solid rgb(76, 132, 206);
    border-bottom: 15px solid transparent;
    right: -16px;
    top: 20px;
}

.bottom-right-img {
    width: 100px; /* Adjust the size as needed */
    display: block;
    margin-left: 10px; /* Add some spacing between the image and the bubble */
}

.close-btn {
    position: absolute;
    top: -10px; /* Move the button closer to the image */
    right: -10px;
    background-color: transparent;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: #888;
}

.close-btn:hover {
    color: #000;
}
</style>

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
<div id="welcome-container" class="welcome-container">
    <div class="box3 sb13">Welcome onboard, {{ Auth::user()->name }}!</div>
    <button class="close-btn" onclick="closeImage()">&#10005;</button>
    <img src="{{ asset('img/people-ptmc.png') }}" alt="Character" class="bottom-right-img">
</div>
<div class="animated fadeIn">
    <!-- Widgets  -->
    <div class="row" id="dashboard">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                <div class="h5 m-0">
                    Dashboard MTC
                </div>
                <small><a href="#" id="fullscreenButton" onclick="toggleFullScreen('dashboard')">
                    <i class="fa fa-arrows-alt"></i> Fullscreen
                </a></small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="row align-items-center p-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="daterange">Date Range :</label>
                                    <input type="text" class="form-control underline-input" name="daterange" id="daterange" value="{{ date('Y') }}-01-01 - {{ date('Y') }}-12-31" autocomplete="off" required/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="stcw">STCW/Non :</label>
                                    <select class="form-control" id="stcw" name="stcw">
                                        <option value="-1" selected>Show All</option>
                                        <option value="1">STCW</option>
                                        <option value="0">Non STCW</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">Type :</label>
                                    <select class="form-control" id="type" name="type">
                                        <option value="-1">Show All</option>
                                        @foreach($infographicTypes as $type)
                                            <option value="{{ $type->jenis_pelatihan }}">{{ $type->jenis_pelatihan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card2 shadow">
                        <div class="card-header2 d-flex align-items-center">
                            <h4 class="mb-0">STCW & NON</h4>
                        </div>
                        <div class="card-block2 bg-white">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card-body d-flex justify-content-center align-items-center">
                                        <div id="barChart" style="height: 700px; width: 100%;"></div>
                                        <div id="loader" style="display: none;">Loading...</div>
                                    </div>
                                </div>
                            </div> <!-- /.row -->
                        </div>
                    </div>
                    <div class="card2 shadow">
                        <div class="card-header2 d-flex align-items-center">
                            <h4 class="mb-0">Training Trend by Type</h4>
                            <button
                                type="button"
                                id="backButtonType"
                                class="btn btn-outline-white btn-md ml-auto zoom90 invisible">
                                <i class="fa fa-arrow-left fa-sm"></i> Back
                            </button>
                        </div>
                        <div class="card-block2 bg-white">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card-body d-flex justify-content-center align-items-center">
                                        <div id="trainingTypeChart" style="height: 370px; width: 100%;"></div>
                                    </div>
                                </div>
                            </div> <!-- /.row -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-lg-6 col-md-6 animateBox">
                    <a href="{{ route('participant-infographics') }}" class="clickable-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="stat-widget-five">
                                    <div class="stat-icon dib flat-color-1">
                                        <i class="ti-cup"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="text-left dib">
                                            <div class="stat-text"><span id="pesertaCount">-</span></div>
                                            <div class="stat-heading">Realisasi Peserta</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-6 col-md-6 animateBox">
                    <a href="{{ route('plan-dev') }}" class="clickable-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="stat-widget-five">
                                    <div class="stat-icon dib flat-color-3">
                                        <i class="fa fa-male"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="text-left dib">
                                            <div class="stat-text"><span><i class="fa fa-star text-warning"></i> <span id="avgFeedbackScore">-</span></span></div>
                                            <div class="stat-heading">Avg Trainer Feedback</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-6 col-md-6 animateBox">
                    <a href="{{ route('plan-dev') }}" class="clickable-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="stat-widget-five">
                                    <div class="stat-icon dib flat-color-3">
                                        <i class="fa fa-trophy"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="text-left dib">
                                            <div class="stat-text"><span><i class="fa fa-star text-warning"></i> <span id="avgTrainingFeedbackScore">-</span></span></div>
                                            <div class="stat-heading">Avg Training Feedback</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-6 col-md-6 animateBox">
                    <a href="{{ route('finance') }}" class="clickable-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="stat-widget-five">
                                    <div class="stat-icon dib flat-color-4">
                                        <i class="pe-7s-cash"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="text-left dib">
                                            <div class="stat-text"><span id="totalRevenue" style="font-size: 15px;">-</span></div>
                                            <div class="stat-heading">Total Revenue</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-6 col-md-6 animateBox">
                    <a href="{{ route('batch-penlat') }}" class="clickable-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="stat-widget-five">
                                    <div class="stat-icon dib flat-color-9">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="text-left dib">
                                            <div class="stat-text"><span id="totalTraining">-</span></div>
                                            <div class="stat-heading">Total Training</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-6 col-md-6 animateBox">
                    <a href="{{ route('finance') }}" class="clickable-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="stat-widget-five">
                                    <div class="stat-icon dib flat-color-4">
                                        <i class="ti-stats-down"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="text-left dib">
                                            <div class="stat-text"><span id="totalCost" style="font-size: 15px;">-</span></div>
                                            <div class="stat-heading">Total Cost</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="card2 shadow">
                <div class="card-header2 d-flex align-items-center">
                    <h4 class="mb-0">Training Session by Location</h4>
                    <button
                        type="button"
                        id="backButton"
                        class="btn btn-outline-white btn-md ml-auto zoom90 invisible">
                        <i class="fa fa-arrow-left fa-sm"></i> Back
                    </button>
                </div>
                <div class="card-block2 bg-white">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <div id="locationChart" style="height: 200px; width: 100%;"></div>
                            </div>
                        </div>
                    </div> <!-- /.row -->
                </div>
            </div>
            <div class="card2 shadow">
                <div class="card-header2 d-flex align-items-center">
                    <h4 class="mb-0">Certificate Status</h4>
                </div>
                <div class="card-block2 bg-white">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <div id="certificateChart" style="height: 300px; width: 100%;"></div>
                                <div id="loader" style="display: none;">Loading...</div>
                            </div>
                            <div class="text-center mb-3">
                                <span><strong>Pending:</strong> <span id="pendingCount">0</span></span>
                                <span><strong>Issued:</strong> <span id="issuedCount">0</span></span>
                                <span><strong>Not Registered:</strong> <span id="notRegisteredCount">0</span></span>
                            </div>
                        </div>
                    </div> <!-- /.row -->
                </div>
            </div>
            <div class="card2 shadow">
                <div class="card-header2 d-flex align-items-center">
                    <h4 class="mb-0">Training Trend by Revenue</h4>
                    <button
                        type="button"
                        id="backButtonRevenue"
                        class="btn btn-outline-white btn-md ml-auto zoom90 invisible">
                        <i class="fa fa-arrow-left fa-sm"></i> Back
                    </button>
                </div>
                <div class="card-block2 bg-white">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <div id="trendRevenueChart" style="height: 400px; width: 100%;"></div>
                            </div>
                        </div>
                    </div> <!-- /.row -->
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card2 shadow">
                <div class="card-header2 d-flex align-items-center">
                    <h4 class="mb-0">Training Trend</h4>
                </div>
                <div class="card-block2 bg-white">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <div id="overallChart" style="height: 300px; width: 100%;"></div>
                                <div id="loader" style="display: none;">Loading...</div>
                            </div>
                        </div>
                    </div> <!-- /.row -->
                </div>
            </div>
        </div>
    </div>
    <!-- /Widgets -->
    <!--  Traffic  -->

    <div class="row">
        <div class="col-md-12">
            <div class="pb-2 mb-3 mt-1 border-bottom h5">
                Calendar & Events
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-header d-flex justify-content-start">
                            <h6 class="m-0 font-weight-bold">Training Calendar</h6>
                        </div>
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div> <!-- /.row -->
            </div>
        </div>
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
                                                <div class="card" style="border: 1px solid #e1e1e1;">
                                                    <img class="img-fluid" alt="100%x280" style="max-height: 200px;" src="{{ asset($hl->img_filepath) }}">
                                                    @php
                                                        $created_athl = \Carbon\Carbon::parse($hl->created_at);
                                                        $nowhl = \Carbon\Carbon::now();
                                                        $diffInDayshl = $created_athl->diffInDays($nowhl);
                                                    @endphp
                                                    <div class="card-body">
                                                        <h4 class="card-title">{{ $hl->campaign_name }}</h4>
                                                        <div class="card-text short-news mb-3">{!! Str::limit($hl->campaign_result, 300, '...') !!}</div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span>
                                                                @if($diffInDayshl < 7)
                                                                    {{ $created_athl->diffForHumans() }}
                                                                @else
                                                                    a long time ago
                                                                @endif
                                                            </span>
                                                            <a class="btn btn-secondary btn-sm read-more-button" href="{{ route('preview-campaign', $hl->id) }}">Read More</a>
                                                        </div>
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
    </div>
</div>
<div id="participantModal" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 1200px;" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="modalTitle">Participant Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="participantTable">
                    <thead>
                        <tr>
                            <th>Tgl Pelaksanaan</th>
                            <th>Nama Peserta</th>
                            <th>Batch</th>
                            <th>Jenis Pelatihan</th>
                            <th>Kategori Program</th>
                            <th>Realisasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data populated dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function () {
    let isDrilldown = false; // Track drilldown state
    let mainChartOptions; // Store the main chart options globally

    let isTrainingDrilldown = false; // Track drilldown state for Training Type chart
    let mainTrainingChartOptions; // Store the main chart options globally

    let mainRevenueChartOptions = {}; // Store main chart options globally
    let isRevenueDrilldown = false; // Track drilldown state

    // Attach change event listeners to the dropdowns
    $('#stcw, #type').on('change', function () {
        // Call the necessary functions when a value is selected
        updateDashboard();
        refreshChart();
        refreshTrendRevenueChart();
        refreshLocationChart();
        refreshTrainingTypeChart();
        refreshOverallChart();
        refreshCertificateData();

        // Show success message
        swal("Success! The Filter is applied successfully!", {
            icon: "success",
        });
    });

    $(function() {
        // Initialize daterangepicker with default start and end dates from Blade
        $('input[name="daterange"]').daterangepicker({
            startDate: moment("{{ date('Y') }}-01-01"),
            endDate: moment("{{ date('Y') }}-12-31"),
            autoUpdateInput: true, // Automatically update input
            locale: {
                cancelLabel: 'Clear',
                format: 'YYYY-MM-DD' // Display format for dates
            }
        });

        // Apply the daterangepicker actions
        $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            updateDashboard();
            refreshChart();
            refreshTrendRevenueChart();
            refreshLocationChart();
            refreshTrainingTypeChart();
            refreshOverallChart();
            refreshCertificateData();
            swal("Success! The Filter is applied successfully!", {
                icon: "success",
            });
        });

        $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            updateDashboard();
            refreshChart();
            refreshTrendRevenueChart();
            refreshLocationChart();
            refreshTrainingTypeChart();
            refreshOverallChart();
            refreshCertificateData();
        });
    });

    const dateRangeInput = $("#daterange");
    const typeSelect = $("#type");
    const stcwSelect = $("#stcw");

    const elementsToUpdate = {
        pesertaCount: $("#pesertaCount"),
        avgFeedbackScore: $("#avgFeedbackScore"),
        avgTrainingFeedbackScore: $("#avgTrainingFeedbackScore"),
        totalRevenue: $("#totalRevenue"),
        totalTraining: $("#totalTraining"),
        totalCost: $("#totalCost"),
    };

    const updateDashboard = () => {
        const dateRange = dateRangeInput.val(); // Default date range
        const type = typeSelect.val() || "-1"; // Default type to "Show All"
        const stcw = stcwSelect.val() || "-1"; // Default type to "Show All"

        $.ajax({
            url: "/api/fetchAmountData",
            type: "POST",
            data: JSON.stringify({ _token: '{{ csrf_token() }}', periode: dateRange, type, stcw }),
            contentType: "application/json",
            success: function (data) {
                // Update DOM elements
                elementsToUpdate.pesertaCount.text(data.peserta_count ?? "-");
                elementsToUpdate.avgFeedbackScore.text(
                    data.average_feedback_score?.toFixed(2) ?? "-"
                );
                elementsToUpdate.avgTrainingFeedbackScore.text(
                    data.average_feedback_training_score?.toFixed(2) ?? "-"
                );
                elementsToUpdate.totalRevenue.text(
                    data.raw_profits?.toLocaleString("en-US", {
                        style: "currency",
                        currency: "USD",
                    }) ?? "-"
                );
                elementsToUpdate.totalTraining.text(data.total_training ?? "-");
                elementsToUpdate.totalCost.text(
                    data.raw_costs?.toLocaleString("en-US", {
                        style: "currency",
                        currency: "USD",
                    }) ?? "-"
                );
            },
            error: function (xhr, status, error) {
                console.error("Error updating dashboard:", error);
            },
        });
    };

    // Trigger default data load
    updateDashboard();
    refreshChart();
    refreshTrendRevenueChart();
    refreshLocationChart();
    refreshTrainingTypeChart();
    refreshOverallChart();
    refreshCertificateData();

    function refreshChart() {
        const dateRange = $('#daterange').val(); // Default date range
        const type = $('#type').val() || "-1"; // Default type (show all)
        const stcw = $('#stcw').val() || "-1"; // Default STCW type (show all)

        // Prepare request payload
        const payload = {
            periode: dateRange,
            type: type,
            stcw: stcw,
            _token: '{{ csrf_token() }}' // Include CSRF token for Laravel
        };

        // Send data via AJAX POST request
        $.ajax({
            url: "/api/chart-bar-data",
            type: "POST",
            data: JSON.stringify(payload),
            contentType: "application/json",
            success: function (data) {
                const chart = new CanvasJS.Chart("barChart", {
                    animationEnabled: true,
                    zoomEnabled: true,
                    theme: "light2",
                    axisX: {
                        interval: 1,
                        labelAngle: -45,
                        labelFontSize: 12
                    },
                    axisY: {
                        title: "Participants",
                        includeZero: true,
                        titleFontSize: 14,
                        labelFontSize: 12
                    },
                    legend: {
                        cursor: "pointer",
                        verticalAlign: "top",
                        horizontalAlign: "center",
                        fontSize: 12,
                        itemWidth: 150
                    },
                    data: [
                        {
                            type: "bar",
                            name: "STCW Participants",
                            showInLegend: true,
                            legendText: "STCW Participants",
                            dataPoints: data.dataPoints1.map(point => ({
                                ...point,
                                click: function (e) {
                                    handleStcwBarClick(e);
                                }
                            }))
                        },
                        {
                            type: "bar",
                            name: "NON STCW Participants",
                            showInLegend: true,
                            legendText: "NON STCW Participants",
                            dataPoints: data.dataPoints2.map(point => ({
                                ...point,
                                click: function (e) {
                                    handleNonStcwBarClick(e);
                                }
                            }))
                        }
                    ]
                });
                chart.render();
            },
            error: function (xhr, status, error) {
                console.error("Error fetching chart data:", status, error);
            }
        });
    }

    // Initialize the main trend revenue chart
    function refreshTrendRevenueChart() {
        const dateRange = $('#daterange').val();
        const type = $('#type').val() || "-1";

        const payload = {
            periode: dateRange,
            type: type,
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: "/api/chart-trend-revenue-data",
            type: "POST",
            data: JSON.stringify(payload),
            contentType: "application/json",
            success: function (data) {
                const chartData = data.map(item => ({
                    label: shortenLabel(item.label),
                    y: item.y,
                    fullLabel: item.label,
                    click: function (e) {
                        showDrilldownRevenueChart(item.label);
                    }
                }));

                mainRevenueChartOptions = {
                    animationEnabled: true,
                    zoomEnabled: true,
                    theme: "light2",
                    axisY: { title: "Revenue" },
                    data: [{
                        type: "column",
                        toolTipContent: "{fullLabel}: {y}",
                        dataPoints: chartData
                    }]
                };

                const chart = new CanvasJS.Chart("trendRevenueChart", mainRevenueChartOptions);
                chart.render();

                $("#backButtonRevenue").addClass("invisible");
                isRevenueDrilldown = false;
            },
            error: function () {
                swal("An Error Occurred", "Failed to fetch trend data.", "error");
            }
        });
    }

    // Show drilldown chart with details
    function showDrilldownRevenueChart(description) {
        const dateRange = $('#daterange').val();

        const payload = {
            periode: dateRange,
            description: description,
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: "/api/chart-drilldown-revenue-data",
            type: "POST",
            data: JSON.stringify(payload),
            contentType: "application/json",
            success: function (data) {
                const chartData = data.map(item => ({
                    label: item.label,
                    y: item.y,
                    click: function (e) {
                        fetchParticipantsByRevenue(description, e.dataPoint.label);
                    }
                }));

                const chart = new CanvasJS.Chart("trendRevenueChart", {
                    animationEnabled: true,
                    theme: "light2",
                    title: { text: `Details for ${description}` },
                    axisX: { title: "Date" },
                    axisY: { title: "Revenue", includeZero: true },
                    data: [{
                        type: "column",
                        dataPoints: chartData
                    }]
                });

                chart.render();
                $("#backButtonRevenue").removeClass("invisible");
                isRevenueDrilldown = true;
            },
            error: function () {
                swal("An Error Occurred", "Failed to fetch drilldown data.", "error");
            }
        });
    }

    // Fetch participant details and open modal
    function fetchParticipantsByRevenue(description, period) {
        const dateRange = $('#daterange').val();

        const payload = {
            periode: dateRange,
            description: description,
            period: period,
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: "/api/fetch-participants-by-revenue",
            type: "POST",
            data: JSON.stringify(payload),
            contentType: "application/json",
            success: function (data) {
                initializeDataTable(data);
                $('#participantModal').modal('show');
            },
            error: function () {
                swal("An Error Occurred", "Failed to fetch participant details.", "error");
            }
        });
    }

    // Back button functionality
    $("#backButtonRevenue").click(function () {
        if (isRevenueDrilldown) {
            refreshTrendRevenueChart();
        }
    });

    function refreshLocationChart() {
        const dateRange = $('#daterange').val();
        const type = $('#type').val() || "-1";
        const stcw = $('#stcw').val() || "-1";

        const payload = {
            periode: dateRange,
            type: type,
            stcw: stcw,
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: "/api/chart-location-data",
            type: "POST",
            data: JSON.stringify(payload),
            contentType: "application/json",
            success: function (data) {
                const chartData = data.map(item => ({
                    label: item.label, // Location name
                    y: item.y, // Number of participants
                    click: function (e) {
                        showDrilldownChart(e.dataPoint.label); // Drill-down by location
                    }
                }));

                // Store main chart options globally for resetting later
                mainChartOptions = {
                    animationEnabled: true,
                    zoomEnabled: true,
                    theme: "light2",
                    axisY: {
                        title: "Number of Participants",
                        includeZero: true
                    },
                    data: [{
                        type: "column",
                        showInLegend: true,
                        legendText: "{label}",
                        dataPoints: chartData
                    }]
                };

                const chart = new CanvasJS.Chart("locationChart", mainChartOptions);
                chart.render();

                // Hide the back button when rendering the main chart
                $("#backButton").addClass("invisible");
                isDrilldown = false; // Reset drilldown state
            },
            error: function (xhr, status, error) {
                console.error("Error fetching data:", status, error);
                swal({
                    title: "An Error Occurred",
                    text: "Something went wrong. Please refresh your browser.",
                    icon: "error",
                });
            }
        });
    }


    function showDrilldownChart(location) {
        const dateRange = $('#daterange').val();
        const payload = {
            periode: dateRange,
            location: location,
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: "/api/chart-location-drilldown",
            type: "POST",
            data: JSON.stringify(payload),
            contentType: "application/json",
            success: function (data) {
                const chartData = data.map(item => ({
                    label: item.label, // Month-Year
                    y: item.y // Total participants
                }));

                const chart = new CanvasJS.Chart("locationChart", {
                    animationEnabled: true,
                    theme: "light2",
                    title: {
                        text: `Participants in ${location}`,
                        margin: 20
                    },
                    axisX: {
                        title: "Month-Year",
                    },
                    axisY: {
                        title: "Number of Participants",
                        includeZero: true
                    },
                    data: [{
                        type: "column",
                        dataPoints: chartData
                    }]
                });

                chart.render();

                // Show the back button when drilldown is triggered
                $("#backButton").removeClass("invisible");
                isDrilldown = true;

                // Add click event to chart data points for drilldown
                chart.options.data[0].click = function (e) {
                    const selectedPeriod = e.dataPoint.label; // Get selected drilldown label (Month-Year)
                    fetchParticipantsByLocation(location, selectedPeriod); // Fetch participants for this period
                };
            },
            error: function (xhr, status, error) {
                console.error("Error fetching drilldown data:", status, error);
                swal({
                    title: "An Error Occurred",
                    text: "Unable to fetch drilldown data.",
                    icon: "error",
                });
            }
        });
    }

    // Fetch participants by location and period
    function fetchParticipantsByLocation(location, period) {
        const dateRange = $('#daterange').val();
        const payload = {
            periode: dateRange,
            location: location,
            period: period,
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: "/api/fetch-participants-by-location", // New endpoint to fetch participant details
            type: "POST",
            data: JSON.stringify(payload),
            contentType: "application/json",
            success: function (data) {
                // Initialize the DataTable with participant data
                initializeDataTable(data);

                // Show the modal with participant details
                $('#participantModal').modal('show');
            },
            error: function (xhr, status, error) {
                console.error("Error fetching participant data:", status, error);
                swal({
                    title: "An Error Occurred",
                    text: "Unable to fetch participant data.",
                    icon: "error",
                });
            }
        });
    }

    // Back/Reset button functionality
    $("#backButton").click(function () {
        if (isDrilldown) {
            refreshLocationChart(); // Call the main chart rendering function
        }
    });

    function refreshTrainingTypeChart() {
        const dateRange = $('#daterange').val();
        const type = $('#type').val() || "-1";
        const stcw = $('#stcw').val() || "-1";

        const payload = {
            periode: dateRange,
            type: type,
            stcw: stcw,
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: "/api/chart-training-type-data",
            type: "POST",
            data: JSON.stringify(payload),
            contentType: "application/json",
            success: function (data) {
                // Map data to CanvasJS format
                const chartData = data.map(item => ({
                    label: item.label, // Training type name
                    y: item.y, // Total participants
                    click: function (e) {
                        showDrilldownTrainingTypeChart(e.dataPoint.label); // Drill-down
                    }
                }));

                // Store main chart options for resetting later
                mainTrainingChartOptions = {
                    animationEnabled: true,
                    theme: "light2",
                    data: [{
                        type: "pie",
                        showInLegend: true,
                        legendText: "{label}",
                        indexLabel: "{label} - #percent%",
                        yValueFormatString: "#,##0",
                        dataPoints: chartData
                    }]
                };

                const chart = new CanvasJS.Chart("trainingTypeChart", mainTrainingChartOptions);
                chart.render();

                // Hide back button when rendering the main chart
                $("#backButtonType").addClass("invisible");
                isTrainingDrilldown = false; // Reset drilldown state
            },
            error: function (xhr, status, error) {
                console.error("Error fetching data:", status, error);
                swal({
                    title: "An Error Occurred",
                    text: "Something went wrong. Please refresh your browser.",
                    icon: "error",
                });
            }
        });
    }

    function showDrilldownTrainingTypeChart(type) {
        const dateRange = $('#daterange').val();

        const payload = {
            periode: dateRange,
            type: type,
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: "/api/chart-training-type-drilldown", // Adjust endpoint if necessary
            type: "POST",
            data: JSON.stringify(payload),
            contentType: "application/json",
            success: function (data) {
                const chartData = data.map(item => ({
                    label: item.label, // Period (Month-Year)
                    y: item.y, // Total participants
                    period: item.label // Store the period for drilldown
                }));

                const chart = new CanvasJS.Chart("trainingTypeChart", {
                    animationEnabled: true,
                    theme: "light2",
                    title: {
                        text: `Details for ${type}`,
                        margin: 20
                    },
                    axisX: {
                        title: "Details",
                    },
                    axisY: {
                        title: "Number of Participants",
                        includeZero: true
                    },
                    data: [{
                        type: "column",
                        dataPoints: chartData
                    }]
                });

                chart.render();

                // Show back button when drilldown is triggered
                $("#backButtonType").removeClass("invisible");
                isTrainingDrilldown = true;

                // Add click event to chart data points for drilldown
                chart.options.data[0].click = function (e) {
                    const selectedPeriod = e.dataPoint.period; // Get the selected drilldown label (Period)
                    fetchParticipantsByTrainingType(type, selectedPeriod); // Fetch participant data for the selected period
                };
            },
            error: function (xhr, status, error) {
                console.error("Error fetching drilldown data:", status, error);
                swal({
                    title: "An Error Occurred",
                    text: "Unable to fetch drilldown data.",
                    icon: "error",
                });
            }
        });
    }

    function fetchParticipantsByTrainingType(type, period) {
        const dateRange = $('#daterange').val();

        const payload = {
            periode: dateRange,
            type: type,
            period: period, // Pass the selected period (Month-Year)
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: "/api/fetch-participants-by-training-type", // New endpoint to fetch participant details
            type: "POST",
            data: JSON.stringify(payload),
            contentType: "application/json",
            success: function (data) {
                // Initialize the DataTable with participant data
                initializeDataTable(data);

                // Show the modal with participant details
                $('#participantModal').modal('show');
            },
            error: function (xhr, status, error) {
                console.error("Error fetching participant data:", status, error);
                swal({
                    title: "An Error Occurred",
                    text: "Unable to fetch participant data.",
                    icon: "error",
                });
            }
        });
    }

    // Back/Reset button functionality
    $("#backButtonType").click(function () {
        if (isTrainingDrilldown) {
            refreshTrainingTypeChart(); // Call the main chart rendering function
        }
    });

    function refreshOverallChart() {
        const dateRange = $('#daterange').val(); // Default date range
        const type = $('#type').val() || "-1"; // Default type (show all)
        const stcw = $('#stcw').val() || "-1"; // Default type to "Show All"

        // Prepare request payload
        const payload = {
            periode: dateRange,
            type: type,
            stcw: stcw,
            _token: '{{ csrf_token() }}' // Include CSRF token for Laravel
        };

        // Send data via AJAX POST request
        $.ajax({
            url: "/api/chart-overall-data",
            type: "POST",
            data: JSON.stringify(payload),
            contentType: "application/json",
            success: function (data) {
                const chart = new CanvasJS.Chart("overallChart", {
                    animationEnabled: true,
                    zoomEnabled: true,
                    theme: "light2",
                    axisX: {
                        title: "Month",
                        interval: 1,
                        labelAngle: -45 // Rotate labels for better readability
                    },
                    axisY: {
                        title: "Total Participants",
                        includeZero: true,
                    },
                    legend: {
                        cursor: "pointer",
                        verticalAlign: "top", // Place the legend at the top
                        horizontalAlign: "center", // Center the legend horizontally
                        fontSize: 12, // Adjust the font size
                        itemWidth: 150, // Add space for the legend text to prevent overlap
                    },
                    data: [
                        {
                            type: "spline", // Spline chart for smooth lines
                            name: "Overall Participants",
                            showInLegend: true,
                            legendText: "Overall Participants",
                            dataPoints: data.dataPoints // Overall data
                        }
                    ]
                });
                chart.render();
            },
            error: function (xhr, status, error) {
                console.error("Error fetching overall data:", status, error);
                // Generic error message for other errors
                swal({
                    title: "An Error Occurred",
                    text: "Something went wrong. Please refresh your browser.",
                    icon: "error",
                });
            }
        });
    }

    function refreshCertificateData() {
        const dateRange = $('#daterange').val(); // Default date range
        const type = $('#type').val() || "-1"; // Default type (show all)
        const stcw = $('#stcw').val() || "-1"; // Default type to "Show All"

        // Prepare request payload
        const payload = {
            periode: dateRange,
            type: type,
            stcw: stcw,
            _token: '{{ csrf_token() }}' // Include CSRF token for Laravel
        };

        // Send data via AJAX POST request
        $.ajax({
            url: "/api/chart-issued-certificate-data",
            type: "POST", // Correct HTTP method
            data: JSON.stringify(payload),
            contentType: "application/json",
            success: function (data) {
                let totalPending = 0;
                let totalIssued = 0;
                let totalNotRegistered = 0;

                // Sum up all the values for totals
                data.dataPointsRegisteredButNotYetIssued.forEach(point => {
                    totalPending += point.y;
                });
                data.dataPointsIssued.forEach(point => {
                    totalIssued += point.y;
                });
                data.dataPointsPending.forEach(point => {
                    totalNotRegistered += point.y;
                });

                // Update the summary section
                $('#pendingCount').text(totalPending);
                $('#issuedCount').text(totalIssued);
                $('#notRegisteredCount').text(totalNotRegistered);

                // Initialize the chart
                const chart = new CanvasJS.Chart("certificateChart", {
                    animationEnabled: true,
                    zoomEnabled: true,
                    theme: "light2",
                    axisX: {
                        title: "Month",
                        interval: 1,
                        labelAngle: -45
                    },
                    axisY: {
                        title: "Total Participants",
                        includeZero: true,
                    },
                    legend: {
                        verticalAlign: "top",
                        horizontalAlign: "center",
                        fontSize: 14,
                        itemWidth: 150,
                    },
                    data: [
                        {
                            type: "stackedColumn",
                            name: "Pending Certificates",
                            showInLegend: true,
                            legendText: "Pending Certificates",
                            dataPoints: data.dataPointsRegisteredButNotYetIssued.map(point => ({
                                ...point,
                                click: function (e) {
                                    handleBarClick(e);
                                }
                            }))
                        },
                        {
                            type: "stackedColumn",
                            name: "Issued Certificates",
                            showInLegend: true,
                            legendText: "Issued Certificates",
                            dataPoints: data.dataPointsIssued.map(point => ({
                                ...point,
                                click: function (e) {
                                    handleBarClick(e);
                                }
                            }))
                        },
                        {
                            type: "stackedColumn",
                            name: "Not Registered",
                            showInLegend: true,
                            legendText: "Not Registered",
                            dataPoints: data.dataPointsPending.map(point => ({
                                ...point,
                                click: function (e) {
                                    handleBarClick(e);
                                }
                            }))
                        }
                    ]
                });
                chart.render();
            },
            error: function (xhr, status, error) {
                console.error("Error fetching overall data:", status, error);
                swal({
                    title: "An Error Occurred",
                    text: "Something went wrong. Please refresh your browser.",
                    icon: "error",
                });
            }
        });
    }

    function shortenLabel(label) {
        const words = label.split(" "); // Split label into words
        if (words.length > 2) {
            return words.slice(0, 2).join(" ") + "..."; // Keep only first 2 words and append "..."
        }
        return label; // Return the original label if it's 2 words or less
    }
});

function handleBarClick(e) {
    // Split the label to extract the month and year
    const [month, year] = e.dataPoint.label.split(' '); // "Jun 2024" -> ["Jun", "2024"]
    const status = e.dataSeries.name; // Get the status from the series name

    console.log(`Clicked Bar - Month: ${month}, Year: ${year}, Status: ${status}`); // Debugging

    // AJAX request to fetch data
    $.ajax({
        url: '/api/get-participants',
        type: 'POST',
        data: JSON.stringify({
            month: month, // Correct month (e.g., "Jun")
            year: year,   // Correct year (e.g., "2024")
            status: status,
            _token: '{{ csrf_token() }}' // Pass CSRF token
        }),
        contentType: 'application/json',
        success: function (response) {
            // Populate the table with Yajra DataTables
            initializeDataTable(response.data);
            // Show the modal
            $('#participantModal').modal('show');
        },
        error: function (xhr, status, error) {
            console.error('Error fetching participants:', status, error);
        }
    });
}

function initializeDataTable(data) {
    // Destroy existing DataTable if already initialized
    if ($.fn.DataTable.isDataTable('#participantTable')) {
        $('#participantTable').DataTable().clear().destroy();
    }

    // Initialize the DataTable
    $('#participantTable').DataTable({
        data: data,
        columns: [
            { data: 'tgl_pelaksanaan', title: 'Tgl Pelaksanaan' },
            { data: 'nama_peserta', title: 'Nama Peserta' },
            { data: 'batch', title: 'Batch' },
            { data: 'jenis_pelatihan', title: 'Jenis Pelatihan' },
            { data: 'kategori_program', title: 'Kategori Program' },
            { data: 'realisasi', title: 'Realisasi' }
        ],
        responsive: true,
        pageLength: 10,
        searching: true,
        lengthChange: true
    });
}


function handleStcwBarClick(e) {
    handleBarClickFetchProcess(e, "STCW");
}

function handleNonStcwBarClick(e) {
    handleBarClickFetchProcess(e, "NON STCW");
}

function handleBarClickFetchProcess(e, status) {
    // Extract month and year from the clicked bar
    const [month, year] = e.dataPoint.label.split(' ');

    console.log(`Clicked Bar - Month: ${month}, Year: ${year}, Status: ${status}`); // Debugging

    // AJAX request to fetch data
    $.ajax({
        url: '/api/get-participants-stcw-non',
        type: 'POST',
        data: JSON.stringify({
            month: month,
            year: year,
            status: status,
            _token: '{{ csrf_token() }}'
        }),
        contentType: 'application/json',
        success: function (response) {
            // Populate the table with DataTables
            initializeParticipantDataTable(response.data);
            // Show the modal
            $('#participantModal').modal('show');
        },
        error: function (xhr, status, error) {
            console.error('Error fetching participants:', status, error);
        }
    });
}

function initializeParticipantDataTable(data) {
    // Destroy existing DataTable if already initialized
    if ($.fn.DataTable.isDataTable('#participantTable')) {
        $('#participantTable').DataTable().clear().destroy();
    }

    // Initialize the DataTable
    $('#participantTable').DataTable({
        data: data,
        columns: [
            { data: 'tgl_pelaksanaan', title: 'Tgl Pelaksanaan' },
            { data: 'nama_peserta', title: 'Nama Peserta' },
            { data: 'batch', title: 'Batch' },
            { data: 'jenis_pelatihan', title: 'Jenis Pelatihan' },
            { data: 'kategori_program', title: 'Kategori Program' },
            { data: 'realisasi', title: 'Realisasi' }
        ],
        responsive: true,
        pageLength: 10,
        searching: true,
        lengthChange: true
    });
}

function closeImage() {
    document.getElementById('welcome-container').style.display = 'none';
}

function toggleFullScreen(elementId) {
    var element = document.getElementById(elementId);

    if (!document.fullscreenElement) {
        // Request fullscreen
        if (element.requestFullscreen) {
            element.requestFullscreen();
        } else if (element.mozRequestFullScreen) { // Firefox
            element.mozRequestFullScreen();
        } else if (element.webkitRequestFullscreen) { // Chrome, Safari and Opera
            element.webkitRequestFullscreen();
        } else if (element.msRequestFullscreen) { // IE/Edge
            element.msRequestFullscreen();
        }

        // Set scrolling styles
        element.style.overflow = 'auto'; // Enable scrolling if needed
    } else {
        // Exit fullscreen
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.mozCancelFullScreen) { // Firefox
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) { // Chrome, Safari and Opera
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) { // IE/Edge
            document.msExitFullscreen();
        }

        // Reset scrolling styles
        element.style.overflow = ''; // Reset to default
    }
}
</script>
@endsection
