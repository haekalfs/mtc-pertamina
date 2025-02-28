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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">Data Assets </h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Asset</a>
                    </div>
                </div>
                <div class="card-body zoom90">
                    <form id="filterForm">
                        <div class="row d-flex justify-content-right mb-4">
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="locationFilter">Filter by Location:</label>
                                            <select class="form-control" id="locationFilter" name="locationFilter">
                                                <option value="-1" selected>Show All</option>
                                                @foreach ($locations as $item)
                                                    <option value="{{ $item->id }}" @if($item->id == $selectedLocation) selected @endif>{{ $item->description }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="conditionFilter">Filter by Condition:</label>
                                            <select name="conditionFilter" id="conditionFilter" class="form-control" required>
                                                <option value="-1" selected>Show All</option>
                                                @foreach($assetCondition as $item)
                                                <option value="{{ $item->id }}">{{ $item->condition }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex align-self-end justify-content-start">
                                        <div class="form-group">
                                            <div class="align-self-center">
                                                <button type="submit" class="btn btn-primary" style="padding-left: 1.2em; padding-right: 1.2em;">
                                                    <i class="ti-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table id="listInventory" class="table table-bordered mt-4">
                            <thead class="thead-light">
                                <tr>
                                    <th>Asset</th>
                                    <th>Stock</th>
                                    <th>Used</th>
                                    <th>Kondisi Alat</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade zoom90" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 1000px;" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('asset.store') }}" onsubmit="return validateForm('file-upload')">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="file-upload" style="cursor: pointer;">
                                <img id="image-preview" src="{{ asset('img/default-img.png') }}" style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="file-upload" type="file" name="tool_image" style="display: none;" accept="image/*" onchange="previewImage(event)">
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
                                                <input type="text" class="form-control" name="asset_name" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 170px;" class="mr-2">
                                                <p style="margin: 0;">Nomor Asset :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="asset_number" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 170px;" class="mr-2">
                                                <p style="margin: 0;">Maker :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="maker" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 170px;" class="mr-2">
                                                <p style="margin: 0;">Lokasi :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <select class="form-control" name="location" required>
                                                    @foreach ($locations as $item)
                                                        <option value="{{ $item->id }}">{{ $item->description }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 170px;" class="mr-2">
                                                        <p style="margin: 0;">Kondisi Alat :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <select name="condition" class="form-control" required>
                                                            @foreach($assetCondition as $item)
                                                            <option value="{{ $item->id }}">{{ $item->condition }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-center mb-4">
                                                    <div style="width: 170px;" class="mr-2">
                                                        <p style="margin: 0;">Stock :</p>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <input type="text" class="form-control" name="stock" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 170px;" class="mr-2">
                                                <p style="margin: 0;">Last Maintenance :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="date" class="form-control" name="last_maintenance" value="{{ date('Y-m-d') }}" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 170px;" class="mr-2">
                                                <p style="margin: 0;">Next Maintenance :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="date" class="form-control" name="next_maintenance" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start mb-4">
                                            <div style="width: 170px;" class="mr-2">
                                                <p style="margin: 0;">Panduan Maintenance :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="file" class="form-control" name="maintenance_guide">
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
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade zoom90" id="editToolModal" tabindex="-1" role="dialog" aria-labelledby="editToolModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 850px;" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="editToolModalLabel">Quick Edit Tool</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editToolForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="tool_id">
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="edit-file-upload" style="cursor: pointer;">
                                <img id="edit-image-preview" src="{{ asset('img/default-img.png') }}" style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                <small style="font-size: 10px;"><i><u>Editing image is Optional!</u></i></small>
                            </label>
                            <input id="edit-file-upload" type="file" name="tool_image" style="display: none;" accept="image/*" onchange="previewImageEditor(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" name="stock" id="edit_initial_stock" hidden>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 160px;" class="mr-1">
                                                <p style="margin: 0;">Asset In Used :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="input-group">
                                                    <!-- Left Icon -->
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text down" onclick="decreaseCount(event, this)">
                                                            <i class="fa fa-minus text-danger"></i>
                                                        </span>
                                                    </div>
                                                    <!-- Input Field -->
                                                    <input name="used_amount" id="edit_used_amount" type="text" min="0" class="form-control" value="0">
                                                    <!-- Right Icon -->
                                                    <div class="input-group-append">
                                                        <span class="input-group-text up" onclick="increaseCount(event, this)">
                                                            <i class="fa fa-plus text-success"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <u><a id="advance_edit_asset_page"><i>Advance Options</i></a></u>
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
<div class="modal fade zoom90" id="viewToolModal" tabindex="-1" role="dialog" aria-labelledby="viewToolModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="viewToolModalLabel">Asset Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mr-2 ml-2">
                <div class="row">
                    <div class="col-md-6 product_img">
                        <img id="view-image-preview" src="" class="img-responsive">
                    </div>
                    <div class="col-md-6 product_content">
                        <p><strong>Asset ID:</strong> <span id="view_asset_number"></span></p>
                        <p><strong>Asset Name:</strong> <span id="view_asset_name"></span></p>
                        <p><strong>Maker:</strong> <span id="view_maker"></span></p>
                        <p><strong>Location:</strong> <span id="view_location"></span></p>
                        <p><strong>Stock:</strong> <span id="view_stock"></span></p>
                        <p><strong>Used:</strong> <span id="view_used_amount"></span></p>
                        <p><strong>Last Maintenance:</strong> <span id="view_last_maintenance"></span></p>
                        <p><strong>Next Maintenance:</strong> <span id="view_next_maintenance"></span></p>
                        <p><strong>Guide:</strong> <span id="view_maintenance_guide"></span></p>
                        <div class="space-ten"></div>
                        <div class="btn-ground text-right">
                            <a class="btn btn-primary btn-md" id="edit_asset_page">Edit Asset Page</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var table = $('#listInventory').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('tool-inventory') }}",
                data: function (d) {
                    d.locationFilter = $('#locationFilter').val(); // Pass location filter
                    d.conditionFilter = $('#conditionFilter').val(); // Pass location filter
                }
            },
            columns: [
                { data: 'tool', name: 'tool', orderable: true, searchable: true },
                { data: 'stock', name: 'stock', orderable: true, searchable: true },
                { data: 'used', name: 'used', orderable: true, searchable: true },
                { data: 'condition', name: 'condition', orderable: false, searchable: true },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']] // Default ordering
        });

        $('#filterForm').on('submit', function (e) {
            e.preventDefault();
            table.draw(); // Redraw the table when the filter is applied
        });


        $('#editToolForm').on('submit', function(e) {
            e.preventDefault();

            var toolId = $('#tool_id').val();
            var formData = new FormData(this);

            var routeUrl = "{{ route('update.asset.partially', ':id') }}";
            routeUrl = routeUrl.replace(':id', toolId);

            $.ajax({
                url: routeUrl,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Show success alert dynamically
                    alert(response.message);
                    $('#editToolModal').modal('hide');
                    table.draw();
                },
                error: function(xhr) {
                    // Show error message dynamically
                    var errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to update the tool. Please check the input and try again.';
                    alert(errorMessage);
                }
            });
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
    }
}
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('image-preview');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
function previewImageEditor(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('edit-image-preview');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
<script>
$(document).on('click', '.view-tool', function(e) {
    e.preventDefault();
    var toolId = $(this).data('id');

    var routeUrl = "{{ route('preview-asset', ':id') }}";
    routeUrl = routeUrl.replace(':id', toolId);

    $.ajax({
        url: '/inventory-tools-view-info/' + toolId,
        method: 'GET',
        success: function(response) {
            // Populate the modal fields with the asset data
            $('#view_asset_name').text(response.asset_name);
            $('#view_asset_number').text(response.asset_id);
            $('#view_maker').text(response.asset_maker);
            $('#view_location').text(response.location);
            $('#view_stock').text(response.asset_stock + ' Items out of ' + response.initial_stock);
            $('#view_used_amount').text(response.used_amount ? response.used_amount + ' Items' : '0 Items');
            $('#view_last_maintenance').text(response.last_maintenance);
            $('#view_next_maintenance').text(response.next_maintenance);
            $('#view-image-preview').attr('src', response.tool_image ? response.tool_image : 'https://via.placeholder.com/150x150');
            $('#edit_asset_page').attr('href', routeUrl);

            // Handle guide download link
            if(response.asset_guidance) {
                $('#view_maintenance_guide').html(`<a href="${response.asset_guidance}" target="_blank"><i class="fa fa-download"></i><u> Download Guide</u></a>`);
            } else {
                $('#view_maintenance_guide').text('No guide available');
            }

            // Show the modal
            $('#viewToolModal').modal('show');
        }
    });
});
$(document).on('click', '.edit-tool', function(e) {
    e.preventDefault();
    var toolId = $(this).data('id');

    var routeUrl = "{{ route('preview-asset', ':id') }}";
    routeUrl = routeUrl.replace(':id', toolId);

    $.ajax({
        url: '/inventory-tools/' + toolId + '/edit',
        method: 'GET',
        success: function(response) {
            // Populate the fields with the tool data
            $('#tool_id').val(response.id);
            $('#edit_initial_stock').val(response.initial_stock);
            $('#edit_used_amount').val(response.used_amount ? response.used_amount : '0');
            $('#edit-image-preview').attr('src', response.tool_image ? response.tool_image : 'https://via.placeholder.com/150x150');
            $('#advance_edit_asset_page').attr('href', routeUrl);

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

function increaseCount(event, element) {
    // Find the input field within the same parent (input-group)
    var input = element.closest('.input-group').querySelector('input');
    var value = parseInt(input.value, 10);
    value = isNaN(value) ? 0 : value;
    value++;
    input.value = value;
}

function decreaseCount(event, element) {
    // Find the input field within the same parent (input-group)
    var input = element.closest('.input-group').querySelector('input');
    var value = parseInt(input.value, 10);
    if (value > 0) {
        value = isNaN(value) ? 0 : value;
        value--;
        input.value = value;
    }
}
function validateForm(...fileInputIds) {
    for (let i = 0; i < fileInputIds.length; i++) {
        const fileInput = document.getElementById(fileInputIds[i]);
        if (!fileInput || fileInput.files.length === 0) {
            alert(`Please upload an image for ${fileInputIds[i]} before submitting. Only JPEG, JPG, PNG & SVG Allowed!`);
            return false; // Prevent form submission
        }
    }
    return true; // Allow form submission if all file inputs have files
}

</script>
@endsection

