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
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-gavel mr-1"></i> List Amendments</h1>
        <p class="mb-4">List Amendments PMTC.</a></p>
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
                                <th>Description</th>
                                <th>Translation</th>
                                <th width="150px">Action</th>
                            </tr>
                        </thead>
                        <tbody>@php $no = 1; @endphp
                            @foreach($listAmendments as $amendments)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $amendments->description }}</td>
                                <td>{{ $amendments->translation }}</td>
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
                        <li>Adding new locations will affect the Locations Table.</li>
                        <li>Ensure that each location has a unique location code and an appropriate description.</li>
                        <li>Locations linked to existing assets cannot be deleted; ensure relationships are properly managed before attempting deletion.</li>
                        <li>Follow the correct procedure when updating or registering locations to maintain database integrity.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
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
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-md-12">
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
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
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
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-md-12">
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
                                    </div>
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
<script>
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
</script>
@endsection
