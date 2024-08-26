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
@if ($message = Session::get('out-of-stock'))
<div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('maintenance'))
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
                <div class="card-body zoom90">
                    <form method="GET" action="{{ route('tool-inventory') }}">
                        @csrf
                        <div class="row d-flex justify-content-right mb-4">
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="year">Filter :</label>
                                            <select class="form-control" name="locationFilter">
                                                <option value="-1" selected>Show All</option>
                                                @foreach ($locations as $item)
                                                    <option value="{{ $item->id }}" @if($item->id == $selectedLocation) selected @endif>{{ $item->description }}</option>
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
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered mt-4">
                            <thead>
                                <tr>
                                    <th>Tool</th>
                                    <th>Stock</th>
                                    <th>Used</th>
                                    <th>Kondisi Alat</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assets as $item)
                                <tr>
                                    <td data-th="Product">
                                        <div class="row">
                                            <div class="col-md-4 d-flex justify-content-center align-items-start mt-2">
                                                <img src="{{ asset($item->img->filepath) }}" style="height: 150px; width: 160px; border: 1px solid rgb(202, 202, 202);" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
                                            </div>
                                            <div class="col-md-8 text-left mt-sm-2">
                                                <h5 class="card-title font-weight-bold">{{ $item->asset_name }}</h5>
                                                <div class="ml-2">
                                                    <table class="table table-borderless table-sm">
                                                        <tr>
                                                            <td style="width: 200px;" class="mb-2"><i class="ti-minus mr-2"></i> Nomor Aset</td>
                                                            <td style="text-align: start;">: {{ $item->asset_id }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 200px;" class="mb-2"><i class="ti-minus mr-2"></i> Maker</td>
                                                            <td style="text-align: start;">: {{ $item->asset_maker }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 200px;" class="mb-2"><i class="ti-minus mr-2"></i> Running Hour</td>
                                                            <td style="text-align: start;">: {{ $item->used_time }} Hours</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 200px;" class="mb-2"><i class="ti-minus mr-2"></i> Next Maintenance</td>
                                                            <td style="text-align: start;">: {{ $item->next_maintenance }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 200px;" class="mb-2"><i class="ti-minus mr-2"></i> Last Maintenance</td>
                                                            <td style="text-align: start;">: {{ $item->last_maintenance }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 200px;" class="mb-2"><i class="ti-minus mr-2"></i> Panduan Maintenance</td>
                                                            <td style="text-align: start;">: &nbsp; <a href="{{ asset($item->asset_guidance) }}" target="_blank" class="text-secondary"><i class="fa fa-external-link fa-sm"></i> <u>View</u></a></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Price">
                                        @if($item->asset_stock)
                                            {{ $item->asset_stock }} @if($item->asset_stock > 1) Units @else Unit @endif
                                        @else
                                            0 Unit
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->used_amount)
                                            {{ $item->used_amount }} @if($item->used_amount > 1) Units @else Unit @endif
                                        @else
                                            0 Unit
                                        @endif
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
                                            <a data-id="{{ $item->id }}" href="#" class="btn btn-outline-secondary btn-md mb-2 mr-2 edit-tool">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button data-id="{{ $item->id }}" class="btn btn-outline-danger btn-md mb-2">
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
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="file-upload" style="cursor: pointer;">
                                <img id="image-preview" src="https://via.placeholder.com/50x50/5fa9f8/ffffff"
                                     style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                     <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
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
                                                <p style="margin: 0;">Lokasi :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <select class="form-control" name="location">
                                                    @foreach ($locations as $item)
                                                        <option value="{{ $item->id }}">{{ $item->description }}</option>
                                                    @endforeach
                                                </select>
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
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="edit-file-upload" style="cursor: pointer;">
                                <img id="edit-image-preview" src="https://via.placeholder.com/50x50/5fa9f8/ffffff"
                                     style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                     <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
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
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Lokasi :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <select class="form-control" id="edit_location" name="location">
                                                    @foreach ($locations as $item)
                                                        <option value="{{ $item->id }}">{{ $item->description }}</option>
                                                    @endforeach
                                                </select>
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
                                            <div class="col-md-7">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 200px;" class="mr-2">
                                                        <p style="margin: 0;">Initial Stock :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <input type="text" class="form-control" name="stock" id="edit_initial_stock">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 140px;" class="mr-2">
                                                        <p style="margin: 0;">Used :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <input type="text" class="form-control" name="used_amount" id="edit_used_amount">
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
                                        <div class="d-flex align-items-center mb-2">
                                            <div style="width: 250px;" class="mr-2">
                                                <p style="margin: 0;">Update Panduan (Optional) :</p> <small id="existing-file"></small>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="file" class="form-control" name="maintenance_guide" id="edit_maintenance_guide">
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
                $('#edit_location').val(response.location_id);
                $('#edit_last_maintenance').val(response.last_maintenance);
                $('#edit_next_maintenance').val(response.next_maintenance);
                $('#edit-image-preview').attr('src', response.tool_image ? response.tool_image : 'https://via.placeholder.com/150x150');

                // Handle the existing file
                if(response.asset_guidance) {
                    $('#existing-file').html(`<a href="${response.asset_guidance}" target="_blank">Download Existing File</a>`);
                } else {
                    $('#existing-file').html('No existing file');
                }

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

    $(document).on('click', '.btn-outline-danger', function(e) {
        e.preventDefault();
        let id = $(this).data('id');

        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this record!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: '{{ route("delete.asset", ":id") }}'.replace(':id', id),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(result) {
                        swal("Poof! Your record has been deleted!", {
                            icon: "success",
                        }).then(() => {
                            location.reload(); // Reload the page after success
                        });
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = xhr.responseJSON && xhr.responseJSON.error
                            ? xhr.responseJSON.error
                            : "Oops! Something went wrong!";

                        swal("Error!", errorMessage, "error");
                    }
                });
            } else {
                swal("Your record is safe!");
            }
        });
    });
</script>
@endsection

