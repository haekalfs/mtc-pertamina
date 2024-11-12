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
{{-- <div class="d-sm-flex align-items-center zoom90 justify-content-between mb-4">
    <h1 class="h4 mb-0 font-weight-bold text-gray-800 text-secondary"><i class="far fa-smile-beam"></i> Welcome onboard, {{ Auth::user()->name }}!</h1>
    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-smile-beam fa-sm text-white-50"></i> Show Details</a>
</div> --}}
<div class="animated fadeIn">
    <!-- Widgets  -->
    <div class="row">
        <div class="col-md-12">
            <div class="pb-2 mb-3 border-bottom h5">
                Dashboard MTC
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="row align-items-center p-4">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="day">Day :</label>
                                    <select class="form-control" id="day" name="day">
                                        <option value="-1" selected>Show All</option>
                                        @foreach(range(1, 31) as $day)
                                            <option value="{{ $day }}">{{ $day }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="month">Month :</label>
                                    <select class="form-control" id="month" name="month">
                                        <option value="-1">Show All</option>
                                        @foreach(range(1, 12) as $month)
                                            <option value="{{ $month }}">
                                                {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="email">Year :</label>
                                    <select class="form-control" id="year" name="year">
                                        <option value="-1" selected>Show All</option>
                                        @foreach(range(date('Y'), date('Y') - 5) as $year)
                                            <option value="{{ $year }}" @if ($year == date('Y')) selected @endif>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category">Subholding :</label>
                                    <select class="form-control" id="category" name="category">
                                        <option value="-1" selected>Show All</option>
                                        @foreach($infographicCategories as $category)
                                            <option value="{{ $category->subholding }}">{{ $category->subholding }}</option>
                                        @endforeach
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
                </div>
                <div class="col-md-12">
                    <div class="card2 shadow">
                        <div class="card-header2 d-flex align-items-center">
                            <h4 class="mb-0">Increase Amount</h4>
                        </div>
                        <div class="card-block2 bg-white">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card-body d-flex justify-content-center align-items-center">
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
                                    </div>
                                </div>
                            </div> <!-- /.row -->
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card2 shadow">
                        <div class="card-header2 d-flex align-items-center">
                            <h4 class="mb-0">Training Session by Location</h4>
                        </div>
                        <div class="card-block2 bg-white">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card-body d-flex justify-content-center align-items-center">
                                        <div id="chartContainer" style="height: 200px; width: 100%;"></div>
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
                                <div id="trendRevenueChart" style="height: 200px; width: 100%;"></div>
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
                                <div id="trainingTrendChart" style="height: 200px; width: 100%;"></div>
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
<script>
function closeImage() {
    document.getElementById('welcome-container').style.display = 'none';
}
</script>
<script>
function loadDashboardData() {
    const selectedDay = document.getElementById("day").value;
    const selectedMonth = document.getElementById("month").value;
    const selectedYear = document.getElementById("year").value;
    const selectedCategory = document.getElementById("category").value;
    const selectedType = document.getElementById("type").value;

    fetch(`/api/dashboard-chart-data?year=${selectedYear}&month=${selectedMonth}&day=${selectedDay}&category=${selectedCategory}&type=${selectedType}`)
        .then(response => response.json())
        .then(data => {
            // 1. Location Chart
            const locationChartData = data.locationData.map(item => ({
                label: item.tempat_pelaksanaan,
                y: item.total
            }));

            new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                theme: "light2",
                axisY: { title: "Total Trainings", includeZero: true },
                data: [{ type: "column", dataPoints: locationChartData }]
            }).render();

            // 2. Trend Revenue Chart
            var chartData = data.trendRevenueData.map(item => ({
                label: item.description, // Use nama_pelatihan as label
                y: parseInt(item.total_biaya)
            }));

            var chart = new CanvasJS.Chart("trendRevenueChart", {
                animationEnabled: true,
                theme: "light2",
                data: [{
                    type: "column",
                    dataPoints: chartData
                }]
            });

            chart.render();

            // 3. Gauge Charts for STCW and NON STCW
            Plotly.newPlot('myDiv', [{
                domain: { x: [0, 1], y: [0, 1] },
                value: data.countSTCWGauge,
                title: { text: "STCW" },
                type: "indicator",
                mode: "gauge+number+delta",
                delta: { reference: data.stcwDelta, relative: true },
                gauge: { axis: { range: [null, data.stcwDelta + 100] } }
            }], { width: 280, height: 170, margin: { l: 40, r: 40, t: 40, b: 0 } });

            Plotly.newPlot('myDiv2', [{
                domain: { x: [0, 1], y: [0, 1] },
                value: data.countNonSTCWGauge,
                title: { text: "NON STCW" },
                type: "indicator",
                mode: "gauge+number+delta",
                delta: { reference: data.nonStcwDelta, relative: true },
                gauge: { axis: { range: [null, data.nonStcwDelta + 100] } }
            }], { width: 280, height: 170, margin: { l: 40, r: 40, t: 40, b: 0 } });

            document.getElementById("pesertaCount").textContent = data.getPesertaCount;
            document.getElementById("totalRevenue").textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(data.rawProfits);
            document.getElementById("totalCost").textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(data.rawCosts);
            document.getElementById("avgFeedbackScore").textContent = data.averageFeedbackScore ?? '-';
            document.getElementById("totalTraining").textContent = data.totalTraining ?? '-';
            document.getElementById("avgTrainingFeedbackScore").textContent = data.averageFeedbackTrainingScore ?? '-';
        });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', loadDashboardData);
document.getElementById("year").addEventListener("change", loadDashboardData);
document.getElementById("month").addEventListener("change", loadDashboardData);
document.getElementById("day").addEventListener("change", loadDashboardData);
document.getElementById("category").addEventListener("change", loadDashboardData);
document.getElementById("type").addEventListener("change", loadDashboardData);
</script>
<script>
// Function to fetch data and render the chart
async function loadTrendData() {
    try {
        const selectedYear = document.getElementById("year").value;
        // Fetch data from the API
        const response = await fetch(`/api/trend-chart-data?year=${selectedYear}`);
        const result = await response.json();

        // Initialize CanvasJS chart with the data points from API
        const chart = new CanvasJS.Chart("trainingTrendChart", {
            animationEnabled: true,
            theme: "light2",
            axisY: {
                title: "Total Participants",
                includeZero: true
            },
            data: [{
                type: "spline",
                dataPoints: result.dataPoints
            }]
        });

        chart.render();
    } catch (error) {
        console.error("Error fetching or rendering data:", error);
    }
}
// Load the chart for the current year when the page loads
document.addEventListener("DOMContentLoaded", () => {
    const selectedYear = document.getElementById("year").value;
    loadTrendData(selectedYear);
});
document.getElementById("year").addEventListener("change", loadTrendData);
</script>
@endsection
