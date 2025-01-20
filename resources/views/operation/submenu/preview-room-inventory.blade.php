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
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-2">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="ti-minus mr-2"></i> Preview Room Inventory</h1>
        <p class="mb-3">Room Detail Information.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('room-inventory') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
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
                <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-building-o"></i> Detail Ruangan</h6>
            </div>
            <div class="card-body" style="position: relative;">
                <a class="position-absolute" href="#" data-toggle="modal" data-target="#editDataModal" style="top: 10px; right: 15px; z-index: 10;">
                    <i class="fa fa-edit fa-lg ml-2" style="color: rgb(181, 181, 181);"></i>
                </a>
                <div class="col-md-12 mb-3">
                    <div class="row">
                        <div class="col-md-3 d-flex align-items-center justify-content-center" style="padding-left: 1em;">
                            <img src="{{ $data->filepath ? asset($data->filepath) : asset('img/default-img.png') }}" style="height: 150px; width: 200px; border: 1px solid rgb(202, 202, 202);" class="img-fluid d-none d-md-block rounded mb-2 shadow ">
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th style="width: 200px;">Nama Ruangan</th>
                                    <td style="text-align: start; font-weight:500">: <span class="ml-3">{{ $data->room_name }}</span></td>
                                </tr>
                                <tr>
                                    <th style="width: 200px;">Lokasi</th>
                                    <td style="text-align: start; font-weight:500">: <span class="ml-3">{{ $data->location->description }}</span></td>
                                </tr>
                                <tr>
                                    <th>Jumlah Asset</th>
                                    <td style="text-align: start; font-weight:500">: <span class="ml-3">{{ $data->list->sum('amount') }} Items</span></td>
                                </tr>
                                <tr>
                                    <th>Last Updated At</th>
                                    <td style="text-align: start; font-weight:500">: <span class="ml-3">{{ $data->updated_at }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="animated fadeIn zoom90">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">List Assets</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Add New Assets</a>
                    </div>
                </div>
                <div class="card-body">
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

<div class="modal fade" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('room.insert.item', $data->id) }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-12">
                            <div>
                                <div class="document-list-item mb-4 mt-3">
                                    <div class="d-flex align-items-start">
                                        <div style="width: 180px;" class="mr-2">
                                            <p style="margin: 0;">List Alat :</p>
                                        </div>
                                        <div class="flex-grow-1 textarea-container" id="documents-list-container">
                                            <div class="document-item mb-2">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <select class="form-control mb-2" name="tool[]" required>
                                                            @foreach ($assets as $item)
                                                                <option value="{{ $item->id }}">{{ $item->asset_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ml-4 text-white">
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
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 900px;" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="editDataModalLabel">Edit Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('room.data.update', $data->id) }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="file-upload" style="cursor: pointer;">
                                <img id="image-preview" src="{{ $data->filepath ? asset($data->filepath) : asset('img/default-img.png') }}" style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                     <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="file-upload" type="file" name="room_image" style="display: none;" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <div class="col-md-9">
                            <div>
                                <div class="document-list-item mb-4 mt-3">
                                    <div class="d-flex align-items-center mb-4">
                                        <div style="width: 140px;" class="mr-2">
                                            <p style="margin: 0;">Nama Ruangan :</p>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control" name="nama_ruangan" value="{{ $data->room_name }}" required/>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-4">
                                        <div style="width: 140px;" class="mr-2">
                                            <p style="margin: 0;">Lokasi :</p>
                                        </div>
                                        <div class="flex-grow-1">
                                            <select class="form-control" name="location">
                                                @foreach ($locations as $item)
                                                    <option value="{{ $item->id }}" {{ $item->id == $data->location_id ? 'selected' : '' }}>{{ $item->description }}</option>
                                                @endforeach
                                            </select>
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
                url: "{{ route('preview-room', $data->id) }}",
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
$(document).on('click', '.view-tool', function(e) {
    e.preventDefault();
    var toolId = $(this).data('id');

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
</script>
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
            var clonedContainer = $("<div class='document-item mb-2'></div>").append(clonedDocumentItem.html());

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


    $('#listInventory').on('click', '.delete-item', function () {
        let id = $(this).data('id'); // Get the ID of the inventory_room

        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this record!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: '{{ route("delete.item.room", ":id") }}'.replace(':id', id),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (result) {
                        swal("Poof! Your record has been deleted!", {
                            icon: "success",
                        });
                        $('#listInventory').DataTable().ajax.reload(); // Reload the DataTable
                    },
                    error: function (xhr, status, error) {
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

    $(document).on('click', '.update-amount', function(e) {
        e.preventDefault();
        let toolId = $(this).data('id');
        let quantity = $('input[name="amount_' + toolId + '"]').val();

        // Confirmation using SweetAlert
        swal({
            title: "Are you sure?",
            text: "Do you want to update the quantity?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willUpdate) => {
            if (willUpdate) {
                $.ajax({
                    url: '{{ route("room.item.update", ":id") }}'.replace(':id', toolId),
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        amount: quantity
                    },
                    success: function(response) {
                        swal("Success!", "Utility usage updated successfully.", "success")
                            .then(() => {
                                location.reload(); // Reload the page after success
                            });
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : "Oops! Something went wrong!";

                        swal("Error!", errorMessage, "error");
                    }
                });
            }
        });
    });
</script>
@endsection

