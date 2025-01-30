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
<style>
    /* Dropdown container */
    .dropdown {
        display: inline-block;
        position: relative;
    }

    /* Select input */
    .dropdown select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 5px 10px;
        font-size: 14px;
        cursor: pointer;
    }

    /* Apply button */
    .apply-btn {
        margin-left: 10px;
        padding: 5px 10px;
        font-size: 14px;
        color: #fff;
        background-color: #007bff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .apply-btn:hover {
        background-color: #0056b3;
    }
</style>
<div class="row zoom90">
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-secondary">Detail Asset</h6>
                    </div>
                    <div class="card-body" style="position: relative;">
                        <a href="#" data-id="{{ $data->id }}" class="position-absolute edit-tool" style="top: 10px; right: 15px; z-index: 10;">
                            <i class="fa fa-edit fa-lg ml-2" style="color: rgb(181, 181, 181);"></i>
                        </a>
                        <div class="row mt-3 mb-3">
                            <div class="col-md-5 product_img d-flex justify-content-center align-items-center">
                                <img src="{{ $data->img->filepath ? asset($data->img->filepath) : asset('img/default-img.png') }}" style="height: 350px; width: 400px" class="img-responsive">
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
                                    {{-- <tr>
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
                                    </tr> --}}
                                    <tr>
                                        <th>User Manual</th>
                                        <td style="text-align: start; font-weight:500">: @if($data->asset_guidance) <a href="{{ asset($data->asset_guidance) }}" target="_blank"><span class="ml-3 btn btn-sm btn-outline-secondary">Download File <i class="fa fa-download"></i></span></a> @else <span class="ml-3"> - </span> @endif</td>
                                    </tr>
                                    <tr>
                                        <th>Created By</th>
                                        <td style="text-align: start; font-weight:500">: <span class="ml-3">{{ $data->created_by ? $data->user->name : '-' }}</span></td>
                                    </tr>
                                </table>
                            </div>
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
                        <span class="font-weight-bold text-secondary">Delete Asset</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <span>Deleting this asset is a permanent action and cannot be undone.</span>
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
                        <span class="font-weight-bold text-secondary">Assets Conditions</span>
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
        </div>
    </div>
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-secondary">Asset Stocks Management</h6>
                <div class="text-right">
                    <input type="text" name="formId" id="formId" value="" />
                </div>
            </div>
            <div class="card-body">
                <div class="row d-flex justify-content-start mb-4">
                    <div class="col-md-12">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <div class="dropdown">
                                    <select id="bulkActions">
                                        <option value="1">Set as Used</option>
                                        <option value="2">Set as Unused</option>
                                        <option value="3">Maintenance Update</option>
                                        <option value="4">Change Conditions</option>
                                    </select>
                                    <button class="apply-btn">Execute</button>
                                </div>
                            </div>
                            <div class="col-md-9 text-right">
                                <span id="selectedCountBadge" class="badge bg-secondary text-white" style="display: none;">-</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered zoom90" id="assetsTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" width="80px">
                                    <div class="form-check form-check-inline larger-checkbox" style="transform: scale(1.5);">
                                        <input class="form-check-input" type="checkbox" id="checkAll" onclick="toggleCheckboxes()">
                                    </div>
                                </th>
                                <th>Asset Code</th>
                                <th>Asset Condition</th>
                                <th>Status</th>
                                <th>Last Maintenance</th>
                                <th>Running Hours</th>
                                <th>Next Maintenance</th>
                                <th width="150px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded dynamically via AJAX -->
                        </tbody>
                    </table>
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
            <div class="modal-body ml-2 mr-2">
                <form id="editConditionForm" action="{{ route('update-asset-condition') }}" method="POST">
                    @csrf
                    <!-- Hidden input for asset ID -->
                    <input type="hidden" name="asset_id" id="edit_asset_id">

                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 160px;" class="mr-2">
                            <p style="margin: 0;">Asset Condition :</p>
                        </div>
                        <div class="flex-grow-1">
                            <select name="condition" id="edit_condition" class="form-control">
                                @foreach($assetCondition as $item)
                                <option value="{{ $item->id }}">{{ $item->condition }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 160px;" class="mr-2">
                            <p style="margin: 0;">Last Maintenance :</p>
                        </div>
                        <div class="flex-grow-1">
                            <input type="date" class="form-control" name="lastMaintenance" id="edit_lastMaintenance">
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 160px;" class="mr-2">
                            <p style="margin: 0;">Next Maintenance :</p>
                        </div>
                        <div class="flex-grow-1">
                            <input type="date" class="form-control" name="nextMaintenance" id="edit_nextMaintenance">
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 160px;" class="mr-2">
                            <p style="margin: 0;">Status :</p>
                        </div>
                        <div class="flex-grow-1">
                            <select name="status" id="edit_status" class="form-control">
                                <option value="1">In Used</option>
                                <option value="0">Available</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
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
                                <img id="edit-image-preview" src="{{ asset('img/default-img.png') }}" style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="edit-file-upload" type="file" name="tool_image" style="display: none;" accept="image/*" onchange="previewEditImage(event)">
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

    function previewEditImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('edit-image-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
<script>
    $(document).ready(function () {
        var tableAssets = $('#assetsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('preview-asset', $data->id) }}", // Route to fetch data
            columns: [
                {
                    data: 'checkbox',
                    name: 'checkbox',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function (data, type, row) {
                        return `
                            <label class="switch switch-3d switch-primary mr-3" style="transform: scale(1.5);">
                                <input type="checkbox" class="switch-input status-checkbox data-checkbox" data-form-id="${row.id}">
                                <span class="switch-label"></span>
                                <span class="switch-handle"></span>
                            </label>
                        `;
                    }
                },
                { data: 'asset_code', name: 'asset_code' },
                { data: 'condition', name: 'condition' },
                {
                    data: 'status',
                    name: 'status',
                    className: 'text-center',
                    render: function (data, type, row) {
                        return row.isUsed == true
                            ? '<i class="fa fa-lock text-danger"></i> Used'
                            : 'Available';
                    }
                },
                { data: 'lastMaintenance', name: 'lastMaintenance' },
                { data: 'runningHours', name: 'runningHours' },
                { data: 'nextMaintenance', name: 'nextMaintenance' },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function (data, type, row) {
                        return `
                            <a class="btn btn-outline-secondary btn-md mr-2 edit-asset"
                                data-asset-id="${row.id}"
                                data-asset-code="${row.asset_code}"
                                data-condition-id="${row.asset_condition_id}"
                                data-last-maintenance="${row.lastMaintenance}"
                                data-next-maintenance="${row.nextMaintenance}"
                                data-is-used="${row.isUsed}"
                                data-url-used="${row.urlUsed}"
                                data-url-unused="${row.urlUnused}">
                                <i class="fa fa-fw fa-edit"></i>
                            </a>
                            <a class="btn btn-outline-success btn-md mr-2 generateQR" data-id="${row.id}" href="javascript:void(0)">
                                <i class="fa fa-qrcode"></i>
                            </a>
                        `;
                    }
                }
            ]
        });

        $('#assetsTable').on('click', '.generateQR', function () {
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

        tableAssets.on('draw', function () {
            $('#assetsTable').on('change', '.data-checkbox', toggleCheckboxes2);
            $('#checkAll').on('change', toggleCheckboxes);
        });
    });

    // Event listener for individual checkboxes
    document.querySelectorAll('.data-checkbox').forEach((checkbox) => {
        checkbox.addEventListener('change', toggleCheckboxes2);
    });


    document.querySelector('.apply-btn').addEventListener('click', () => {
        const action = document.getElementById('bulkActions').value;
        const formIds = document.getElementById('formId').value;

        if (formIds === '') {
            swal({
                title: "No items selected!",
                text: "Please select at least one items to proceed.",
                icon: "warning",
                button: "OK",
            });
            return;
        }

        if (action === "1") {
            swal({
                title: "Processing...",
                text: "Your request is being processed.",
                icon: "info",
                buttons: false,
                closeOnClickOutside: false,
            });

            // Automatically close the swal after 5 seconds
            setTimeout(() => swal.close(), 1000);

            // Send AJAX request to process the selected action
            $.ajax({
                url: "{{ route('set.used') }}",
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                data: {
                    formIds: formIds,
                },
                success: function (response) {
                    if (response.success) {
                        // Trigger file download
                        $('#assetsTable').DataTable().draw();
                    } else {
                        swal({
                            title: "Error!",
                            text: response.message || "Something went wrong while processing the request.",
                            icon: "error",
                            button: "OK",
                        });
                    }
                },
                error: function (xhr, status, error) {
                    swal({
                        title: "Error!",
                        text: "There was an error processing the request: " + xhr.responseText,
                        icon: "error",
                        button: "OK",
                    });
                },
            });
        } else if (action === "2") {
            swal({
                title: "Processing...",
                text: "Your request is being processed.",
                icon: "info",
                buttons: false,
                closeOnClickOutside: false,
            });

            // Automatically close the swal after 5 seconds
            setTimeout(() => swal.close(), 1000);

            // Send AJAX request to process the selected action
            $.ajax({
                url: "{{ route('set.unused') }}",
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                data: {
                    formIds: formIds,
                },
                success: function (response) {
                    if (response.success) {
                        // Trigger file download
                        $('#assetsTable').DataTable().draw();
                    } else {
                        swal({
                            title: "Error!",
                            text: response.message || "Something went wrong while processing the request.",
                            icon: "error",
                            button: "OK",
                        });
                    }
                },
                error: function (xhr, status, error) {
                    swal({
                        title: "Error!",
                        text: "There was an error processing the request: " + xhr.responseText,
                        icon: "error",
                        button: "OK",
                    });
                },
            });
        } else if (action === "3") {
            swal({
                title: "Set Last Maintenance Date",
                text: "Please select the last maintenance date:",
                content: {
                    element: "input",
                    attributes: {
                        type: "date",
                        id: "lastMaintenanceDate"
                    }
                },
                icon: "info",
                buttons: ["Cancel", "Next"],
            }).then((lastMaintenanceDate) => {
                if (!lastMaintenanceDate) return;

                swal({
                    title: "Set Next Maintenance Date",
                    text: "Please select the next maintenance date:",
                    content: {
                        element: "input",
                        attributes: {
                            type: "date",
                            id: "nextMaintenanceDate"
                        }
                    },
                    icon: "info",
                    buttons: ["Cancel", "Proceed"],
                }).then((nextMaintenanceDate) => {
                    if (!nextMaintenanceDate) return;

                    swal({
                        title: "Processing...",
                        text: `Marking maintenance dates as Last: ${lastMaintenanceDate}, Next: ${nextMaintenanceDate}`,
                        icon: "info",
                        buttons: false,
                        closeOnClickOutside: false,
                    });

                    // Send AJAX request
                    $.ajax({
                        url: "{{ route('maintenance.update') }}",
                        method: "POST",
                        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                        data: {
                            formIds: formIds,
                            lastMaintenanceDate: lastMaintenanceDate,
                            nextMaintenanceDate: nextMaintenanceDate,
                        },
                        success: function (response) {
                            swal({
                                title: "Success!",
                                text: "The maintenance dates have been set.",
                                icon: "success",
                                button: "OK",
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function (xhr) {
                            swal({
                                title: "Error!",
                                text: "There was an error processing the request: " + xhr.responseText,
                                icon: "error",
                                button: "OK",
                            });
                        },
                    });
                });
            });
        } else if (action === "4") {
            let assetConditionOptions = `@foreach($assetCondition as $item)
                <option value="{{ $item->id }}">{{ $item->condition }}</option>
            @endforeach`;

            swal({
                title: "Select Asset Condition",
                content: (function () {
                    let div = document.createElement("div");
                    let select = document.createElement("select");
                    select.className = "swal-select form-control";
                    select.innerHTML = assetConditionOptions;
                    div.appendChild(select);
                    return div;
                })(),
                icon: "info",
                buttons: ["Cancel", "Submit"],
            }).then((value) => {
                let selectedCondition = document.querySelector(".swal-select").value;
                if (!selectedCondition) return;

                swal({
                    title: "Processing...",
                    text: `Updating asset condition to: ${selectedCondition}`,
                    icon: "info",
                    buttons: false,
                    closeOnClickOutside: false,
                });

                // Send AJAX request
                $.ajax({
                    url: "{{ route('updateConditions') }}",
                    method: "POST",
                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                    data: {
                        formIds: formIds,
                        assetCondition: selectedCondition,
                    },
                    success: function (response) {
                        swal({
                            title: "Success!",
                            text: "The asset condition has been updated.",
                            icon: "success",
                            button: "OK",
                        }).then(() => {
                            $('#assetsTable').DataTable().draw();
                        });
                    },
                    error: function (xhr) {
                        swal({
                            title: "Error!",
                            text: "There was an error processing the request: " + xhr.responseText,
                            icon: "error",
                            button: "OK",
                        });
                    },
                });
            });
        } else {
            // Handle other actions normally
            console.log(`Action: ${action}, Form IDs: ${formIds}`);
            // Add further logic if needed for other actions
        }
    });

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

    $(document).on('click', '.edit-asset', function() {
        var assetId = $(this).data('asset-id');  // Get asset ID
        var conditionId = $(this).data('condition-id');  // Get current condition ID
        var isUsed = $(this).data('is-used');  // Get the current used status
        var assetCode = $(this).data('asset-code');  // Get the asset code
        var lastMaintenance = $(this).data('last-maintenance');  // Get the asset code
        var nextMaintenance = $(this).data('next-maintenance');  // Get the asset code

        // Set asset ID in hidden input
        $('#edit_asset_id').val(assetId);

        // Set selected condition in the dropdown
        $('#edit_condition').val(conditionId);
        $('#edit_lastMaintenance').val(lastMaintenance);
        $('#edit_nextMaintenance').val(nextMaintenance);
        $('#edit_status').val(isUsed);

        // Update the delete button with the correct asset ID
        $('#deleteAssetBtn').data('id', assetId);

        // Give asset code
        $('#deleteAssetBtn').data('asset-code', assetCode);

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

    function confirmMarkAsUsed(usedRoute, assetCode) {
        swal({
            title: "Mark asset " + assetCode + " to be used?",
            text: "This will reduce the available stocks.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willMark) => {
            if (willMark) {
                // Create a form and submit it
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = usedRoute;
                form.innerHTML = '@csrf @method("PATCH")';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function confirmMarkAsUnused(unusedRoute, assetCode) {
        swal({
            title: "Mark asset " + assetCode + " to be unused?",
            text: "This will increase the stocks that available to use.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willMark) => {
            if (willMark) {
                // Create a form and submit it
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = unusedRoute;
                form.innerHTML = '@csrf @method("PATCH")';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function updateSelectedCount(count) {
        const badge = document.getElementById('selectedCountBadge');
        if (count > 0) {
            badge.style.display = 'inline-block';
            badge.textContent = `${count} selected`;
        } else {
            badge.style.display = 'none';
        }
    }

    function toggleCheckboxes() {
        const checkboxes = document.querySelectorAll('.data-checkbox');
        const checkAllCheckbox = document.getElementById('checkAll');
        const formIdInput = document.getElementById('formId');

        const checkedFormIds = [];

        checkboxes.forEach((checkbox) => {
            checkbox.checked = checkAllCheckbox.checked;
            if (checkbox.checked) {
                const formId = checkbox.getAttribute('data-form-id');
                checkedFormIds.push(formId);
            }
        });

        formIdInput.value = checkedFormIds.join(', ');

        // Update badge with the count of selected checkboxes
        updateSelectedCount(checkedFormIds.length);
    }

    function toggleCheckboxes2() {
        const checkboxes = document.querySelectorAll('.data-checkbox');
        const formIdInput = document.getElementById('formId');

        const checkedFormIds = [];

        checkboxes.forEach((checkbox) => {
            if (checkbox.checked) {
                const formId = checkbox.getAttribute('data-form-id');
                checkedFormIds.push(formId);
            }
        });

        formIdInput.value = checkedFormIds.join(', ');

        // Update badge with the count of selected checkboxes
        updateSelectedCount(checkedFormIds.length);
    }

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

