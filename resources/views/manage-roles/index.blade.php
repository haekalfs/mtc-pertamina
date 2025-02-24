@extends('layouts.main')

@section('active-user')
active font-weight-bold
@endsection

@section('show-user')
show
@endsection

@section('manage-roles')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-unlock-alt"></i> Manage Roles</h1>
        <p class="mb-4">Managing Staff Roles</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        {{-- <a class="btn btn-secondary btn-sm shadow-sm mr-2" href="/invoicing/list"><i class="fas fa-solid fa-backward fa-sm text-white-50"></i> Go Back</a> --}}
    </div>
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
                    <h6 class="m-0 font-weight-bold" id="judul">List Roles</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" type="button" data-toggle="modal" data-target="#addRole" id="addRoleBtn"><i class="fa fa-plus"></i> Register New Role</a>
                    </div>
                </div>
                <div class="card-body">
                    <table  id="docLetter" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Role Name</th>
                                <th>Role Description</th>
                                <th>Super Admin</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($roles as $item)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->role_description }}</td>
                                <td>
                                    <div class="custom-control custom-switch ml-3">
                                        <input type="checkbox" class="custom-control-input" id="autoSubmit-{{ $item->id }}"
                                               name="photo_placeholder" value="0"
                                               {{ $item->isSuperAdmin ? 'checked' : '' }}
                                               onchange="toggleSuperAdmin({{ $item->id }}, this)">
                                        <label class="custom-control-label" for="autoSubmit-{{ $item->id }}"
                                               id="placeholderLabel-{{ $item->id }}">
                                            {{ $item->isSuperAdmin ? 'Yes' : 'No' }}
                                        </label>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-outline-danger btn-sm btn-details mr-2"
                                       onclick="confirmDelete('{{ $item->id }}'); return false;">
                                       <i class="fa fa-ban"></i> Delete
                                    </a>
                                    <form id="delete-role-form-{{ $item->id }}"
                                          action="{{ route('roles.destroy', $item->id) }}"
                                          method="POST" style="display: none;">
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
<div class="modal fade" id="addRole" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header d-flex flex-row align-items-center justify-content-between border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Role Registration<a id="entry-date-update"></a></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" action="{{ route('roles.store') }}">
                @csrf
				<div class="modal-body">
                    <div class="col-md-12 zoom90">
                        @if ($message = Session::get('disclaimer'))
                        <div class="alert alert-warning alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="role_code">Role Code :</label>
                                    <input type="text" class="form-control" autocomplete="off" placeholder="Unique ID" name="role_code" id="role_code">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="role_name">Role Name :</label>
                                    <input type="text" class="form-control" autocomplete="off" placeholder="Role Name" name="role_name" id="role_name">
                                </div>
                            </div>
                        </div>
                        <div class="row isWR">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="role_description">Role Description :</label>
                                    <textarea type="text" class="form-control" rows="3" name="role_description" placeholder="Role Authority / Jobdesk" id="role_description"></textarea>
                                </div>
                            </div>
                        </div>
                        <span style="display: none;" class="text-danger hidWR"><small>Be careful, once submitted, it cannot be undone.</small></span>
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
    function confirmDelete(id) {
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this role!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                document.getElementById('delete-role-form-' + id).submit();
            }
        });
    }

    function toggleSuperAdmin(roleId, element) {
    let isChecked = element.checked;
    let label = document.getElementById("placeholderLabel-" + roleId);

    if (isChecked) {
        // Show SweetAlert confirmation only when switching to "Yes"
        swal({
            title: "Are you sure you want to set this role as Super Admin?",
            text: "Once active, they will be able to see sensitive data!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willConfirm) => {
            if (willConfirm) {
                // Proceed with update if confirmed
                updateSuperAdminStatus(roleId, isChecked, label, element);
            } else {
                // Revert toggle switch if canceled
                element.checked = false;
                label.textContent = "No";
            }
        });
    } else {
        // Directly update if switching to "No"
        updateSuperAdminStatus(roleId, isChecked, label, element);
    }
}

// Function to send AJAX request
function updateSuperAdminStatus(roleId, isChecked, label, element) {
    $.ajax({
        url: '/roles/update-superadmin/' + roleId,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            isSuperAdmin: isChecked ? 1 : 0
        },
        success: function(response) {
            label.textContent = isChecked ? "Yes" : "No";
            swal("Success! Settings have been applied successfully!", {
                icon: "success",
            });
        },
        error: function(xhr) {
            console.error("Error updating role:", xhr.responseText);
            alert("Failed to update role. Please try again.");
            // Revert toggle if update fails
            element.checked = !isChecked;
            label.textContent = isChecked ? "No" : "Yes";
        }
    });
}

</script>
@endsection
