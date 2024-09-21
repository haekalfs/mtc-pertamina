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
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-2">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-check-square-o"></i> Penlat Requirement</h1>
        <p class="mb-4">Kebutuhan Penlat.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('tool-requirement-penlat') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
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
                <h6 class="m-0 font-weight-bold text-secondary" id="judul">Detail Kebutuhan</h6>
            </div>
            <div class="card-body">
                <div class="col-md-12 mb-3">
                    <div class="row">
                        <div class="col-md-3 d-flex align-items-center justify-content-center" style="padding-left: 1em;">
                            <img src="{{ $data->filepath ? asset($data->filepath) : asset('img/default-img.png') }}" style="height: 150px; width: 200px; border-radius: 15px;" class="card-img" alt="...">
                        </div>
                        <div class="col-md-9">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th style="width: 200px;">Nama Pelatihan</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->description }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 200px;">Alias</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->alias }}</td>
                                </tr>
                                <tr>
                                    <th>Jenis Pelatihan</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->jenis_pelatihan }}</td>
                                </tr>
                                <tr>
                                    <th>Kategori Pelatihan</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->kategori_pelatihan }}</td>
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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">List Data</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Add New Assets</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="listUtilities" class="table table-bordered mt-4">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tool</th>
                                    <th>Quantity</th>
                                    <th>Satuan</th>
                                    <th width="200px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data->requirement as $tool)
                                    <tr>
                                        <td data-th="Product">
                                            <div class="row">
                                                <div class="col-md-4 text-center d-flex justify-content-center align-items-center">
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
                                            <a href="javascript:void(0)" class="btn btn-outline-danger btn-sm text-danger" onclick="confirmDelete({{ $tool->id }})">
                                                <i class="fa fa-trash-o"></i> Delete
                                            </a>
                                            <form id="delete-form-{{ $tool->id }}" action="{{ route('delete.item.requirement', $tool->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
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
            <form method="post" enctype="multipart/form-data" action="{{ route('requirement.insert.item', $data->id) }}">
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
                    url: '{{ route("requirement.update", ":id") }}'.replace(':id', toolId),
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

    function confirmDelete(id) {
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this item!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection

