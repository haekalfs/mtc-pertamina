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
<div class="animated fadeIn zoom90">
    <div class="row">

        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <section class="shadow bg-secondary">
                        <div class="indicators">
                            <div class="indicator">
                                <div class="indicator__name">{{ $kpiItem->indicator }}</div>
                                <div class="indicator__data">
                                    <div class="data__entry">
                                        <div class="mb-1 @if($kpiItem->target / 4 <= $kpiItem->pencapaian->sum('score')) text-success @else text-danger @endif">Target :</div>
                                        <div class="data__description">{{ $kpiItem->goal }}</div>
                                        <div class="data__amount">{{ number_format($kpiItem->target / 4, 0, ',', '.') }}</div>
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
                <div class="col-md-9">
                    <div class="card">

                        <div class="card-body">
                            <div class="progress-box-modified progress-1" style="font-size: 20px;">
                                <h4 class="por-title mb-3">Realisasi Pencapaian {{ $kpiItem->indicator }}</h4>
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
                            <div class="alert @if($kpiItem->target / 4 <= $kpiItem->pencapaian->sum('score')) alert-success @else alert-danger @endif alert-block mt-3">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong>@if($kpiItem->target / 4 <= $kpiItem->pencapaian->sum('score')) Target is Reached! @else Target is not Reached yet! @endif</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body card-body-modified" id="cardBody2024">
                    <div id="chartContainer" style="height: 300px; width: 100%; margin-bottom: 20px;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" id="judul">List Pencapaian - {{ $kpiItem->indicator }}</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-icon fa fa-plus"></i> Insert Pencapaian</a>
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
                            <label for="score">Score Tercapai <small class="text-danger"><i>(in percentage)</i></small></label>
                            <input type="text" class="form-control" id="score" name="score" placeholder="85%" required>
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
            markerSize: 5,
            xValueType: "dateTime",
            dataPoints: {!! json_encode($dataPoints, JSON_NUMERIC_CHECK) !!}
        }]
    });
    chart.render();
}
</script>
<script>
    function confirmDelete(itemId) {
        event.preventDefault();
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this KPI!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                document.getElementById('delete-pencapaian-kpi-' + itemId).submit();
            }
        });
    }
</script>
@endsection
