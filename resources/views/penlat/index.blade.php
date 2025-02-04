@extends('layouts.main')

@section('active-penlat')
active font-weight-bold
@endsection

@section('show-penlat')
show
@endsection

@section('penlat')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-list-alt"></i> List Pelatihan</h1>
        <p class="mb-4">List Pelatihan at MTC.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('penlat-import') }}" class="btn btn-sm btn-primary shadow-sm text-white"><i class="fa fa-file-text fa-sm"></i> Import Data</a>
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
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Penlat</a>
                    </div>
                </div>
                <div class="card-body zoom90">
                    <div class="row d-flex justify-content-start mb-4">
                        <div class="col-md-12">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="form-group" id="penlatContainer">
                                        <label for="namaPenlat">Training Name :</label>
                                        <select class="custom-select" id="namaPenlat" name="namaPenlat">
                                            <option value="-1" selected>Show All</option>
                                            @foreach($data as $penlat)
                                                <option value="{{ $penlat->id }}">{{ $penlat->description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="jenisPenlat">Type :</label>
                                        <select class="form-control" id="jenisPenlat" name="jenisPenlat">
                                            <option value="-1" selected>Show All</option>
                                            @foreach($data->unique('jenis_pelatihan') as $penlat)
                                                <option value="{{ $penlat->jenis_pelatihan }}">{{ $penlat->jenis_pelatihan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="stcw">STCW/Non :</label>
                                        <select name="stcw" class="form-control" id="stcw">
                                            <option value="-1">Show All</option>
                                            @foreach($data->unique('kategori_pelatihan') as $penlat)
                                                <option value="{{ $penlat->kategori_pelatihan }}">{{ $penlat->kategori_pelatihan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex align-self-end justify-content-start">
                                    <div class="form-group">
                                        <div class="align-self-center">
                                            <button id="filterButton" class="btn btn-primary" style="padding-left: 1.2em; padding-right: 1.2em;"><i class="ti-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table id="penlatTables" class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Display</th>
                                <th>Training Name</th>
                                <th>Aliases</th>
                                <th>Training Type</th>
                                <th>Category</th>
                                <th width="115px">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">
                    <span class="text-danger font-weight-bold">Notes</span>
                </div>
                <div class="card-body" style="background-color: rgb(247, 247, 247);">
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">General Guidelines</h6>
                    <ul class="ml-4">
                        <li>Penlat can be registered in two ways: through <span class="text-danger">Excel import</span> or <span class="text-danger">manual registration</span>.</li>
                        <li>Ensure that you select the appropriate method for batch registration based on your training data needs.</li>
                        <li>Users <span class="text-danger">should not delete Penlat carelessly</span> if they are already linked with other functions such as Training Reference, Penlat Requirements, or the Batch Program menu.</li>
                        <li>Penlat that are linked to Batches Menu will impact related data in the system, so be cautious before proceeding with deletion.</li>
                        <li>You can view detailed information for each batch by clicking on it, which will display more in-depth details, including the list of associated costs.</li>
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
            <form method="post" enctype="multipart/form-data" action="{{ route('penlat.store') }}">
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
                                                <p style="margin: 0;">Nama Pelatihan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="nama_program" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Jenis Pelatihan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="jenis_pelatihan" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Kategori Program :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="kategori_program" required>
                                            </div>
                                        </div>
                                        <div class="row d-flex justify-content-right mb-1">
                                            <div class="col-md-12">
                                                <div class="row align-items-start">
                                                    <div class="col-md-12">
                                                        <div class="d-flex">
                                                            <div style="width: 140px;" class="mr-2">
                                                                <p style="margin: 0;">Aliases :</p>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control" id="aliasDisplay" placeholder="Add new aliases..."/>
                                                                        <div class="input-group-addon" id="addAliasBtn"><i class="fa fa-plus"></i></div>
                                                                    </div>
                                                                </div>
                                                                <small id="alias_help" class="help-block form-text text-danger">
                                                                    Only alphabetics & symbols are allowed.
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Hidden input to store the comma-separated aliases for form submission -->
                                        <input type="hidden" id="alias" name="alias" />

                                        <div class="d-flex align-items-start mb-4 zoom90">
                                            <table class="table table-bordered mt-3">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Aliases</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="aliasesTableBody">
                                                    <!-- Filled dynamically with aliases -->
                                                    <tr id="no-data">
                                                        <td colspan="2" class="text-center">No data available</td>
                                                    </tr>
                                                </tbody>
                                            </table>
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
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="editDataModalLabel">Edit Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body mr-2 ml-2">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="edit-file-upload" style="cursor: pointer;">
                                <img id="edit-image-preview" src="{{ asset('img/default-img.png') }}" style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="edit-file-upload" type="file" name="display" style="display: none;" accept="image/*" onchange="previewEditImage(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Input fields for edit -->
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Nama Program :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" id="edit_nama_program" name="nama_program" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Jenis Pelatihan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" id="edit_jenis_pelatihan" name="jenis_pelatihan" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Kategori Pelatihan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" id="edit_kategori_pelatihan" name="kategori_pelatihan" required>
                                            </div>
                                        </div>
                                        <div class="row d-flex justify-content-right mb-1">
                                            <div class="col-md-12">
                                                <div class="row align-items-start">
                                                    <div class="col-md-12">
                                                        <div class="d-flex">
                                                            <div style="width: 140px;" class="mr-2">
                                                                <p style="margin: 0;">Aliases :</p>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="form-group">
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control" id="edit_alias" name="alias" placeholder="Add new aliases..."/>
                                                                        <div class="input-group-addon" id="addAliasesBtn"><i class="fa fa-plus"></i></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Hidden input to store the comma-separated aliases for form submission -->
                                        <input type="hidden" id="alias_hidden" name="alias_hidden" />

                                        <div class="d-flex align-items-start mb-4 zoom90">
                                            <table class="table table-bordered mt-3">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Aliases</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="aliasTableBody">
                                                    <!-- Filled dynamically with aliases -->
                                                    <tr id="no-data">
                                                        <td colspan="2" class="text-center">No data available</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submitEditForm">Update Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Custom CSS to align the Select2 container */
    .select2-container--default .select2-selection--single {
        height: calc(2.25rem + 2px); /* Adjust this value to match your input height */
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: calc(2.25rem + 2px); /* Adjust this to vertically align the text */
    }

    .select2-container .select2-selection--single {
        height: 100% !important; /* Ensure the height is consistent */
    }

    .select2-container {
        width: 100% !important; /* Ensure the width matches the form control */
    }
</style>
<script>
setupAliasInput('aliasDisplay');
setupAliasInput('edit_alias');
function setupAliasInput(inputId) {
    document.getElementById(inputId).addEventListener('input', function(e) {
        let value = e.target.value;

        // Replace spaces with dashes
        value = value.replace(/\s+/g, '-');

        // Remove numbers, commas, and dots, and only allow alphabetic characters and dashes
        value = value.replace(/[^a-zA-Z\-]/g, '');

        // Set the updated value back to the input
        e.target.value = value;
    });
}
// Intercept form submission to show SweetAlert confirmation
document.getElementById('submitEditForm').addEventListener('click', function (event) {
    event.preventDefault();  // Prevent form from submitting immediately

    swal({
        title: "Update Data Pelatihan?",
        text: "Do you really want to update the data? Please be careful with the aliases. Ensure they are in the correct format (e.g., R-BST, R-PSRCB-NP) without duplicates or incorrect characters. Any changes will directly affect integration between Infografis, Profit Menu, and other related systems.",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willUpdate) => {
        if (willUpdate) {
            // Submit the form if the user confirms
            document.getElementById('editForm').submit();
        } else {
            swal("Your changes are safe!");
        }
    });
});
$(document).ready(function () {
    $('#namaPenlat').select2({
        placeholder: "Select Pelatihan...",
        width: '100%',
        allowClear: true,
        dropdownParent: $('#penlatContainer'),
        language: {
            noResults: function() {
                return "No result match your request... Create new in Master Data Menu!"; // Customize this message as needed
            }
        }
    });
    let aliasArray = [];

    // Handle Add Alias Button Click
    $('#addAliasBtn').on('click', function () {
        let newAlias = $('#aliasDisplay').val().trim();
        if (newAlias) {
            aliasArray.push(newAlias);
            updateAliasTable();
            swal("Success", "Aliases inserted successfully!", "success");
            updateHiddenInput();
            $('#aliasDisplay').val(''); // Clear the input field
        }
    });

    // Function to update the alias table dynamically
    function updateAliasTable() {
        let tableBody = $('#aliasesTableBody');
        tableBody.empty(); // Clear existing table rows

        if (aliasArray.length === 0) {
            // Show 'No data available' if there are no aliases
            tableBody.append(`<tr id="no-data">
                                <td colspan="2" class="text-center">No data available</td>
                              </tr>`);
        } else {
            // Populate table with aliases if available
            aliasArray.forEach((alias, index) => {
                let row = `<tr>
                                <td>${alias}</td>
                                <td class="text-center" style="width:5%">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-aliases" data-index="${index}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>`;
                tableBody.append(row);
            });
        }
    }

    // Function to update the hidden input field
    function updateHiddenInput() {
        $('#alias').val(aliasArray.join(','));
    }

    // Handle removing an alias from the table
    $('#aliasesTableBody').on('click', '.remove-aliases', function () {
        let index = $(this).data('index');
        aliasArray.splice(index, 1);
        updateAliasTable();
        updateHiddenInput();
    });
});

function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('image-preview');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

function previewEditImage(event) {
    const output = document.getElementById('edit-image-preview');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function () {
        URL.revokeObjectURL(output.src) // Free up memory
    }
}
</script>
<script>
$(document).ready(function() {
    var table = $('#penlatTables').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('penlat') }}",
            data: function (d) {
                d.namaPenlat = $('#namaPenlat').val();
                d.jenisPenlat = $('#jenisPenlat').val();
                d.stcw = $('#stcw').val();
            }
        },
        columns: [
            { data: 'display', name: 'display', orderable: false, searchable: false },
            { data: 'description', name: 'description' },
            { data: 'alias', name: 'alias' },
            { data: 'jenis_pelatihan', name: 'jenis_pelatihan' },
            { data: 'kategori_pelatihan', name: 'kategori_pelatihan' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
    order: [[1, 'asc']]
    });

    let aliasesArray = [];

    // Load existing aliases when the edit button is clicked
    $('#penlatTables').on('click', '.edit-tool', function () {
        let id = $(this).data('id');

        $.get('/penlat/' + id + '/edit', function (data) {
            // Prefill form fields
            $('#edit_id').val(data.id);
            $('#edit_nama_program').val(data.description);
            $('#edit_alias').val('');
            $('#edit_jenis_pelatihan').val(data.jenis_pelatihan);
            $('#edit_kategori_pelatihan').val(data.kategori_pelatihan);
            var imageUrl = data.image ? data.image : '{{ asset('img/default-img.png') }}';
            $('#edit-image-preview').attr('src', imageUrl);
            $('#editForm').attr('action', '/penlat-update/' + id);

            // Populate the alias table and hidden input
            aliasesArray = data.alias ? data.alias.split(',') : [];
            updateAliasTable();
            updateHiddenInput();

            // Show the modal
            $('#editDataModal').modal('show');
        });
    });

    // Handle Add Alias Button Click
    $('#addAliasesBtn').on('click', function () {
        let newAlias = $('#edit_alias').val().trim();
        if (newAlias) {
            aliasesArray.push(newAlias);
            updateAliasTable();
            swal("Success", "Aliases inserted successfully!", "success");
            updateHiddenInput();
            $('#edit_alias').val(''); // Clear the input field
        }
    });

    // Function to update the alias table dynamically
    function updateAliasTable() {
        let tableBody = $('#aliasTableBody');
        tableBody.empty(); // Clear existing table rows

        if (aliasesArray.length === 0) {
            // Show 'No data available' if there are no aliases
            tableBody.append(`<tr id="no-data">
                                <td colspan="2" class="text-center">No data available</td>
                              </tr>`);
        } else {
            // Populate table with aliases if available
            aliasesArray.forEach((alias, index) => {
                let row = `<tr>
                                <td>${alias}</td>
                                <td class="text-center" style="width:5%">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-alias" data-index="${index}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>`;
                tableBody.append(row);
            });
        }
    }

    // Function to update the hidden input field
    function updateHiddenInput() {
        $('#alias_hidden').val(aliasesArray.join(','));
    }

    // Handle removing an alias from the table
    $('#aliasTableBody').on('click', '.remove-alias', function () {
        let index = $(this).data('index');
        aliasesArray.splice(index, 1);
        updateAliasTable();
        updateHiddenInput();
    });

    // Delete functionality
    $('#penlatTables').on('click', '.btn-outline-danger', function () {
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
                    url: '{{ route("delete.penlat", ":id") }}'.replace(':id', id),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(result) {
                        swal("Poof! Your record has been deleted!", {
                            icon: "success",
                        });
                        table.draw(); // Redraw the table
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

    // Filter button click event
    $('#filterButton').click(function() {
        table.draw();
    });
});
</script>
@endsection
