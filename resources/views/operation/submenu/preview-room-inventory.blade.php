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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> List Data</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Add New Assets</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="listUtilities" class="table table-bordered mt-4">
                            <thead>
                                <tr>
                                    <th>Tool</th>
                                    <th>Quantity</th>
                                    <th>Satuan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data->list as $tool)
                                    <tr>
                                        <td data-th="Product">
                                            <div class="row">
                                                <div class="col-md-3 text-left">
                                                    <img src="{{ asset($tool->tools->img->filepath) }}" style="height: 100px; width: 100px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow ">
                                                </div>
                                                <div class="col-md-8 text-left mt-sm-2">
                                                    <h5>{{ $tool->tools->asset_name }}</h5>
                                                    {{-- <p class="font-weight-light">Satuan Default ({{$tool->tools->utility_unit}})</p> --}}
                                                </div>
                                            </div>
                                        </td>
                                        <td data-th="Quantity" style="width:10%">
                                            <input type="number" class="form-control form-control-md text-center underline-input" name="amount_{{ $tool->id }}" value="{{ $tool->amount }}">
                                        </td>
                                        <td data-th="Price" style="width:10%" class="text-center">
                                            Pcs
                                        </td>
                                        <td class="actions text-center">
                                            <button class="btn btn-outline-secondary btn-sm mr-2 update-amount" data-id="{{ $tool->id }}">
                                                <i class="fa fa-save"></i> Update
                                            </button>
                                            <a href="{{ route('delete.item.room', $tool->id) }}" class="btn btn-outline-danger btn-sm text-danger">
                                                <i class="fa fa-trash-o"></i> Delete
                                            </a>
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
                                                    <div class="col-md-10">
                                                        <select class="form-control mb-2" name="tool[]" required>
                                                            @foreach ($assets as $item)
                                                                <option value="{{ $item->id }}">{{ $item->asset_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2 p-0">
                                                        <input type="number" class="form-control" name="amount[]" placeholder="Pcs" required></input>
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

