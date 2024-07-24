@extends('layouts.main')

@section('active-akhlak')
active
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .header-pencapaian {
        display: flex;
        align-items: center;
    }
    .logo-img {
        height: 2em; /* Match the height of the text */
        margin-left: 1px;
        margin-bottom: 2px; /* Optional: Add some space between the text and the image */
    }
    .m-t-5{
        margin-top: 5px;
    }
    .card {
        background: #fff;
        margin-bottom: 30px;
        transition: .5s;
        border: 0;
        border-radius: .1875rem;
        display: inline-block;
        position: relative;
        width: 100%;
        box-shadow: none;
    }
    .card .body {
        font-size: 14px;
        color: #424242;
        padding: 20px;
        font-weight: 400;
    }
    .profile-page .profile-header {
        position: relative
    }

    .profile-page .profile-header .profile-image img {
        border-radius: 50%;
        width: 140px;
        border: 3px solid #fff;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23)
    }

    .profile-page .profile-header .social-icon a {
        margin: 0 5px
    }

    .profile-page .profile-sub-header {
        min-height: 60px;
        width: 100%
    }

    .profile-page .profile-sub-header ul.box-list {
        display: inline-table;
        table-layout: fixed;
        width: 100%;
        background: #eee
    }

    .profile-page .profile-sub-header ul.box-list li {
        border-right: 1px solid #e0e0e0;
        display: table-cell;
        list-style: none
    }

    .profile-page .profile-sub-header ul.box-list li:last-child {
        border-right: none
    }

    .profile-page .profile-sub-header ul.box-list li a {
        display: block;
        padding: 15px 0;
        color: #424242
    }
</style>
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="ti-stats-up"></i> Pencapaian Akhlak</h1>
        <p class="mb-4">Managing Access based on roles.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <img class="logo-img" src="{{ asset('/img/logo_akhlak.png') }}" alt="User name">
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
<div class="text-right">
    {{-- <a class="btn btn-primary btn-sm text-white mb-4" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-icon fa fa-plus"></i> Insert Pencapaian</a> --}}
</div>
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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">Pencapaian AKHLAK - @if ($userSelected) {{ $userSelected->name }} @else Overall @endif</h6>
                    {{-- <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-icon fa fa-plus"></i> Insert Pencapaian</a>
                    </div> --}}
                    <div class="text-right">
                        <select class="form-control" id="yearSelected" name="yearSelected" required onchange="redirectToPage()">
                            <option value="1" selected>All</option>
                            @foreach (array_reverse($yearsBefore) as $year)
                                <option value="{{ $year }}" @if ($year == $yearSelected) selected @endif>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="/akhlak-achievements/{{$yearSelected}}">
                        @csrf
                        <div class="row d-flex justify-content-start mb-4 zoom90">
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="email">Nama Pekerja :</label>
                                            <select class="custom-select" id="userId" name="userId">
                                                <option value="1" selected>Show All</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}" @if ($user->id == $userSelectedOpt) selected @endif>{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="position_id">Start Periode :</label>
                                            <input type="date" class="form-control" id="periode_start" name="periode_start" @if ($periode_start) value="{{ $periode_start }}" @endif>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status">End Periode :</label>
                                            <input type="date" class="form-control" id="periode_end" name="periode_end" @if ($periode_end) value="{{ $periode_end }}" @endif>
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex align-self-end justify-content-start">
                                        <div class="form-group">
                                            <div class="align-self-center">
                                                <button type="submit" class="btn btn-primary" style="padding-left: 1.2em; padding-right: 1.2em;"><i class="ti-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="col-md-12">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h6 class="h6 m-0 font-weight-bold text-secondary"><i class="fa fa-user"></i> Avg. Pencapaian <span style="color: #403D4D;">AKH</span><span style="color: #029195;">LAK</span> @if ($userSelected) {{ $userSelected->name }} @else Pekerja @endif </h6>
                            <a class="btn btn-primary btn-sm text-white" href="#" id="btn-insert" data-toggle="modal" data-target="#inputDataModal"><i class="menu-icon fa fa-plus"></i> Insert Pencapaian</a>
                        </div>
                        <hr class="sidebar-divider">
                    </div>
                    <div class="row mb-4 bg-white" id="akhlakContainer">
                        <!-- Information Panel -->
                        <div class="col-md-6">
                            <ul class="ml-4 zoom90" style="padding-left: 1em;">
                                @foreach ($data as $index => $score)
                                    <li><strong>{{ $labels[$index] }}:</strong> {{ $score }} %
                                        @if ($labels[$index] == 'Kompeten')
                                            <p>This score indicates the level of competence achieved. A higher value reflects greater proficiency in required skills.</p>
                                        @elseif ($labels[$index] == 'Harmonis')
                                            <p>This score measures harmony within the team. A higher score suggests better interpersonal relationships and teamwork.</p>
                                        @elseif ($labels[$index] == 'Loyal')
                                            <p>This score represents the degree of loyalty demonstrated. A high score indicates strong commitment and reliability.</p>
                                        @elseif ($labels[$index] == 'Adaptif')
                                            <p>This score shows adaptability to changing circumstances. A higher value means greater flexibility and responsiveness.</p>
                                        @elseif ($labels[$index] == 'Kolaboratif')
                                            <p>This score evaluates collaboration efforts. A higher score reflects effective teamwork and cooperative behavior.</p>
                                        @elseif ($labels[$index] == 'Amanah')
                                            <p>This score reflects trustworthiness and integrity. A higher score suggests a strong adherence to ethical standards.</p>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <!-- Chart -->
                        <div class="col-md-6">
                            <canvas id="radarChart" style="max-width: 100%; height: 500px; margin: auto;"></canvas>
                        </div>
                        @if($userSelected)
                        <div class="col-md-12">
                            {{-- <div class="d-flex align-items-center justify-content-between mt-4 mb-4">
                                <h6 class="h6 m-0 font-weight-bold"><i class="fa fa-user"></i> Pencapaian Akhlak - {{ $userSelected->name }}</h6>
                                <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-icon fa fa-plus"></i> Insert Pencapaian</a>
                            </div>
                            <hr class="sidebar-divider"> --}}
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-12 mt-4">
                            <div class="card profile-header">
                                <div class="body">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-12">
                                            <div class="profile-image float-md-right"> <img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt=""> </div>
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-12">
                                            <h4 class="m-t-0 m-b-0 mb-2"><strong>{{ $userSelected->name }}</strong></h4>
                                            <p>Nomor Pekerja : {{ $userSelected->users_detail->employee_id }}</p>
                                            <span class="job_post">{{ $userSelected->users_detail->position->position_name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-8 col-lg-8 col-md-12 mt-4 mb-4 zoom90">
                            <table  id="listPencapaianUser" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Judul Kegiatan</th>
                                        <th>Score</th>
                                        <th>Intended to</th>
                                        <th>Periode Start</th>
                                        <th>Periode End</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pencapaian as $item)
                                    <tr>
                                        <td>{{ $item->judul_kegiatan }}</td>
                                        <td>{{ $item->score }} %</td>
                                        <td>{{ $item->akhlak->indicator }}</td>
                                        <td>{{ $item->periode_start }}</td>
                                        <td>{{ $item->periode_end }}</td>
                                        <td class="text-center">
                                            <a href="#" class="btn btn-outline-secondary btn-sm btn-details mr-2"><i class="fa fa-info-circle"></i> Preview</a>
                                            {{-- <a data-toggle="modal" data-target="#editInvoiceModal" data-item-id="{{ $docs->id }}" class="btn btn-danger btn-sm"><i class="fas fa-fw fa-trash-alt"></i> Delete</a> --}}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="col-xl-12 col-lg-12 col-md-12 mt-4">
                            <table  id="docLetter" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Employee Name</th>
                                        <th>Judul Kegiatan</th>
                                        <th>Score</th>
                                        <th>Intended to</th>
                                        <th>Periode Start</th>
                                        <th>Periode End</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pencapaian as $item)
                                    <tr>
                                        <td>{{ $item->user->name }}</td>
                                        <td>{{ $item->judul_kegiatan }}</td>
                                        <td>{{ $item->score }} %</td>
                                        <td>{{ $item->akhlak->indicator }}</td>
                                        <td>{{ $item->periode_start }}</td>
                                        <td>{{ $item->periode_end }}</td>
                                        <td class="text-center">
                                            <a href="#" class="btn btn-outline-secondary btn-sm btn-details mr-2"><i class="fa fa-info-circle"></i> Preview</a>
                                            {{-- <a data-toggle="modal" data-target="#editInvoiceModal" data-item-id="{{ $docs->id }}" class="btn btn-danger btn-sm"><i class="fas fa-fw fa-trash-alt"></i> Delete</a> --}}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                        <div class="col-md-12 text-right mt-4">
                            <small><i class="ti-fullscreen"></i>
                                <a href="#" onclick="toggleFullScreen('akhlakContainer')">&nbsp;<i>Fullscreen</i></a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('radarChart').getContext('2d');
    const radarChart = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Kompeten', 'Harmonis', 'Loyal', 'Adaptif', 'Kolaboratif', 'Amanah'],
            datasets: [{
                label: 'Pencapaian Rata-Rata AKHLAK @if ($userSelected) {{ $userSelected->name }} @else Overall @endif',
                data: @json($data),
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
                <h5 class="modal-title" id="inputDataModalLabel">Input Data Pencapaian <span class="font-weight-bold" style="color: #403D4D;">AKH</span><span class="font-weight-bold" style="color: #029195;">LAK</span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{ route('akhlak.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="col-md-12 zoom90">
                        <div class="form-group">
                            <label for="userId">Nama Pekerja :</label>
                            <select class="custom-select" id="userId" name="userId">
                                <option disabled selected>Select User...</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @if ($user->id == $userSelectedOpt) selected @endif>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="activity_title">Judul Kegiatan</label>
                            <input type="text" class="form-control" id="activity_title" name="activity_title" placeholder="Average Test Score..." required>
                        </div>
                        <div class="form-group">
                            <label for="akhlak_points"><span style="color: #403D4D;">AKH</span><span style="color: #029195;">LAK</span></label>
                            <select data-placeholder="Akhlak Poin" multiple class="standardSelect form-control" id="akhlak_points" name="akhlak_points[]">
                                @foreach ($akhlakPoin as $item)
                                <option value="{{ $item->id }}">{{ $item->indicator }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="akhlak_value">Nilai <span style="color: #403D4D;">AKH</span><span style="color: #029195;">LAK</span> <small class="text-danger"><i>(in percentage)</i></small></label>
                            <input type="text" class="form-control" id="akhlak_value" name="akhlak_value" placeholder="85%" required>
                        </div>
                        <div class="form-group">
                            <label for="evidence">Evidence <small class="text-danger"><i>(only document allowed!)</i></small></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="evidence" name="evidence" onchange="changeFileName('evidence', 'evidence-label')">
                                <label class="custom-file-label" for="evidence" id="evidence-label">Choose file</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="period">Periode</label>
                            <div class="row">
                                @php
                                    if($periode_start){
                                        // Deduct one day from the start date if it exists
                                        $adjustedStartDate = $periode_start ? \Carbon\Carbon::createFromFormat('Y-m-d', $periode_start)->addDay()->format('Y-m-d') : '';
                                    }
                                @endphp
                                <div class="col-md-6">
                                    <input type="date" class="form-control" id="period_start" name="period_start" @if ($periode_start) value="{{ $adjustedStartDate }}" @endif>
                                </div>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" id="period_end" name="period_end" @if ($periode_end) value="{{ $periode_end }}" @endif>
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

<script>
    function redirectToPage() {
        var selectedOption = document.getElementById("yearSelected").value;
        var url = "{{ url('/akhlak-achievements') }}"; // Specify the base URL

        url += "/" + selectedOption;

        window.location.href = url; // Redirect to the desired page
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
