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
                                <div class="stat-heading mb-1 font-weight-bold">Rating Feedback Pelatihan</div>
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

        <div class="col-lg-6 mb-2">
            <div class="card-leaderboard">
                <div class="card-header-leaderboard bg-secondary">
                    <i class="trophy-icon"></i>
                    <span class="title">Top Rated Instructors</span>
                    <span class="subtitle">LEADERBOARD</span>
                </div>
                <div class="card-body-leaderboard bg-white">
                    <table id="leaderboard" class="table table-borderless">
                        <tbody>@php $no = 1; @endphp
                            @foreach($instructors as $instructor)
                            <tr class="text-secondary">
                                <td>
                                    <a href="{{ route('preview-instructor', ['id' => $instructor->id, 'penlatId' => '-1']) }}">
                                        <div class="d-flex align-items-center animateBox">
                                            {{ $no++ }}. &nbsp;&nbsp;<img src="{{ asset($instructor->imgFilepath) }}" alt="" class="rounded mr-2 shadow" style="width:50px; height:60px; border: 0.5px solid rgb(211, 211, 211);">
                                            <span class="instructor-name zoom90 mt-2">{{ $instructor->instructor_name }}<br>{{ \Carbon\Carbon::parse($instructor->instructor_dob)->age }} Tahun</span>
                                        </div>
                                    </a>
                                </td>
                                <td class="text-center zoom90 pt-4">
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
                                            <i class="fa fa-star-o text-secondary"></i>
                                        @endif
                                    @endfor
                                    <span class="ml-2">{{ $roundedScore }}</span><br>
                                    <small class="ml-4">({{ $instructor->feedbacks_count / 5 }} feedbacks)</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-2">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">Newest Regulations</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            @foreach($regulations as $item)
                                <tr class="text-secondary">
                                    <td class="d-flex align-items-center animateBox">
                                        <a href="{{ route('preview-regulation', $item->id) }}">
                                            <div>
                                                <i class="fa fa-info-circle mr-2"></i> <span>{{ $item->description }}</span>
                                            </div>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                                <tr class="text-secondary">
                                    <td class="text-right">
                                        <a href="{{ route('regulation') }}">
                                            <small>Show More...</small>
                                        </a>
                                    </td>
                                </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
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
<style>
.card-leaderboard {
    width: 100%;
    margin-bottom: 20px;
    border-radius: 8px;
    box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    font-family: Arial, sans-serif;
}
/* Header Section */
.card-header-leaderboard {
    background-color: #4589b4;
    color: white;
    padding: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.card-header-leaderboard .trophy-icon {
    width: 24px;
    height: 24px;
    background: url('trophy-icon.png') no-repeat center center;
    background-size: contain;
    margin-right: 10px;
}

.card-header-leaderboard .title {
    font-size: 14px;
    font-weight: bold;
}

.card-header-leaderboard .subtitle {
    font-size: 10px;
    font-weight: normal;
}

/* Body Section */
.card-body-leaderboard {
    background-color: #f9f9f9;
    padding-top: 4px;
    padding-right: 16px;
    padding-bottom: 16px;
    padding-left: 16px;
}

.table-borderless {
    margin-bottom: 0;
}

.text-secondary {
    color: #6c757d;
}

.instructor-name {
    padding-left: 10px;
    margin-bottom: 20px;
}

.fa-star, .fa-star-half-o, .fa-star-o {
    color: #ffd700; /* Gold color for stars */
}

.ml-2 {
    margin-left: 8px;
}

/* Loader Animation */
.loader {
    border: 4px solid rgba(0, 0, 0, 0.1);
    border-left-color: #4978f4;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
window.onload = function () {
    var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        title: {
            text: "Rating Feedback Pelatihan : {{ round($averageFeedbackScore, 2) ?? '-' }} Avg "
        },
        axisY: {
            title: "Average Score",
            minimum: 2,
            maximum: 5,
            stripLines: [
                {
                    value: 4.5,
                    label: "Target Minimum: 4.5",
                    color: "#FF0000", // Color of the line
                    thickness: 2, // Thickness of the line
                    labelPlacement: "outside",
                    labelAlign: "center",
                    zIndex: 10 // Ensure line is in front of bars
                }
            ]
        },
        data: [{
            type: "column",
            yValueFormatString: "#0.##",
            dataPointWidth: 50, // Set the width of each bar (in pixels)
            dataPoints: [
                @foreach ($averages as $column => $average)
                    { label: "{{ str_replace('_', ' ', ucwords($column)) }}", y: {{ $average }} },
                @endforeach
            ]
        }]
    });
    chart.render();
}
</script>
@endsection
