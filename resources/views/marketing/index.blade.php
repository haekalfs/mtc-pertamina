@extends('layouts.main')

@section('active-marketing')
active font-weight-bold
@endsection

@section('show-marketing')
show
@endsection

@section('marketing')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="menu-icon fa fa-suitcase"></i> Dashboard Marketing</h1>
        <p class="mb-4">Dashboard Marketing.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
    </div>
</div>
<div class="animated fadeIn">
    <div class="row">
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 animateBox">
            <a href="{{ route('marketing-campaign') }}" class="clickable-card">
                <div class="card border-left-primary shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">
                                    Marketing Campaign</div>
                                <div class="h6 mb-0 text-gray-800">{{ $countCampaign }} Events</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-bullhorn fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 animateBox">
            <a href="" class="clickable-card">
                <div class="card border-left-success shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">
                                    Enggagement Social Media</div>
                            </div>
                            <div class="col-auto">
                                <i class="ti-instagram fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 animateBox">
            <a href="{{ route('company-agreement') }}" class="clickable-card">
                <div class="card border-left-info shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">
                                    Company Agreement</div>
                                <div class="h6 mb-0 text-gray-800">{{ $countAgreement }} Documents</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-sitemap fa-2x text-info"></i>
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
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> Company Agreement</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Add Utilities</a> --}}
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-borderless zoom90">
                        <thead>
                            <tr class="text-center" style="display: none;">
                                <th>Instructors</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($getAgreements as $agreement)
                            <tr>
                                <td data-th="Product">
                                    <div class="row">
                                        <div class="col-md-4 text-left">
                                            <img src="{{ asset($agreement->img_filepath ? $agreement->img_filepath : 'https://via.placeholder.com/250x150/5fa9f8/ffffff') }}" style="height: 100px; width: 150px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow ">
                                        </div>
                                        <div class="col-md-8 text-left mt-sm-2">
                                            <h5 class="card-title font-weight-bold mb-1">{{ $agreement->company_name }}</h5>
                                            <div class="ml-2">
                                                <table class="table table-borderless table-sm mb-0">
                                                    <tr>
                                                        <td style="width: 150px;"><i class="ti-minus mr-2"></i> Document SPK</td>
                                                        <td style="text-align: start;">: &nbsp;<a href="{{ asset($agreement->spk_filepath) }}" target="_blank" class="text-secondary"><u>View</u> <i class="fa fa-external-link fa-sm"></i></a></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div><script>
    window.onload = function () {
        var chartData = @json($campaignChart);

        // Prepare data points for the chart
        var dataPoints = chartData.map(function(row) {
            return {
                x: new Date(row.date), // Parse the date string into a Date object
                y: row.count
            };
        });

        var chart = new CanvasJS.Chart("chartContainer", {
            animationEnabled: true,
            title:{
                text: "Marketing Campaign",
                margin: 30,
            },
            axisX:{
                valueFormatString: "DD MMM",
                crosshair: {
                    enabled: true,
                    snapToDataPoint: true
                }
            },
            axisY: {
                valueFormatString: "##0",
                crosshair: {
                    enabled: true,
                    snapToDataPoint: true
                }
            },
            data: [{
                type: "area",
                xValueFormatString: "DD MMM",
                yValueFormatString: "##0",
                toolTipContent: "{x}: {y} events", // Add suffix here
                dataPoints: dataPoints
            }]
        });

        chart.render();
    }
</script>

<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
@endsection
