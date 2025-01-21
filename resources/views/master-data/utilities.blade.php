@extends('layouts.main')

@section('active-penlat')
active font-weight-bold
@endsection

@section('show-penlat')
show
@endsection

@section('list-utilitas')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-gears mr-1"></i> List Utilities</h1>
        <p class="mb-4">List Utilities.</a></p>
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
                    <h6 class="m-0 font-weight-bold" id="judul">List Data</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Utility</a>
                    </div>
                </div>
                <div class="card-body zoom90">
                    <table id="docLetter" class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Tool</th>
                                <th>Satuan</th>
                                <th width="150px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($utilities as $tool)
                            <tr>
                                <td>
                                    <div class="row">
                                        <div class="col-md-4 d-flex justify-content-center align-items-center">
                                            <img src="{{ asset($tool->filepath) }}" style="height: 100px; width: 100px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow ">
                                        </div>
                                        <div class="col-md-8 text-left mt-sm-2">
                                            <h5>{{ $tool->utility_name }}</h5>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="font-weight-light">Satuan Default ({{$tool->utility_unit}})</p>
                                </td>
                                <td class="actions text-center">
                                    <div>
                                        <a href="javascript:void(0);" class="btn btn-outline-secondary btn-md mb-2 mr-2 edit-button" onclick="showEditModal({{ $tool->id }})">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <button class="btn btn-outline-danger btn-md mb-2" onclick="confirmDelete('{{ $tool->id }}', '{{ $tool->utility_name }}')">
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
            <div class="card mb-4">
                <div class="card-header">
                    <span class="text-danger font-weight-bold">Notes</span>
                </div>
                <div class="card-body" style="background-color: rgb(247, 247, 247);">
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">Utility Management Guidelines</h6>
                    <ul class="ml-4">
                        <li>Adding new utilities will affect the Utilitas Table.</li>
                        <li>Utilities must be registered manually based on operational requirements.</li>
                        <li>Ensure that you follow the correct procedure when registering utilities to avoid errors.</li>
                        <li>Users <span class="text-danger">should not delete utilities with IDs 1 to 6</span> as they are protected for system integrity.</li>
                        <li>Utilities that are linked to the <span class="text-danger">Utilitas Menu</span> cannot be deleted as they are associated with other critical data in the system.</li>
                        <li>All utility images must follow the required format and size specifications when uploading.</li>
                    </ul>
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
            <form method="post" enctype="multipart/form-data" action="{{ route('store-new-utility') }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="file-upload" style="cursor: pointer;">
                                <img id="image-preview" src="{{ asset('img/default-img.png') }}" style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="file-upload" type="file" name="display" style="display: none;" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Utility Name :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" id="utility_name" name="utility_name" required oninput="generateFieldName()">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Utility Unit :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" id="utility_unit" name="utility_unit" required oninput="validateUtilityUnit()">
                                                <small id="alias_help" class="help-block form-text text-danger">
                                                    Only alphabetics are allowed e.g L, KG, PCS.
                                                </small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Field Name :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" id="utility_field_name" name="utility_field_name" required readonly>
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
<div class="modal fade" id="editUtilityModal" tabindex="-1" role="dialog" aria-labelledby="editUtilityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="editUtilityModalLabel">Edit Utility</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" id="editUtilityForm">
                @csrf
                <input type="hidden" id="utility_id" name="utility_id">
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="file-upload-edit" style="cursor: pointer;">
                                <img id="image-preview-edit" src="{{ asset('img/default-img.png') }}" style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="file-upload-edit" type="file" name="display" style="display: none;" accept="image/*" onchange="previewImageEdit(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Utility Name :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" id="edit_utility_name" name="utility_name" required oninput="generateFieldNameEdit()">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Utility Unit :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" id="edit_utility_unit" name="utility_unit" required oninput="validateUtilityUnitEdit()">
                                                <small id="alias_help_edit" class="help-block form-text text-danger">
                                                    Only alphabetics are allowed e.g L, KG, PCS.
                                                </small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Field Name :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" id="edit_utility_field_name" name="utility_field_name" required readonly>
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
                    <button type="submit" class="btn btn-primary">Update Utility</button>
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
// Function to auto-generate utility field name from utility name
function generateFieldName() {
    const utilityName = document.getElementById('utility_name').value;
    const utilityFieldName = utilityName
        .toLowerCase()                     // Convert to lowercase
        .replace(/[^a-z0-9\s]/g, '')       // Remove non-alphanumeric characters
        .replace(/\s+/g, '_');             // Replace spaces with underscores
    document.getElementById('utility_field_name').value = utilityFieldName;
}

// Function to validate utility unit to allow only letters
function validateUtilityUnit() {
    const utilityUnit = document.getElementById('utility_unit').value;
    const validatedUtilityUnit = utilityUnit.replace(/[^a-zA-Z]/g, '');  // Remove non-letter characters
    document.getElementById('utility_unit').value = validatedUtilityUnit;
}

function confirmDelete(id, name) {
    swal({
        title: "Are you sure?",
        text: "You are about to delete the utility: " + name,
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            deleteUtility(id);
        }
    });
}

function deleteUtility(id) {
    $.ajax({
        url: '{{ route("delete-utility") }}', // Define route for deletion
        type: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}',
            id: id
        },
        success: function(response) {
            if (response.success) {
                swal("Deleted!", "Utility has been deleted successfully.", "success")
                .then(() => location.reload()); // Refresh the page after deletion
            } else {
                swal("Error", response.error, "error");
            }
        },
        error: function(xhr) {
            // If the status is 404 (Utility not found)
            if (xhr.status === 404) {
                swal("Error!", "Utility not found.", "error");
            }
            // If the status is 400 (Cannot delete utility due to related records or restricted IDs)
            else if (xhr.status === 400) {
                var response = xhr.responseJSON;
                swal("Error!", response.error, "error");
            }
            // Generic error handling for other cases
            else {
                swal("Error!", "An error occurred while trying to delete the utility.", "error");
            }
        }
    });
}
function showEditModal(id) {
    $.ajax({
        url: '/get-utility/' + id, // Your route to get utility data
        type: 'GET',
        success: function(response) {
            if (response.success) {
                $('#utility_id').val(response.data.id);
                $('#edit_utility_name').val(response.data.utility_name);
                $('#edit_utility_unit').val(response.data.utility_unit);
                $('#edit_utility_field_name').val(response.data.field_name);

                // Set the image preview
                $('#image-preview-edit').attr('src', response.data.filepath ? `{{ asset('') }}${response.data.filepath}` : `{{ asset('img/default-img.png') }}`);

                // Show the modal
                $('#editUtilityModal').modal('show');
            } else {
                alert('Error: Could not retrieve data.');
            }
        },
        error: function() {
            alert('Error: Could not retrieve data.');
        }
    });
}

function previewImageEdit(event) {
    var reader = new FileReader();
    reader.onload = function() {
        var output = document.getElementById('image-preview-edit');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

// Utility name to field name generation
function generateFieldNameEdit() {
    var utilityName = document.getElementById('edit_utility_name').value.toLowerCase();
    var fieldName = utilityName.replace(/\s+/g, '_'); // Replace spaces with underscores
    document.getElementById('edit_utility_field_name').value = fieldName;
}

// Validate utility unit input for allowed characters
function validateUtilityUnitEdit() {
    var utilityUnit = document.getElementById('edit_utility_unit').value;
    if (/[^a-zA-Z]/.test(utilityUnit)) {
        $('#alias_help_edit').show();
    } else {
        $('#alias_help_edit').hide();
    }
}
$('#editUtilityForm').on('submit', function(e) {
    e.preventDefault();

    var formData = new FormData(this);

    $.ajax({
        url: '{{ route("update-utility") }}',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response.success) {
                $('#editUtilityModal').modal('hide');
                swal("Success", "Item updated successfully!", "success");
                location.reload(); // Optionally reload the page or update the table
            } else {
                alert('Error: Could not update utility.');
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors;
                var errorMessage = '';
                $.each(errors, function(key, value) {
                    errorMessage += value + '\n';
                });
                swal("Error!", errorMessage, "error");
            } else {
                swal("Error: Could not update utility.");
            }
        }
    });
});
</script>
@endsection
