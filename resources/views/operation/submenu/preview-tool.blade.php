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
    <div class="col-xl-12 col-lg-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-fire-extinguisher"></i> Detail Asset</h6>
            </div>
            <div class="card-body" style="position: relative;">
                <a href="#" data-id="{{ $data->id }}" class="position-absolute edit-tool" style="top: 10px; right: 15px; z-index: 10;">
                    <i class="fa fa-edit fa-lg ml-2" style="color: rgb(181, 181, 181);"></i>
                </a>
                <div class="row">
                    <div class="col-md-4 product_img">
                        <img src="{{ $data->img->filepath ? asset($data->img->filepath) : asset('img/default-img.png') }}" class="img-responsive">
                    </div>
                    <div class="col-md-8 product_content">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <th style="width: 200px;">Asset Name</th>
                                <td style="text-align: start; font-weight:500">: <span class="ml-3">{{ $data->asset_name }}</span></td>
                            </tr>
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
                                <th>Condition</th>
                                <td style="text-align: start; font-weight:500">: <span class="ml-3">{!! $data->condition->badge !!}</span></td>
                            </tr>
                            <tr>
                                <th>Stock</th>
                                <td style="text-align: start; font-weight:500">: <span class="ml-3">{{ $data->asset_stock }} <small class="ml-2">(Initial Stock : {{ $data->initial_stock }})</small></span></td>
                            </tr>
                            <tr>
                                <th>Used</th>
                                <td style="text-align: start; font-weight:500">: <span class="ml-3">{{ $data->used_amount ? $data->used_amount : '0' }}</span></td>
                            </tr>
                            <tr>
                                <th>Last Maintenance At</th>
                                <td style="text-align: start; font-weight:500">: <span class="ml-3">{{ $data->last_maintenance }}</span></td>
                            </tr>
                            <tr>
                                <th>Next Maintenance At</th>
                                <td style="text-align: start; font-weight:500">: <span class="ml-3">{{ $data->next_maintenance }}</span></td>
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
                                <th>Guide</th>
                                <td style="text-align: start; font-weight:500">: <a href="{{ asset($data->asset_guidance) }}" target="_blank"><span class="ml-3 btn btn-sm btn-outline-secondary">Download Existing File <i class="fa fa-download"></i></span></a></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-4 shadow">
            <div class="card-header">
                <span class="text-danger font-weight-bold">Delete Asset</span>
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
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <span class="text-primary font-weight-bold">Asset Management Guidelines</span>
            </div>
            <div class="card-body" style="background-color: rgb(247, 247, 247);">
                <h6 class="h6 mb-2 font-weight-bold text-gray-800">Overview</h6>
                <p>The Asset Management menu provides essential sub-menus for viewing and editing assets within the system:</p>
                <ul class="ml-4">
                    <li><strong>View Assets</strong>: This menu allows authorized personnel to access a detailed overview of all assets in the system. It includes current stock, asset status, and other relevant information for effective management.</li>
                    <li><strong>Edit Assets</strong>: This menu enables authorized users to make modifications to asset details, such as stock levels, asset descriptions, and status updates. Ensuring accurate and up-to-date information is crucial for effective asset management.</li>
                </ul>
                <h6 class="h6 mb-2 font-weight-bold text-gray-800">Key Points</h6>
                <ul class="ml-4">
                    <li>Access to both viewing and editing menus is restricted to authorized personnel only to maintain data integrity and security.</li>
                    <li>All edits made to asset details should be carefully reviewed to ensure accuracy and reflect the most current status.</li>
                    <li>Regularly update asset information to ensure it aligns with actual stock levels, conditions, and availability.</li>
                    <li>Ensure that asset data is backed up and audited regularly to maintain accuracy and compliance with organizational policies.</li>
                </ul>
            </div>
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
                $('#edit_condition').val(response.asset_condition_id);
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
                            window.location.href = "{{ route('tool-inventory') }}";
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

