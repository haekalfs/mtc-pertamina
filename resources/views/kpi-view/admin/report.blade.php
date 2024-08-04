@extends('layouts.main')

@section('active-kpi')
active font-weight-bold
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
<style>
/* Form Labels */
.modal-body label {
    font-weight: bold;
}

/* Form Inputs */
.modal-body input[type="text"], .modal-body input[type="date"] {
    margin-bottom: 10px;
}

/* Radio Buttons */
.radio-inline {
    margin-right: 50px;
}

</style>
<div class="animated fadeIn zoom90">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">Search Report</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-icon fa fa-download"></i> Download</a> --}}
                    </div>
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
                                            <select name="nilai_akhlak" class="form-control" id="nilai_akhlak">
                                                <option value="7">All Indicators</option>
                                                @foreach ($kpiPoin as $item)
                                                <option value="{{ $item->id }}" @if ($item->id == $kpiSelected) selected @endif>{{ $item->indicator }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="status">Periode :</label>
                                            <select class="form-control" id="periode" name="periode" required>
                                                <option value="1" selected>All</option>
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
    </div>
</div>
@if($kpiSelected)
<div class="animated fadeIn zoom90">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> Laporan Pencapaian <span style="color: #403D4D;">AKH</span><span style="color: #029195;">LAK</span></h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-icon fa fa-download"></i> Download</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row d-flex justify-content-start mb-3">
                        <div class="col-md-12">
                            <table class='table table-bordered table-sm mt-4 mb-4'>
                                <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>Nilai Akhlak</th>
                                        <th>Periode</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>asd</td>
                                        <td>Kompeten</td>
                                        <td>2022</td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Employee Name</th>
                                        <th>Average Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>John Doe</td>
                                        <td>75</td>
                                    </tr>
                                    <tr>
                                        <td>Jane Smith</td>
                                        <td>85</td>
                                    </tr>
                                    <tr>
                                        <td>Bob Johnson</td>
                                        <td>90</td>
                                    </tr>
                                </tbody>
                            </table>
                            {{-- <table class="table table-borderless mt-4">
                                <tbody>
                                    <tr class="table-sm">
                                        <td class="m-0" width="250px">
                                            <div class="font-weight-bold">Average Nilai Akhlak</div>
                                            <div>Avg. Amanah</div>
                                            <div>Avg. Amanah</div>
                                            <div>Avg. Amanah</div>
                                            <div>Avg. Amanah</div>
                                            <div>Avg. Amanah</div>
                                        </td>
                                        <td>
                                            <div>&nbsp;</div>
                                            <div>:</div>
                                            <div>:</div>
                                            <div>:</div>
                                            <div>:</div>
                                            <div>:</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="animated fadeIn zoom90">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> Laporan Pencapaian KPI</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-icon fa fa-download"></i> Download</a> --}}
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-center">No Data Available, Filter out to show data!.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<script>
    const ctx = document.getElementById('radarChart').getContext('2d');
    const radarChart = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Kompeten', 'Harmonis', 'Loyal', 'Adaptif', 'Kolaboratif', 'Amanah'],
            datasets: [{
                label: 'Pencapaian AKHLAK',
                data: [65, 59, 90, 81, 56, 55],
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                r: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<div class="modal fade" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header d-flex flex-row align-items-center justify-content-between">
            <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="#">
                @csrf
                <div class="modal-body">
                    <div class="col-md-12 zoom90">
                        <div class="form-group">
                            <label for="position_name">KPI</label>
                            <input type="text" class="form-control" id="position_name" name="position_name" placeholder="Average Test Score..." required>
                        </div>
                        <div class="form-group">
                            <label for="position_name">Target <small class="text-danger"><i>(in percentage)</i></small></label>
                            <input type="text" class="form-control" id="position_name" name="position_name" placeholder="85%" required>
                        </div>
                        <div class="form-group">
                            <label for="period">Periode</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" class="form-control" id="period_start" name="period_start">
                                </div>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" id="period_end" name="period_end">
                                </div>
                            </div>
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
@endsection
