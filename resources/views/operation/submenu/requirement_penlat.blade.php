@extends('layouts.main')

@section('active-penlat')
active font-weight-bold
@endsection

@section('show-penlat')
show
@endsection

@section('tool-requirement-penlat')
font-weight-bold
@endsection

@section('content')
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
                    <table id="dataTable" class="table table-bordered mt-4 zoom90">
                        <thead class="thead-light">
                            <tr>
                                <th>Penlat</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penlat as $item)
                            <tr>
                                <td data-th="Product">
                                    <div class="row">
                                        <div class="col-md-3 d-flex justify-content-center align-items-center text-center">
                                            <a href="{{ route('preview-requirement', $item->id) }}" class="animateBox">
                                                <img src="{{ $item->filepath ? asset($item->filepath) : 'https://via.placeholder.com/150x150/5fa9f8/ffffff' }}" style="height: 150px; width: 150px; border: 1px solid rgb(202, 202, 202);" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
                                            </a>
                                        </div>
                                        <div class="col-md-9 text-left mt-sm-2">
                                            <h5 class="card-title font-weight-bold">{{ $item->description }}</h5>
                                            <div class="ml-2">
                                                <table class="table table-borderless table-sm">
                                                    @foreach($item->requirement as $index => $list)
                                                        @if($index < 4)
                                                        <tr>
                                                            <td class="mb-2"><i class="ti-minus mr-2"></i>{{ $list->tools->asset_name }}&nbsp;&nbsp;&nbsp; : &nbsp;</span><span>{{ $list->amount }} Units</td>
                                                        </tr>
                                                        @endif
                                                    @endforeach

                                                    @if($item->requirement->count() > 4)
                                                        <li><span class=""><a href="{{ route('preview-room-user', $item->id) }}"><i>Show More...</i></li>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="actions text-center">
                                    <div>
                                        <a href="{{ route('preview-requirement', $item->id) }}" class="btn btn-outline-secondary btn-md mb-2 mr-2">
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

<div class="modal fade" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 700px;" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('requirement.store') }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-12">
                            <div>
                                <div class="document-list-item mb-4 mt-3">
                                    <div class="d-flex align-items-center mb-4">
                                        <div style="width: 120px;" class="mr-2">
                                            <p style="margin: 0;">Nama Penlat :</p>
                                        </div>
                                        <div class="flex-grow-1">
                                            <select id="penlatSelect" class="form-control select2" name="penlat">
                                                <option selected disabled>Select Pelatihan...</option>
                                                @foreach ($penlatList as $item)
                                                <option value="{{ $item->id }}">{{ $item->description }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start">
                                        <div style="width: 120px;" class="mr-2">
                                            <p style="margin: 0;">List Alat :</p>
                                        </div>
                                        <div class="flex-grow-1 textarea-container" id="documents-list-container">
                                            <div class="document-item mb-2">
                                                <div class="row">
                                                    <div class="col-md-9">
                                                        <select class="form-control mb-2" name="tool[]" required>
                                                            @foreach ($assets as $item)
                                                                <option value="{{ $item->id }}">{{ $item->asset_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 p-0">
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
    $(document).ready(function() {
        // Initialize Select2
        $('#penlatSelect').select2({
            dropdownParent: $('#inputDataModal'),
            theme: "classic",
            placeholder: "Select Pelatihan...",
            width: '100%',
            tags: true,
        });
        $('#editpenlatSelect').select2({
            dropdownParent: $('#editDataModal'),
            theme: "classic",
            placeholder: "Select Pelatihan...",
            width: '100%',
            tags: true,
        });
    });
    $(document).ready(function () {
        // Handle click event for the "Add More" button
        $(".add-document-list").on("click", function () {
            // Clone the entire document-item div
            var clonedDocumentItem = $(".document-item:first").clone();

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

    $(document).on('click', '.btn-outline-danger', function() {
        let id = $(this).data('id');
        let url = "{{ route('delete.requirement', ':id') }}";
        url = url.replace(':id', id);

        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this file!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        swal("Poof! Your file has been deleted!", {
                            icon: "success",
                        }).then(() => {
                            location.reload(); // Reload the page to reflect the changes
                        });
                    },
                    error: function(response) {
                        swal("An error occurred while deleting the item.", {
                            icon: "error",
                        });
                    }
                });
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
        }
    }
</script>
@endsection

