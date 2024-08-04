@extends('layouts.main')

@section('active-operation')
active font-weight-bold
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-check-square-o"></i> Penlat Requirement</h1>
        <p class="mb-4">Unduh Pencapaian Akhlak.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
    </div>
</div>
<div class="overlay overlay-mid" style="display: none;"></div>

<div class="alert alert-danger alert-success-delete-mid" role="alert" style="display: none;">
</div>

<div class="alert alert-success alert-success-saving-mid" role="alert" style="display: none;">
    Your entry has been saved successfully.
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
.alert-success-saving-mid {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 20px;
    border-radius: 5px;
    text-align: center;
    z-index: 10000;
}

.custom-card {
    border: none;
    color: white;
    border-radius: 15px;
}

.card-text {
    margin-bottom: 0;
    color: rgb(0, 0, 0);
}

.out-of-stock {
    background-color: #dc3545;
    color: white;
    font-weight: bold;
}

.card-icons {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-icons .badge {
    background-color: red;
    color: rgb(255, 255, 255);
}

.card-icons i {
    font-size: 20px;
    color: rgb(0, 0, 0);
}

.card-icons a {
    color: rgb(0, 0, 0);
    text-decoration: none;
}

.card-icons a:hover {
    color: lightgray;
}

.img-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
}

.img-container img {
    max-height: 100%;
    max-width: 100%;
    object-fit: cover;
}

</style>
<div class="animated fadeIn zoom90">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> List Data</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Penlat</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row-toolbar">
                        <div class="col">
                            <select style="max-width: 13%;" class="form-control" id="rowsPerPage">
                                <option disabled>Jenis Penlat</option>
                                <option value="-1" selected>Show All</option>
                            </select>
                        </div>
                        <div class="col-auto text-right">
                            <input class="form-control" type="text" id="searchInput" placeholder="Search...">
                        </div>
                    </div>
                    <div class="row zoom80 p-2">
                        <div class="col-md-6 mt-2">
                            <div class="card custom-card mb-3 bg-white shadow">
                                <div class="row no-gutters">
                                    <div class="col-md-4 d-flex align-items-center justify-content-center" style="padding-left: 1em;">
                                        <img src="{{ asset('img/kilang-minyak.jpg') }}" style="border-radius: 15px;" class="card-img" alt="...">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body text-secondary">
                                            <div>
                                                <h5 class="card-title font-weight-bold">Nama Penlat</h5>
                                                <ul class="ml-3">
                                                    <li class="card-text">Kebutuhan Tool 1</li>
                                                    <li class="card-text">Kebutuhan Tool 2</li>
                                                    <li class="card-text">Kebutuhan Tool 3</li>
                                                    <li class="card-text">Kebutuhan Tool 4</li>
                                                    <li class="card-text">Kebutuhan Tool 5</li>
                                                    <li class="card-text"><i>Show More...</i></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-icons">
                                            <a href="#"><i class="fa fa-cog"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function displayFileName() {
        const input = document.getElementById('file');
        const label = document.getElementById('file-label');
        const file = input.files[0];
        if (file) {
            label.textContent = file.name;
            previewExcel(file);
        }
    }
    function previewExcel(file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const data = new Uint8Array(event.target.result);
            const workbook = XLSX.read(data, {type: 'array'});
            const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
            const excelRows = XLSX.utils.sheet_to_json(firstSheet, {header: 1});
            displayExcelData(excelRows);
        };
        reader.readAsArrayBuffer(file);
    }

    function displayExcelData(rows) {
        const previewDiv = document.getElementById('excel-preview');
        previewDiv.innerHTML = '';

        if (rows.length === 0) {
            previewDiv.textContent = 'No data found in the file.';
            return;
        }

        const table = document.createElement('table');
        table.className = 'table table-bordered';
        const thead = document.createElement('thead');
        const tbody = document.createElement('tbody');

        rows.forEach((row, index) => {
            const tr = document.createElement('tr');
            row.forEach(cell => {
                const td = document.createElement(index === 0 ? 'th' : 'td');
                td.textContent = cell;
                tr.appendChild(td);
            });
            if (index === 0) {
                thead.appendChild(tr);
            } else {
                tbody.appendChild(tr);
            }
        });

        table.appendChild(thead);
        table.appendChild(tbody);
        previewDiv.appendChild(table);
    }
</script>
@endsection

