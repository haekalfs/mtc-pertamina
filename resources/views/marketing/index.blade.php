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
        <div class="col-lg-8">
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
        <div class="col-md-4">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-sitemap"></i> Company Agreement</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Add Utilities</a> --}}
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($getAgreements as $agreement)
                        <div class="col-md-12">
                            <div class="container-video">
                                <div class="file-man-box">
                                    <div class="file-img-box">
                                        <img src="{{ asset($agreement->img_filepath ? $agreement->img_filepath : 'https://via.placeholder.com/250x150/5fa9f8/ffffff') }}" alt="icon">
                                    </div>
                                    <a href="{{ route('preview-company', $agreement->id) }}" target="_blank" class="file-download">
                                        <small><i class="fa fa-external-link"></i></small>
                                    </a>
                                    <div class="file-man-title">
                                        <h5 class="mb-0 text-overflow">{{ $agreement->company_name }}</h5>
                                        <p class="mb-0"><small>Status : @if($agreement->spk_filepath) SPK @elseif($agreement->non_spk) NON-SPK @else No File or Agreement Exist! @endif </small> </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div class="col-md-12 text-right">
                            <a href="{{ route('company-agreement') }}">
                                <small>Show More...</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
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
<style>

.file-man-box {
    padding: 20px;
    border: 1px solid #e3eaef;
    border-radius: 5px;
    position: relative;
    margin-bottom: 20px
}

.file-man-box .file-close {
    color: #f1556c;
    position: absolute;
    line-height: 24px;
    font-size: 24px;
    right: 10px;
    top: 10px;
    visibility: hidden
}

.file-man-box .file-img-box {
    line-height: 120px;
    text-align: center
}

.file-man-box .file-img-box img {
    height: 64px
}

.file-man-box .file-download {
    font-size: 32px;
    color: #98a6ad;
    position: absolute;
    right: 10px
}

.file-man-box .file-download:hover {
    color: #313a46
}

.file-man-box .file-man-title {
    padding-right: 25px
}

.file-man-box:hover {
    -webkit-box-shadow: 0 0 24px 0 rgba(0, 0, 0, .06), 0 1px 0 0 rgba(0, 0, 0, .02);
    box-shadow: 0 0 24px 0 rgba(0, 0, 0, .06), 0 1px 0 0 rgba(0, 0, 0, .02)
}

.file-man-box:hover .file-close {
    visibility: visible
}
.text-overflow {
    text-overflow: ellipsis;
    white-space: nowrap;
    display: block;
    width: 100%;
    overflow: hidden;
}
</style>
@endsection
