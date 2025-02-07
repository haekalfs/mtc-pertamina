@extends('layouts.main')

@section('active-templates')
active font-weight-bold
@endsection

@section('show-templates')
show
@endsection

@section('list-amendment')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-gavel mr-1"></i> List Regulators</h1>
        <p class="mb-4">List Regulators PMTC.</a></p>
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
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Amendments</a>
                    </div>
                </div>
                <div class="card-body zoom90">
                    <table id="docLetter" class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Training Name</th>
                                <th>Description</th>
                                <th>Translation</th>
                                <th>Regulator</th>
                                <th width="150px">Action</th>
                            </tr>
                        </thead>
                        <tbody>@php $no = 1; @endphp
                            @foreach($listAmendments as $amendments)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $amendments->penlats->description }}</td>
                                <td>{{ $amendments->description }}</td>
                                <td>{{ $amendments->translation }}</td>
                                <td>{{ $amendments->regulators->description }}</td>
                                <td class="actions text-center">
                                    <div>
                                        <button onclick="openEditModal({{ $amendments->id }})" class="btn btn-outline-secondary btn-md mb-2 mr-2"><i class="fa fa-edit"></i></button>
                                        <button class="btn btn-outline-danger btn-md mb-2" onclick="deleteAmendments({{ $amendments->id }})">
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
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">Amendments Guidelines</h6>
                    <ul class="ml-4">
                        <li>Adding new Regulator will affect the Regulator Table.</li>
                        <li>Ensure that each location has a unique location code and an appropriate description.</li>
                        <li>Regulator linked to existing assets cannot be deleted; ensure relationships are properly managed before attempting deletion.</li>
                        <li>Follow the correct procedure when updating or registering Regulator to maintain database integrity.</li>
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
            <form method="post" action="{{ route('store-new-amendment') }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-12">
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Nama Pelatihan :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <select id="penlatSelect" name="penlatId" class="form-control select2" required>
                                        <option selected disabled>Select Pelatihan...</option>
                                        @foreach($penlatList as $penlat)
                                            <option value="{{ $penlat->id }}">{{ $penlat->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Description :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="description" name="description" required>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Translation :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="translation" name="translation" required>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Regulator :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <select class="form-control" id="regulator" name="regulator"></select>
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
                <h5 class="modal-title" id="editDataModalLabel">Edit Regulator</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm" method="post">
                @csrf
                @method('PUT')
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-12">
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Nama Pelatihan :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <select id="penlatSelectEdit" name="penlatId" class="form-control select2" required>
                                        <option selected disabled>Select Pelatihan...</option>
                                        @foreach($penlatList as $penlat)
                                            <option value="{{ $penlat->id }}">{{ $penlat->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Description:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="edit_description" name="edit_description" required>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Translation:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" id="edit_translation" name="edit_translation" required>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Remarks :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <select class="form-control" id="edit_regulator" name="edit_regulator">
                                        <option selected disabled>Select Remarks...</option>
                                        @foreach($listRegulator as $regulator)
                                            <option value="{{ $regulator->id }}">{{ $regulator->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
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
$(document).ready(function() {
    $('#penlatSelect').select2({
        dropdownParent: $('#inputDataModal'),
        placeholder: "Select Pelatihan...",
        width: '100%',
        language: {
            noResults: function() {
                return "No result match your request... Create new in Master Data Menu!"; // Customize this message as needed
            }
        }
    });
    $('#penlatSelectEdit').select2({
        dropdownParent: $('#editDataModal'),
        placeholder: "Select Pelatihan...",
        width: '100%',
        language: {
            noResults: function() {
                return "No result match your request... Create new in Master Data Menu!"; // Customize this message as needed
            }
        }
    });
    $('#edit_regulator').select2({
        dropdownParent: $('#editDataModal'),
        placeholder: "Select Pelatihan...",
        width: '100%',
        language: {
            noResults: function() {
                return "No result match your request... Create new in Master Data Menu!"; // Customize this message as needed
            }
        }
    });
    initSelect2WithRegulators();
});
function deleteAmendments(locationId) {
    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this amendment!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            // Make an AJAX request to delete the location
            $.ajax({
                url: `/amendments/${locationId}`,
                type: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}", // Laravel CSRF token
                },
                success: function (response) {
                    if (response.status === "success") {
                        swal("Success!", response.message, "success").then(() => {
                            // Reload the page or remove the deleted row
                            location.reload();
                        });
                    } else {
                        swal("Error!", response.message, "error");
                    }
                },
                error: function () {
                    swal("Error!", "Something went wrong. Please try again.", "error");
                },
            });
        }
    });
}
function openEditModal(locationId) {
    // Fetch location data via AJAX
    $.ajax({
        url: `/amendments/${locationId}/edit`,
        type: "GET",
        success: function (data) {
            // Fill modal with existing location data
            $("#edit_translation").val(data.translation);
            $("#edit_description").val(data.description);
            $("#penlatSelectEdit").val(data.penlat_id);
            $('#regulator_amendment').val(data.regulator_id).trigger('change');
            // Set the form action to the update route
            $("#editForm").attr("action", `/amendments/${locationId}`);

            // Show the modal
            $("#editDataModal").modal("show");
        },
        error: function () {
            swal("Error!", "Unable to fetch location data.", "error");
        },
    });
}

function initSelect2WithRegulators() {
    $('#regulator').select2({
        ajax: {
            url: '{{ route('regulators.fetch') }}', // Define the route for fetching regulators
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // Search query
                    page: params.page || 1, // Pagination
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: $.map(data.items, function (item) {
                        return {
                            id: item.id,
                            text: item.description,
                        };
                    }),
                    pagination: {
                        more: data.total_count > (params.page * 10),
                    },
                };
            },
            cache: true,
        },
        placeholder: 'Select or add a Regulator',
        minimumInputLength: 1,
        width: '100%',
        dropdownParent: $('#inputDataModal'),
        tags: true, // Enable tagging for new entries
        allowClear: true,
        createTag: function (params) {
            var term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true, // Mark as new
            };
        },
        templateResult: function (data) {
            if (data.newTag) {
                return $('<span><em>Add new: "' + data.text + '"</em></span>');
            }
            return data.text;
        },
        templateSelection: function (data) {
            return data.text;
        },
    });

    // Listen for selection and handle new tag creation
    $('#regulator').on('select2:select', function (e) {
        var selectedData = e.params.data;
        if (selectedData.newTag) {
            // If it's a new entry, save it to the database
            $.ajax({
                url: '{{ route('regulators.store') }}', // Define the route to save the new regulator
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', // CSRF protection
                },
                data: {
                    description: selectedData.text, // New regulator description
                },
                success: function (response) {
                    // Replace the temporary new tag ID with the real one from the database
                    var newOption = new Option(response.description, response.id, false, true);
                    $('#regulator').append(newOption).trigger('change');
                },
                error: function () {
                    alert('Failed to save the new regulator.');
                },
            });
        }
    });
}
</script>
@endsection
