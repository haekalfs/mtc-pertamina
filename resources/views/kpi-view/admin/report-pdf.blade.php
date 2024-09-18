<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KPI Report</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"> --}}

    <style>
        @page{
            margin-top: 80px;
        }

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


    <div class="col-md-12">
        <h6 class="h6 mb-2 font-weight-bold text-gray-800">Guidelines for KPI Realization</h6>
        <ul class="ml-4">
            <li><strong>KPI Realization Overview:</strong> This section provides an overview of the Key Performance Indicators (KPIs) realization for Pertamina MTC. It focuses on evaluating the performance and achievements of various KPIs, highlighting areas of success and identifying opportunities for improvement. The goal is to assess how well the targets have been met and to provide insights for strategic planning and decision-making within the organization.</li>
        </ul>
    </div>
    <!-- Report Content -->
    <h1 class="text-center my-4">KPI Report Summary</h1>

    <table class="table table-bordered mb-4">
        <thead class="text-white">
            <tr>
                <th class="bg-primary" rowspan="3" style="vertical-align: middle; text-align: center;">Indicator</th>
                <th class="bg-primary" colspan="8">2024</th>
            </tr>
            <tr>
                <th class="bg-primary" colspan="2">Quarter 1</th>
                <th class="bg-primary" colspan="2">Quarter 2</th>
                <th class="bg-primary" colspan="2">Quarter 3</th>
                <th class="bg-primary" colspan="2">Quarter 4</th>
            </tr>
            <tr>
                <th class="bg-primary">Tercapai</th>
                <th class="bg-primary">%</th>
                <th class="bg-primary">Tercapai</th>
                <th class="bg-primary">%</th>
                <th class="bg-primary">Tercapai</th>
                <th class="bg-primary">%</th>
                <th class="bg-primary">Tercapai</th>
                <th class="bg-primary">%</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $metric => $quarters)
                <tr>
                    <td>{{ $metric }}</td>
                    @foreach($quarters as $quarter)
                        <td>{{ number_format($quarter['Tercapai'], 0, ',', '.') }}</td>
                        <td>{{ number_format($quarter['%'], 1) }}%</td>
                    @endforeach
                </tr>
            @endforeach
            <tr class="bg-secondary text-white">
                <td>Overall KPI</td>
                <th colspan="2">{{ number_format($overallKPI['Q1'], 1) }}%</th>
                <th colspan="2">{{ number_format($overallKPI['Q2'], 1) }}%</th>
                <th colspan="2">{{ number_format($overallKPI['Q3'], 1) }}%</th>
                <th colspan="2">{{ number_format($overallKPI['Q4'], 1) }}%</th>
            </tr>
        </tbody>
    </table>


    <div class="col-md-12">
        <div class="card-body d-flex align-items-center justify-content-center">
            {{-- circle progress bar --}}
            <h4 class="mb-2">Realisasi KPI Overall</h4>
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
    </div>

    <!-- Include the chart image -->
    <div class="text-center mt-4">
        <img src="{{ $chartImage }}" alt="KPI Chart" style="width: 100%; height: 100%; object-fit: contain;">
    </div>

</body>
</html>
