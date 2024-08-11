@extends('layouts.main')

@section('active-operation')
active font-weight-bold
@endsection

@section('show-operation')
show
@endsection

@section('tool-inventory')
font-weight-bold
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="menu-icon fa fa-fire-extinguisher"></i> Tool Inventory</h1>
        <p class="mb-4">Inventaris Alat/Assets.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        {{-- <a href="{{ route('tool-usage') }}" class="btn btn-sm btn-primary shadow-sm text-white"> Penggunaan Alat</a> --}}
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
    .drop-zone {
    border: 2px dashed #ccc;
    padding: 10px;
    text-align: center;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.3s;
    /* Smooth transition for animations */
}

.drop-zone.dragging {
    background-color: #e0e0e0;
    transform: scale(1.05);
    animation: pulse 1s infinite;
    /* Adds a subtle scale effect while dragging */
}

.drop-zone.clicked {
    animation: clickEffect 0.5s ease-out;
    /* Triggers a quick animation when clicked */
}

@keyframes pulse {
    0% {
        transform: scale(1);
        border-color: #ccc;
    }
    50% {
        transform: scale(1.05);
        border-color: #007bff;
    }
    100% {
        transform: scale(1);
        border-color: #ccc;
    }
}

@keyframes clickEffect {
    0% {
        transform: scale(1);
        background-color: #e0e0e0;
    }
    50% {
        transform: scale(1.1);
        background-color: #d0d0d0;
    }
    100% {
        transform: scale(1);
        background-color: #e0e0e0;
    }
}
</style>
<div class="animated fadeIn zoom90">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="menu-icon fa fa-fire-extinguisher"></i> Data Assets MTC</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Tool</a>
                    </div>
                </div>
                {{-- <div class="row-toolbar mt-4 ml-2">
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
                </div> --}}
                <div class="card-body zoom90">
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered mt-4">
                            <thead>
                                <tr>
                                    <th>Tool</th>
                                    <th>Stock</th>
                                    <th>Kondisi Alat</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assets as $item)
                                <tr>
                                    <td data-th="Product">
                                        <div class="row">
                                            <div class="col-md-3 text-left">
                                                <img src="{{ asset($item->img->filepath) }}" style="height: 150px; width: 150px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow ">
                                            </div>
                                            <div class="col-md-9 text-left mt-sm-2">
                                                <h5 class="card-title font-weight-bold">{{ $item->asset_name }}</h5>
                                                <ul class="ml-4">
                                                    <li class="card-text mb-1">Nomor Aset : {{ $item->asset_id }}</li>
                                                    <li class="card-text mb-1">Maker : {{ $item->asset_maker }}</li>
                                                    <li class="card-text mb-1">Running Hour : {{ $item->used_time }} Hours</li>
                                                    <li class="card-text mb-1">Jadwal Maintenance Next : {{ $item->next_maintenance }}</li>
                                                    <li class="card-text mb-1">Last Maintenance : {{ $item->last_maintenance }}</li>
                                                    <li class="card-text mb-1">Panduan Maintenance : <a href="{{ asset($item->asset_guidance) }}" target="_blank" class="text-secondary">&nbsp;&nbsp;<i class="fa fa-external-link fa-sm"></i> <u>View</u></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Price">
                                        {{ $item->asset_stock }} <small><span class="text-danger"><i>Out of {{ $item->initial_stock }}</i></span></small>
                                    </td>
                                    <td data-th="Quantity">
                                        {!! $item->condition->badge !!}<br>
                                        @php
                                            // Convert $item->next_maintenance to a timestamp
                                            $nextMaintenanceDate = strtotime($item->next_maintenance);
                                            $currentDate = strtotime(date('Y-m-d'));
                                        @endphp

                                        @if($nextMaintenanceDate < $currentDate)
                                            <span class="badge out-of-stock">Maintenance Required</span><br>
                                        @endif

                                        @if($item->asset_stock <= 0)
                                            <span class="badge out-of-stock">Out of Stock</span><br>
                                        @endif
                                    </td>
                                    <td class="actions text-center" data-th="">
                                        <div>
                                            <a data-id="{{ $item->id }}" href="#" class="btn btn-outline-secondary btn-md mb-2 edit-tool">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button class="btn btn-outline-danger btn-md mb-2">
                                                <i class="fa fa-trash-o"></i>
                                            </button>
                                        </div>
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
</div>

<div class="modal fade zoom90" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('asset.store') }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center">
                            <label for="file-upload" style="cursor: pointer;">
                                <img id="image-preview" src="https://via.placeholder.com/50x50/5fa9f8/ffffff"
                                     style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #000;" class="card-img" alt="..."><br>
                                     <small><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="file-upload" type="file" name="tool_image" style="display: none;" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Asset Name :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="asset_name" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Nomor Asset :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="asset_number">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Maker :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="maker">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Running Hour :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="running_hour">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 140px;" class="mr-2">
                                                        <p style="margin: 0;">Kondisi Alat :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <select name="condition" class="form-control">
                                                            @foreach($assetCondition as $item)
                                                            <option value="{{ $item->id }}">{{ $item->condition }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 140px;" class="mr-2">
                                                        <p style="margin: 0;">Stock :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <input type="text" class="form-control" name="stock">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 145px;" class="mr-1">
                                                <p style="margin: 0;">Last Maintenance :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="date" class="form-control" name="last_maintenance">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 145px;" class="mr-1">
                                                <p style="margin: 0;">Next Maintenance :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="date" class="form-control" name="next_maintenance">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 200px;" class="mr-2">
                                                <p style="margin: 0;">Panduan Maintenance :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="file" class="form-control" name="maintenance_guide" required>
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
<div class="modal fade zoom90" id="editToolModal" tabindex="-1" role="dialog" aria-labelledby="editToolModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="editToolModalLabel">Edit Tool</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editToolForm" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="tool_id">
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center">
                            <label for="edit-file-upload" style="cursor: pointer;">
                                <img id="edit-image-preview" src="https://via.placeholder.com/50x50/5fa9f8/ffffff"
                                     style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #000;" class="card-img" alt="..."><br>
                                     <small><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="edit-file-upload" type="file" name="tool_image" style="display: none;" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Asset Name :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="asset_name" id="edit_asset_name" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Nomor Asset :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="asset_number" id="edit_asset_number">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Maker :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="maker" id="edit_maker">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Running Hour :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="running_hour" id="edit_running_hour">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 140px;" class="mr-2">
                                                        <p style="margin: 0;">Kondisi Alat :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <select name="condition" id="edit_condition" class="form-control">
                                                            @foreach($assetCondition as $item)
                                                            <option value="{{ $item->id }}">{{ $item->condition }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 290px;" class="mr-2">
                                                        <p style="margin: 0;">Initial Stock :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <input type="text" class="form-control" name="stock" id="edit_initial_stock">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 290px;" class="mr-2">
                                                        <p style="margin: 0;">Used :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <input type="text" class="form-control" name="stock" id="edit_used_amount">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 145px;" class="mr-1">
                                                <p style="margin: 0;">Last Maintenance :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="date" class="form-control" name="last_maintenance" id="edit_last_maintenance">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 145px;" class="mr-1">
                                                <p style="margin: 0;">Next Maintenance :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="date" class="form-control" name="next_maintenance" id="edit_next_maintenance">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 200px;" class="mr-2">
                                                <p style="margin: 0;">Panduan Maintenance :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="file" class="form-control" name="maintenance_guide" id="edit_maintenance_guide" required>
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
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
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
        }
    }


    function addImagePreviewListener(fileInput, imgPreview, discardButton, dropZone, dropZoneText) {
        // Function to display the image preview and hide the text
        function displayImagePreview(src) {
            imgPreview.src = src;
            imgPreview.style.display = "block";
            discardButton.style.display = "inline-block";
            dropZoneText.style.display = "none";
        }
</script>
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('image-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
<script>
    $(document).on('click', '.edit-tool', function(e) {
        e.preventDefault();
        var toolId = $(this).data('id');

        $.ajax({
            url: '/inventory-tools/' + toolId + '/edit',
            method: 'GET',
            success: function(response) {
                // Populate the fields with the tool data
                $('#tool_id').val(response.id);
                $('#edit_asset_name').val(response.asset_name);
                $('#edit_asset_number').val(response.asset_id);
                $('#edit_maker').val(response.asset_maker);
                $('#edit_running_hour').val(response.used_time);
                $('#edit_condition').val(response.asset_condition_id);
                $('#edit_initial_stock').val(response.initial_stock);
                $('#edit_used_amount').val(response.used_amount);
                $('#edit_last_maintenance').val(response.last_maintenance);
                $('#edit_next_maintenance').val(response.next_maintenance);
                $('#edit-image-preview').attr('src', response.tool_image ? response.tool_image : 'https://via.placeholder.com/150x150');
                // Show the modal
                $('#editToolModal').modal('show');
            }
        });
    });

    $('#editToolForm').on('submit', function(e) {
        e.preventDefault();

        var toolId = $('#tool_id').val();
        var formData = new FormData(this);

        $.ajax({
            url: '/inventory-tools/' + toolId,
            method: 'POST', // Use POST since we're sending FormData with files
            data: formData,
            processData: false, // Prevent jQuery from processing the data
            contentType: false, // Prevent jQuery from setting the content type
            success: function(response) {
                // Handle success (e.g., close modal, show success message, refresh table)
                $('#editToolModal').modal('hide');
                alert('Tool updated successfully');
                location.reload(); // Reload the page or update the table dynamically
            },
            error: function(xhr) {
                // Handle errors
                alert('Failed to update the tool. Please check the input and try again.');
            }
        });
    });
</script>
@endsection

