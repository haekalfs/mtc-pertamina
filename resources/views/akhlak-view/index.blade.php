@extends('layouts.main')

@section('active-akhlak')
active font-weight-bold
@endsection

@section('show-akhlak')
show
@endsection

@section('akhlak')
font-weight-bold
@endsection

@section('content')
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
    .akhlak-indicator {
        font-weight: bold;
    }

    .akhlak-indicator .akh {
        color: #1d2a57; /* Dark Blue for AKH */
    }

    .akhlak-indicator .lak {
        color: #009eaa; /* Green for LAK */
    }
</style>
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="ti-stats-up"></i> Pencapaian Akhlak</h1>
        <p class="mb-4">Menampilkan grafik & hasil pencapaian AKHLAK secara overall dari berbagai core values AKHLAK.</a></p>
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
<div class="animated fadeIn">
    <div class="row">
        <div class="col-md-12 zoom90">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">Search Report</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="/akhlak-achievements">
                        @csrf
                        <div class="row d-flex justify-content-start mb-4 zoom90">
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="userId">Nama Pekerja :</label>
                                            <select class="custom-select" id="userId" name="userId">
                                                <option value="-1" selected>Show All</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}" @if ($user->id == $userSelectedOpt) selected @endif>{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="quarter">Periode</label>
                                            <select class="form-control" name="quarter" required>
                                                <option value="-1" selected>Show All</option>
                                                @foreach ($quarterList as $quarter)
                                                    <option value="{{ $quarter->id }}" @if ($quarter->id == $quarterSelected) selected @endif>{{ $quarter->quarter_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="year">Year</label>
                                            <select class="form-control" name="year" required>
                                                <option value="-1" selected>Show All</option>
                                                @foreach (array_reverse($yearsBefore) as $year)
                                                    <option value="{{ $year }}" @if ($year == $yearSelected) selected @endif>{{ $year }}</option>
                                                @endforeach
                                            </select>
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
                </div>
            </div>
        </div>
        <!-- Information Panel -->
        @if($userSelected)
        <div class="col-md-6 zoom90">
            <section class="card" style="border: 1px solid grey;">
                <div class="card-header bg-login alt mb-4 p-4" style="background-image: url('{{ asset('img/kilang-minyak.png') }}');">
                    <div class="media">
                        <a href="#">
                            <img class="align-self-center rounded-circle mr-3" style="width:85px; height:85px;" alt="" src="{{ asset($userSelected->users_detail->profile_pic) }}">
                        </a>
                        <div class="media-body">
                            <h3 class="text-white display-6 mt-1">{{ $userSelected->name }}</h3>
                            <p class="text-white">Nomor Pekerja : {{ $userSelected->users_detail->employee_id }}</p>
                        </div>
                    </div>
                </div>
                <ul class="ml-4 zoom90" style="padding-left: 1em; padding-bottom: 1em;">
                    @if($generalPencapaianResults->isNotEmpty())
                    @foreach ($generalPencapaianResults as $result)
                        <li>
                            <strong class="akhlak-indicator">
                            @if ($result->akhlak->indicator == 'Amanah')
                                <span class="akh">{{ substr($result->akhlak->indicator, 0, 1) }}</span><span>{{ substr($result->akhlak->indicator, 1) }}</span>
                                </strong>: {{ $result->nilai_description }}
                                <p>Memegang teguh kepercayaan yang diberikan.</p>
                            @elseif ($result->akhlak->indicator == 'Kompeten')
                                <span class="akh">{{ substr($result->akhlak->indicator, 0, 1) }}</span><span>{{ substr($result->akhlak->indicator, 1) }}</span>
                                </strong>: {{ $result->nilai_description }}
                                <p>Berupaya terus menerus meningkatkan kapabilitas dan memberikan hasil terbaik.</p>
                            @elseif ($result->akhlak->indicator == 'Harmonis')
                                <span class="akh">{{ substr($result->akhlak->indicator, 0, 1) }}</span><span>{{ substr($result->akhlak->indicator, 1) }}</span>
                                </strong>: {{ $result->nilai_description }}
                                <p>Menghargai perbedaan, saling peduli, dan membangun lingkungan kerja yang kondusif.</p>
                            @elseif ($result->akhlak->indicator == 'Loyal')
                                <span class="lak">{{ substr($result->akhlak->indicator, 0, 1) }}</span><span>{{ substr($result->akhlak->indicator, 1) }}</span>
                                </strong>: {{ $result->nilai_description }}
                                <p>Berdedikasi dan mengutamakan kepentingan Bangsa dan Negara.</p>
                            @elseif ($result->akhlak->indicator == 'Adaptif')
                                <span class="lak">{{ substr($result->akhlak->indicator, 0, 1) }}</span><span>{{ substr($result->akhlak->indicator, 1) }}</span>
                                </strong>: {{ $result->nilai_description }}
                                <p>Terus berinovasi dan antusias dalam menggerakkan atau menghadapi perubahan.</p>
                            @elseif ($result->akhlak->indicator == 'Kolaboratif')
                                <span class="lak">{{ substr($result->akhlak->indicator, 0, 1) }}</span><span>{{ substr($result->akhlak->indicator, 1) }}</span>
                                </strong>: {{ $result->nilai_description }}
                                <p>Membangun kerjasama yang sinergis.</p>
                            @endif
                        </li>
                    @endforeach
                    @else
                    <li>No Data Available</li>
                    @endif
                </ul>
            </section>
        </div>
        @endif
        @if(!$userSelected)
        <div class="col-md-6 zoom90">
            <div class="card mb-4 shadow">
                <div class="card-header">
                    <span class="text-primary font-weight-bold">Overview</span>
                </div>
                <div class="card-body" style="background-color: rgb(247, 247, 247);">
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">General Guidelines</h6>
                    <ul class="ml-4">
                        <li>Review the overall and specific user charts to ensure alignment with Akhlak BUMN values.</li>
                        <li>Ensure that all user data is presented accurately and reflects the principles of integrity and professionalism.</li>
                        <li>Unauthorized changes to user performance data or charts are not permitted to maintain data accuracy.</li>
                        <li>Thoroughly verify all data visualizations for accuracy and completeness before finalizing any reports or updates.</li>
                    </ul>
                </div>
            </div>
        </div>
        @endif
        <div class="col-md-6">
            <div class="card">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <canvas id="radarChart" style="max-width: 100%; height: 500px; margin: auto;"></canvas>
                        </div>
                    </div>
                </div> <!-- /.row -->
            </div>
        </div>
        <div class="col-md-12 zoom90">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">Average Pencapaian AKHLAK - @if ($userSelected) {{ $userSelected->name }} @else Overall @endif</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-icon fa fa-plus"></i> Insert Pencapaian</a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="listPencapaianAkhlak" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Core Value</th>
                                <th>Nilai Akhlak</th>
                                {{-- <th>Avg. Nilai</th> --}}
                                <th>Quarter</th>
                                <th>Periode</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pencapaianResults as $item)
                            <tr>
                                <td>{{ $item->akhlak->indicator }}</td>
                                <td>{{ $item->nilai_description }}</td>
                                {{-- <td>{{ $item->average_score }} %</td> --}}
                                <td>{{ $item->quarter->quarter_name }}</td>
                                <td>{{ $item->periode }}</td>
                                <td class="text-center">
                                    <a href="" class="btn btn-outline-secondary btn-sm btn-details mr-2"><i class="fa fa-info-circle"></i> Preview</a>
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
                            <input type="text" class="form-control" id="activity_title" name="activity_title" placeholder="e.g Corporate Social Responsibility (CSR) “Event 1”" required>
                        </div>
                        <div class="form-group">
                            <label for="akhlak_points">Core Values</label>
                            <select data-placeholder="Akhlak Poin" multiple class="standardSelect form-control" id="akhlak_points" name="akhlak_points[]">
                                @foreach ($akhlakPoin as $item)
                                <option value="{{ $item->id }}">{{ $item->indicator }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="akhlak_value">Nilai <span style="color: #403D4D;">AKH</span><span style="color: #029195;">LAK</span></label>
                            <select class="form-control" id="akhlak_value" name="akhlak_value" required>
                                @foreach ($nilaiList as $item)
                                    <option value="{{ $item->id }}">{{ $item->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="evidence">Evidence <small class="text-danger"><i>(pdf,docx,xlsx,xls,jpeg,png,jpg,gif)</i></small></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="evidence" name="evidence" onchange="changeFileName('evidence', 'evidence-label')">
                                <label class="custom-file-label" for="evidence" id="evidence-label">Choose file</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="periode_start">Periode</label>
                                    <select class="form-control" name="quarter" required>
                                        <option value="1">Quarter 1</option>
                                        <option value="2">Quarter 2</option>
                                        <option value="3">Quarter 3</option>
                                        <option value="4">Quarter 4</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="periode_end">Year</label>
                                    <select class="form-control" name="year" required>
                                        @foreach (array_reverse($yearsBefore) as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
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
<script>
    const ctx = document.getElementById('radarChart').getContext('2d');

    const radarChart = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: {!! $akhlakLabels !!},  // Akhlak descriptions (e.g., 'Kompeten', 'Harmonis', etc.)
            datasets: [{
                label: 'Pencapaian Rata-Rata AKHLAK @if ($userSelected) {{ $userSelected->name }} @else Overall MTC @endif',
                data: {!! $averageScores !!},  // Average scores for each akhlak point
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                r: {
                    beginAtZero: true,
                    ticks: {
                        min: 0,
                        max: 5  // Adjust based on the scoring system (e.g., if max score is 5)
                    }
                }
            }
        }
    });
</script>
@endsection
