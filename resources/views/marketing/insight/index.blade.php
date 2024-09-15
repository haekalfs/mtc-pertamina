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

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6" style="position: relative;">
                    <a href="#" data-id="" class="position-absolute edit-campaign" style="right: 15px; z-index: 1;">
                        <i class="fa fa-edit fa-1x ml-2" style="color: rgb(181, 181, 181);"></i>
                    </a>
                    <div class="row">
                        <div class="col-md-2 d-flex align-items-center justify-content-center" style="padding-left: 1em;">
                            <i class="fa fa-instagram fa-3x"></i>
                        </div>
                        <div class="col-md-10">
                            <div style="margin: 0;">Account : -</div>
                            <div style="margin: 0;">Page ID : -</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" style="position: relative;">
                    <a href="#" data-id="{{ $socialMedia->id }}" class="position-absolute edit-facebook" style="right: 15px; z-index: 1;">
                        <i class="fa fa-edit fa-1x ml-2" style="color: rgb(181, 181, 181);"></i>
                    </a>
                    <div class="row">
                        <div class="col-md-2 d-flex align-items-center justify-content-center" style="padding-left: 1em;">
                            <i class="fa fa-facebook fa-3x"></i>
                        </div>
                        <div class="col-md-10">
                            <div style="margin: 0;">Account : {{ $socialMedia->account_name }}</div>
                            <div style="margin: 0;">Page ID : {{ $socialMedia->page_id }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
<!-- Edit Facebook Token Modal -->
<div class="modal fade" id="editFacebookModal" tabindex="-1" role="dialog" aria-labelledby="editFacebookModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header d-flex flex-row align-items-center justify-content-between">
          <h5 class="modal-title" id="editFacebookModalLabel">Edit Facebook Token</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="editFacebookTokenForm">
          <div class="modal-body">
            <div class="form-group">
              <label for="token">Long Lived Facebook Token</label>
              <input type="text" class="form-control" id="token" name="token" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
          </div>
        </form>
      </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Open modal on edit button click
        $('.edit-facebook').on('click', function(e) {
            e.preventDefault();
            var socialId = $(this).data('id');
            $('#editFacebookModal').modal('show');

            // Handle form submission
            $('#editFacebookTokenForm').on('submit', function(e) {
                e.preventDefault();
                var token = $('#token').val();

                $.ajax({
                    url: '/social-media/update-facebook-token/' + socialId,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        token: token
                    },
                    success: function(response) {
                        // Close the modal after successful update
                        $('#editFacebookModal').modal('hide');
                        alert('Facebook token updated successfully');
                        location.reload();  // Reload the page to reflect the changes
                    },
                    error: function(xhr) {
                        alert('Error updating token: ' + xhr.responseText);
                    }
                });
            });
        });
    });
</script>
<script>
    window.onload = function() {
        // Check if daily insights exist
        @if(isset($insights['day']))
        var dailyDataPoints = [
            @foreach(collect($insights['day']['values'])->sortBy(function($value) {
                return \Carbon\Carbon::parse($value['end_time'])->timestamp;
            }) as $value)
                @if(isset($value['value']))
                    { label: "{{ \Carbon\Carbon::parse($value['end_time'])->format('Y-m-d') }}", y: {{ $value['value'] }} },
                @endif
            @endforeach
        ];

        var dailyChart = new CanvasJS.Chart("dailyChart", {
            title: { text: "Daily Total Facebook Visitors", margin: 50 },
            axisX:{
                crosshair: {
                    enabled: true,
                    snapToDataPoint: true
                }
            },
            axisY:{
                title: "in Metric Tons",
                includeZero: true,
                crosshair: {
                    enabled: true,
                    snapToDataPoint: true
                }
            },
            theme: "light2",
            animationEnabled: true,
            zoomEnabled: true,
            data: [{
                type: "splineArea",
                dataPoints: dailyDataPoints
            }]
        });
        dailyChart.render();
        @endif

        // Check if weekly insights exist
        @if(isset($insights['week']))
        var weeklyDataPoints = [
            @foreach(collect($insights['week']['values'])->sortBy(function($value) {
                return \Carbon\Carbon::parse($value['end_time'])->timestamp;
            }) as $value)
                @if(isset($value['value']))
                    { label: "{{ \Carbon\Carbon::parse($value['end_time'])->format('Y-m-d') }}", y: {{ $value['value'] }} },
                @endif
            @endforeach
        ];

        var weeklyChart = new CanvasJS.Chart("weeklyChart", {
            title: { text: "Weekly Total Facebook Visitors", margin: 50 },
            axisX:{
                crosshair: {
                    enabled: true,
                    snapToDataPoint: true
                }
            },
            axisY:{
                title: "in Metric Tons",
                includeZero: true,
                crosshair: {
                    enabled: true,
                    snapToDataPoint: true
                }
            },
            theme: "light2",
            animationEnabled: true,
            zoomEnabled: true,
            data: [{
                type: "splineArea",
                dataPoints: weeklyDataPoints
            }]
        });
        weeklyChart.render();
        @endif

        // Check if 28 days insights exist
        @if(isset($insights['days_28']))
        var monthlyDataPoints = [
            @foreach(collect($insights['days_28']['values'])->sortBy(function($value) {
                return \Carbon\Carbon::parse($value['end_time'])->timestamp;
            }) as $value)
                @if(isset($value['value']))
                    { label: "{{ \Carbon\Carbon::parse($value['end_time'])->format('Y-m-d') }}", y: {{ $value['value'] }} },
                @endif
            @endforeach
        ];

        var monthlyChart = new CanvasJS.Chart("monthlyChart", {
            title: { text: "28 Days Total Facebook Visitors", margin: 50 },
            axisX:{
                crosshair: {
                    enabled: true,
                    snapToDataPoint: true
                }
            },
            axisY:{
                title: "in Metric Tons",
                includeZero: true,
                crosshair: {
                    enabled: true,
                    snapToDataPoint: true
                }
            },
            theme: "light2",
            animationEnabled: true,
            zoomEnabled: true,
            data: [{
                type: "splineArea",
                dataPoints: monthlyDataPoints
            }]
        });
        monthlyChart.render();
        @endif
    };
</script>

@endsection
