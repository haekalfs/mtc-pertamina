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
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-2">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="ti-minus mr-2"></i> Preview Asset</h1>
        <p class="mb-3">Asset Detail Information.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('tool-inventory') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
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

<div class="row zoom90">
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold" id="judul">Detail Asset</h6>
                    </div>
                    <div class="card-body" style="position: relative;">
                        <a href="#" data-id="{{ $data->id }}" class="position-absolute edit-tool" style="top: 10px; right: 15px; z-index: 10;">
                            <i class="fa fa-edit fa-lg ml-2" style="color: rgb(181, 181, 181);"></i>
                        </a>
                        <div class="row mt-3 mb-3">
                            <div class="col-md-5 product_img d-flex justify-content-center align-items-center">
                                <img src="{{ $data->img->filepath ? asset($data->img->filepath) : asset('img/default-img.png') }}" class="img-responsive">
                            </div>
                            <div class="col-md-7 product_content">
                                <h3 class="card-title font-weight-bold">{{ $data->asset_name }}</h3>
                                <table class="ml-3 table table-borderless table-sm">
                                    <tr>
                                        <th style="width: 200px;">Asset ID</th>
                                        <td style="text-align: start; font-weight:500">: <span class="ml-3">{{ $data->asset_id }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Maker</th>
                                        <td style="text-align: start; font-weight:500">: <span class="ml-3">{{ $data->asset_maker }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Location</th>
                                        <td style="text-align: start; font-weight:500">: <span class="ml-3">{{ $data->location->description }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Initial Stock</th>
                                        <td style="text-align: start; font-weight:500">: <span class="ml-3">{{ $data->initial_stock  }} Units</span></td>
                                    </tr>
                                    <tr>
                                        <th>Used</th>
                                        <td style="text-align: start; font-weight:500">: <span class="ml-3">{{ $data->used_amount ? $data->used_amount : '0' }} Units</span></td>
                                    </tr>
                                    <tr>
                                        <th>Current Stock</th>
                                        <td style="text-align: start; font-weight:500">:
                                            <span class="ml-3">{{ $data->asset_stock }} Units
                                                @if($data->asset_stock < 5) <span class="position-absolute top-0 ml-1 start-100 translate-middle badge bg-danger text-white">!</span>@endif
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width: 200px;">Running Hours</th>
                                        @php
                                            $purchaseDate = \Carbon\Carbon::parse($data->last_maintenance);
                                            $currentDate = \Carbon\Carbon::now();
                                            $hoursDifference = $currentDate->diffInHours($purchaseDate);
                                        @endphp

                                        <td style="text-align: start; font-weight:500">: <span class="ml-3">{{ $hoursDifference }} hours</span></td>
                                    </tr>
                                    <tr>
                                        <th>Last Maintenance At</th>
                                        <td style="text-align: start; font-weight:500">: <span class="ml-3">{{ \Carbon\Carbon::parse($data->last_maintenance)->format('d-M-Y') }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Next Maintenance At</th>
                                        <td style="text-align: start; font-weight:500">: <span class="ml-3">{{ \Carbon\Carbon::parse($data->next_maintenance)->format('d-M-Y') }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>User Manual</th>
                                        <td style="text-align: start; font-weight:500">: @if($data->asset_guidance) <a href="{{ asset($data->asset_guidance) }}" target="_blank"><span class="ml-3 btn btn-sm btn-outline-secondary">Download File <i class="fa fa-download"></i></span></a> @else - @endif</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <span class="font-weight-bold">Asset Stocks Management</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered zoom90" id="listAsset" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Asset Code</th>
                                        <th>Asset Condition</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                </thead>@php $no = 1 @endphp
                                    @foreach($data->items as $asset)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td class="font-weight-bold text-secondary">{{ $asset->asset_code }}</td>
                                        <td><span class="ml-3">{{ $asset->condition->condition }}</span></td>
                                        <td>@if($asset->isUsed) <i class="fa fa-lock text-danger"></i> @else <i class="fa fa-unlock text-primary"></i> @endif</td>
                                        <td width='250' class="text-center">
                                            <a class="btn btn-sm btn-secondary mr-2 edit-asset text-white"
                                                data-asset-id="{{ $asset->id }}"
                                                data-asset-code="{{ $asset->asset_code }}"
                                                data-condition-id="{{ $asset->asset_condition_id }}"
                                                data-is-used="{{ $asset->isUsed }}"
                                                data-url-used="{{ route('inventory-tools.mark-as-used', $asset->id) }}"
                                                data-url-unused="{{ route('inventory-tools.mark-as-unused', $asset->id) }}">
                                                <i class="fa fa-fw fa-edit"></i> Edit
                                            </a>
                                            <a class="btn btn-sm btn-success mr-2 generateQR" data-id="{{ $asset->id }}" href="javascript:void(0)">
                                                <i class="fa fa-qrcode"></i> Generate QR
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                <tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4 shadow">
                    <div class="card-header">
                        <span class="font-weight-bold">Delete Asset</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <span>Deleting this asset is a permanent action and cannot be undone. If you are sure you want to delete this asset, select the button below.</span>
                        </div>
                        <div>
                            <a data-id="{{ $data->id }}" class="btn btn-outline-danger btn-md text-danger">I Understand, delete the asset</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card mb-4 shadow">
                    <div class="card-header">
                        <span class="font-weight-bold">Assets Conditions</span>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            @foreach($itemConditions as $condition)
                            <tr>
                                <td><i class="ti-minus mr-2"></i>{{ $condition['count'] }} Items are {!! $condition['condition'] !!}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <span class="text-danger font-weight-bold">Notes</span>
                    </div>
                    <div class="card-body" style="background-color: rgb(247, 247, 247);">
                        <h6 class="h6 mb-2 font-weight-bold text-gray-800">Asset Management Guidelines</h6>
                        <ul class="ml-4">
                            <li>Asset management impacts the overall inventory, reflecting changes directly in the system.</li>
                            <li>Assets must be managed carefully, with updates to conditions and statuses performed manually to ensure accuracy.</li>
                            <li>Legends :
                                <ul>
                                    <li><i class="fa fa-lock text-danger"></i> - Indicates the asset is currently in use and unavailable for allocation.</li>
                                    <li><i class="fa fa-unlock text-primary"></i> - Indicates the asset is available and ready for use.</li>
                                </ul>
                            </li>
                            <li>You can generate a QR Code for each asset, which encodes a link used for validating and verifying the asset information.</li>
                            <li>Editing asset conditions is possible by selecting the appropriate action in the "Action" column. The system will reflect the asset’s updated status immediately after modification.</li>
                            <li>The system displays a count of assets by their respective conditions, such as "Normal," "Damaged," or "Under Maintenance," giving you a quick overview of asset health.</li>
                            <li>All images uploaded for assets must meet the required format and size specifications to maintain system standards.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editConditionModal" tabindex="-1" role="dialog" aria-labelledby="editConditionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="editConditionModalLabel">Edit Asset Condition</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editConditionForm" action="{{ route('update-asset-condition') }}" method="POST">
                    @csrf
                    <!-- Hidden input for asset ID -->
                    <input type="hidden" name="asset_id" id="edit_asset_id">

                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 160px;" class="mr-2">
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
                </form>
            </div>
            <div class="modal-footer">
                <div class="d-flex align-items-center justify-content-start mr-auto">
                    <!-- Button will change dynamically based on the asset's condition -->
                    <div id="markAsUsedUnused">
                        <!-- The forms will be inserted dynamically here by JavaScript -->
                    </div>
                </div>
                <button type="button" class="btn btn-danger" id="deleteAssetBtn">Delete Asset</button>
                <button type="submit" form="editConditionForm" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="qrModalLabel">QR Code for <span id="qrAssetCode"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="printContainer">
                    <span id="qrTitle" class="p-2 font-weight-bold" style="margin-bottom: 10px;"></span>
                    <div id="qrCodeContainer"></div>
                </div>
                <a id="link" class="p-2 font-weight-bold">Click here</a>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button class="btn btn-primary" onclick="printModalContent()">Print QR</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade zoom90" id="editToolModal" tabindex="-1" role="dialog" aria-labelledby="editToolModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 1000px;" role="document">
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
                                            <div style="width: 170px;" class="mr-2">
                                                <p style="margin: 0;">Asset Name :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="asset_name" id="edit_asset_name" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 170px;" class="mr-2">
                                                <p style="margin: 0;">Nomor Asset :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="asset_number" id="edit_asset_number">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 170px;" class="mr-2">
                                                <p style="margin: 0;">Maker :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="maker" id="edit_maker">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 170px;" class="mr-2">
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
                                                    <div style="width: 170px;" class="mr-2">
                                                        <p style="margin: 0;">Used :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <input type="number" min="1" max="{{ $data->initial_stock  }}" class="form-control" name="used_amount" id="edit_used_amount">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 170px;" class="mr-2">
                                                <p style="margin: 0;">Last Maintenance :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="date" class="form-control" name="last_maintenance" id="edit_last_maintenance">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 170px;" class="mr-2">
                                                <p style="margin: 0;">Next Maintenance :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="date" class="form-control" name="next_maintenance" id="edit_next_maintenance">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start mb-2">
                                            <div style="width: 170px;" class="mr-2">
                                                <p style="margin: 0;">Update Panduan (Optional) :</p> <small id="existing-file"></small>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="file" class="form-control" name="maintenance_guide" id="edit_maintenance_guide">
                                                <small id="alias_help" class="help-block form-text text-danger">
                                                    Only pdf,word,excel file allowed!
                                                </small>
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
                $('#edit_initial_stock').val(response.initial_stock);
                $('#edit_used_amount').val(response.used_amount ? response.used_amount : '0');
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

        var routeUrl = "{{ route('update.asset', ':id') }}";
        routeUrl = routeUrl.replace(':id', toolId);

        $.ajax({
            url: routeUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Handle success dynamically
                alert(response.message);  // Show success message from server
                $('#editToolModal').modal('hide');
                location.reload();  // Reload the page
            },
            error: function(xhr) {
                // Handle error dynamically
                var errorMessage = xhr.responseJSON?.message || 'Failed to update the tool. Please try again.';
                alert(errorMessage);  // Show error message from server
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
                            window.location.href = "{{ route('tool-inventory') }}";
                        });
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = xhr.responseJSON && xhr.responseJSON.error
                            ? xhr.responseJSON.error
                            : "Oops! Something went wrong!";

                        swal("Cannot Delete Asset!", errorMessage, "error");
                    }
                });
            } else {
                swal("Your record is safe!");
            }
        });
    });

    $(document).ready(function() {
        $('.generateQR').click(function() {
            var assetId = $(this).data('id');
            $.ajax({
                url: '{{ route("generate-qr", "") }}/' + assetId,
                type: 'GET',
                success: function(response) {
                    $('#qrAssetCode').text(response.asset_code);
                    $('#qrTitle').text(response.asset_code);
                    $('#link').attr('href', response.link);  // Use .attr() to set the href attribute
                    $('#qrCodeContainer').html('<img src="' + response.qr_code + '" alt="QR Code" />');  // Embed the QR code as an image
                    $('#qrModal').modal('show');  // Show the modal
                },
                error: function() {
                    alert('Failed to generate QR code.');
                }
            });
        });
    });

    $(document).on('click', '.edit-asset', function() {
        var assetId = $(this).data('asset-id');  // Get asset ID
        var conditionId = $(this).data('condition-id');  // Get current condition ID
        var isUsed = $(this).data('is-used');  // Get the current used status
        var assetCode = $(this).data('asset-code');  // Get the asset code

        // Set asset ID in hidden input
        $('#edit_asset_id').val(assetId);

        // Set selected condition in the dropdown
        $('#edit_condition').val(conditionId);

        // Update the delete button with the correct asset ID
        $('#deleteAssetBtn').data('id', assetId);

        //give asset code
        $('#deleteAssetBtn').data('asset-code', assetCode);

        // Get route URLs from data attributes
        var usedRoute = $(this).data('url-used');
        var unusedRoute = $(this).data('url-unused');

        // Dynamically generate the Mark as Used/Unused buttons and forms
        var markAsButtonHtml = '';
        if (isUsed) {
            markAsButtonHtml = `
                <form action="${unusedRoute}" method="POST" style="display:inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class='btn btn-primary mr-2'>
                        <i class="fa fa-unlock"></i>
                    </button>
                </form>`;
        } else {
            markAsButtonHtml = `
                <form action="${usedRoute}" method="POST" style="display:inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class='btn btn-danger mr-2'>
                        <i class="fa fa-lock"></i>
                    </button>
                </form>`;
        }

        // Insert the buttons into the modal footer
        $('#markAsUsedUnused').html(markAsButtonHtml);

        // Show the modal
        $('#editConditionModal').modal('show');
    });

    $('#deleteAssetBtn').on('click', function() {
        var assetId = $(this).data('id');  // Get the asset ID dynamically
        var assetCode = $(this).data('asset-code');  // Get the asset code

        // Show SweetAlert confirmation dialog
        swal({
            title: "Delete Asset: " + assetCode + "?",
            text: "This will reduce the initial stocks and once deleted, you will not be able to recover this asset!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                // Perform AJAX request to delete the asset
                $.ajax({
                    url: '{{ route("inventory-tools.delete", ":id") }}'.replace(':id', assetId),  // Use route name dynamically
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Handle success case
                        if (response.success) {
                            swal("Success", response.message, "success").then(() => {
                                location.reload();  // Refresh the page after successful deletion
                            });
                        } else {
                            // Display error message from the server
                            swal("Error", response.message, "error");
                        }
                    },
                    error: function(xhr) {
                        // Handle different error statuses
                        if (xhr.status === 404) {
                            swal("Error!", "Asset not found.", "error");  // Asset not found
                        } else if (xhr.status === 403) {
                            swal("Error!", "Cannot delete asset. It is currently in use.", "error");  // Asset is in use
                        } else if (xhr.status === 400) {
                            var response = xhr.responseJSON;
                            swal("Error!", response.error, "error");  // Custom error message
                        } else {
                            swal("Error!", "An error occurred while trying to delete the asset.", "error");  // General error
                        }
                    }
                });
            }
        });
    });

    function printModalContent() {
        var printContent = document.getElementById('printContainer').innerHTML; // Get the content inside printContainer
        var modalTitle = document.getElementById('qrModalLabel').innerHTML; // Get the modal title

        // Open a new window for printing
        var printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>' + modalTitle + '</title>');

        // Optional: Add print-specific styling
        printWindow.document.write('<style>body { font-family: Arial, sans-serif; text-align: center; } h3 { font-size: 18px; margin-bottom: 20px; } .qr-code { margin: 20px auto; }</style>');
        printWindow.document.write('</head><body>');

        printWindow.document.write(printContent);
        printWindow.document.write('</body></html>');

        // Finalize the document and open print dialog
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close(); // Close after printing
    }
</script>
@endsection

