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
<style>
    .badge-container {
        display: flex;
        justify-content: space-around;
        align-items: center;
        flex-wrap: wrap;
    }

    .badge-item {
        text-align: center;
        width: 120px; /* Set a fixed width */
    }

    .badge-item img {
        width: 85px;
        height: 85px;
        border-radius: 50%;
    }

    .badge-label {
        margin-top: 5px;
        display: block; /* Ensure the label is displayed as a block element */
        word-wrap: break-word; /* Ensure long words break to the next line */
    }

    .card-body-modified {
        transition: background-color 0.3s;
    }

    .card-body-modified.inactive {
        background-color: rgb(220, 220, 220);
    }

    .card-body-modified.active {
        background-color: rgb(255, 255, 255);
        border: 4px solid #57a0ff;
        border-radius: 12px; /* Add border-radius to round the border */
    }
</style>
</style>
<div class="animated fadeIn zoom90">
    <div class="row">


        <div class="col-lg-4">
            <div class="card">
                <div class="card-body card-body-modified" id="cardBody2024">
                    <div id="chartContainer2024" style="height: 300px; width: 100%; margin-bottom: 20px;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body card-body-modified" id="cardBody2023">
                    <div id="chartContainer2023" style="height: 300px; width: 100%; margin-bottom: 20px;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body card-body-modified" id="cardBody2022">
                    <div id="chartContainer2022" style="height: 300px; width: 100%; margin-bottom: 20px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="progress-box-modified progress-1" style="font-size: 20px;">
                        <h4 class="por-title">Realisasi Pencapaian</h4>
                        <div class="por-txt" style="font-size: 15px;">Target : {{ $kpiItem->target }}</div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 40%; font-size: 15px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">24%</div>
                        </div>
                    </div>
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
                    <table id="listPencapaian" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Pencapaian</th>
                                <th>Quarter</th>
                                <th>Periode</th>
                                <th>Target Tercapai</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Table rows will be inserted dynamically -->
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
                            <label for="pencapaian">Pencapaian</label>
                            <input type="text" class="form-control" id="pencapaian" name="pencapaian" placeholder="Average Test Score..." required>
                        </div>
                        <div class="form-group">
                            <label for="score">Score Tercapai <small class="text-danger"><i>(in percentage)</i></small></label>
                            <input type="text" class="form-control" id="score" name="score" placeholder="85%" required>
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
<?php
$data2024 = array(
    array("label" => "Q1", "y" => 30),
    array("label" => "Q2", "y" => 40),
    array("label" => "Q3", "y" => 50),
    array("label" => "Q4", "y" => 60)
);

$data2023 = array(
    array("label" => "Q1", "y" => 10),
    array("label" => "Q2", "y" => 20),
    array("label" => "Q3", "y" => 30),
    array("label" => "Q4", "y" => 40)
);

$data2022 = array(
    array("label" => "Q1", "y" => 20),
    array("label" => "Q2", "y" => 30),
    array("label" => "Q3", "y" => 40),
    array("label" => "Q4", "y" => 50)
);
?>
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Chart for 2024 Data
        var chart2024 = new CanvasJS.Chart("chartContainer2024", {
            backgroundColor: "transparent",
            animationEnabled: true,
            theme: "light2",
            title: {
                fontSize: 20,
                margin: 30,
                text: "Pencapaian Tahun 2024"
            },
            axisY: {
                maximum: 100,
                stripLines: [{
                    value: {{ $kpiItem->target }},
                    label: "Target {{ $kpiItem->target }}%",
                    labelAlign: "center",
                    labelFontColor: "#FF0000",
                    color: "#FF0000",
                    thickness: 2
                }]
            },
            data: [{
                type: "column",
                yValueFormatString: "#,##0\"%\"",
                dataPoints: <?php echo json_encode($data2024, JSON_NUMERIC_CHECK); ?>
            }]
        });
        chart2024.render();

        // Chart for 2023 Data
        var chart2023 = new CanvasJS.Chart("chartContainer2023", {
            backgroundColor: "transparent",
            animationEnabled: true,
            theme: "light2",
            title: {
                fontSize: 20,
                margin: 30,
                text: "Pencapaian Tahun 2023"
            },
            axisY: {
                suffix: "%",
                maximum: 100,
                stripLines: [{
                    value: {{ $kpiItem->target }},
                    label: "Target {{ $kpiItem->target }}%",
                    labelAlign: "center",
                    labelFontColor: "#FF0000",
                    color: "#FF0000",
                    thickness: 2
                }]
            },
            data: [{
                type: "column",
                yValueFormatString: "#,##0\"%\"",
                dataPoints: <?php echo json_encode($data2023, JSON_NUMERIC_CHECK); ?>
            }]
        });
        chart2023.render();

        // Chart for 2022 Data
        var chart2022 = new CanvasJS.Chart("chartContainer2022", {
            backgroundColor: "transparent",
            animationEnabled: true,
            theme: "light2",
            title: {
                fontSize: 20,
                margin: 30,
                text: "Pencapaian Tahun 2022"
            },
            axisY: {
                suffix: "%",
                maximum: 100,
                stripLines: [{
                    value: {{ $kpiItem->target }},
                    label: "Target {{ $kpiItem->target }}%",
                    labelAlign: "center",
                    labelFontColor: "#FF0000",
                    color: "#FF0000",
                    thickness: 2
                }]
            },
            data: [{
                type: "column",
                yValueFormatString: "#,##0\"%\"",
                dataPoints: <?php echo json_encode($data2022, JSON_NUMERIC_CHECK); ?>
            }]
        });
        chart2022.render();

        const tableData = @json($tableData);
        let dataTable = $('#listPencapaian').DataTable();

        function setActiveCard(cardId, year) {
            const cards = document.querySelectorAll('.card-body');
            cards.forEach(card => {
                card.classList.remove('active');
                card.classList.add('inactive');
            });
            const activeCard = document.getElementById(cardId);
            activeCard.classList.remove('inactive');
            activeCard.classList.add('active');

            // Update the table title and content
            document.getElementById('judul').innerText = `List Pencapaian - ${year}`;
            const tableBody = document.querySelector('#listPencapaian tbody');
            tableBody.innerHTML = '';
            // Pass the CSRF token and delete route to JavaScript
            const csrfToken = '{{ csrf_token() }}';
            const deleteRoute = '{{ route('pencapaian.kpi.destroy', '') }}'; // Route without the ID

            if (tableData[year]) {
                tableData[year].forEach((item, index) => {
                    const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.pencapaian}</td>
                        <td>${item.quarter_id}</td>
                        <td>${item.periode}</td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: ${item.score ?? 0}%;"
                                     aria-valuenow="${item.score ?? 0}" aria-valuemin="0" aria-valuemax="100">${item.score ?? 0}%
                                </div>
                            </div>
                        </td>
                        <td class="text-center" width='240px'>
                            <a href="#" class="btn btn-outline-secondary btn-sm mr-2"><i class="ti-eye"></i> Edit</a>
                            <a href="#" class="btn btn-outline-danger btn-sm btn-details" onclick="confirmDelete(${item.no});"><i class="fa fa-ban"></i> Delete</a>
                            <form id="delete-pencapaian-kpi-${item.no}" action="${deleteRoute}/${item.no}" method="POST" style="display: none;">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="_method" value="DELETE">
                            </form>
                        </td>
                    </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });

                // Refresh the DataTable
                dataTable.clear().rows.add($(tableBody).find('tr')).draw();
            } else {
                // If there is no data for the selected year
                dataTable.clear().draw();
            }
        }

        document.getElementById('cardBody2024').addEventListener('click', () => setActiveCard('cardBody2024', '2024'));
        document.getElementById('cardBody2023').addEventListener('click', () => setActiveCard('cardBody2023', '2023'));
        document.getElementById('cardBody2022').addEventListener('click', () => setActiveCard('cardBody2022', '2022'));

        // Set the initial active card
        setActiveCard('cardBody2024', '2024');
    });
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
