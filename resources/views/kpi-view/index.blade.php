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
        <p class="mb-4">Menampilkan berbagai grafik hasil pencapaian KPI MTC secara umum.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <select class="form-control mr-3" id="quarterSelected" name="quarterSelected" onchange="redirectToPage()" required style="width: 200px;">
            <option value="-1" {{ $selectedQuarter == -1 ? 'selected' : '' }}>Show All Periode</option>
            @foreach ($quarters as $quarter)
                <option value="{{ $quarter->id }}" {{ $quarter->id == $selectedQuarter ? 'selected' : '' }}>{{ $quarter->months }}</option>
            @endforeach
        </select>
        <select class="form-control" id="yearSelected" name="yearSelected" required onchange="redirectToPage()" style="width: 100px;">
            @foreach (array_reverse($yearsBefore) as $year)
                <option value="{{ $year }}" {{ $year == $yearSelected ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
        </select>
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
@if($kpis->isNotEmpty())
<section>
    <div class="indicators">
        @foreach ($kpis as $kpi)
        @php
        if($selectedQuarter == '-1'){
            $target = $kpi->target;
        } else {
            $target = $kpi->target / 4;
        }
        @endphp
        <a href="{{ route('pencapaian-kpi', [$kpi->id, $selectedQuarter, $yearSelected]) }}">
            <div class="indicator">
                <div class="indicator__name bg-primary text-white">{{ $kpi->indicator }}</div>
                <div class="indicator__data">
                    <div class="data__entry">
                        <div class="mb-1 @if($target <= $kpi->pencapaian->sum('score')) text-success @else text-danger @endif">Target :</div>
                        <div class="data__description">{{ $kpi->goal }}</div>
                        <div class="data__amount">{{ number_format($target, 0, ',', '.') }}</div>
                    </div>
                    <div class="data__entry">
                        <div class="data__description">Tercapai :</div>
                        <div class="data__spend">{{ number_format($kpi->pencapaian->sum('score'), 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</section>
@else
<div class="alert alert-warning alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>
        <h5 class="mb-2">KPI for this year is not exist, please create the indicator in <a href="{{ route('manage-kpi') }}"><u>Manage KPI</u></a> menu or if you want to create the indicators same as before, click below!</h5>
        <form id="duplicateKpisForm" method="POST" action="{{ route('kpis.duplicate', $yearSelected) }}">
            @csrf
            <button type="button" class="btn btn-primary btn-sm" onclick="confirmDuplicate()">Duplicate KPIs for {{ $yearSelected }}</button>
        </form>
    </strong>
</div>
@endif
<div class="animated fadeIn">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 mt-2">
                <div class="card-header">
                    <span class="text-secondary font-weight-bold">Realisasi KPI {{ $quarterRow ? $quarterRow->months : 'Overall' }}</span>
                </div>
                <div class="card-body" style="background-color: rgb(247, 247, 247);">
                    <div class="row">
                        <div class="col-md-3 d-flex justify-content-center">
                            <a class="progress-circle-wrapper animateBox">
                                <div class="progress-circle p{{ round($overallProgress, 0) }} @if(round($overallProgress, 2) >= 50) over50 @endif">
                                    <span>{{ round($overallProgress, 2) }}%</span>
                                    <div class="left-half-clipper">
                                        <div class="first50-bar"></div>
                                        <div class="value-bar"></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @php
                            $indicatorsArray = $kpis->pluck('indicator')->toArray();
                            $lastIndicator = array_pop($indicatorsArray); // Remove the last item
                            $indicators = implode(', ', $indicatorsArray); // Join the remaining items with commas
                            if ($lastIndicator) {
                                $indicators .= ' and ' . $lastIndicator; // Append the last item with 'and'
                            }
                        @endphp
                        <div class="col-md-9">
                            <h6 class="h6 mb-2 font-weight-bold text-gray-800">Guidelines for KPI Realization</h6>
                            <ul class="ml-4">
                                <li><strong>KPI Realization Overview:</strong> This section provides an overview of the Key Performance Indicators (KPIs) realization for Pertamina MTC. It focuses on evaluating the performance and achievements of various KPIs such as {{ $indicators }} highlighting areas of success and identifying opportunities for improvement.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @foreach($kpiChartsData as $index => $chartData)
        <div class="@if($index == 0) col-lg-8 @elseif($index == 1) col-lg-4 @elseif($index == 4) col-lg-12 @else col-lg-6 @endif">
            <div class="card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <!-- <canvas id="TrafficChart"></canvas>   -->
                            <div id="chartContainer{{$index}}" style="height: 370px; width: 100%;"></div>
                        </div>
                    </div>
                </div> <!-- /.row -->
            </div>
        </div>
        @endforeach
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex flex-row p-3 align-items-center justify-content-between zoom90">
                    <h6 class="m-0 font-weight-bold" id="judul">Data Pencapaian Overall</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Create KPI</a> --}}
                    </div>
                </div>
                <div class="card-body" style="padding-top: 10px;">
                    <div class="row d-flex justify-content-start mb-2">
                        <div class="col-md-12">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email">Indicator :</label>
                                        <select class="custom-select" id="namaPenlat" name="namaPenlat">
                                            <option value="1" selected>Show All</option>
                                            @foreach ($kpis as $kpi)
                                                <option value="{{ $kpi->indicator }}">{{ $kpi->indicator }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table id="docLetter" class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>No.</th>
                                <th>Pencapaian</th>
                                <th>Goal</th>
                                <th>Tercapai</th>
                                <th>KPI</th>
                                <th>Periode</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($kpis as $kpi) <!-- Loop through each KPI -->
                                @foreach ($kpi->pencapaian as $item) <!-- Loop through each 'pencapaian' related to the current KPI -->
                                <tr data-indicator="{{ $item->indicator->indicator }}">
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->pencapaian }}</td>
                                    <td>{{ $item->indicator->goal }}</td>
                                    <td>{{ number_format($item->score, 0, ',', '.') }}</td>
                                    <td>{{ $item->indicator->indicator }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->periode_start)->format('d/m/y') }} - {{ \Carbon\Carbon::parse($item->periode_end)->format('d/m/y') }}</td>
                                    {{-- <td class="text-center">
                                        <a href="{{ route('preview-kpi', ['id' => $item->kpi_id]) }}" class="btn btn-outline-secondary btn-sm mr-2"><i class="ti-eye"></i> Preview</a>
                                        <a href="#" class="btn btn-outline-danger btn-sm btn-details" onclick="confirmDelete({{ $item->id }});"><i class="fa fa-ban"></i> Delete</a>
                                        <form id="delete-kpi-{{ $item->id }}" action="{{ route('kpi.destroy', $item->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td> --}}
                                </tr>
                                @endforeach
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
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('kpis.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="col-md-12 zoom90">
                        <div class="form-group">
                            <label for="kpi">KPI</label>
                            <input type="text" class="form-control" id="kpi" name="kpi" placeholder="Yearly Revenue..." required>
                        </div>
                        <div class="form-group">
                            <label for="target">Goal</label>
                            <input type="text" class="form-control" id="target" name="target" placeholder="Gain new subscribers..." required>
                        </div>
                        <div class="form-group">
                            <label for="target">Target</label>
                            <input type="text" class="form-control" id="target" name="target" placeholder="1000" required>
                        </div>
                        <div class="form-group">
                            <label for="periode">Periode</label>
                            <select class="form-control" id="periode" name="periode" required>
                                @foreach (array_reverse($yearsBefore) as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
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

<script>
    function confirmDelete(itemId) {
        event.preventDefault();
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this KPI!",
            Logo: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                document.getElementById('delete-kpi-' + itemId).submit();
            }
        });
    }

    function redirectToPage() {
        var year = document.getElementById("yearSelected").value;
        var quarter = document.getElementById("quarterSelected").value;

        // Redirect to the encryption route
        var url = "{{ url('/encrypt-params') }}?quarter=" + quarter + "&year=" + year;
        window.location.href = url;
    }
</script>
<script>
    $(document).ready(function() {
        // Event listener for dropdown change
        $('#namaPenlat').on('change', function() {
            var selectedIndicator = $(this).val();
            $('#docLetter tbody tr').each(function() {
                var rowIndicator = $(this).data('indicator');

                if (selectedIndicator === '1' || rowIndicator === selectedIndicator) {
                    $(this).show(); // Show rows that match the selected indicator
                } else {
                    $(this).hide(); // Hide rows that do not match
                }
            });
        });
    });
</script>
<script type="text/javascript">
function confirmDuplicate() {
    swal({
        title: "Are you sure?",
        text: "Do you want to duplicate the KPIs for {{ $yearSelected }}?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDuplicate) => {
        if (willDuplicate) {
            document.getElementById('duplicateKpisForm').submit();
        } else {
            swal("Your KPIs were not duplicated.");
        }
    });
}
const kpiChartsData = {!! json_encode($kpiChartsData, JSON_NUMERIC_CHECK) !!};

window.onload = function() {
    kpiChartsData.forEach((chartData, index) => {
        const chart = new CanvasJS.Chart(`chartContainer${index}`, {
            animationEnabled: true,
            theme: "light2",
            zoomEnabled: true,
            title:{
                text: chartData.kpiName + " Statistic"
            },
            axisY: {
                valueFormatString: "#0,,.",
                suffix: "mn",
                stripLines: [
                    {
                        value: chartData.kpiTarget,
                        label: "Target Minimum: " + chartData.kpiTarget,
                        color: "#FF0000", // Color of the line
                        thickness: 2, // Thickness of the line
                        labelPlacement: "inside",
                        labelAlign: "center",
                        zIndex: 10 // Ensure line is in front of bars
                    }
                ]
            },
            axisX: {
                labelFormatter: function (e) {
                    return CanvasJS.formatDate(e.value, "DD-MMM");
                },
                interval: 1,
                intervalType: "month"
            },
            data: [{
                type: "splineArea",
		        color: "#6599FF",
                markerSize: 5,
                xValueType: "dateTime",
                dataPoints: chartData.dataPoints
            }]
        });

        chart.render();
    });
};
</script>
@endsection
