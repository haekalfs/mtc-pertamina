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
                                <div class="stat-heading mb-1">
                                    Infografis Peserta</div>
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
                                <div class="stat-heading mb-1">
                                    Inventaris Alat</div>
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
                                <div class="stat-heading mb-1">
                                    Kebutuhan Alat</div>
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
                            <div id="chartContainer" style="height: 370px; width: 100%;"></div>
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
<?php

 $dataPoints1 = array(
	array("x" => 1451586600000, "y" => 96.709),
	array("x" => 1454265000000, "y" => 94.918),
	array("x" => 1456770600000, "y" => 95.152),
	array("x" => 1459449000000, "y" => 97.070),
	array("x" => 1462041000000, "y" => 97.305),
	array("x" => 1464719400000, "y" => 89.854),
	array("x" => 1467311400000, "y" => 88.158),
	array("x" => 1469989800000, "y" => 87.989),
	array("x" => 1472668200000, "y" => 86.366),
	array("x" => 1475260200000, "y" => 81.650),
	array("x" => 1477938600000, "y" => 85.789),
	array("x" => 1480530600000, "y" => 83.846),
	array("x" => 1483209000000, "y" => 84.927),
	array("x" => 1485887400000, "y" => 82.609),
	array("x" => 1488306600000, "y" => 81.428),
	array("x" => 1490985000000, "y" => 83.259),
	array("x" => 1493577000000, "y" => 83.153),
	array("x" => 1496255400000, "y" => 84.180),
	array("x" => 1498847400000, "y" => 84.840),
	array("x" => 1501525800000, "y" => 82.671),
	array("x" => 1504204200000, "y" => 87.496),
	array("x" => 1506796200000, "y" => 86.007),
	array("x" => 1509474600000, "y" => 87.233),
	array("x" => 1512066600000, "y"=> 86.276)
 );

 $dataPoints2 = array(
	array("x"=> 1451586600000, "y" => 73.5555),
	array("x"=> 1454265000000, "y" => 74.1625),
	array("x"=> 1456770600000, "y" => 75.3980),
	array("x"=> 1459449000000, "y" => 76.0965),
	array("x"=> 1462041000000, "y" => 74.8165),
	array("x"=> 1464719400000, "y" => 74.9660),
	array("x"=> 1467311400000, "y" => 74.4805),
	array("x"=> 1469989800000, "y" => 74.7355),
	array("x"=> 1472668200000, "y" => 74.8155),
	array("x"=> 1475260200000, "y" => 73.2275),
	array("x"=> 1477938600000, "y" => 72.6315),
	array("x"=> 1480530600000, "y" => 71.4610),
	array("x"=> 1483209000000, "y" => 72.9025),
	array("x"=> 1485887400000, "y" => 70.5750),
	array("x"=> 1488306600000, "y" => 69.0955),
	array("x"=> 1490985000000, "y" => 70.0565),
	array("x"=> 1493577000000, "y" => 72.5320),
	array("x"=> 1496255400000, "y" => 73.8350),
	array("x"=> 1498847400000, "y" => 76.0255),
	array("x"=> 1501525800000, "y" => 76.1465),
	array("x"=> 1504204200000, "y" => 77.1570),
	array("x"=> 1506796200000, "y" => 75.4075),
	array("x"=> 1509474600000, "y" => 76.7690),
	array("x"=> 1512066600000, "y" => 76.5950)
 );

 $dataPointsPie = array(
	array("label"=>"Oxygen", "symbol" => "O","y"=>46.6),
	array("label"=>"Silicon", "symbol" => "Si","y"=>27.7),
	array("label"=>"Aluminium", "symbol" => "Al","y"=>13.9),
	array("label"=>"Iron", "symbol" => "Fe","y"=>5),
	array("label"=>"Calcium", "symbol" => "Ca","y"=>3.6),
	array("label"=>"Sodium", "symbol" => "Na","y"=>2.6),
	array("label"=>"Magnesium", "symbol" => "Mg","y"=>2.1),
	array("label"=>"Others", "symbol" => "Others","y"=>1.5),

)

?>
<script>
    window.onload = function () {

    var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        title:{
            text: "Contoh Grafik"
        },
        subtitles: [{
            fontSize: 18
        }],
        axisY: {
            prefix: "₹"
        },
        legend:{
            cursor: "pointer",
            itemclick: toggleDataSeries
        },
        toolTip: {
            shared: true
        },
        data: [
        {
            type: "area",
            name: "GBP",
            showInLegend: "true",
            xValueType: "dateTime",
            xValueFormatString: "MMM YYYY",
            yValueFormatString: "₹#,##0.##",
            dataPoints: <?php echo json_encode($dataPoints1); ?>
        },
        {
            type: "area",
            name: "EUR",
            showInLegend: "true",
            xValueType: "dateTime",
            xValueFormatString: "MMM YYYY",
            yValueFormatString: "₹#,##0.##",
            dataPoints: <?php echo json_encode($dataPoints2); ?>
        }
        ]
    });

    chart.render();

    function toggleDataSeries(e){
        if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
            e.dataSeries.visible = false;
        }
        else{
            e.dataSeries.visible = true;
        }
        chart.render();
    }

    var chart = new CanvasJS.Chart("chartContainerPie", {
        theme: "light2",
        animationEnabled: true,
        title: {
            text: "Contoh Grafik"
        },
        data: [{
            type: "doughnut",
            indexLabel: "{symbol} - {y}",
            yValueFormatString: "#,##0.0\"%\"",
            showInLegend: true,
            legendText: "{label} : {y}",
            dataPoints: <?php echo json_encode($dataPointsPie, JSON_NUMERIC_CHECK); ?>
        }]
    });
    chart.render();

}
</script>

<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
@endsection
