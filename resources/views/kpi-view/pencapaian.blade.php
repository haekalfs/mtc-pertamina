@extends('layouts.main')

@section('active-kpi')
active font-weight-bold
@endsection

@section('show-kpi')
show
@endsection

@section('kpi')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-users"></i> KPI - {{ $kpiItem->indicator }}</h1>
        <p class="mb-4">Managing pencapaian KPI.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a class="btn btn-secondary btn-sm shadow-sm mr-2" href="{{ route('kpi') }}"><i class="fa fa-backward fa-sm text-white-50"></i> Go Back</a>
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
<div class="animated fadeIn">
    <div class="row">

        @php
        if($selectedQuarter == '-1'){
            $target = $kpiItem->target;
        } else {
            $target = $kpiItem->target / 4;
        }
        @endphp
        <div class="col-md-3 zoom90">
            <section>
                <div class="indicators">
                    <div class="indicator" style="border-radius: 5%;">
                        <div class="indicator__name bg-dark text-white">{{ $kpiItem->indicator }}</div>
                        <div class="indicator__data">
                            <div class="data__entry">
                                <div class="mb-1 @if($target <= $kpiItem->pencapaian->sum('score')) text-success @else text-danger @endif">Target :</div>
                                <div class="data__description">{{ $kpiItem->goal }}</div>
                                <div class="data__amount">{{ number_format($target, 0, ',', '.') }}</div>
                            </div>
                            <div class="data__entry">
                                <div class="data__description">Tercapai :</div>
                                <div class="por-txt mt-1">{{ number_format($kpiItem->pencapaian->sum('score'), 0, ',', '.') }} ({{ round($percentage) }}%)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="col-md-9 zoom90">
            <div class="card" style="border: 1px solid rgb(220, 219, 219);">
                <div class="card-body">
                    <div class="progress-box-modified progress-1" style="font-size: 20px;">
                        <h4 class="por-title">Realisasi Pencapaian {{ $kpiItem->indicator }}</h4>
                        <div class="por-txt">Target : {{ number_format($kpiItem->pencapaian->sum('score'), 0, ',', '.') }} ({{ round($percentage) }}%)</div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar
                                 @if($percentage >= 75) bg-success
                                 @elseif($percentage >= 50) bg-warning
                                 @else bg-danger
                                 @endif"
                                 role="progressbar" style="width: {{ $percentage }}%; font-size: 15px;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                {{ round($percentage) }}%
                            </div>
                        </div>
                    </div>
                    <div class="alert @if($target <= $kpiItem->pencapaian->sum('score')) alert-success @else alert-danger @endif alert-block mt-3">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>@if($target <= $kpiItem->pencapaian->sum('score')) Target is Reached! @else Target is not Reached yet! @endif</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card" style="border: 1px solid grey;">
                <div class="card-body card-body-modified" id="cardBody2024">
                    <div id="chartContainer" style="height: 300px; width: 100%; margin-bottom: 20px;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-12 zoom90">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" id="judul">List Pencapaian - {{ $kpiItem->indicator }}</h6>
                    <div class="text-right">
                        @if($selectedQuarter != '-1')
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-icon fa fa-plus"></i> Insert Pencapaian</a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <table id="docLetter" class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>No.</th>
                                <th>Pencapaian</th>
                                <th>Goal</th>
                                <th>Tercapai</th>
                                <th>KPI</th>
                                <th>Periode</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody> @php $no = 1; @endphp
                        @foreach ($kpiItem->pencapaian as $item)
                            <tr data-indicator="{{ $item->indicator->indicator }}">
                                <td>{{ $no++ }}</td>
                                <td>{{ $item->pencapaian }}</td>
                                <td>{{ $item->indicator->goal }}</td>
                                <td>{{ number_format($item->score, 0, ',', '.') }}</td>
                                <td>{{ $item->indicator->indicator }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->periode_start)->format('d/m/y') }} - {{ \Carbon\Carbon::parse($item->periode_end)->format('d/m/y') }}</td>
                                <td class="text-center">
                                    <a class="btn btn-outline-secondary btn-sm btn-update" data-id="{{ $item->id }}"><i class="fa fa-edit"></i> Update</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data Pencapaian KPI</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{ route('pencapaian.kpi.store', $kpiItem->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="col-md-12 zoom90">
                        <div class="form-group">
                            <label for="score">Periode</label>
                            <input type="text" class="form-control" name="daterange" id="daterange"/>
                        </div>
                        <div class="form-group">
                            <label for="pencapaian">Pencapaian</label>
                            <input type="text" class="form-control" id="pencapaian" name="pencapaian" placeholder="Average Test Score..." required>
                        </div>
                        <div class="form-group">
                            <label for="score">Score Tercapai <small class="text-danger"><i>(Number Format)</i></small></label>
                            <input type="text" class="form-control" id="score" name="score_display" oninput="formatAmount(this)" placeholder="Nominal Angka Tercapai..." required>
                            <input type="hidden" id="target_hidden" name="score"> <!-- Unformatted value for submission -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="kpiModal" tabindex="-1" role="dialog" aria-labelledby="kpiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="kpiModalLabel">Update KPI Achievement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="" id="kpiForm">
                @csrf
                <div class="modal-body zoom90 ml-2 mr-2">
                    <div class="form-group">
                        <label for="daterange">Periode</label>
                        <input type="text" class="form-control" id="edit_daterange" name="edit_daterange" placeholder="Select date range" required>
                    </div>

                    <div class="form-group">
                        <label for="pencapaian">Pencapaian</label>
                        <input type="text" class="form-control" id="edit_pencapaian" name="edit_pencapaian" placeholder="Enter achievement" required>
                    </div>

                    <div class="form-group">
                        <label for="score">Score</label>
                        <input type="text" class="form-control" id="edit_score" oninput="formatAmount2(this)" placeholder="Nominal Angka Tercapai..." required>
                        <input type="hidden" id="score_hidden" name="edit_score"> <!-- Unformatted value for submission -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="deleteButton">Delete</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
$(function() {
    var startDate = moment('{{ $startDate }}'); // Using startDate from the controller
    var endDate = moment('{{ $endDate }}');     // Using endDate from the controller

    $('input[name="daterange"]').daterangepicker({
        "startDate": startDate,
        "endDate": endDate,
        "opens": "right",
        "minDate": startDate,
        "maxDate": endDate
    }, function(start, end, label) {
        console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    });

    $('input[name="edit_daterange"]').daterangepicker({
        "opens": "right",
        "minDate": startDate,
        "maxDate": endDate
    }, function(start, end, label) {
        console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    });
});
</script>
<script>
window.onload = function () {
    var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        theme: "light2",
        zoomEnabled: true,
        title:{
            text: "KPI Statistic"
        },
        axisY: {
            valueFormatString: "#0,,.",
            suffix: "mn"
        },
        axisX: {
            labelFormatter: function (e) {
                return CanvasJS.formatDate(e.value, "DD-MMM-YYYY");
            },
            interval: 1,
            intervalType: "month" // Ensure this is set to 'week'
        },
        data: [{
            type: "splineArea",
            color: "#6599FF",
            markerSize: 5,
            xValueType: "dateTime",
            dataPoints: {!! json_encode($dataPoints, JSON_NUMERIC_CHECK) !!}
        }]
    });
    chart.render();
}
</script>
<script>
// Update KPI Modal Trigger
$(document).on('click', '.btn-update', function() {
    var id = $(this).data('id');

    // Fetch existing KPI data using AJAX
    $.ajax({
        url: '/pencapaian-kpi/edit/' + id,
        method: 'GET',
        success: function(data) {
            // Prefill the modal with the fetched data
            $('#edit_pencapaian').val(data.pencapaian);

            // Properly format the score
            var score = data.score || 0; // Ensure score is not undefined or null, default to 0 if it is
            var formattedScore = score.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            // Set the formatted value in the visible input field
            $('#edit_score').val(formattedScore);

            // Set the unformatted value in the hidden input field
            $('#score_hidden').val(score); // Use the raw unformatted score here

            // Set the date range in the daterangepicker
            var startDate = moment(data.periode_start);
            var endDate = moment(data.periode_end);
            $('input[name="edit_daterange"]').data('daterangepicker').setStartDate(startDate);
            $('input[name="edit_daterange"]').data('daterangepicker').setEndDate(endDate);

            // Update the form action using Laravel route helper in Blade
            var updateRoute = '{{ route("pencapaian.update", ":id") }}';
            updateRoute = updateRoute.replace(':id', id);
            $('#kpiForm').attr('action', updateRoute);

            // Assign the ID to the delete button dynamically
            $('#deleteButton').data('id', id); // Attach the KPI ID to the delete button

            // Show the modal
            $('#kpiModal').modal('show');
        }
    });
});

// Handle the delete button click event with SweetAlert
$(document).on('click', '#deleteButton', function() {
    var id = $(this).data('id'); // Fetch the ID of the KPI to be deleted

    // Show SweetAlert confirmation dialog
    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this KPI data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            // Send delete request via AJAX if confirmed
            $.ajax({
                url: '/pencapaian-kpi/delete/' + id, // Delete route
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}' // Ensure CSRF token is included
                },
                success: function(response) {
                    swal("Success!", "The KPI data has been deleted.", "success")
                    .then(() => {
                        // Optionally close the modal and refresh the page or table
                        $('#kpiModal').modal('hide');
                        location.reload(); // Refresh the page to reflect the deletion
                    });
                },
                error: function(xhr) {
                    swal("Error", "There was an issue deleting the KPI data.", "error");
                }
            });
        } else {
            swal("Your KPI data is safe!");
        }
    });
});


function formatAmount(input) {
    // Remove non-numeric characters for display purposes
    let displayValue = input.value.replace(/[^0-9]/g, '');

    // Add thousands separator (dots) for display
    displayValue = displayValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    // Set the formatted value back to the input (for display)
    input.value = displayValue;

    // Also set the unformatted value in a hidden input for submission
    document.getElementById('target_hidden').value = input.value.replace(/\./g, '');
}

function formatAmount2(input) {
    // Remove non-numeric characters for display purposes
    let displayValue = input.value.replace(/[^0-9]/g, '');

    // Add thousands separator (dots) for display
    input.value = displayValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    // Also set the unformatted value in the hidden input for submission
    document.getElementById('score_hidden').value = displayValue;
}
</script>
@endsection
