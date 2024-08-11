@extends('layouts.main')

@section('active-operation')
active font-weight-bold
@endsection

@section('show-operation')
show
@endsection

@section('tool-requirement-penlat')
font-weight-bold
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-check-square-o"></i> Penlat Requirement</h1>
        <p class="mb-4">Kebutuhan Penlat.</a></p>
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
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Penlat</a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered mt-4">
                        <thead>
                            <tr>
                                <th>Tool</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td data-th="Product">
                                    <div class="row">
                                        <div class="col-md-3 justify-content-center align-items-center text-center">
                                            <img src="https://via.placeholder.com/150x150/5fa9f8/ffffff" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow ">
                                        </div>
                                        <div class="col-md-9 text-left mt-sm-2">
                                            <h5 class="card-title font-weight-bold">Nama Penlat</h5>
                                            <ul class="ml-4">
                                                <li class="card-text">Kebutuhan Tool 1</li>
                                                <li class="card-text">Kebutuhan Tool 2</li>
                                                <li class="card-text">Kebutuhan Tool 3</li>
                                                <li class="card-text">Kebutuhan Tool 4</li>
                                                <li class="card-text">Kebutuhan Tool 5</li>
                                                <li class="card-text"><i>Show More...</i></li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                                <td class="actions text-center" data-th="">
                                    <div>
                                        <button class="btn btn-white border-secondary bg-white btn-md mb-2">
                                            <i class="fa fa-save"></i>
                                        </button>
                                        <button class="btn btn-white border-secondary bg-white btn-md mb-2">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('kpis.store') }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center" style="padding-top: 10px;">
                            <img src="https://via.placeholder.com/50x50/5fa9f8/ffffff" style="height: 150px; width: 150px; border-radius: 15px;" class="card-img" alt="...">
                        </div>
                        <div class="col-md-9">
                            <div>
                                <div class="document-list-item mb-4 mt-3">
                                    <div class="d-flex align-items-start">
                                        <div style="width: 130px;" class="mr-2">
                                            <p style="margin: 0;">Kebutuhan :</p>
                                        </div>
                                        <div class="flex-grow-1 textarea-container" id="documents-list-container">
                                            <div class="document-item">
                                                <input type="text" class="form-control mb-2" rows="2" name="documents[]" required></input>
                                            </div>
                                        </div>
                                        <div class="ml-2 text-white">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-success mb-2 shadow-sm btn-sm add-document-list"><i class="fa fa-plus"></i> Add More &nbsp;&nbsp;</button>
                                            </div>
                                            <div class="col-md-12">
                                                <a class="btn shadow-sm btn-sm btn-danger delete-document-list" style="display: none;"><i class="fa fa-trash-alt"></i> Delete Item</a>
                                            </div>
                                        </div>
                                    </div>
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

<div class="modal fade" id="editDataModal" tabindex="-1" role="dialog" aria-labelledby="editDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="editDataModalLabel">Edit Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('kpis.store') }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center" style="padding-top: 10px;">
                            <img src="https://via.placeholder.com/50x50/5fa9f8/ffffff" style="height: 150px; width: 150px; border-radius: 15px;" class="card-img" alt="...">
                        </div>
                        <div class="col-md-9">
                            <div>
                                <div class="document-list-item mb-4 mt-3">
                                    <div class="d-flex align-items-start">
                                        <div style="width: 140px;" class="mr-2">
                                            <p style="margin: 0;">Kebutuhan :</p>
                                        </div>
                                        <div class="flex-grow-1 textarea-container" id="documents-list-container">
                                            <div class="document-item">
                                                <input type="text" class="form-control mb-2" rows="2" name="documents[]" value="Kebutuhan 1" required/>
                                            </div>
                                        </div>
                                        <div class="ml-2 text-white">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-success mb-2 shadow-sm btn-sm add-document-list"><i class="fa fa-plus"></i> Add More &nbsp;&nbsp;</button>
                                            </div>
                                            <div class="col-md-12">
                                                <a class="btn shadow-sm btn-sm btn-danger delete-document-list" style="display: none;"><i class="fa fa-trash-alt"></i> Delete Item</a>
                                            </div>
                                        </div>
                                    </div>
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
    $(document).ready(function () {
        // Handle click event for the "Add More" button
        $(".add-document-list").on("click", function () {
            // Clone the entire document-item div
            var clonedDocumentItem = $(".document-item:first").clone();

            // Clear the content of the cloned textarea and file input
            clonedDocumentItem.find("textarea").val("");
            clonedDocumentItem.find("input[type=file]").val("");

            // Create a new container for the cloned document-item div
            var clonedContainer = $("<div class='document-item'></div>").append(clonedDocumentItem.html());

            // Append the new container to the container
            $("#documents-list-container").append(clonedContainer);

            // Show the delete button when there are multiple items
            $(".delete-document-list").show();
        });

        // Handle click event for the "Delete Item" button
        $(".delete-document-list").on("click", function () {
            // Remove the last cloned container when the delete button is clicked
            $(".document-item:last").remove();

            // Hide the delete button if there's only one item left
            if ($(".document-item").length <= 1) {
                $(".delete-document-list").hide();
            }
        });
    });
</script>
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

