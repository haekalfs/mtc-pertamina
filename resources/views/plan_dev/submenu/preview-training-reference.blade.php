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
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-2">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-tag"></i> Preview Referensi Pelatihan</h1>
        <p class="mb-3">Referensi Pelatihan.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('training-reference') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
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
                <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-list-alt"></i> Detail Pelatihan</h6>
                {{-- <div class="text-right">
                    <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#editDataModal"><i class="menu-Logo fa fa-plus"></i> Update Data</a>
                </div> --}}
            </div>
            <div class="card-body">
                <div class="col-md-12 mb-3">
                    <div class="row">
                        <div class="col-md-3 d-flex align-items-center justify-content-center" style="padding-left: 1em;">
                            <img src="{{ $data->filepath ? asset($data->filepath) : asset('img/default-img.png') }}" style="height: 150px; width: 200px; border: 1px solid rgb(202, 202, 202);" class="img-fluid d-none d-md-block rounded mb-2 shadow ">
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th style="width: 200px;">Nama Pelatihan</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->description }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 200px;">Aliases</th>
                                    <td style="text-align: start; font-weight:500">
                                        : {{ $data->aliases->pluck('alias')->implode(', ') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Jenis Pelatihan</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->jenis_pelatihan }} Items</td>
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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-tag"></i> List Data</h6>
                    <div class="text-right">
                        <button data-toggle="modal" data-target="#inputDataModal" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> Add References
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-bordered mt-4 zoom90">
                        <thead>
                            <tr>
                                <th>Referensi</th>
                                <th width="150px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data->references as $item)
                            <tr>
                                <td data-th="Product">
                                    <div class="row">
                                        <div class="col-md-12 text-left mt-sm-2">
                                            <h5 class="card-title font-weight-bold">{{ $item->references }}</h5>
                                            <div class="ml-2">
                                                <i class="ti-minus mr-2"></i> <a href="{{ asset($item->filepath) }}" target="_blank">{{ $item->filepath }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="actions text-center">
                                    <div>
                                        <button data-id="{{ $item->id }}" class="btn btn-outline-secondary btn-md mb-2 mr-2 edit-button">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button data-id="{{ $item->id }}" class="btn btn-outline-danger btn-md mb-2 delete-button">
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
            <form method="post" enctype="multipart/form-data" action="{{ route('references.new.item', $data->id) }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
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
                                        <div class="mb-4"><small class="text-danger"><i>pdf,docx,xlsx,xls,jpeg,png,jpg,gif</i></small></div>
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
<!-- Edit Reference Modal -->
<div class="modal fade" id="editReferenceModal" tabindex="-1" role="dialog" aria-labelledby="editReferenceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="editReferenceModalLabel">Edit Reference</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editReferenceForm" method="POST" enctype="multipart/form-data" action="{{ route('references.update') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="referenceId">

                    <div class="form-group">
                        <label for="referenceText">References :</label>
                        <textarea id="referenceText" class="form-control" rows="2" name="document" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="attachment">Attachment :</label>
                        <div id="fileSection">
                            <!-- This will be dynamically populated -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
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
        $('.delete-button').on('click', function(event) {
            event.preventDefault();
            var id = $(this).data('id');

            // Construct the URL using the route() helper in Blade
            var url = "{{ route('training-reference.destroy', ':id') }}";
            url = url.replace(':id', id);

            // SweetAlert confirmation
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this item!",
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

    $(document).ready(function() {
        $('.edit-button').on('click', function() {
            var id = $(this).data('id');

            $.ajax({
                url: '/fetch-penlat-references/' + id,
                method: 'GET',
                success: function(response) {
                    $('#referenceId').val(response.id);
                    $('#referenceText').val(response.references);

                    // Handle the file section
                    if (response.filepath) {
                        $('#fileSection').html(`
                            <div class="d-flex align-items-center">
                                <a href="{{ asset('${response.filepath}') }}" target="_blank" class="mr-3">View Document</a>
                                <button type="button" class="btn btn-danger btn-sm remove-file"><i class="fa fa-times"></i></button>
                            </div>
                            <input type="hidden" name="existing_file" value="${response.filepath}">
                        `);
                    } else {
                        $('#fileSection').html(`
                            <input type="file" id="attachment" class="form-control-file" name="attachment">
                        `);
                    }

                    $('#editReferenceModal').modal('show');
                },
                error: function() {
                    alert('Failed to fetch reference data. Please try again.');
                }
            });
        });

        // Handle the removal of the existing file
        $(document).on('click', '.remove-file', function() {
            $('#fileSection').html(`
                <input type="file" id="attachment" class="form-control-file" name="attachment">
            `);
        });
    });

</script>
@endsection

