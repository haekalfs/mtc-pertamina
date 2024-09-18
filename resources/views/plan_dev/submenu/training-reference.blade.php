@extends('layouts.main')

@section('active-pd')
active font-weight-bold
@endsection

@section('show-pd')
show
@endsection

@section('training-reference')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-tag"></i> Referensi Pelatihan</h1>
        <p class="mb-4">Menu Referensi Pelatihan</a></p>
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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">List Pelatihan</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Pelatihan</a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('training-reference') }}">
                        @csrf
                        <div class="row d-flex justify-content-start mb-2 p-1">
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="namaPenlat">Nama Pelatihan :</label>
                                            <select class="custom-select" id="namaPenlat" name="namaPenlat">
                                                <option value="-1" {{ $selectedNamaPenlat == '-1' ? 'selected' : '' }}>Show All</option>
                                                @foreach ($penlatList as $item)
                                                    <option value="{{ $item->id }}" {{ $item->id == $selectedNamaPenlat ? 'selected' : '' }}>{{ $item->description }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="stcw">Kategori Pelatihan :</label>
                                            <select class="custom-select" id="stcw" name="stcw">
                                                <option value="-1" {{ $selectedStcw == '-1' ? 'selected' : '' }}>Show All</option>
                                                <option value="STCW" {{ $selectedStcw == 'STCW' ? 'selected' : '' }}>STCW</option>
                                                <option value="NON STCW" {{ $selectedStcw == 'NON STCW' ? 'selected' : '' }}>NON STCW</option>
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
                    <table id="dataTable" class="table table-bordered mt-4 zoom90">
                        <thead class="thead-light">
                            <tr>
                                <th>Penlat</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                            <tr>
                                <td data-th="Product">
                                    <div class="row">
                                        <div class="col-md-3 d-flex justify-content-center align-items-start text-center">
                                            <a href="{{ route('preview-training-reference', $item->id) }}">
                                                <img src="{{ $item->filepath ? asset($item->filepath) : asset('img/default-img.png') }}" style="height: 150px; width: 200px; border: 1px solid rgb(202, 202, 202);" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow animateBox">
                                            </a>
                                        </div>
                                        <div class="col-md-9 text-left mt-sm-2">
                                            <h5 class="card-title font-weight-bold">{{ $item->description }}</h5>
                                            <div class="ml-2">
                                                <table class="table table-borderless table-sm">
                                                    @foreach($item->references as $index => $list)
                                                        @if($index < 3)
                                                        <tr>
                                                            <td class="mb-2"><i class="ti-minus mr-2"></i> {{ $list->references }} &nbsp; <small><a href="{{ asset($list->filepath) }}" target="_blank" class="text-secondary"><i class="fa fa-external-link fa-sm"></i> <u>View</u></a></small></td>
                                                        </tr>
                                                        @endif
                                                    @endforeach

                                                    @if($item->references->count() > 3)
                                                        <tr>
                                                            <td colspan="2" style="width: 300px;" class="mb-2">
                                                                <i class="ti-minus mr-2"></i> <a href="{{ route('preview-training-reference', $item->id) }}"><i>Show More</i></a>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="actions text-center">
                                    <div>
                                        <a href="{{ route('preview-training-reference', $item->id) }}" class="btn btn-outline-secondary btn-md mb-2 mr-2">
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
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('references.store') }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 140px;" class="mr-2">
                            <p style="margin: 0;">Nama Pelatihan :</p>
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
                    <div>
                        <div class="document-list-item mb-4">
                            <div class="d-flex align-items-start">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">References :</p>
                                </div>
                                <div class="flex-grow-1 textarea-container" id="documents-list-container">
                                    <div class="document-item">
                                        <textarea type="text" class="form-control mb-2" rows="2" name="documents[]" required></textarea>
                                        <input type="file" class="form-control-file mb-3" name="attachments[]" multiple>
                                    </div>
                                </div>
                                <div class="ml-2">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-success mb-2 shadow-sm btn-sm add-document-list"><i class="fa fa-plus"></i> Add More &nbsp;&nbsp;</button>
                                    </div>
                                    <div class="col-md-12">
                                        <a class="btn shadow-sm btn-sm btn-danger text-white delete-document-list" style="display: none;"><i class="fa fa-trash-alt"></i> Delete Item</a>
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
        language: {
            noResults: function() {
                return "No result match your request... Create new in Master Data Menu!"; // Customize this message as needed
            }
        }
    });
});
$(document).ready(function () {
    // Handle click event for the "Add More" button
    $(".add-document-list").on("click", function () {
        // Clone the entire document-item div
        var clonedDocumentItem = $(".document-item:first").clone();

        // Clear the content of the cloned textarea and file input
        clonedDocumentItem.find("textarea").val("");
        clonedDocumentItem.find("input[type=file]").val("");

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

$(document).ready(function() {
    $('.btn-outline-danger').on('click', function(event) {
        event.preventDefault();
        var id = $(this).data('id');

        // Construct the URL using the route() helper in Blade
        var url = "{{ route('training_reference.delete', ':id') }}";
        url = url.replace(':id', id);

        // SweetAlert confirmation
        swal({
            title: "Are you sure?",
            text: "This action will delete all references for this Pelatihan!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                // AJAX request to delete the item
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        swal("Poof! Your item has been deleted!", {
                            icon: "success",
                        }).then(() => {
                            location.reload(); // Optionally reload the page
                        });
                    },
                    error: function(response) {
                        swal("Oops! Something went wrong.", {
                            icon: "error",
                        });
                    }
                });
            } else {
                swal("Your item is safe!");
            }
        });
    });
});
</script>
@endsection

