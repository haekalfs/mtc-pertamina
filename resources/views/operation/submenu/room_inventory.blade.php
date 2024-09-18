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
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-building-o"></i> Room Inventory</h1>
        <p class="mb-4">Inventaris Ruangan.</a></p>
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
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Room</a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('room-inventory') }}">
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
                    <table id="dataTable" class="table table-bordered mt-2">
                        <thead class="thead-light">
                            <tr>
                                <th>Nama Ruangan</th>
                                <th>Lokasi</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rooms as $item)
                            <tr>
                                <td data-th="Product">
                                    <div class="row">
                                        <div class="col-md-4 d-flex justify-content-center align-items-start mt-2">
                                            <a href="{{ route('preview-room-user', $item->id) }}" class="animateBox">
                                                <img src="{{ $item->filepath ? asset($item->filepath) : asset('img/default-img.png') }}" style="height: 150px; width: 200px; border: 1px solid rgb(202, 202, 202);" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
                                            </a>
                                        </div>
                                        <div class="col-md-8 text-left mt-sm-2">
                                            <h5 class="card-title font-weight-bold">{{ $item->room_name }}</h5>
                                            <div class="ml-2">
                                                <ol class="ml-4" style="line-height:200%">
                                                    @foreach($item->list as $index => $list)
                                                        @if($index < 4)
                                                        <li><span class="">{{ $list->tools->asset_name }}&nbsp;&nbsp;&nbsp; : &nbsp;</span><span>{{ $list->amount }} Units</li>
                                                        @endif
                                                    @endforeach

                                                    @if($item->list->count() > 4)
                                                        <li><span class=""><a href="{{ route('preview-room-user', $item->id) }}"><i>Show More...</i></li>
                                                    @endif
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $item->location->description }}</td>
                                <td class="actions text-center" data-th="">
                                    <div>
                                        <a href="{{ route('preview-room', $item->id) }}" class="btn btn-outline-secondary btn-md mb-2 mr-2 edit-button">
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

<div class="modal fade zoom90" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 800px;" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('room.store') }}" onsubmit="return validateForm('file-upload')">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="file-upload" style="cursor: pointer;">
                                <img id="image-preview" src="{{ asset('img/default-img.png') }}" style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
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
                                            <input type="text" class="form-control" name="nama_ruangan" required></input>
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
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="form-group has-success">
                                <div class="d-flex justify-content-between align-items-center">
                                    <!-- Label aligned with buttons -->
                                    <p class="control-label mb-3" style="margin-bottom: 0;">List Alat:</p>
                                    <div class="mb-3">
                                        <a class="btn shadow-sm btn-sm mr-2 btn-danger delete-document-list text-white" style="display: none;">
                                            <i class="fa fa-trash-alt"></i> Delete Item
                                        </a>
                                        <button type="button" class="btn btn-success shadow-sm btn-sm add-document-list">
                                            <i class="fa fa-plus"></i> Add More
                                        </button>
                                    </div>
                                </div>

                                <div class="flex-grow-1 textarea-container mt-2" id="documents-list-container">
                                    <div class="document-item mb-3">
                                        <div class="row">
                                            <div class="col-md-10 pr-0">
                                                <!-- Ensure long text is truncated -->
                                                <select class="form-control mb-2 select-tool underline-input" name="tool[]" style="width: 98%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" required>
                                                    <option disabled selected>Choose item...</option>
                                                    @foreach ($assets as $item)
                                                        <option value="{{ $item->id }}">{{ $item->asset_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2 pl-0">
                                                <input type="number" class="form-control underline-input" name="amount[]" placeholder="Amount" style="width: 100%;" min="1" required></input>
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
            var clonedContainer = $("<div class='document-item mb-3'></div>").append(clonedDocumentItem.html());

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
        let url = "{{ route('delete.room', ':id') }}";
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
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('image-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
    function displayFileName() {
        const input = document.getElementById('file');
        const label = document.getElementById('file-label');
        const file = input.files[0];
        if (file) {
            label.textContent = file.name;
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

