@extends('layouts.main')

@section('active-kpi')
active font-weight-bold
@endsection

@section('show-kpi')
show
@endsection

@section('report-kpi')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-file-text-o"></i> Report Pencapaian KPI</h1>
        <p class="mb-4">Unduh Report Pencapaian KPI.</a></p>
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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">Search Report</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="/key-performance-indicators/report">
                        @csrf
                        <div class="row d-flex justify-content-start mb-3">
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="position_id">Indicator :</label>
                                            <select name="indicator" class="form-control" id="indicator">
                                                <option value="-1">All Indicators</option>
                                                @foreach ($kpis as $item)
                                                <option value="{{ $item->id }}" @if ($item->id == $kpiSelected) selected @endif>{{ $item->indicator }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="status">Periode :</label>
                                            <select class="form-control" id="periode" name="periode" required>
                                                @foreach (array_reverse($yearsBefore) as $year)
                                                    <option value="{{ $year }}" @if ($year == $periode) selected @endif>{{ $year }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex align-self-end justify-content-start">
                                        <div class="form-group">
                                            <div class="align-self-center">
                                                <input type="submit" class="btn btn-primary" value="Show"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">Laporan Pencapaian KPI</h6>
                    <div class="text-right">
                        <form method="POST" action="{{ route('kpi.downloadPdf') }}" target="_blank" id="pdfForm">
                            @csrf
                            <input type="hidden" name="chartImage" id="chartImageInput">
                            <input type="hidden" name="kpiInput" id="kpiInput" value="{{$kpiSelected}}">
                            <input type="hidden" name="periodeInput" id="periodeInput" value="{{$periode}}">
                            <button id="downloadPdfButton" class="btn btn-primary btn-sm">Download PDF</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-12 d-flex justify-content-center">
                        <table>
                            <tr>
                                <td style="border: none; padding-right: 10px;">
                                    <img src="https://ccdjayaekspres.id/MTC.png" style="height: 130px; width: 130px;" alt="">
                                </td>
                                <td style="border: none;">
                                    <address class="text-center">
                                        <strong>Pertamina Maritime Training Center</strong><br>
                                        Jl. Pemuda No.44, RT.2/RW.4, Jati, Kec. Pulo Gadung, <br>
                                        Kota Jakarta Timur, DKI Jakarta 13220
                                    </address>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <hr style="border-top: 3px solid rgb(197, 197, 197); margin-top:-1%;">
                    <div class="col-md-12 pt-2">
                        <h6 class="h6 mb-2 font-weight-bold text-gray-800">Guidelines for KPI Realization</h6>
                        <ul class="ml-4">
                            <li><strong>KPI Realization Overview:</strong> This section provides an overview of the Key Performance Indicators (KPIs) realization for Pertamina MTC. It focuses on evaluating the performance and achievements of various KPIs, highlighting areas of success and identifying opportunities for improvement. The goal is to assess how well the targets have been met and to provide insights for strategic planning and decision-making within the organization.</li>
                        </ul>
                    </div>
                    <h1 class="text-center my-4">KPI Report Summary</h1>
                    <table class="table table-bordered mb-4">
                        <thead class="text-white">
                            <tr>
                                <th class="bg-secondary" rowspan="3" style="vertical-align: middle; text-align: center;">Indicator</th>
                                <th class="text-center bg-secondary" colspan="8">{{ $periode }}</th>
                            </tr>
                            <tr class="bg-secondary">
                                <th class="text-center" colspan="2">Quarter 1</th>
                                <th class="text-center" colspan="2">Quarter 2</th>
                                <th class="text-center" colspan="2">Quarter 3</th>
                                <th class="text-center" colspan="2">Quarter 4</th>
                            </tr>
                            <tr class="bg-secondary">
                                <th class="text-center">Pencapaian</th>
                                <th class="text-center">(%)</th>
                                <th class="text-center">Pencapaian</th>
                                <th class="text-center">(%)</th>
                                <th class="text-center">Pencapaian</th>
                                <th class="text-center">(%)</th>
                                <th class="text-center">Pencapaian</th>
                                <th class="text-center">(%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!$data)
                                <tr class="text-center">
                                    <td colspan="9">No Data Available</td> <!-- Use colspan to span across the entire row -->
                                </tr>
                            @else
                                @foreach($data as $metric => $quarters)
                                    <tr>
                                        <td>{{ $metric }}</td>
                                        @foreach($quarters as $quarter)
                                            <td>{{ number_format($quarter['Tercapai'], 0, ',', '.') }}</td>
                                            <td>{{ number_format($quarter['%'], 1) }}%</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endif
                            <tr class="bg-secondary text-white">
                                <td>Overall KPI</td>
                                <th colspan="2">{{ number_format($overallKPI['Q1'], 1) }}%</th>
                                <th colspan="2">{{ number_format($overallKPI['Q2'], 1) }}%</th>
                                <th colspan="2">{{ number_format($overallKPI['Q3'], 1) }}%</th>
                                <th colspan="2">{{ number_format($overallKPI['Q4'], 1) }}%</th>
                            </tr>
                        </tbody>
                    </table>
                    <div class="col-md-12 mb-3 mt-3">
                        <h4 class="font-weight-bold">Realisasi KPI Overall</h4>
                        <p>{{ round($overallProgress, 2) }}%</p>
                    </div>
                    <!-- Here we will insert the chart as an image -->
                    <div id="charts">
                        <div class="row">
                            @foreach($kpiChartsData as $index => $chartData)
                            <div class="@if($index == 0) col-lg-8 @elseif($index == 1) col-lg-4 @elseif($index == 4) col-lg-12 @else col-lg-6 @endif">
                                <div class="card shadow-none" style="border: 1px solid grey;">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('downloadPdfButton').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default form submission

        html2canvas(document.querySelector("#charts")).then(function(canvas) {
            let chartImage = canvas.toDataURL('image/png');
            document.getElementById('chartImageInput').value = chartImage;

            // Now submit the form
            document.getElementById('pdfForm').submit();
        });
    });
});
</script>
@endsection
