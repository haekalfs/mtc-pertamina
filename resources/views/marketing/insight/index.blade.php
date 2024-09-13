@extends('layouts.main')

@section('active-marketing')
active font-weight-bold
@endsection

@section('show-marketing')
show
@endsection

@section('socmed')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-instagram"></i> Social Media Insights</h1>
        <p class="mb-4">Monitoring Insights Social Media MTC.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
    </div>
</div>
<div class="animated fadeIn">
    <!-- Widgets  -->
    <div class="row">
        <div class="col-lg-3 col-md-6 animateBox">
            <a href="#" class="clickable-card">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-widget-five">
                            <div class="stat-icon dib flat-color-1">
                                <i class="fa fa-camera"></i>
                            </div>
                            <div class="stat-content">
                                <div class="text-left dib">
                                    <div class="stat-text"><span class="count">{{ $getFacebookInsights->posts_count }}</span></div>
                                    <div class="stat-heading">Facebook Page Post</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 animateBox">
            <a href="#" class="clickable-card">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-widget-five">
                            <div class="stat-icon dib flat-color-2">
                                <i class="fa fa-thumbs-up"></i>
                            </div>
                            <div class="stat-content">
                                <div class="text-left dib">
                                    <div class="stat-text"><span class="count">{{ $getFacebookInsights->likes_count }}</span></div>
                                    <div class="stat-heading">Facebook Total Likes</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 animateBox">
            <a href="#" class="clickable-card">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-widget-five">
                            <div class="stat-icon dib flat-color-3">
                                <i class="fa fa-eye"></i>
                            </div>
                            <div class="stat-content">
                                <div class="text-left dib">
                                    <div class="stat-text"><span class="count">{{ $getFacebookInsights->visitors_count }}</span></div>
                                    <div class="stat-heading">Facebook Visitor</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 animateBox">
            <a href="#" class="clickable-card">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-widget-five">
                            <div class="stat-icon dib flat-color-4">
                                <i class="fa fa-comments"></i>
                            </div>
                            <div class="stat-content">
                                <div class="text-left dib">
                                    <div class="h6"><span>{{ $getFacebookInsights->comments_count }}</span></div>
                                    <div class="stat-heading">Facebook Post Comments</div>
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
        <div class="col-lg-12">
            <div class="card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <div id="dailyChart" style="height: 370px; width: 100%;"></div>
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
                            <div id="weeklyChart" style="height: 370px; width: 100%;"></div>
                        </div>
                    </div>
                </div> <!-- /.row -->
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <div id="monthlyChart" style="height: 370px; width: 100%;"></div>
                        </div>
                    </div>
                </div> <!-- /.row -->
            </div>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        // Check if daily insights exist
        @if(isset($insights['day']))
        var dailyChart = new CanvasJS.Chart("dailyChart", {
            title: { text: "Daily Total Facebook Visitors", margin: 50 },
            theme: "light2",
            data: [{
                type: "splineArea",
                dataPoints: [
                    @foreach($insights['day']['values'] as $value)
                        @if(isset($value['value']))
                            { label: "{{ \Carbon\Carbon::parse($value['end_time'])->format('Y-m-d') }}", y: {{ $value['value'] }} },
                        @endif
                    @endforeach
                ]
            }]
        });
        dailyChart.render();
        @endif

        // Check if weekly insights exist
        @if(isset($insights['week']))
        var weeklyChart = new CanvasJS.Chart("weeklyChart", {
            title: { text: "Weekly Total Facebook Visitors", margin: 50 },
            theme: "light2",
            data: [{
                type: "splineArea",
                dataPoints: [
                    @foreach($insights['week']['values'] as $value)
                        @if(isset($value['value']))
                            { label: "{{ \Carbon\Carbon::parse($value['end_time'])->format('Y-m-d') }}", y: {{ $value['value'] }} },
                        @endif
                    @endforeach
                ]
            }]
        });
        weeklyChart.render();
        @endif

        // Check if 28 days insights exist
        @if(isset($insights['days_28']))
        var monthlyChart = new CanvasJS.Chart("monthlyChart", {
            title: { text: "28 Days Total Facebook Visitors", margin: 50 },
            theme: "light2",
            data: [{
                type: "splineArea",
                dataPoints: [
                    @foreach($insights['days_28']['values'] as $value)
                        @if(isset($value['value']))
                            { label: "{{ \Carbon\Carbon::parse($value['end_time'])->format('Y-m-d') }}", y: {{ $value['value'] }} },
                        @endif
                    @endforeach
                ]
            }]
        });
        monthlyChart.render();
        @endif
    };
</script>

@endsection
