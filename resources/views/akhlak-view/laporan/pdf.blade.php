<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AKHLAK Report</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        @page{
            margin-top: 80px;
        }
        /* Apply Poppins font to the entire document */
        body {
            font-family: 'Poppins', sans-serif;
        }
        .parent {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: 1fr;
            grid-column-gap: 0px;
            grid-row-gap: 0px;
        }

        .div1 { grid-area: 1 / 2 / 2 / 3; }
        .div2 { grid-area: 1 / 1 / 2 / 2; }
        header{
            left: 0px;
            right: 0px;
            height: 60px;
            margin-top: -60px;
            margin-bottom: 100px;
            font-family: 'Poppins', sans-serif;
        }

        /* Simplified styles for the letterhead */
        .letterhead {
            background-color: #dd2476;
            padding: 20px;
            color: white;
            border-bottom: 5px solid #ff512f;
        }
        .letterhead .logo {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .letterhead .report-title {
            font-size: 1.25rem;
            margin-top: 0;
        }
        table {
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .bg-primary {
            background-color: #929292;
            color: white;
        }
    </style>
</head>
<body>

    <header class="row">
        <table>
            <tr>
                <td style="border: none; padding-left: 50px; padding-right: 15px;"><img src="https://ccdjayaekspres.id/MTC.png" style="height: 130px; width: 130px;" alt="" class=""></td>
                <td style="border: none;">
                    <address>
                        <strong>Pertamina Maritime Training Center</strong><br>

                        Jl. Pemuda No.44, RT.2/RW.4, Jati, Kec. Pulo Gadung, <br>
                        Kota Jakarta Timur, DKI Jakarta 13220
                    </address>
                </td>
            </tr>
        </table>
        <hr style="border-top: 3px solid black; margin-top:-2%">
    </header>
    <!-- Simplified Letterhead -->

    <h2 class="text-center my-4 font-weight-bold">Summary AKHLAK Report</h2>
    <div class="parent">
        <div class="div2 text-center">
            <img src="{{ $chartImage }}" alt="KPI Chart" style="width: 100%;">
        </div>
    </div>

    <h2 class="text-center my-4 font-weight-bold">Avg. Quarterly Report</h2>
    <table class="table table-bordered mb-4">
        <thead class="text-white">
            <tr class="thead-light">
                <th class="" rowspan="2" style="vertical-align: middle; text-align: center;">Core Values</th>
                <th class="text-center" colspan="4">{{ $year ? $year : 'Periode' }}</th>
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
    <h2 class="text-center my-4 font-weight-bold">Detail Activities</h2>
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
            @foreach ($allPencapaian as $item)
            <tr>
                <th>{{ $item->judul_kegiatan }}</th>
                <td>{{ $item->scores->description }}</td>
                <td>{{ $item->scores->score }}</td>
                <td>{{ $item->akhlak->indicator }}</td>
                <td>{{ $item->quarter->quarter_name }}</td>
                <td>{{ $item->periode }}</td>
                <td class="text-center">
                    <a href="http://localhost:8111/" class="btn btn-outline-secondary btn-sm btn-details mr-2"><i class="fa fa-info-circle"></i> View</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <h2 class="text-center my-4 font-weight-bold">Overall Score</h2>
    <section class="card shadow-none">
        <div class="media">
            <div class="media-body">
                <h3 class="text-white display-6 mt-1">{{ $userSelected->name }}</h3>
                <p class="text-white">Nomor Pekerja : {{ $userSelected->users_detail->employee_id }}</p>
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
</body>
</html>
