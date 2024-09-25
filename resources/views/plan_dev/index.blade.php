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
        <select class="form-control" id="yearSelected" name="yearSelected" required onchange="redirectToPage()" style="width: 100px;">
            @foreach (array_reverse($yearsBefore) as $year)
                <option value="{{ $year }}" {{ $year == $yearSelected ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
        </select>
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
                                <div class="h6 mb-0 text-gray-800">{{ $instructorCount }} Instruktur</div>
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
            <a href="{{ route('certificate') }}" class="clickable-card">
                <div class="card border-left-info shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">Issued Certificate</div>
                                <div class="h6 mb-0 text-gray-800">{{ $countIncompleteCert ? $countIncompleteCert . ' Certificates Pending' : $countCert . ' Total Issued' }} </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-trophy fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">

        <div class="col-lg-8 mb-2">
            <div class="card-leaderboard">
                <div class="card-header-leaderboard bg-white text-secondary" style="border-bottom: 1px solid rgb(205, 205, 205);">
                    <i class="fa fa-trophy"></i>
                    <span class="title">Top Rated Instructors</span>
                    <span class="subtitle">LEADERBOARD</span>
                </div>
                <div class="card-body-leaderboard bg-white">
                    <table id="leaderboard" class="table table-borderless">
                        <tbody>
                            @if(!$instructors || $instructors->isEmpty())
                                No Data Available
                            @else
                                @php $no = 1; @endphp
                                @foreach($instructors as $instructor)
                                <tr class="text-secondary">
                                    <td>
                                        <a href="{{ route('preview-instructor', ['id' => $instructor->id, 'penlatId' => '-1']) }}">
                                            <div class="d-flex align-items-center">
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
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-2">
            <div class="card">
                <div class="col-md-12 sidebar-two">
                    <h2>Regulations</h2>
                    <ul class="">
                        @foreach($regulations as $regulation)
                        <li>
                            <a href="{{ route('preview-regulation', $regulation->id) }}">
                                <div>
                                    <h3><i class="fa fa-info-circle mr-2"></i> {{ $regulation->description }}</h3>
                                    @php
                                        $created_at = \Carbon\Carbon::parse($regulation->created_at);
                                        $now = \Carbon\Carbon::now();
                                        $diffInDays = $created_at->diffInDays($now);
                                    @endphp
                                    <span>
                                        @if($diffInDays < 7)
                                            {{ $created_at->diffForHumans() }}
                                        @else
                                            a long time ago
                                        @endif
                                    </span>
                                </div>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <!-- Fix "Show More" to the right corner -->
                <div class="text-right">
                    <a class="btn btn-sm btn-default" href="{{ route('regulation') }}">
                        <small>Show More...</small>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <h4 class="pt-3 pb-0 pl-3">Rating Feedback Pelatihan (All Parameter)</h4>
                <hr>
                <div class="row mt-1">
                    @foreach($feedbackScoresPerYear as $yearMtc => $averageScoreMtc)
                        <div class="col-lg-4">
                            <div class="d-flex flex-column justify-content-center align-items-center">
                                <span>{{ $yearMtc }}</span> <!-- Year above the progress bar -->
                                <a class="progress-circle-wrapper animateBox">
                                    <div class="progress-circle p{{ round($averageScoreMtc * 20, 0) }} @if(round($averageScoreMtc * 20, 2) >= 50) over50 @endif">
                                        <span><i class="fa fa-star text-warning"></i> {{ round($averageScoreMtc, 2) ?? '-' }}</span>
                                        <div class="left-half-clipper">
                                            <div class="first50-bar"></div>
                                            <div class="value-bar"></div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Professional description with padding -->
                <div style="padding: 10px;">
                    <small>
                        This chart displays the average feedback scores for each year, showcasing the participants' evaluation of various aspects of the training programs.
                        The scores are derived from 14 different parameters, such as relevance of the material, training benefits, and overall organization quality.
                        The visual elements compare the results from the current year, last year, and two years ago.
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <h4 class="pt-3 pb-0 pl-3">Saran & Kritik</h4>
                <hr>
                <div class="col-md-12">
                    @if(!$suggestions || $suggestions->isEmpty())
                        No Data Available
                    @else
                    <div id="carouselExampleIndicators2" class="carousel slide" data-ride="carousel">

                        <div class="carousel-inner">
                            @foreach($suggestions->chunk(1) as $index => $chunk)
                                <div class="carousel-item{{ $index === 0 ? ' active' : '' }}">
                                    @foreach($chunk as $hl)
                                        <small>{{ $hl->saran }}</small>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="col-12 text-right pt-4 pb-4 pr-0 pl-4">
                        <a class="btn btn-sm btn-primary mr-1" href="#carouselExampleIndicators2" role="button" data-slide="prev">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                        <a class="btn btn-primary btn-sm" href="#carouselExampleIndicators2" role="button" data-slide="next">
                            <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="row">
                    <div class="col-lg-12 d-flex justify-content-end align-items-end">
                        <!-- Dropdown for filtering -->
                        <select class="form-control mt-3 mr-4 zoom90" id="ratingPelatihan" name="ratingPelatihan" style="width: 200px;">
                            <option value="all">Show All</option> <!-- Show All option -->
                            @foreach($trainingTitles as $title)
                                <option value="{{ $title }}">{{ $title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <!-- Chart container -->
                            <div id="chartContainer" style="height: 400px; width: 100%;"></div>
                        </div>
                    </div>
                </div> <!-- /.row -->
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="row">
                    <div class="col-lg-12 d-flex justify-content-end align-items-end">
                        <select class="form-control mt-3 mr-4 zoom90" id="instructorSelected" name="instructorSelected" style="width: 200px;">
                            <option value="all" selected>Show All</option> <!-- Add Show All option -->
                            @foreach ($instructorsList as $instructor)
                                <option value="{{ $instructor->instructor_name }}">{{ $instructor->instructor_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <div id="feedbackChart" style="height: 410px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
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
    const instructorSelected = document.getElementById('instructorSelected');
    // Fetch and render chart data based on selected instructor
    function fetchAndRenderChart(instructorId) {
        const url = instructorId === 'all' ? '/feedback-chart-data/{{ $yearSelected }}' : `/feedback-chart-data/{{ $yearSelected }}?instructorId=${instructorId}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                const dataPoints = data.map(item => {
                    return { label: item.questioner, y: item.average_score };
                });

                var chart = new CanvasJS.Chart("feedbackChart", {
                    animationEnabled: true,
                    theme: "light2",
                    title: {
                        text: instructorId === 'all' ? "Rating Feedback terhadap Semua Instruktur" : "Rating Feedback " + instructorId, margin: 30
                    },
                    axisY: {
                        title: "Average Score",
                        minimum: 2,
                        maximum: 5,
                        stripLines: [{
                            value: 4.5,
                            label: "Target Minimum: 4.5",
                            color: "#FF0000",
                            thickness: 2,
                            labelPlacement: "outside",
                            labelAlign: "center",
                            zIndex: 10
                        }]
                    },
                    data: [{
                        type: "bar",
                        yValueFormatString: "#0.##",
                        dataPointWidth: 50,
                        dataPoints: dataPoints
                    }]
                });
                chart.render();
            });
    }

    // Initial fetch and render for "Show All" option
    fetchAndRenderChart('all');

    // Event listener to fetch and render data whenever the instructor is changed
    instructorSelected.addEventListener('change', function() {
        fetchAndRenderChart(this.value);
    });


    const ratingPelatihan = document.getElementById('ratingPelatihan');

    // Fetch and render chart data based on selected training title
    function fetchAndRenderMTCChart(trainingTitle) {
        const yearSelected = "{{ $yearSelected }}"; // Assuming $yearSelected is passed from the backend
        const url = trainingTitle === 'all'
            ? `/feedback-MTC-chart-data/${yearSelected}`
            : `/feedback-MTC-chart-data/${yearSelected}?ratingPelatihan=${encodeURIComponent(trainingTitle)}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                // Render chart directly here
                var chart = new CanvasJS.Chart("chartContainer", {
                    animationEnabled: true,
                    theme: "light2",
                    title: {
                        text: "Rating Feedback Pelatihan " + trainingTitle, margin: 20
                    },
                    axisY: {
                        title: "Average Score",
                        minimum: 2,
                        maximum: 5,
                        stripLines: [{
                            value: 4.5,
                            label: "Target Minimum: 4.5",
                            color: "#FF0000",
                            thickness: 2,
                            labelPlacement: "outside",
                            labelAlign: "center",
                            zIndex: 10
                        }]
                    },
                    data: [{
                        type: "column",
                        yValueFormatString: "#0.##",
                        dataPointWidth: 50, // Set the width of each bar (in pixels)
                        dataPoints: Object.keys(data).map(key => ({
                            label: key.replace('_', ' ').toUpperCase(), // Properly format the label
                            y: parseFloat(data[key]) || 0 // Convert the string values to floats and ensure 'y' is not null
                        }))
                    }]
                });
                chart.render();
            })
            .catch(error => {
                console.error('Error fetching chart data:', error);
            });
    }

    // Initial chart render: Show all feedback on page load
    fetchAndRenderMTCChart('all');

    // Function to update the chart when dropdown value changes
    ratingPelatihan.addEventListener('change', function() {
        fetchAndRenderMTCChart(this.value);
    });
}
function redirectToPage() {
    var selectedOption = document.getElementById("yearSelected").value;
    var url = "{{ url('/planning-development-dashboard') }}" + "/" + selectedOption;
    window.location.href = url; // Redirect to the desired page
}
</script>
@endsection
