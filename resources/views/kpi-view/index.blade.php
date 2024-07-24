@extends('layouts.main')

@section('active-kpi')
active
@endsection

@section('content')
<style>
    /* Form Labels */
    .modal-body label {
        font-weight: bold;
    }

    /* Form Inputs */
    .modal-body input[type="text"],
    .modal-body input[type="date"] {
        margin-bottom: 10px;
    }

    /* Radio Buttons */
    .radio-inline {
        margin-right: 50px;
    }
</style>
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-users"></i> Dashboard KPI</h1>
        <p class="mb-4">Managing Access based on roles.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        {{-- <a class="btn btn-secondary btn-sm shadow-sm mr-2" href="/invoicing/list"><i class="fas fa-solid fa-backward fa-sm text-white-50"></i> Go Back</a> --}}
    </div>
</div>
@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('failed'))
<div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif
<div class="animated fadeIn zoom90">
    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" id="judul">List Indicators</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-icon fa fa-plus"></i> Create KPI</a> --}}
                    </div>
                </div>
                <div class="card-body">
                    <div class="container">
                        <div class="row d-flex justify-content-start mb-3 zoom90">
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="position_id">Start Periode :</label>
                                            <input type="date" class="form-control" id="period_start" name="period_start">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status">End Periode :</label>
                                            <input type="date" class="form-control" id="period_start" name="period_start">
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-self-end justify-content-start">
                                        <div class="form-group">
                                            <div class="align-self-center">
                                                <input type="submit" class="btn btn-primary" value="Filter"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container text-right mb-4">
                        <small><i class="ti-fullscreen"></i>
                            <a href="#" onclick="toggleFullScreen('mainContainer')">&nbsp;<i>Fullscreen</i></a>
                        </small>
                    </div>
                    <div class="container" id="mainContainer">
                        <div class="row" id="chartsRow"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="chartModal" tabindex="-1" role="dialog" aria-labelledby="chartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content zoom90">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between border-bottom-1">
                <h5 class="modal-title" id="chartModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div id="chartModalBodyWeekly" class="mb-2">
                            <!-- Weekly Chart will be inserted here -->
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div id="chartModalBodyMonthly" class="mb-2">
                            <!-- Monthly Chart will be inserted here -->
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div id="chartModalBodyYearly" class="mb-2">
                            <!-- Yearly Chart will be inserted here -->
                        </div>
                    </div>
                </div>
                <table id="pencapaianModal" class="table table-striped table-bordered mt-4">
                    <thead>
                        <tr>
                            <th>Pencapaian</th>
                            <th>Target</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Test Score</td>
                            <td>75%</td>
                            <td class="text-center">
                                Tidak Tercapai
                            </td>
                        </tr>
                        <tr>
                            <td>Test Score</td>
                            <td>75%</td>
                            <td class="text-center">
                                Tidak Tercapai
                            </td>
                        </tr>
                        <tr>
                            <td>Test Score</td>
                            <td>75%</td>
                            <td class="text-center">
                                Tidak Tercapai
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const kpiData = [
            {
                title: 'Daily Sales',
                type: 'line',
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                data: [120, 150, 180, 100, 90, 130, 170],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)'
            },
            {
                title: 'Summary',
                type: 'bar',
                labels: ['Completed', 'Pending', 'Failed'],
                data: [75, 25, 34],
                backgroundColor: [
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(75, 192, 192, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 206, 86, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)'
                ]
            },
            {
                title: 'Total Orders',
                type: 'bar',
                labels: ['Today', 'This week', 'This month'],
                data: [356, 678, 1234],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)'
            },
            {
                title: 'Monthly Sales',
                type: 'line',
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                data: [300, 350, 400, 450, 500, 550, 600],
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)'
            },
            {
                title: 'Performance',
                type: 'bar',
                labels: ['Excellent', 'Good', 'Average', 'Poor'],
                data: [40, 35, 20, 5],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ]
            },
            {
                title: 'Market Value',
                type: 'radar',
                labels: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                data: [65, 59, 90, 81, 56, 55, 40],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)'
            }
        ];

        const chartsRow = document.getElementById('chartsRow');

        kpiData.forEach((kpi, index) => {
            // Create a new div for each KPI chart
            const colDiv = document.createElement('div');
            colDiv.className = 'col-lg-4 col-md-6 mb-2';

            const cardDiv = document.createElement('div');
            cardDiv.className = 'card';

            const cardBodyDiv = document.createElement('div');
            cardBodyDiv.className = 'card-body';

            const title = document.createElement('h5');
            title.className = 'card-title';
            title.innerText = kpi.title;

            const canvas = document.createElement('canvas');
            canvas.id = 'chart' + index;
            canvas.onclick = function() {
                showModal(kpi);
            };

            // Append elements
            cardBodyDiv.appendChild(title);
            cardBodyDiv.appendChild(canvas);
            cardDiv.appendChild(cardBodyDiv);
            colDiv.appendChild(cardDiv);
            chartsRow.appendChild(colDiv);

            // Create the chart
            new Chart(canvas.getContext('2d'), {
                type: kpi.type,
                data: {
                    labels: kpi.labels,
                    datasets: [{
                        label: kpi.title,
                        data: kpi.data,
                        backgroundColor: kpi.backgroundColor,
                        borderColor: kpi.borderColor,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutBounce'
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });

        window.showModal = showModal;
    });

    function showModal(kpi) {
        const modal = document.getElementById('chartModal');
        const modalTitle = document.getElementById('chartModalLabel');
        const modalBodyWeekly = document.getElementById('chartModalBodyWeekly');
        const modalBodyMonthly = document.getElementById('chartModalBodyMonthly');
        const modalBodyYearly = document.getElementById('chartModalBodyYearly');

        modalTitle.innerText = kpi.title;
        modalBodyWeekly.innerHTML = '';
        modalBodyMonthly.innerHTML = '';
        modalBodyYearly.innerHTML = '';

        const weeklyCanvas = document.createElement('canvas');
        const monthlyCanvas = document.createElement('canvas');
        const yearlyCanvas = document.createElement('canvas');
        weeklyCanvas.id = 'modalChartWeekly';
        monthlyCanvas.id = 'modalChartMonthly';
        yearlyCanvas.id = 'modalChartYearly';

        modalBodyWeekly.appendChild(weeklyCanvas);
        modalBodyMonthly.appendChild(monthlyCanvas);
        modalBodyYearly.appendChild(yearlyCanvas);

        const chartDataWeekly = kpi.data.map(d => d * Math.random());
        const chartDataMonthly = kpi.data.map(d => d * Math.random());
        const chartDataYearly = kpi.data.map(d => d * Math.random());

        new Chart(weeklyCanvas.getContext('2d'), {
            type: kpi.type,
            data: {
                labels: kpi.labels,
                datasets: [{
                    label: 'Weekly ' + kpi.title,
                    data: chartDataWeekly,
                    backgroundColor: kpi.backgroundColor,
                    borderColor: kpi.borderColor,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                animation: {
                    duration: 1000,
                    easing: 'easeInOutBounce'
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        new Chart(monthlyCanvas.getContext('2d'), {
            type: kpi.type,
            data: {
                labels: kpi.labels,
                datasets: [{
                    label: 'Monthly ' + kpi.title,
                    data: chartDataMonthly,
                    backgroundColor: kpi.backgroundColor,
                    borderColor: kpi.borderColor,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                animation: {
                    duration: 1000,
                    easing: 'easeInOutBounce'
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        new Chart(yearlyCanvas.getContext('2d'), {
            type: kpi.type,
            data: {
                labels: kpi.labels,
                datasets: [{
                    label: 'Yearly ' + kpi.title,
                    data: chartDataYearly,
                    backgroundColor: kpi.backgroundColor,
                    borderColor: kpi.borderColor,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                animation: {
                    duration: 1000,
                    easing: 'easeInOutBounce'
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        $('#chartModal').modal('show');
    }

    function toggleFullScreen(elementId) {
        var element = document.getElementById(elementId);

        if (!document.fullscreenElement) {
            // Request fullscreen
            if (element.requestFullscreen) {
                element.requestFullscreen();
            } else if (element.mozRequestFullScreen) { // Firefox
                element.mozRequestFullScreen();
            } else if (element.webkitRequestFullscreen) { // Chrome, Safari and Opera
                element.webkitRequestFullscreen();
            } else if (element.msRequestFullscreen) { // IE/Edge
                element.msRequestFullscreen();
            }

            // Set scrolling styles
            element.style.overflow = 'auto'; // Enable scrolling if needed
        } else {
            // Exit fullscreen
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) { // Firefox
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) { // Chrome, Safari and Opera
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) { // IE/Edge
                document.msExitFullscreen();
            }

            // Reset scrolling styles
            element.style.overflow = ''; // Reset to default
        }
    }
</script>
@endsection
