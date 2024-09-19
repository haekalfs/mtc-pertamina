@extends('layouts.main')

@section('active-akhlak')
active font-weight-bold
@endsection

@section('show-akhlak')
show
@endsection

@section('report-akhlak')
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
</style>
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-file-text-o"></i> Report Pencapaian Akhlak</h1>
        <p class="mb-4">Unduh Pencapaian Akhlak.</a></p>
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
                    <form method="GET" action="/akhlak-report">
                        @csrf
                        <div class="row d-flex justify-content-start mb-4 zoom90">
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="userId">Nama Pekerja :</label>
                                            <select class="custom-select" id="userId" name="userId">
                                                <option selected disabled>Select Employee...</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}" @if ($user->id == $userSelectedOpt) selected @endif>{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="year">Periode</label>
                                            <select class="form-control" name="year" required>
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
    </div>
</div>

<div class="animated fadeIn zoom90">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> Laporan Pencapaian <span style="color: #403D4D;">AKH</span><span style="color: #029195;">LAK</span></h6>
                    <div class="text-right">
                        @if($userSelected)
                        <form method="POST" action="{{ route('akhlak.downloadPdf') }}" target="_blank" id="pdfForm">
                            @csrf
                            <input type="hidden" name="chartImage" id="chartImageInput">
                            <input type="hidden" name="userId" id="userId" value="{{$userSelectedOpt}}">
                            <input type="hidden" name="year" id="year" value="{{$yearSelected}}">
                            <button id="downloadPdfButton" class="btn btn-primary btn-sm">Download PDF</button>
                        </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($userSelected)
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
                    <h3 class="text-center my-4 font-weight-bold">Summary Akhlak Report</h3>
                    <ul class="ml-4">
                        <li>Review the overall and specific user charts to ensure alignment with Akhlak BUMN values.</li>
                        <li>Ensure that all user data is presented accurately and reflects the principles of integrity and professionalism.</li>
                        <li>Unauthorized changes to user performance data or charts are not permitted to maintain data accuracy.</li>
                        <li>Thoroughly verify all data visualizations for accuracy and completeness before finalizing any reports or updates.</li>
                    </ul>
                    <div class="row pt-4">
                        <div class="col-md-6">
                            <section class="card shadow-none" style="border: 1px solid grey;">
                                <div class="card-header bg-login alt mb-4 p-4" style="background-image: url('{{ asset('img/kilang-minyak.png') }}');">
                                    <div class="media">
                                        <a href="#">
                                            <img class="align-self-center rounded-circle mr-3" style="width:85px; height:85px;" alt="" src="{{ url($userSelected->users_detail->profile_pic) }}">
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
                        <div class="col-md-6">
                            <div class="card shadow-none">
                                <div class="row">
                                    <div class="col-lg-12" id="charts">
                                        <div class="card-body d-flex justify-content-center align-items-center">
                                            <canvas id="radarChart" style="max-width: 100%; height: 500px; margin: auto;"></canvas>
                                        </div>
                                    </div>
                                </div> <!-- /.row -->
                            </div>
                        </div>
                    </div>
                    <h3 class="text-center my-4 font-weight-bold">Average Score (Quarterly)</h3>
                    <table class="table table-bordered mb-4">
                        <thead class="text-white">
                            <tr class="thead-light">
                                <th class="" rowspan="2" style="vertical-align: middle; text-align: center;">Core Values</th>
                                <th class="text-center" colspan="4">{{ $yearSelected ? $yearSelected : 'Periode' }}</th>
                            </tr>
                            <tr class="thead-light">
                                <th class="text-center">Quarter 1</th>
                                <th class="text-center">Quarter 2</th>
                                <th class="text-center">Quarter 3</th>
                                <th class="text-center">Quarter 4</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!$pencapaianByAkhlak || $pencapaianByAkhlak->isEmpty())
                                <tr class="text-center">
                                    <td colspan="5">No Data Available</td>
                                </tr>
                            @else
                                @foreach($pencapaianByAkhlak as $akhlakId => $quarters)
                                    <tr>
                                        <td class="font-weight-bold">{{ $quarters->first()->akhlak->indicator }}</td>
                                        <td>{{ $quarters->firstWhere('quarter_id', 1)->nilai_description ?? '-' }}</td>
                                        <td>{{ $quarters->firstWhere('quarter_id', 2)->nilai_description ?? '-' }}</td>
                                        <td>{{ $quarters->firstWhere('quarter_id', 3)->nilai_description ?? '-' }}</td>
                                        <td>{{ $quarters->firstWhere('quarter_id', 4)->nilai_description ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    <h3 class="text-center my-4 font-weight-bold">Detail Activities</h3>
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Kegiatan</th>
                                <th>Nilai Akhlak</th>
                                <th>Score</th>
                                <th>Core Value</th>
                                <th>Quarter</th>
                                <th>Periode</th>
                                <th>Evidence</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($allPencapaian->isEmpty())
                                <tr class="text-center">
                                    <td colspan="7">No Data Available</td> <!-- Use colspan to span across the entire row -->
                                </tr>
                            @else
                            @foreach ($allPencapaian as $item)
                            <tr>
                                <th>{{ $item->judul_kegiatan }}</th>
                                <td>{{ $item->scores->description }}</td>
                                <td>{{ $item->scores->score }}</td>
                                <td>{{ $item->akhlak->indicator }}</td>
                                <td>{{ $item->quarter->quarter_name }}</td>
                                <td>{{ $item->periode }}</td>
                                <td class="text-center">
                                    <a href="{{ $item->file->filepath }}" class="btn btn-outline-secondary btn-sm btn-details mr-2"><i class="fa fa-info-circle"></i> View</a>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                    @else
                    <p>Please select data in search report section...</p>
                    @endif
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
