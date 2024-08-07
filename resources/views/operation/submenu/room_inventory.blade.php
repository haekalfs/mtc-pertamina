@extends('layouts.main')

@section('active-operation')
active font-weight-bold
@endsection

@section('show-operation')
show
@endsection

@section('room-inventory')
font-weight-bold
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-building-o"></i> Room Inventory</h1>
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
<div class="animated fadeIn zoom90">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> List Data</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Room</a>
                    </div>
                </div>
                <div class="row-toolbar mt-4 ml-2">
                    <div class="col">
                        <select style="max-width: 18%;" class="form-control" id="rowsPerPage">
                            <option value="-1">Show All</option>
                            @foreach($locations as $item)
                            <option value="{{ $item->id }}">{{ $item->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto text-right mr-2">
                        <input class="form-control" type="text" id="searchInput" placeholder="Search...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="row zoom80">
                        <div class="col-md-6 mt-2">
                            <div class="card custom-card mb-3 bg-white shadow">
                                <div class="row no-gutters">
                                    <div class="col-md-3 d-flex align-items-center justify-content-center" style="padding-left: 1em;">
                                        <img src="https://via.placeholder.com/50x50/5fa9f8/ffffff" style="height: 150px; width: 150px; border-radius: 15px;" class="card-img" alt="...">
                                    </div>
                                    <div class="col-md-9">
                                        <div class="card-body text-secondary">
                                            <div>
                                                <h5 class="card-title font-weight-bold">Nama Ruangan</h5>
                                                <ul class="ml-4">
                                                    <li class="card-text">Jumlah Kursi : 1</li>
                                                    <li class="card-text">Jumlah Meja : 1</li>
                                                    <li class="card-text">Jumlah Kelas : 1</li>
                                                    <li class="card-text">Jumlah Ruangan : 1</li>
                                                    <li class="card-text">Jumlah Simulator : 1</li>
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
                    <div class="col-md-12 text-center d-flex align-items-center justify-content-center mt-4">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination">
                              <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                              <li class="page-item"><a class="page-link" href="#">1</a></li>
                              <li class="page-item"><a class="page-link" href="#">2</a></li>
                              <li class="page-item"><a class="page-link" href="#">3</a></li>
                              <li class="page-item"><a class="page-link" href="#">Next</a></li>
                            </ul>
                          </nav>
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

