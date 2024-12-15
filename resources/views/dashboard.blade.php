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
                            <h4 class="mb-0">Training Trend by Type</h4>
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
                    <div class="card2 shadow">
                        <div class="card-header2 d-flex align-items-center">
                            <h4 class="mb-0">STCW & NON</h4>
                        </div>
                        <div class="card-block2 bg-white">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card-body d-flex justify-content-center align-items-center">
                                        <div id="barChart" style="height: 600px; width: 100%;"></div>
                                        <div id="loader" style="display: none;">Loading...</div>
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
                    <a href="{{ route('operation') }}" class="clickable-card">
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
                    <a href="{{ route('finance') }}" class="clickable-card">
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
                    <h4 class="mb-0">Training Trend by Revenue</h4>
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
            <div class="card2 shadow">
                <div class="card-header2 d-flex align-items-center">
                    <h4 class="mb-0">Training Session by Location</h4>
                </div>
                <div class="card-block2 bg-white">
                    <div class="row">
                        <div class="col-lg-12 zoom90">
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <div id="locationChart" style="height: 200px; width: 100%;"></div>
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
                                <div id="overallChart" style="height: 200px; width: 100%;"></div>
                                <div id="loader" style="display: none;">Loading...</div>
                            </div>
                        </div>
                    </div> <!-- /.row -->
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">

                </div>
                <div class="col-md-6">

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
<script>
$(document).ready(function () {

    // Attach change event listeners to the dropdowns
    $('#stcw, #type').on('change', function () {
        // Call the necessary functions when a value is selected
        updateDashboard();
        refreshChart();
        refreshTrendRevenueChart();
        refreshLocationChart();
        refreshTrainingTypeChart();
        refreshOverallChart();

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
        });
    });

    const dateRangeInput = $("#daterange");
    const typeSelect = $("#type");

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

        $.ajax({
            url: "/api/fetchAmountData",
            type: "POST",
            data: JSON.stringify({ _token: '{{ csrf_token() }}', periode: dateRange, type }),
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

    function refreshChart() {
        const dateRange = $('#daterange').val(); // Default date range
        const type = $('#type').val() || "-1"; // Default type (show all)

        // Prepare request payload
        const payload = {
            periode: dateRange,
            type: type,
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
                theme: "light2",
                axisX: {
                    interval: 1,
                    labelAngle: -45, // Rotate labels for better readability
                    labelFontSize: 12 // Reduce font size of X-axis labels
                },
                axisY: {
                    title: "Participants",
                    includeZero: true,
                    titleFontSize: 14, // Reduce font size of Y-axis title
                    labelFontSize: 12 // Reduce font size of Y-axis labels
                },
                legend: {
                    cursor: "pointer",
                    verticalAlign: "top",
                    horizontalAlign: "center",
                    dockInsidePlotArea: true,
                    fontSize: 12 // Reduce font size of legend
                },
                data: [
                    {
                        type: "bar",
                        name: "STCW Participants",
                        showInLegend: true,
                        legendText: "STCW Participants",
                        dataPoints: data.dataPoints1 // Updated data for STCW
                    },
                    {
                        type: "bar",
                        name: "NON STCW Participants",
                        showInLegend: true,
                        legendText: "NON STCW Participants",
                        dataPoints: data.dataPoints2 // Updated data for NON STCW
                    }
                ]
            });
            chart.render();
            },
            error: function (xhr, status, error) {
                console.error("Error fetching data:", status, error);
            }
        });
    }

    function refreshTrendRevenueChart() {
        const dateRange = $('#daterange').val(); // Default date range
        const type = $('#type').val() || "-1"; // Default type (show all)

        // Prepare request payload
        const payload = {
            periode: dateRange,
            type: type,
            _token: '{{ csrf_token() }}' // Include CSRF token for Laravel
        };

        // Send data via AJAX POST request
        $.ajax({
            url: "/api/chart-trend-revenue-data",
            type: "POST",
            data: JSON.stringify(payload),
            contentType: "application/json",
            success: function (data) {
                // Preprocess data for X-axis and tooltips
                const chartData = data.map(item => ({
                    label: shortenLabel(item.label), // Shorten label for display on X-axis
                    y: item.y, // Use total_biaya as value
                    fullLabel: item.label // Store full label for tooltip
                }));

                // Create and render the chart
                const chart = new CanvasJS.Chart("trendRevenueChart", {
                    animationEnabled: true,
                    theme: "light2",
                    axisY: {
                        title: "Revenue"
                    },
                    data: [{
                        type: "column",
                        toolTipContent: "{fullLabel}: {y}", // Tooltip shows full label
                        dataPoints: chartData
                    }]
                });

                chart.render();
            },
            error: function (xhr, status, error) {
                console.error("Error fetching data:", status, error);
                // Generic error message for other errors
                swal({
                    title: "An Error Occurred",
                    text: "Something went wrong. Please refresh your browser.",
                    icon: "error",
                });
            }
        });
    }

    function refreshLocationChart() {
        const dateRange = $('#daterange').val(); // Default date range
        const type = $('#type').val() || "-1"; // Default type (show all)

        // Prepare request payload
        const payload = {
            periode: dateRange,
            type: type,
            _token: '{{ csrf_token() }}' // Include CSRF token for Laravel
        };

        // Send data via AJAX POST request
        $.ajax({
            url: "/api/chart-location-data",
            type: "POST",
            data: JSON.stringify(payload),
            contentType: "application/json",
            success: function (data) {
                // Map data to CanvasJS format
                const chartData = data.map(item => ({
                    label: item.label, // Location name
                    y: item.y // Total participants
                }));

                // Create and render the chart
                const chart = new CanvasJS.Chart("locationChart", {
                    animationEnabled: true,
                    theme: "light2",
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
            },
            error: function (xhr, status, error) {
                console.error("Error fetching data:", status, error);
                // Generic error message for other errors
                swal({
                    title: "An Error Occurred",
                    text: "Something went wrong. Please refresh your browser.",
                    icon: "error",
                });
            }
        });
    }

    function refreshTrainingTypeChart() {
        const dateRange = $('#daterange').val(); // Default date range
        const type = $('#type').val() || "-1"; // Default type (show all)

        // Prepare request payload
        const payload = {
            periode: dateRange,
            type: type,
            _token: '{{ csrf_token() }}' // Include CSRF token for Laravel
        };

        // Send data via AJAX POST request
        $.ajax({
            url: "/api/chart-training-type-data",
            type: "POST",
            data: JSON.stringify(payload),
            contentType: "application/json",
            success: function (data) {
                // Map data to CanvasJS format
                const chartData = data.map(item => ({
                    label: item.label, // Training type name
                    y: item.y // Total participants
                }));

                // Create and render the pie chart
                const chart = new CanvasJS.Chart("trainingTypeChart", {
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
                });

                chart.render();
            },
            error: function (xhr, status, error) {
                console.error("Error fetching data:", status, error);
                // Generic error message for other errors
                swal({
                    title: "An Error Occurred",
                    text: "Something went wrong. Please refresh your browser.",
                    icon: "error",
                });
            }
        });
    }

    function refreshOverallChart() {
        const dateRange = $('#daterange').val(); // Default date range
        const type = $('#type').val() || "-1"; // Default type (show all)

        // Prepare request payload
        const payload = {
            periode: dateRange,
            type: type,
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
                        verticalAlign: "top",
                        horizontalAlign: "center",
                        dockInsidePlotArea: true
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

    function shortenLabel(label) {
        const words = label.split(" "); // Split label into words
        if (words.length > 2) {
            return words.slice(0, 2).join(" ") + "..."; // Keep only first 2 words and append "..."
        }
        return label; // Return the original label if it's 2 words or less
    }
});

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
