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
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="menu-icon fa fa-certificate"></i> Trainee Certificates</h1>
        <p class="mb-4">Sertifikat Trainee.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('certificate-main') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
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
                    <div class="d-flex">
                        <div class="text-right">
                            <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Certificate</a>
                        </div>
                    </div>
                </div>
                <div class="card-body zoom90 p-4">
                    <div class="row d-flex justify-content-start mb-4">
                        <div class="col-md-12">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <div class="form-group" id="penlatContainer">
                                        <label for="penlat">Training:</label>
                                        <select class="form-control" name="penlat" id="penlat">
                                            <option value="">Show All</option>
                                            @foreach ($penlatList as $item)
                                            <option value="{{ $item->id }}">{{ $item->description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" id="batchContainer">
                                        <label for="batch">Batch:</label>
                                        <select name="batch" class="form-control" id="batch">
                                            <option value="">Show All</option>
                                            @foreach ($listBatch as $item)
                                            <option value="{{ $item->batch }}">{{ $item->batch }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="batch">Training Category:</label>
                                        <select name="kategori_pelatihan" class="form-control" id="kategori_pelatihan">
                                            <option value="">Show All</option>
                                            @foreach ($penlatList->unique('kategori_pelatihan') as $item)
                                                <option value="{{ $item->kategori_pelatihan }}">{{ $item->kategori_pelatihan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="email">Year:</label>
                                        <select class="form-control" id="periode" name="periode">
                                            <option value="-1" selected>Show All</option>
                                            @foreach(range(date('Y'), date('Y') - 5) as $year)
                                                <option value="{{ $year }}">{{ $year }}</option>
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
                    <table id="listIssuedCertificates" class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Date of Conduct</th>
                                <th>Training</th>
                                <th>Batch</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Total Issued</th>
                                <th>Created by</th>
                                <th>Created at</th>
                                <th width="225px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be populated by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="inputDataModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 1300px;" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('certificate.store') }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 160px;" class="mr-2">
                                    <p style="margin: 0;">Training Name <span class="text-danger">*</span> :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <select id="penlatSelect" class="form-control" name="penlat">
                                        <option selected disabled>Select Pelatihan...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 160px;" class="mr-2">
                                    <p style="margin: 0;">Certificate Title <span class="text-danger">*</span> :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <textarea id="programInput" class="form-control" name="program" required></textarea>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 160px;" class="mr-2">
                                    <p style="margin: 0;">Batch <span class="text-danger">*</span> :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <select id="mySelect2" class="form-control" name="batch"></select>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 160px; margin-right: 10px;">
                                    <p style="margin: 0;">Periode <span class="text-danger">*</span> :</p>
                                </div>
                                <div class="d-flex flex-grow-1 align-items-center">
                                    <input type="date" id="startDate" class="form-control mr-2" name="startDate" style="max-width: 200px;" required>
                                    <span style="margin: 0 10px;">to</span>
                                    <input type="date" id="endDate" class="form-control" name="endDate" style="max-width: 200px;" required>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 160px;" class="mr-2">
                                    <p style="margin: 0;">Cert. Numbering <span class="text-danger">*</span> :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <select class="form-control" id="numbering" name="numbering">
                                        <option value="1" selected>Get From Master Data</option>
                                        <option value="0">Manually Adding</option>
                                        <option value="2">Custom Number</option>
                                    </select>
                                </div>
                            </div>

                            <div class="align-items-center mb-4" id="initialNumberContainer" style="display: none;">
                                <div style="width: 160px;" class="mr-2">
                                    <p style="margin: 0;">Initial Number <span class="text-danger">*</span> :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="number" id="initial_number" class="form-control" name="initial_number" style="max-width: 200px;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 160px;" class="mr-2">
                                    <p style="margin: 0;">Status <span class="text-danger">*</span> :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <select class="form-control" id="status" name="status">
                                        <option value="On Process" selected>On Process</option>
                                        <option value="Issued">Issued</option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 160px;" class="mr-2">
                                    <p style="margin: 0;">Amendment <span class="text-danger">*</span> :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <select class="form-control custom-select" id="regulator_amendment" name="regulator_amendment">
                                        <option value="-1" selected>No Regulations</option>
                                        @foreach ($listAmendment as $amendment)
                                            <option value="{{ $amendment->id }}" title="{{ $amendment->description }}">
                                                {{ $amendment->description }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="align-items-center mb-4" id="regulator_field" style="display: none;">
                                <div style="width: 160px;" class="mr-2">
                                    <p style="margin: 0;">Remarks <span class="text-danger">*</span> :</p>
                                </div>
                                <div class="flex-grow-1" id="regulator_container">
                                    <select class="form-control" id="regulator" name="regulator">
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
                                <div style="width: 160px;" class="mr-2">
                                    <p style="margin: 0;">Notes :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <textarea class="form-control" rows="3" name="keterangan"></textarea>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 160px;" class="mr-2">
                                    <p style="margin: 0;">Placeholder <span class="text-danger">*</span> :</p>
                                </div>
                                <div class="flex-grow-1 ml-3">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="autoSubmit" name="photo_placeholder" value="0" onchange="togglePlaceholderText()">
                                        <label class="custom-control-label" for="autoSubmit" id="placeholderLabel">Hide Photo Placeholder</label>
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
#regulator_container {
    position: relative !important;
}
#regulator_field {
    display: flex;
    align-items: center;
}
</style>
<script>
$(document).ready(function() {
    var table = $('#listIssuedCertificates').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('certificate') }}",
            data: function(d) {
                d.penlat = $('#penlat').val();
                d.batch = $('#batch').val();
                d.kategori_pelatihan = $('#kategori_pelatihan').val();
                d.periode = $('#periode').val();
            }
        },
        columns: [
            { data: 'tgl_pelaksanaan', name: 'tgl_pelaksanaan' },
            { data: 'batches.penlat.description', name: 'batches.penlat.description' },
            { data: 'batches.batch', name: 'batches.batch' },
            { data: 'kategori_pelatihan', name: 'kategori_pelatihan' },
            { data: 'status', name: 'status' },
            { data: 'keterangan', name: 'keterangan' },
            { data: 'jumlah_issued', name: 'jumlah_issued' },
            { data: 'created_by', name: 'created_by' },
            {
                data: 'created_at',
                name: 'created_at',
                render: function(data, type, row) {
                    if (type === 'display') {
                        return data.display;
                    }
                    return data.timestamp; // used for sorting/filtering
                }
            },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[8, 'desc']],
    });

    // Filter button click event
    $('#filterButton').click(function() {
        table.draw();
    });

    $('#listIssuedCertificates').on('click', '.delete-certificate', function() {
        var certificateId = $(this).data('id');

        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this certificate!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: '{{ route("certificate.delete") }}', // Define the delete route
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: certificateId
                    },
                    success: function (response) {
                        if (response.success) {
                            swal("Success!", "Certificate has been deleted.", "success")
                                .then(() => {
                                    table.draw(); // Redraw the DataTable to refresh the data
                                });
                        } else {
                            swal("Error!", "Certificate not found or could not be deleted.", "error");
                        }
                    },
                    error: function (xhr) {
                        swal("Error!", "There was an error deleting the certificate.", "error");
                    }
                });
            }
        });
    });
});

$(document).ready(function() {
    // Initialize Select2 for Penlat
    $('#penlat').select2({
        placeholder: "Select Pelatihan...",
        width: '100%',
        dropdownParent: $('#penlatContainer'),
        allowClear: true,
        language: {
            noResults: function() {
                return "No result match your request... Create new in Master Data Menu!"; // Customize this message as needed
            }
        }
    });

    $('#regulator_amendment').select2({
        dropdownParent: $('#inputDataModal'),
        placeholder: "Select Amendment...",
        width: '100%',
        allowClear: true,
        language: {
            noResults: function() {
                return "No result match your request... Create new in Master Data Menu!";
            }
        }
    }).on('select2:select', function (e) {
        const selectedValue = $(this).val(); // Get selected value
        const initialNumberContainer = $('#regulator_field');

        if (selectedValue === '-1') {
            initialNumberContainer.hide(); // Hide field
        } else {
            initialNumberContainer.show(); // Show field
        }
    });

    $('#regulator').select2({
        dropdownParent: $('#inputDataModal'),
        placeholder: "Select Regulator...",
        width: '100%',
        allowClear: true,
        language: {
            noResults: function() {
                return "No result match your request... Create new in Master Data Menu!"; // Customize this message as needed
            }
        }
    });

    $('#penlatSelect').select2({
        dropdownParent: $('#inputDataModal'),
        placeholder: "Select Pelatihan...",
        width: '100%',
        allowClear: true,
        ajax: {
            url: '{{ route('penlat.list') }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term // Pass search term
                };
            },
            processResults: function (data) {
                return {
                    results: data // Directly map to results since optgroup is already formatted
                };
            },
            cache: true
        },
        templateResult: function (data) {
            if (!data.id) {
                return $('<b>' + data.text + '</b>'); // Show alias (optgroup)
            }
            return $('<span>' + data.text + '</span>'); // Show description as selectable option
        },
        templateSelection: function (data) {
            return data.text || "Select Pelatihan..."; // Ensure placeholder works
        },
        language: {
            noResults: function () {
                return "No result match your request... Create new in Master Data Menu!";
            }
        }
    });

    $('#penlatSelect').on('change', function() {
        var selectedPenlatId = $(this).val(); // Get selected Training Name (Pelatihan) ID

        var selectedOption = $(this).find('option:selected').text().toUpperCase();
        $('#programInput').val(selectedOption);

        // Reinitialize the batch select dropdown, passing the selected penlat_id
        initSelect2WithAjax('mySelect2', '{{ route('batches.fetch.certificate') }}', 'Select or add a Batch', $(this).val());

        if (!selectedPenlatId) return;

        $.ajax({
            url: '{{ route('penlat.getAmendments') }}', // Create this route in Laravel
            method: 'GET',
            data: { penlat_id: selectedPenlatId }, // Send selected penlat_id
            success: function(response) {
                const initialNumberContainer = $('#regulator_field');

                if (!response.amendment) {
                    // No amendments found, so hide the field and reset values
                    initialNumberContainer.hide();
                    $('#regulator_amendment').val('-1').trigger('change');
                    return;
                }

                const selectedValue = response.amendment.id; // Get selected value

                if (selectedValue === '-1') {
                    initialNumberContainer.hide(); // Hide field
                } else {
                    initialNumberContainer.show(); // Show field
                }

                // Select the amendment in the dropdown
                $('#regulator_amendment').val(response.amendment.id).trigger('change');

                // Check if regulator exists
                if (response.amendment.regulator) {
                    setTimeout(() => { // Ensure it runs after amendment selection
                        $('#regulator').val(response.amendment.regulator.id).trigger('change');
                    }, 500);
                }
            },
            error: function() {
                const initialNumberContainer = $('#regulator_field');
                initialNumberContainer.hide(); // Hide field
                $('#regulator_amendment').val('-1').trigger('change');
            }
        });
    });

    $('#penlat').on('change', function() {
        // Reinitialize the batch select dropdown, passing the selected penlat_id
        initSelectFilter('batch', '{{ route('batches.fetch.certificate') }}', 'Select or add a Batch', $(this).val());
    });
    // Initialize Select2 with AJAX for the batch dropdown (default initialization)
    initSelect2WithAjax('mySelect2', '{{ route('batches.fetch.certificate') }}', 'Select or add a Batch', null);
    initSelectFilter('batch', '{{ route('batches.fetch.certificate') }}', 'Select or add a Batch', null);
});

function initSelect2WithAjax(elementId, ajaxUrl, placeholderText, penlatId = null) {
    $('#' + elementId).select2({
        ajax: {
            url: ajaxUrl,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    penlat_id: penlatId
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: $.map(data.items, function (item) {
                        return {
                            id: item.id,
                            text: item.text,
                            date: item.date // Include the date in the item object
                        };
                    }),
                    pagination: {
                        more: data.total_count > (params.page * 10)
                    }
                };
            },
            cache: true
        },
        placeholder: placeholderText,
        minimumInputLength: 1,
        dropdownParent: $('#inputDataModal'),
        width: '100%',
        tags: true,
        allowClear: true,
        createTag: function (params) {
            var term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true
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
        }
    });

    $('#' + elementId).on('select2:select', function (e) {
        var selectedBatch = e.params.data;

        if (selectedBatch.newTag) {
            var newOption = new Option(selectedBatch.text, selectedBatch.id, true, true);
            $(this).append(newOption).trigger('change');
        }

        // Prefill the date input if the selected batch has a date
        if (selectedBatch.date) {
            $('#startDate').val(selectedBatch.date);
        } else {
            $('#startDate').val(''); // Clear the input if no date is provided
        }
    });
}

function initSelectFilter(elementId, ajaxUrl, placeholderText, penlatId = null) {
    $('#' + elementId).select2({
        ajax: {
            url: ajaxUrl,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    penlat_id: penlatId
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: $.map(data.items, function (item) {
                        return {
                            id: item.id,
                            text: item.text,
                            date: item.date // Include the date in the item object
                        };
                    }),
                    pagination: {
                        more: data.total_count > (params.page * 10)
                    }
                };
            },
            cache: true
        },
        placeholder: placeholderText,
        minimumInputLength: 1,
        width: '100%',
        dropdownParent: $('#batchContainer'),
        tags: true,
        allowClear: true,
        createTag: function (params) {
            var term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true
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
        }
    });
}
function togglePlaceholderText() {
    let checkbox = document.getElementById("autoSubmit");
    let label = document.getElementById("placeholderLabel");
    label.textContent = checkbox.checked ? "Show Photo Placeholder" : "Hide Photo Placeholder";
}
</script>

<script>
    document.getElementById('numbering').addEventListener('change', function () {
        const selectedValue = this.value;
        const initialNumberContainer = document.getElementById('initialNumberContainer');
        const initialNumberInput = document.getElementById('initial_number');

        if (selectedValue === '2') { // Show input for "Custom Number"
            initialNumberContainer.style.display = 'flex';
            initialNumberInput.setAttribute('required', 'required');
        } else { // Hide input and remove "required"
            initialNumberContainer.style.display = 'none';
            initialNumberInput.removeAttribute('required');
            initialNumberInput.value = ''; // Clear the input value if hidden
        }
    });
</script>
@endsection
