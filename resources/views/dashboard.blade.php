@extends('layouts.main')

@section('active-dashboard')
active font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-4">
    <h1 class="h4 mb-0 font-weight-bold text-gray-800 text-secondary"><i class="far fa-smile-beam"></i> Welcome onboard, {{ Auth::user()->name }}!</h1>
    {{-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-smile-beam fa-sm text-white-50"></i> Show Details</a> --}}
</div>
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
<div class="animated fadeIn">
    <!-- Widgets  -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="stat-widget-five">
                        <div class="stat-icon dib flat-color-1">
                            <i class="ti-cup"></i>
                        </div>
                        <div class="stat-content">
                            <div class="text-left dib">
                                <div class="stat-text"><span class="count">23569</span></div>
                                <div class="stat-heading">Realisasi Peserta</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="stat-widget-five">
                        <div class="stat-icon dib flat-color-2">
                            <i class="ti-camera"></i>
                        </div>
                        <div class="stat-content">
                            <div class="text-left dib">
                                <div class="stat-text"><span class="count">3435</span></div>
                                <div class="stat-heading">Jangkauan Media</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="stat-widget-five">
                        <div class="stat-icon dib flat-color-3">
                            <i class="pe-7s-browser"></i>
                        </div>
                        <div class="stat-content">
                            <div class="text-left dib">
                                <div class="stat-text"><span class="count">349</span></div>
                                <div class="stat-heading">Rekap Feedback</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="stat-widget-five">
                        <div class="stat-icon dib flat-color-4">
                            <i class="pe-7s-cash"></i>
                        </div>
                        <div class="stat-content">
                            <div class="text-left dib">
                                <div class="stat-text"><span class="count">2986</span></div>
                                <div class="stat-heading">Profit Margin</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Widgets -->
    <!--  Traffic  -->
    <div class="row">
        <div class="col-xl-12 col-md-12 zoom90 mb-3">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold">News & Event Information</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
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
                            <!-- <canvas id="TrafficChart"></canvas>   -->
                            <div id="chartContainer3" style="height: 300px; width: 100%;"></div>
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
                            <div id="chartContainer" style="height: 300px; width: 100%;"></div>
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
                            <div id="chartContainer2" style="height: 300px; width: 100%;"></div>
                        </div>
                    </div>
                </div> <!-- /.row -->
            </div>
        </div><!-- /# column -->
    </div>
    <!--  /Traffic -->
    <div class="clearfix"></div>
    <!-- Modal - Calendar - Add Category -->
    <div class="modal fade none-border" id="add-category">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><strong>Add a category </strong></h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label">Category Name</label>
                                <input class="form-control form-white" placeholder="Enter name" type="text" name="category-name"/>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Choose Category Color</label>
                                <select class="form-control form-white" data-placeholder="Choose a color..." name="category-color">
                                    <option value="success">Success</option>
                                    <option value="danger">Danger</option>
                                    <option value="info">Info</option>
                                    <option value="pink">Pink</option>
                                    <option value="primary">Primary</option>
                                    <option value="warning">Warning</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger waves-effect waves-light save-category" data-dismiss="modal">Save</button>
                </div>
            </div>
        </div>
    </div>
<!-- /#add-category -->
</div>
<script>
window.onload = function () {


    var chart = new CanvasJS.Chart("chartContainer3", {
        animationEnabled: true,
        zoomEnabled: true,
        title:{
            text: "KPI Statistics"
        },
        data: data  // random generator below
    });
    chart.render();

    var chart = new CanvasJS.Chart("chartContainer2", {
        animationEnabled: true,
        title:{
            text: "Average Score"
        },
        axisX:{
            valueFormatString: "DD MMM",
            crosshair: {
                enabled: true,
                snapToDataPoint: true
            }
        },
        axisY: {
            title: "Closing Price (in USD)",
            valueFormatString: "$##0.00",
            crosshair: {
                enabled: true,
                snapToDataPoint: true,
                labelFormatter: function(e) {
                    return "$" + CanvasJS.formatNumber(e.value, "##0.00");
                }
            }
        },
        data: [{
            type: "area",
            xValueFormatString: "DD MMM",
            yValueFormatString: "$##0.00",
            dataPoints: [
                { x: new Date(2016, 07, 01), y: 76.727997 },
                { x: new Date(2016, 07, 02), y: 75.459999 },
                { x: new Date(2016, 07, 03), y: 76.011002 },
                { x: new Date(2016, 07, 04), y: 75.751999 },
                { x: new Date(2016, 07, 05), y: 77.500000 },
                { x: new Date(2016, 07, 08), y: 77.436996 },
                { x: new Date(2016, 07, 09), y: 79.650002 },
                { x: new Date(2016, 07, 10), y: 79.750999 },
                { x: new Date(2016, 07, 11), y: 80.169998 },
                { x: new Date(2016, 07, 12), y: 79.570000 },
                { x: new Date(2016, 07, 15), y: 80.699997 },
                { x: new Date(2016, 07, 16), y: 79.686996 },
                { x: new Date(2016, 07, 17), y: 78.996002 },
                { x: new Date(2016, 07, 18), y: 78.899002 },
                { x: new Date(2016, 07, 19), y: 77.127998 },
                { x: new Date(2016, 07, 22), y: 76.759003 },
                { x: new Date(2016, 07, 23), y: 77.480003 },
                { x: new Date(2016, 07, 24), y: 77.623001 },
                { x: new Date(2016, 07, 25), y: 76.408997 },
                { x: new Date(2016, 07, 26), y: 76.041000 },
                { x: new Date(2016, 07, 29), y: 76.778999 },
                { x: new Date(2016, 07, 30), y: 78.654999 },
                { x: new Date(2016, 07, 31), y: 77.667000 }
            ]
        }]
    });
    chart.render();


var dps = []; // dataPoints
var chart = new CanvasJS.Chart("chartContainer", {
    title :{
        text: "Dynamic Data"
    },
    data: [{
        type: "line",
        dataPoints: dps
    }]
});

var xVal = 0;
var yVal = 100;
var updateInterval = 1000;
var dataLength = 20; // number of dataPoints visible at any point

var updateChart = function (count) {

    count = count || 1;

    for (var j = 0; j < count; j++) {
        yVal = yVal +  Math.round(5 + Math.random() *(-5-5));
        dps.push({
            x: xVal,
            y: yVal
        });
        xVal++;
    }

    if (dps.length > dataLength) {
        dps.shift();
    }

    chart.render();
};

updateChart(dataLength);
setInterval(function(){updateChart()}, updateInterval);

}

var limit = 1000;

var y = 0;
var data = [];
var dataSeries = { type: "line" };
var dataPoints = [];
for (var i = 0; i < limit; i += 1) {
    y += (Math.random() * 10 - 5);
    dataPoints.push({
        x: i - limit / 2,
        y: y
    });
}
dataSeries.dataPoints = dataPoints;
data.push(dataSeries);
</script>
@endsection
