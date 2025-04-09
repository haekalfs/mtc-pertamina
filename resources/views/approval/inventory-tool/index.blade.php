@extends('layouts.main')

@section('active-approval')
active font-weight-bold
@endsection

@section('content')
<style>
    /* Dropdown container */
    .dropdown {
        display: inline-block;
        position: relative;
    }

    /* Select input */
    .dropdown select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 5px 10px;
        font-size: 14px;
        cursor: pointer;
    }

    /* Apply button */
    .apply-btn {
        margin-left: 10px;
        padding: 5px 10px;
        font-size: 14px;
        color: #fff;
        background-color: #007bff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .apply-btn:hover {
        background-color: #0056b3;
    }
</style>
<div class="d-sm-flex align-items-center justify-content-between">
    <div>
        <h1 class="h4 mb-2 font-weight-bold text-gray-800"><i class="fas fa-plane-departure"></i> Inventory Approval</h1>
        <p class="mb-4">Approval Page.</p>
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

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold">Inventory Management</h6>
        <div class="text-right">
            <input type="hidden" name="formId" id="formId" value="" />
        </div>
    </div>
    <div class="card-body">
        <div class="row d-flex justify-content-start mb-4">
            <div class="col-md-12">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="dropdown">
                            <select id="bulkActions">
                                <option value="1">Approve Changes</option>
                                <option value="2">Reject Changes</option>
                            </select>
                            <button class="apply-btn">Execute</button>
                        </div>
                    </div>
                    <div class="col-md-8 text-right">
                        <span id="selectedCountBadge" class="badge bg-secondary text-white" style="display: none;">-</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered zoom90" id="assetsTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center" width="50px">
                            <div class="form-check form-check-inline larger-checkbox" style="transform: scale(1.5);">
                                <input class="form-check-input" type="checkbox" id="checkAll" onclick="toggleCheckboxes()">
                            </div>
                        </th>
                        <th>Change Requests</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Approve/Reject Modal -->
<div class="modal fade" id="actionModal" tabindex="-1" role="dialog" aria-labelledby="actionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="actionModalLabel">Approval Notes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="actionForm" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="action_notes">Notes:</label>
                        <textarea class="form-control" name="action_notes" id="action_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="modalSubmitButton">Send & Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
$(function () {
    $('#assetsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('approval.inventory-tool.data') }}',
        columns: [
            {
                data: 'checkbox',
                name: 'checkbox',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    return `
                        <label class="switch switch-3d switch-primary mr-3" style="transform: scale(1.5);">
                            <input type="checkbox" class="switch-input status-checkbox data-checkbox" data-form-id="${row.id}" onchange="toggleCheckboxes2()">
                            <span class="switch-label"></span>
                            <span class="switch-handle"></span>
                        </label>
                    `;
                }
            },
            { data: 'details', name: 'details', orderable: false, searchable: false }
        ]
    });
});
</script>
<script>
document.querySelector('.apply-btn').addEventListener('click', () => {
    const action = document.getElementById('bulkActions').value;
    const formIds = document.getElementById('formId').value;

    if (formIds === '') {
        swal({
            title: "No items selected!",
            text: "Please select at least one items to proceed.",
            icon: "warning",
            button: "OK",
        });
        return;
    }

    if (action === "1") {
        swal({
            title: "Are you sure?",
            text: "This will approve and update the selected assets.",
            icon: "warning",
            buttons: {
                cancel: {
                    text: "Cancel",
                    visible: true,
                    closeModal: true,
                    className: "btn btn-secondary"
                },
                confirm: {
                    text: "Yes, approve it!",
                    closeModal: false,
                    className: "btn btn-success"
                }
            },
            dangerMode: true,
        }).then((willApprove) => {
            if (willApprove) {
                $.ajax({
                    url: "{{ route('approval.inventory-tool.approve') }}",
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    data: {
                        formIds: formIds,
                    },
                    success: function (response) {
                        swal({
                            title: "Approved!",
                            text: response.message || "Assets updated successfully.",
                            icon: "success",
                            button: "OK",
                        }).then(() => {
                            $('#assetsTable').DataTable().ajax.reload();
                            $('#formId').val(''); // Reset hidden input if you're using one
                            updateSelectedCount(0); // Reset selected count badge if applicable
                        });
                    },
                    error: function (xhr) {
                        swal({
                            title: "Error!",
                            text: "Something went wrong: " + (xhr.responseJSON?.message || xhr.statusText),
                            icon: "error",
                            button: "OK",
                        });
                    }
                });
            }
        });
    } else if (action === "2") {
        swal({
            title: "Are you sure?",
            text: "This will reject the update.",
            icon: "warning",
            buttons: {
                cancel: {
                    text: "Cancel",
                    visible: true,
                    closeModal: true,
                    className: "btn btn-secondary"
                },
                confirm: {
                    text: "Yes, discard changes!",
                    closeModal: false,
                    className: "btn btn-secondary"
                }
            },
            dangerMode: true,
        }).then((willApprove) => {
            if (willApprove) {
                $.ajax({
                    url: "{{ route('approval.inventory-tool.approve.reject') }}",
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    data: {
                        formIds: formIds,
                    },
                    success: function (response) {
                        swal({
                            title: "Rejected!",
                            text: response.message || "Assets updated successfully.",
                            icon: "success",
                            button: "OK",
                        }).then(() => {
                            $('#assetsTable').DataTable().ajax.reload();
                            $('#formId').val(''); // Reset hidden input if you're using one
                            updateSelectedCount(0); // Reset selected count badge if applicable
                        });
                    },
                    error: function (xhr) {
                        swal({
                            title: "Error!",
                            text: "Something went wrong: " + (xhr.responseJSON?.message || xhr.statusText),
                            icon: "error",
                            button: "OK",
                        });
                    }
                });
            }
        });
    } else {
        // Handle other actions normally
        console.log(`Action: ${action}, Form IDs: ${formIds}`);
        // Add further logic if needed for other actions
    }
});

function toggleCheckboxes() {
    const checkboxes = document.querySelectorAll('.data-checkbox');
    const checkAllCheckbox = document.getElementById('checkAll');
    const formIdInput = document.getElementById('formId');

    const checkedFormIds = [];

    checkboxes.forEach((checkbox) => {
        checkbox.checked = checkAllCheckbox.checked;
        if (checkbox.checked) {
            const formId = checkbox.getAttribute('data-form-id');
            checkedFormIds.push(formId);
        }
    });

    formIdInput.value = checkedFormIds.join(', ');

    // Update badge with the count of selected checkboxes
    updateSelectedCount(checkedFormIds.length);
}

function updateSelectedCount(count) {
    const badge = document.getElementById('selectedCountBadge');
    if (count > 0) {
        badge.style.display = 'inline-block';
        badge.textContent = `${count} selected`;
    } else {
        badge.style.display = 'none';
    }
}


function toggleCheckboxes2() {
    const checkboxes = document.querySelectorAll('.data-checkbox');
    const formIdInput = document.getElementById('formId');

    const checkedFormIds = [];

    checkboxes.forEach((checkbox) => {
        if (checkbox.checked) {
            const formId = checkbox.getAttribute('data-form-id');
            checkedFormIds.push(formId);
        }
    });

    formIdInput.value = checkedFormIds.join(', ');

    // Update badge with the count of selected checkboxes
    updateSelectedCount(checkedFormIds.length);
}
</script>
@endsection
