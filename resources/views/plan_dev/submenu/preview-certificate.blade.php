@extends('layouts.main')

@section('active-pd')
active font-weight-bold
@endsection

@section('show-pd')
show
@endsection

@section('certificate')
font-weight-bold
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
.hoverable-cell:hover {
    /* Add your hover styles here */
    background-color: rgb(238, 238, 238); /* Example background color change on hover */
    cursor: pointer; /* Change cursor on hover */
}
.hoverable-cell i {
    display: none;
}

.hoverable-cell:hover i {
    /* Display the icon when hovering */
    display: initial;
}
</style>
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-certificate"></i> Preview Certificate</h1>
        <p class="mb-3">Status : @if($data->status == 'Issued') <span class="text-success"><i class="fa fa-check"></i> Issued</span> @else <span><i class="fa fa-spinner"></i> On Process</span> @endif</p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('certificate') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
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
                <h6 class="m-0 font-weight-bold text-secondary" id="judul">Detail Pelatihan</h6>
            </div>
            <div class="card-body" style="position: relative;">
                <a href="#"
                    class="editButton position-absolute"
                    data-toggle="modal"
                    data-target="#editDataModal"
                    data-amendment-id="{{ $data->regulator_amendment }}"
                    data-regulator-id="{{ $data->regulator }}"
                    style="top: 10px; right: 15px; z-index: 10;">
                        <i class="fa fa-edit fa-lg ml-2" style="color: rgb(181, 181, 181);"></i>
                </a>
                <div class="col-md-12 mb-3">
                    <div class="row">
                        <div class="col-md-3 d-flex align-items-center justify-content-center" style="padding-left: 1em;">
                            <img src="{{ $data->batch->filepath ? asset($data->batch->filepath) : asset('img/default-img.png') }}" style="height: 150px; width: 200px; border: 1px solid rgb(202, 202, 202);" class="img-fluid d-none d-md-block rounded mb-2 shadow ">
                        </div>
                        <div class="col-md-9">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th style="width: 200px;">Certificate Title</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->certificate_title }}</td>
                                </tr>
                                <tr>
                                    <th>Batch</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->batch->batch }}</td>
                                </tr>
                                <tr>
                                    <th>Training Category</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->batch->penlat->kategori_pelatihan }}</td>
                                </tr>
                                <tr>
                                    <th>Date of Conduct</th>
                                    <td style="text-align: start; font-weight:500">: {{ \Carbon\Carbon::parse($data->start_date)->format('d F Y') }} - {{ \Carbon\Carbon::parse($data->end_date)->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 200px;">Remarks</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->regulation->description ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 200px;">Notes</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->keterangan ?? '-' }}</td>
                                </tr>
                              </tr>
                          </table>
                          <small class="font-weight-bold text-danger">
                            Editing batch is only available on the
                            <a href="{{ route('batch-penlat') }}" class="text-danger">
                                <u><i>Batch Program Page</i></u>
                            </a>.
                            </small>
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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">Bulk Actions</h6>
                    <div class="text-right">
                        <form id="refreshBatchForm">
                            @csrf
                            <input type="hidden" name="batchInput" id="batchInput" value="{{ $data->batch->batch }}" />
                            <input type="hidden" name="penlatCertificateId" id="penlatCertificateId" value="{{ $data->id }}" />
                            <button type="button" id="refreshBatchBtn" class="btn btn-sm btn-secondary shadow-sm text-white">
                                <i class="fa fa-refresh fa-sm"></i> Refresh Data
                            </button>
                        </form>
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
                                            <option value="1">Export Certificate</option>
                                            <option value="2">Mark as Received</option>
                                            <option value="3">Mark as Issued</option>
                                            <option value="4">Mark as Expire</option>
                                            @usr_acc(206)
                                            <option value="5">Delete Permanently</option>
                                            @endusr_acc
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
                    <table id="listCertificates" class="table table-bordered mt-4">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center">
                                    <div class="form-check form-check-inline larger-checkbox" style="transform: scale(1.5);">
                                        <input class="form-check-input" type="checkbox" id="checkAll" onclick="toggleCheckboxes()">
                                    </div>
                                </th>
                                <th>Registration Num.</th>
                                <th>Participant Name</th>
                                <th width="150px">Cert No.</th>
                                <th width="150px">Date of Issued</th>
                                <th width="150px">Date of Expire</th>
                                <th width="150px">Date of Received</th>
                                <th width="50px">Status</th>
                                <th width="150px">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editItemModal" tabindex="-1" role="dialog" aria-labelledby="editItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="editItemModalLabel">Edit Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data">
                @csrf
                <!-- Hidden Input to Store ID -->
                <input type="hidden" id="participantId" name="id" value="">

                <div class="modal-body mr-2 ml-2">
                    <div class="mb-4">
                        <p style="margin: 0;" id="participantName"></p>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 140px;" class="mr-2">
                            <p style="margin: 0;">Tgl Terbit :</p>
                        </div>
                        <div class="flex-grow-1">
                            <input type="date" id="issuedDate" class="form-control" name="issuedDate">
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 140px;" class="mr-2">
                            <p style="margin: 0;">Tgl Expire :</p>
                        </div>
                        <div class="flex-grow-1">
                            <input type="date" id="expireDate" class="form-control" name="expireDate">
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 140px;" class="mr-2">
                            <p style="margin: 0;">Tgl Diterima :</p>
                        </div>
                        <div class="flex-grow-1">
                            <input type="date" id="receivedDate" class="form-control" name="receivedDate">
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 140px;" class="mr-2">
                            <p style="margin: 0;">Nomor Certificate :</p>
                        </div>
                        <div class="flex-grow-1">
                            <input type="text" id="certificateNumber" class="form-control" name="certificateNumber" required>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 140px;" class="mr-2">
                            <p style="margin: 0;">Status :</p>
                        </div>
                        <div class="flex-grow-1">
                            <select class="form-control" id="certificateStatus" name="certificateStatus" required>
                                <option selected disabled>Select Status...</option>
                                <option value="0">Pending</option>
                                <option value="1">Issued</option>
                            </select>
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
<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="qrModalLabel">QR Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="printContainer">
                    <span id="qrTitle" class="p-2 font-weight-bold" style="margin-bottom: 10px;"></span>
                    <div id="qrCodeContainer"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <a id="link" class="btn btn-primary">Validate Certificate</a>
            </div>
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
            <form method="post" enctype="multipart/form-data" action="{{ route('certificate.update', $data->id) }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 140px;" class="mr-2">
                            <p style="margin: 0;">Certificate Title :</p>
                        </div>
                        <div class="flex-grow-1">
                            <input type="text" id="programInput" class="form-control" name="program" value="{{ $data->certificate_title }}" required>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 140px; margin-right: 10px;">
                            <p style="margin: 0;">Periode :</p>
                        </div>
                        <div class="d-flex flex-grow-1 align-items-center">
                            <input type="date" id="startDate" class="form-control mr-2" name="startDate" style="max-width: 200px;" value="{{ $data->start_date }}" required>
                            <span style="margin: 0 10px;">to</span>
                            <input type="date" id="endDate" class="form-control" name="endDate" style="max-width: 200px;" value="{{ $data->end_date }}" required>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 140px;" class="mr-2">
                            <p style="margin: 0;">Amendment :</p>
                        </div>
                        <div class="flex-grow-1">
                            <select class="form-control" id="regulator_amendment" name="regulator_amendment">
                                <option disabled selected>Select Amendment...</option>
                                <option value="-1" selected>No Amandment</option>
                                @foreach ($listAmendment as $amendment)
                                    <option value="{{ $amendment->id }}">{{ $amendment->description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4" id="regulator_field">
                        <div style="width: 140px;" class="mr-2">
                            <p style="margin: 0;">Remarks :</p>
                        </div>
                        <div class="flex-grow-1">
                            <select class="form-control" id="regulator" name="regulator">
                                <option disabled selected>Select Options...</option>
                                <option value="-1" selected>No Remarks</option>
                                @foreach ($listRegulator as $regulator)
                                    <option value="{{ $regulator->id }}" title="{{ $regulator->description }}">
                                        {{ $regulator->description }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-4">
                        <div style="width: 140px;" class="mr-2">
                            <p style="margin: 0;">Notes :</p>
                        </div>
                        <div class="flex-grow-1">
                            <textarea class="form-control" rows="3" name="keterangan">{{ $data->keterangan }}</textarea>
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
    $(document).ready(function () {
        var tableCertificate = $('#listCertificates').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: `/certificates/{{ $data->id }}`, // Pass the participantId dynamically
                type: "GET",
            },
            columns: [
                {
                    data: null,
                    render: function (data, type, row) {
                        return `<label class="switch switch-3d switch-primary mr-3" style="transform: scale(1.5);">
                                    <input type="checkbox" class="switch-input status-checkbox data-checkbox" data-form-id="${row.id}">
                                    <span class="switch-label"></span>
                                    <span class="switch-handle"></span>
                                </label>`;
                    },
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                },
                { data: 'participant_registration_number', name: 'participant_registration_number' },
                { data: 'participant_name', name: 'participant_name' },
                {
                    data: 'certificate_number',
                    name: 'certificate_number',
                },
                {
                    data: 'issued_date',
                    name: 'issued_date',
                },
                {
                    data: 'expire_date',
                    name: 'expire_date',
                },
                {
                    data: 'date_received',
                    name: 'date_received',
                },
                { data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-center' },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    className: 'text-center' // Add this line
                },
            ],
            order: [[2, 'asc']],
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'All']],
            pageLength: 25,
        });

        tableCertificate.on('draw', function () {
            $('#listCertificates').on('change', '.data-checkbox', toggleCheckboxes2);
            $('#checkAll').on('change', toggleCheckboxes);
        });

        $('#listCertificates').on('click', '.edit-button', function () {
            var id = $(this).data('id');
            var participantName = $(this).data('participant-name');
            var expireDate = $(this).data('expire-date');
            var receivedDate = $(this).data('received-date');
            var issuedDate = $(this).data('issued-date');
            var certificateNumber = $(this).data('certificate-number');
            var status = $(this).data('certificate-status');

            $('#participantId').val(id);
            $('#participantName').text(participantName);
            $('#expireDate').val(expireDate);
            $('#receivedDate').val(receivedDate);
            $('#issuedDate').val(issuedDate);
            if (status !== null && status !== false) {
                $('#certificateStatus').val(status.toString()); // Convert status to string to match the <option> values
            } else {
                $('#certificateStatus').val('0'); // Default to "Pending" if status is null or false
            }

            if (!certificateNumber || certificateNumber.trim() === '') {
                // If certificateNumber is empty, fetch the next available number
                $.ajax({
                    url: '/get-next-certificate-number/' + id,
                    type: 'GET',
                    success: function (response) {
                        if (response.nextID) {
                            $('#certificateNumber').val(response.nextID); // Set the fetched number
                        } else {
                            alert('Failed to fetch certificate number.');
                        }
                    },
                    error: function (xhr) {
                        alert('An error occurred while fetching the certificate number.');
                    }
                });
            } else {
                $('#certificateNumber').val(certificateNumber);
            }

            $('#editItemModal').modal('show');
        });

        // Handle form submission via AJAX
        $('#editItemModal form').on('submit', function (e) {
            e.preventDefault();

            var formData = $(this).serialize();
            var participantId = $('#participantId').val();

            swal({
                title: "Confirm Update",
                text: "Are you sure you want to update this data?",
                icon: "warning",
                buttons: ["Cancel", "Proceed"],
                dangerMode: true,
            }).then((willProceed) => {
                if (willProceed) {
                    $.ajax({
                        url: `/certificate/${participantId}/update`,
                        method: "POST",
                        data: formData,
                        success: function (response) {
                            if (response.success) {
                                $('#editItemModal').modal('hide');
                                swal({
                                    title: "Success!",
                                    text: "Data updated successfully.",
                                    icon: "success",
                                }).then(() => {
                                    tableCertificate.draw();
                                });
                            } else {
                                swal({
                                    title: "Error!",
                                    text: response.message,
                                    icon: "error",
                                });
                            }
                        },
                        error: function (xhr) {
                            swal({
                                title: "Error!",
                                text: "There was an error processing the request: " + xhr.responseText,
                                icon: "error",
                            });
                        },
                    });
                }
            });
        });

        $('#listCertificates').on('click', '.generateQR', function () {
            var certficateId = $(this).data('id');
            $.ajax({
                url: '{{ route("generate-qr-certificate", "") }}/' + certficateId,
                type: 'GET',
                success: function(response) {
                    $('#qrTitle').text(response.nama_peserta);
                    $('#link').attr('href', response.link);  // Use .attr() to set the href attribute
                    $('#qrCodeContainer').html('<img src="' + response.qr_code + '" alt="QR Code" />');  // Embed the QR code as an image
                    $('#qrModal').modal('show');  // Show the modal
                },
                error: function() {
                    alert('Failed to generate QR code.');
                }
            });
        });

        $('#refreshBatchBtn').on('click', function () {
            const batch = $('#batchInput').val();
            const penlatCertificateId = $('#penlatCertificateId').val();

            swal({
                title: "Are you sure?",
                text: "This will refresh the data for the selected batch.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willRefresh) => {
                if (willRefresh) {
                    // Send AJAX request
                    $.ajax({
                        url: "{{ route('refresh.participants') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            batch: batch,
                            penlatCertificateId: penlatCertificateId,
                        },
                        success: function (response) {
                            swal({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                            }).then(() => {
                                tableCertificate.draw();
                            });
                        },
                        error: function (xhr, status, error) {
                            swal({
                                title: "Error!",
                                text: "An error occurred while refreshing the data. Please try again.",
                                icon: "error",
                            });
                        },
                    });
                }
            });
        });


        $('#regulator_amendment').select2({
            dropdownParent: $('#editDataModal'),
            placeholder: "Select Amendment...",
            width: '100%',
            allowClear: true,
            language: {
                noResults: function() {
                    return "No result match your request... Create new in Templates Menu!";
                }
            }
        });
        $('#regulator').select2({
            dropdownParent: $('#editDataModal'),
            placeholder: "Select Regulator...",
            width: '100%',
            allowClear: true,
            language: {
                noResults: function() {
                    return "No result match your request... Create new in Templates Menu!";
                }
            }
        });

        $(document).on('click', '.editButton', function () {
            var amendmentId = $(this).data('amendment-id');
            var regulatorId = $(this).data('regulator-id');

            // Set the selected value for amendment select
            $('#regulator_amendment').val(amendmentId || -1).trigger('change');

            // Set the selected value for regulator select
            $('#regulator').val(regulatorId || -1).trigger('change');
        });
    });

    function updateSelectedCount(count) {
        const badge = document.getElementById('selectedCountBadge');
        if (count > 0) {
            badge.style.display = 'inline-block';
            badge.textContent = `${count} selected`;
        } else {
            badge.style.display = 'none';
        }
    }

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

    // Event listener for individual checkboxes
    document.querySelectorAll('.data-checkbox').forEach((checkbox) => {
        checkbox.addEventListener('change', toggleCheckboxes2);
    });

    document.querySelector('.apply-btn').addEventListener('click', () => {
        const action = document.getElementById('bulkActions').value;
        const formIds = document.getElementById('formId').value;

        if (formIds === '') {
            swal({
                title: "No items selected!",
                text: "Please select at least one participant to proceed.",
                icon: "warning",
                button: "OK",
            });
            return;
        }

        if (action === "2") {
            // Trigger SweetAlert with a date input field
            swal({
                title: "Mark as Received",
                text: "Please select the date received:",
                content: {
                    element: "input",
                    attributes: {
                        type: "date",
                    },
                },
                icon: "info",
                buttons: ["Cancel", "Proceed"],
            }).then((selectedDate) => {
                if (selectedDate) {
                    // Show a loading message before sending the request
                    swal({
                        title: "Processing...",
                        text: `Marking participants as received on ${selectedDate}`,
                        icon: "info",
                        buttons: false,
                        closeOnClickOutside: false,
                    });

                    // Send AJAX request to process the selected action
                    $.ajax({
                        url: "{{ route('mark.received') }}", // Replace with your actual route
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}", // CSRF token for protection
                        },
                        data: {
                            formIds: formIds, // Send selected IDs
                            dateReceived: selectedDate, // Send the selected date
                        },
                        success: function (response) {
                            if (response.success) {
                                swal({
                                    title: "Success!",
                                    text: "The participants have been marked as received.",
                                    icon: "success",
                                    button: "OK",
                                }).then(() => {
                                    // Optionally reload the page or update the table
                                    location.reload();
                                });
                            } else {
                                swal({
                                    title: "Error!",
                                    text: "Something went wrong while processing the request.",
                                    icon: "error",
                                    button: "OK",
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            swal({
                                title: "Error!",
                                text: "There was an error processing the request: " + xhr.responseText,
                                icon: "error",
                                button: "OK",
                            });
                        },
                    });
                }
            });
        } else if (action === "4") {
            // Trigger SweetAlert with a date input field
            swal({
                title: "Mark as Expired",
                text: "Please select the date received:",
                content: {
                    element: "input",
                    attributes: {
                        type: "date",
                    },
                },
                icon: "info",
                buttons: ["Cancel", "Proceed"],
            }).then((selectedDate) => {
                if (selectedDate) {
                    // Show a loading message before sending the request
                    swal({
                        title: "Processing...",
                        text: `Marking participants as received on ${selectedDate}`,
                        icon: "info",
                        buttons: false,
                        closeOnClickOutside: false,
                    });

                    // Send AJAX request to process the selected action
                    $.ajax({
                        url: "{{ route('mark.expired') }}", // Replace with your actual route
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}", // CSRF token for protection
                        },
                        data: {
                            formIds: formIds, // Send selected IDs
                            dateReceived: selectedDate, // Send the selected date
                        },
                        success: function (response) {
                            if (response.success) {
                                swal({
                                    title: "Success!",
                                    text: "The participants have been marked as received.",
                                    icon: "success",
                                    button: "OK",
                                }).then(() => {
                                    // Optionally reload the page or update the table
                                    location.reload();
                                });
                            } else {
                                swal({
                                    title: "Error!",
                                    text: "Something went wrong while processing the request.",
                                    icon: "error",
                                    button: "OK",
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            swal({
                                title: "Error!",
                                text: "There was an error processing the request: " + xhr.responseText,
                                icon: "error",
                                button: "OK",
                            });
                        },
                    });
                }
            });
        } else if (action === "1") {
            swal({
                title: "Processing...",
                text: "Your request is being processed. The download will begin shortly.",
                icon: "info",
                buttons: false,
                closeOnClickOutside: false,
            });

            // Automatically close the swal after 3 seconds
            setTimeout(() => swal.close(), 3000);

            // Send AJAX request to process the selected action
            $.ajax({
                url: "{{ route('certificate.export.selected') }}",
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                data: {
                    formIds: formIds,
                },
                success: function (response) {
                    if (response.success) {
                        // Trigger file download
                        window.location.href = response.fileUrl;
                        $('#listCertificates').DataTable().draw();
                    } else {
                        swal({
                            title: "Error!",
                            text: response.message || "Something went wrong while processing the request.",
                            icon: "error",
                            button: "OK",
                        });
                    }
                },
                error: function (xhr, status, error) {
                    swal({
                        title: "Error!",
                        text: "There was an error processing the request: " + xhr.responseText,
                        icon: "error",
                        button: "OK",
                    });
                },
            });
        } else if (action === "5") {
            swal({
                title: "Are you sure?",
                text: "This action will permanently delete the selected participants. This cannot be undone!",
                icon: "warning",
                buttons: ["Cancel", "Delete"],
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    // Show loading message
                    swal({
                        title: "Deleting...",
                        text: "Please wait while the participants are being deleted.",
                        icon: "info",
                        buttons: false,
                        closeOnClickOutside: false,
                    });

                    // Send AJAX request for deletion
                    $.ajax({
                        url: "{{ route('receivable.participant.delete') }}", // Update with your actual route
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}", // CSRF token for protection
                        },
                        data: {
                            ids: formIds.split(','), // Pass selected IDs as an array
                        },
                        success: function (response) {
                            if (response.status === 'success') {
                                swal({
                                    title: "Success!",
                                    text: "The selected participants have been permanently deleted.",
                                    icon: "success",
                                    button: "OK",
                                }).then(() => {
                                    // Optionally reload the page or update the table
                                    location.reload();
                                });
                            } else {
                                swal({
                                    title: "Error!",
                                    text: "Failed to delete the selected participants. Please try again.",
                                    icon: "error",
                                    button: "OK",
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            swal({
                                title: "Error!",
                                text: "There was an error processing the request: " + xhr.responseText,
                                icon: "error",
                                button: "OK",
                            });
                        },
                    });
                }
            });
        } else if (action === "3") {
            swal({
                title: "Mark as Issued",
                text: "Please select the date issued (optional):",
                content: {
                    element: "input",
                    attributes: {
                        type: "date",
                    },
                },
                icon: "info",
                buttons: ["Cancel", "Proceed"],
            }).then((selectedDate) => {
                // If the user clicks "Cancel", selectedDate will be null, and we stop the process.
                if (selectedDate === null) {
                    return;
                }

                swal({
                    title: "Processing...",
                    text: "Your request is being processed. Please wait.",
                    icon: "info",
                    buttons: false,
                    closeOnClickOutside: false,
                });

                // Automatically close the swal after 3 seconds
                setTimeout(() => swal.close(), 3000);

                // Send AJAX request to process the selected action
                $.ajax({
                    url: "{{ route('set-as-issued') }}",
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    data: {
                        formIds: formIds,
                        dateReceived: selectedDate || null, // Pass null if no date is selected
                    },
                    success: function (response) {
                        if (response.success) {
                            swal({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                                button: "OK",
                            }).then(() => {
                                $('#listCertificates').DataTable().draw(); // Refresh the table
                            });
                        } else {
                            swal({
                                title: "Error!",
                                text: response.message || "Something went wrong while processing the request.",
                                icon: "error",
                                button: "OK",
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        swal({
                            title: "Error!",
                            text: "There was an error processing the request: " + xhr.responseText,
                            icon: "error",
                            button: "OK",
                        });
                    },
                });
            });
        } else {
            // Handle other actions normally
            console.log(`Action: ${action}, Form IDs: ${formIds}`);
            // Add further logic if needed for other actions
        }
    });
</script>
@endsection

