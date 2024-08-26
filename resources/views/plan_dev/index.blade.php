@extends('layouts.main')

@section('active-pd')
active font-weight-bold
@endsection

@section('show-pd')
show
@endsection

@section('plan-dev')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-cogs"></i> Dashboard Planning & Development</h1>
        <p class="mb-4">Dashboard Planning & Development.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
    </div>
</div>
<div class="animated fadeIn">
    <div class="row">
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 animateBox">
            <a href="{{ route('feedback-report-main') }}" class="clickable-card">
                <div class="card border-left-primary shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">Rating Feedback MTC</div>
                                <div class="h6 mb-0 text-gray-800"><i class="fa fa-star text-warning"></i> {{ round($averageFeedbackScore, 2) ?? '-' }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-trophy fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 animateBox">
            <a href="{{ route('instructor') }}" class="clickable-card">
                <div class="card border-left-success shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">
                                    Instruktur</div>
                                <div class="h6 mb-0 text-gray-800">{{ $instructorCount }} Orang</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-male fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 animateBox">
            <a href="{{ route('training-reference') }}" class="clickable-card">
                <div class="card border-left-info shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">
                                    Referensi Materi</div>
                                <div class="h6 mb-0 text-gray-800">{{ $referencesCount }} Materi</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-tag fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-star text-warning"></i> Top Rated Instructors</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Add Utilities</a> --}}
                    </div>
                </div>
                <div class="card-body zoom90">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th>Instructor</th>
                                <th>Email</th>
                                <th class="text-center">Age</th>
                                <th class="text-center">Feedback Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($instructors as $instructor)
                            <tr class="text-secondary">
                                <td>
                                    <a href="{{ route('preview-instructor', ['id' => $instructor->id, 'penlatId' => '-1']) }}">
                                        <div class="d-flex align-items-center animateBox">
                                            <img src="{{ asset($instructor->imgFilepath) }}" alt="" class="rounded-circle mr-2 shadow" style="width:50px; height:50px;">
                                            <span style=" padding-left: 10px;">{{ $instructor->instructor_name }}</span>
                                        </div>
                                    </a>
                                </td>
                                <td>{{ $instructor->instructor_email }}</td>
                                <td class="text-center text-secondary">{{ \Carbon\Carbon::parse($instructor->instructor_dob)->age }} Years</td>
                                <td class="text-center text-secondary">
                                    @php
                                        $roundedScore = round($instructor->average_feedback_score, 1);
                                        $wholeStars = floor($roundedScore);
                                        $halfStar = ($roundedScore - $wholeStars) >= 0.5;
                                    @endphp

                                    @for ($i = 0; $i < 5; $i++)
                                        @if ($i < $wholeStars)
                                            <i class="fa fa-star text-warning"></i>
                                        @elseif ($halfStar && $i == $wholeStars)
                                            <i class="fa fa-star-half-o text-warning"></i>
                                        @else
                                            <i class="fa fa-star-o"></i>
                                        @endif
                                    @endfor
                                    <span class="ml-2">{{ $roundedScore }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
        </div>
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
}
</script>
@endsection
