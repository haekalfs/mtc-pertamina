@extends('layouts.main')

@section('active-operation')
active font-weight-bold
@endsection

@section('show-operation')
show
@endsection

@section('utility')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-cogs"></i> Utility Usage</h1>
        <p class="mb-4">Penggunaan Utilities.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        {{-- <a href="{{ route('participant-infographics-import-page') }}" class="btn btn-sm btn-primary shadow-sm text-white"><i class="fa fa-file-text fa-sm"></i> Import Data</a> --}}
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
<style>

.alert-success-saving-mid {
  display: none;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  padding: 20px;
  border-radius: 5px;
  text-align: center;
  z-index: 10000;
}
</style>
<div class="animated fadeIn zoom90">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" id="judul">List Data</h6>
                    <div class="d-flex">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> New Penlat Usage</a>
                    </div>
                </div>
                <div class="card-body zoom90">
                    <div class="row d-flex justify-content-start mb-4">
                        <div class="col-md-12">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="namaPenlat">Nama Pelatihan :</label>
                                        <select class="custom-select" id="namaPenlat" name="namaPenlat" onchange="updateHiddenFields()">
                                            <option value="-1">Show All</option>
                                            @foreach ($penlatList as $item)
                                            <option value="{{ $item->id }}">{{ $item->description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6" id="batchSelectContainer">
                                    <div class="form-group">
                                        <label for="batch">Batch :</label>
                                        <select id="batch" class="form-control" name="batch" onchange="updateHiddenFields()"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table id="listUsages" class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 100px;">Display</th>
                                <th>Pelatihan</th>
                                @foreach($utilities as $tool)
                                    <th>{{ $tool->utility_name }} ({{ $tool->utility_unit }})</th>
                                @endforeach
                                <th>Batch</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="pelatihanId" name="pelatihanId"/>
<input type="hidden" id="batchId" name="batchId"/>

<div class="modal fade" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 950px;" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('utility.store') }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters mb-3">
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="file-upload" style="cursor: pointer;">
                                <img id="image-preview" src="{{ asset('img/default-img.png') }}" style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                <small style="font-size: 12px;" class="text-secondary"><i><u>Uploading Image is Optional!</u></i></small>
                            </label>
                            <input id="file-upload" type="file" name="image" style="display: none;" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 150px;" class="mr-2">
                                    <p style="margin: 0;">Nama Pelatihan <span class="text-danger">*</span>:</p>
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
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 150px;" class="mr-2">
                                    <p style="margin: 0;">Nama Program <span class="text-danger">*</span>:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" id="programInput" class="form-control" name="program">
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 150px;" class="mr-2">
                                    <p style="margin: 0;">Batch <span class="text-danger">*</span>:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <select id="mySelect2" class="form-control" name="batch"></select>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 150px;" class="mr-2">
                                    <p style="margin: 0;">Tgl Pelaksanaan <span class="text-danger">*</span>:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="date" class="form-control" name="date">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-right mb-4">
                        <div class="col-md-12">
                            <div class="row align-items-center">
                                <div class="col-md-11">
                                    <div class="d-flex">
                                        <div style="width: 140px;" class="mr-2">
                                            <p style="margin: 0;">Utilities <span class="text-danger">*</span>:</p>
                                        </div>
                                        <div class="flex-grow-1">
                                            <select id="utilitiesSelect" class="form-control">
                                                @foreach ($utilities as $alat)
                                                    <option value="{{ $alat->id }}" data-name="{{ $alat->utility_name }}"
                                                            data-unit="{{ $alat->utility_unit }}"
                                                            data-img="{{ asset($alat->filepath) }}">
                                                        {{ $alat->utility_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex justify-content-center align-items-center">
                                    <div class="form-group m-0">
                                        <button type="button" class="btn btn-success" id="addUtilityBtn"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive zoom90">
                        <table class="table table-bordered mt-3" id="utilitiesTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tool</th>
                                    <th>Quantity</th>
                                    <th>Harga Satuan</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="no-data">
                                    <td colspan="4" class="text-center">Click + button to add new items...</td>
                                </tr>
                                @foreach($utilities as $tool)
                                <tr id="row_{{ $tool->id }}" style="display: none;">
                                    <td>
                                        <div class="row position-relative">
                                            <div class="col-md-4 text-left">
                                                <div class="image-wrapper">
                                                    <img src="{{ asset($tool->filepath) }}" style="height: 100px; width: 100px;" alt="" class="img-fluid rounded mb-2 shadow">
                                                </div>
                                            </div>
                                            <div class="col-md-8 text-left mt-sm-2">
                                                <h5>{{ $tool->utility_name }}</h5>
                                                <p class="font-weight-light">Satuan Default ({{$tool->utility_unit}})</p>
                                            </div>
                                            <a class="removeUtilityBtn position-absolute" style="top: -10px; right: 10px; font-size: 17px;" data-id="{{ $tool->id }}">
                                                <i class="fa fa-times text-danger"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td style="width:10%">
                                        <input type="text" class="form-control form-control-md underline-input text-center qty-input" name="qty_{{ $tool->id }}" id="qty_{{ $tool->id }}" value="0" min="0">
                                    </td>
                                    <td style="width:25%">
                                        <input type="text" class="form-control form-control-md underline-input price-input" name="price_{{ $tool->id }}" id="price_{{ $tool->id }}" value="0">
                                    </td>
                                    <td style="width:25%">
                                        <input type="text" class="form-control form-control-md underline-input total-input" name="total_{{ $tool->id }}" id="total_{{ $tool->id }}" value="0">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
$(document).ready(function() {
    $('#listUsages').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('utility') }}",
            data: function (d) {
                d.namaPenlat = $('#namaPenlat').val();
                d.batch = $('#batch').val();
            }
        },
        columns: [
            { data: 'image', name: 'image', orderable: false, searchable: false },
            { data: 'description', name: 'penlat.description' },
            @foreach($utilities as $tool)
                { data: 'utilities.utility_{{ $tool->id }}', name: '{{ $tool->id }}' },
            @endforeach
            { data: 'batch', name: 'batch' },
            { data: 'date', name: 'date' }
        ]
    });

    $('#namaPenlat, #batch').change(function() {
        $('#listUsages').DataTable().draw();
    });
});

$(document).ready(function() {
    // Initialize Select2 for Penlat
    $('#penlatSelect').select2({
        dropdownParent: $('#inputDataModal'),
        theme: "classic",
        placeholder: "Select Pelatihan...",
        width: '100%',
        allowClear: true,
        language: {
            noResults: function() {
                return "No result match your request... Create new in Master Data Menu!"; // Customize this message as needed
            }
        }
    });

    // Update hidden input on Penlat change
    $('#penlatSelect').on('change', function() {
        var selectedOption = $(this).find('option:selected').text();
        $('#programInput').val(selectedOption);

        // Reinitialize the batch select dropdown, passing the selected penlat_id
        initSelect2WithAjax('mySelect2', '{{ route('batches.fetch') }}', 'Select or add a Batch', $(this).val());
    });

    // Initialize Select2 with AJAX for the batch dropdown (default initialization)
    initSelect2WithAjax('mySelect2', '{{ route('batches.fetch') }}', 'Select or add a Batch', null);
    initBatchWithAjax('batch', '{{ route('batches.fetch') }}', 'Select or add a Batch', null);
});

function initBatchWithAjax(elementId, ajaxUrl, placeholderText, penlatId = null) {
    $('#' + elementId).select2({
        ajax: {
            url: ajaxUrl,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page || 1, // pagination
                    penlat_id: penlatId // pass penlat_id for filtering, if provided
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: $.map(data.items, function (item) {
                        return {
                            id: item.batch, // Use the 'batch' column for the option value
                            text: item.batch, // Use the 'batch' column for the option label
                            filepath: item.filepath, // Include filepath to use for image preview
                            date: item.date // Include date to prefill the date input
                        };
                    }),
                    pagination: {
                        more: data.total_count > (params.page * 10) // Check if more results are available
                    }
                };
            },
            cache: true
        },
        placeholder: placeholderText,
        minimumInputLength: 1, // Start searching after 1 character
        dropdownParent: $('#batchSelectContainer'),
        width: '100%',
        tags: true, // Allow adding new tags
        allowClear: true,
        createTag: function (params) {
            var term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true // Mark as a new tag
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
function initSelect2WithAjax(elementId, ajaxUrl, placeholderText, penlatId = null) {
    $('#' + elementId).select2({
        ajax: {
            url: ajaxUrl,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page || 1, // pagination
                    penlat_id: penlatId // pass penlat_id for filtering, if provided
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: $.map(data.items, function (item) {
                        return {
                            id: item.batch, // Use the 'batch' column for the option value
                            text: item.batch, // Use the 'batch' column for the option label
                            filepath: item.filepath, // Include filepath to use for image preview
                            date: item.date // Include date to prefill the date input
                        };
                    }),
                    pagination: {
                        more: data.total_count > (params.page * 10) // Check if more results are available
                    }
                };
            },
            cache: true
        },
        placeholder: placeholderText,
        minimumInputLength: 1, // Start searching after 1 character
        dropdownParent: $('#inputDataModal'),
        theme: 'classic',
        width: '100%',
        tags: true, // Allow adding new tags
        allowClear: true,
        createTag: function (params) {
            var term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true // Mark as a new tag
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
        // Check if the selected option is a new tag
        if (e.params.data.newTag) {
            var newOption = new Option(e.params.data.text, e.params.data.id, true, true);
            $(this).append(newOption).trigger('change');
            // For new tags, skip updating the image preview since there's no associated filepath
            $('#image-preview').attr('src', '{{ asset('img/default-img.png') }}');
            $('input[name="date"]').val(''); // Clear the date input for new tags
            $('#file-upload').val(''); // Clear the file input field as well
            return; // Exit early for new tags
        }

        // Proceed with regular batch processing for options fetched via AJAX
        const selectedBatch = e.params.data;

        // Clear the file input whenever a new batch is selected to avoid confusion
        $('#file-upload').val(''); // Reset the file input field
        $('#image-preview').attr('src', '');

        // Check if the selected batch has a valid filepath and update image preview
        if (selectedBatch.filepath && selectedBatch.filepath !== '') {
            $('#file-upload').val(''); // Reset the file input field
            $('#image-preview').attr('src', '{{ asset('') }}' + selectedBatch.filepath);
        } else {
            $('#image-preview').attr('src', '{{ asset('img/default-img.png') }}');
            $('#file-upload').val(''); // Reset the file input field
        }

        // Check if the selected batch has a valid date and prefill the date input
        if (selectedBatch.date) {
            $('input[name="date"]').val(selectedBatch.date);
        } else {
            $('input[name="date"]').val(''); // Clear the input if no date is provided
        }
    });
}
// Image file input preview function
function previewImage(event) {
    const reader = new FileReader();

    // Clear the current image preview immediately
    $('#image-preview').attr('src', '');

    reader.onload = function() {
        // Once the file is read, update the image preview with the new file
        const output = document.getElementById('image-preview');
        output.src = reader.result;
    };

    // Read the new file to trigger the onload event
    reader.readAsDataURL(event.target.files[0]);
}

document.querySelectorAll('.price-input, .qty-input').forEach(function(input) {
    // Listen to input for recalculating the total
    input.addEventListener('input', function() {
        calculateTotalForRow(this);
    });

    // Auto format the price while typing
    if (input.classList.contains('price-input')) {
        input.addEventListener('input', function() {
            formatPriceWhileTyping(this);
        });
    }

    // Restrict qty-input to only allow numbers (no commas or decimals)
    if (input.classList.contains('qty-input')) {
        input.addEventListener('input', function() {
            formatQtyWhileTyping(this);
        });
    }
});

function calculateTotalForRow(element) {
    const row = element.closest('tr');
    const priceInput = row.querySelector('.price-input');
    const qtyInput = row.querySelector('.qty-input');
    const totalInput = row.querySelector('.total-input');

    // Get raw price value (strip non-numeric characters except for commas and dots)
    let priceValue = priceInput.value.replace(/[^0-9.,]/g, '');

    // Replace commas with dots for decimals, and remove any thousands separators
    priceValue = priceValue.replace(/\./g, '').replace(',', '.');

    // Convert to float for calculation
    priceValue = parseFloat(priceValue) || 0;

    const qty = parseFloat(qtyInput.value) || 0;

    // Calculate total
    const total = Math.floor(priceValue * qty); // Remove decimals for total

    // Set the total value formatted as Rupiah
    totalInput.value = formatRupiah(total);
}

function formatPriceWhileTyping(input) {
    let cursorPosition = input.selectionStart; // Save the cursor position
    let priceValue = input.value.replace(/[^0-9]/g, ''); // Only allow numbers

    // Convert the string back to an integer for formatting
    let intValue = parseInt(priceValue) || 0;

    // Format the price as Rupiah with no decimals
    input.value = formatRupiah(intValue);
}

function formatQtyWhileTyping(input) {
    // Restrict the input to only allow numbers (no commas or decimals)
    let qtyValue = input.value.replace(/[^0-9.]/g, ''); // Only allow numbers

    // Set the cleaned qty value back to the input
    input.value = qtyValue;
}

function formatRupiah(number) {
    // Convert the number to a string and format with thousands separator
    var number_string = number.toString(),
        remainder = number_string.length % 3,
        rupiah = number_string.substr(0, remainder),
        thousands = number_string.substr(remainder).match(/\d{3}/gi);

    if (thousands) {
        var separator = remainder ? '.' : '';
        rupiah += separator + thousands.join('.');
    }

    return rupiah === undefined ? rupiah : (rupiah ? rupiah : '');
}
</script>
<script>
$(document).ready(function() {
    // Event listener for adding a utility
    $('#addUtilityBtn').on('click', function() {
        // Get the selected utility id from the dropdown
        let selectedUtilityId = $('#utilitiesSelect').val();

        // Escape special characters in the ID (replace slashes and other special characters)
        let escapedUtilityId = selectedUtilityId.replace(/([!"#$%&'()*+,.\/:;<=>?@[\\\]^`{|}~])/g, '\\$1');

        // Show the corresponding row for the selected utility
        let utilityRow = $('#row_' + escapedUtilityId);

        // Check if the row is already visible
        if (utilityRow.is(':visible')) {
            alert('This utility is already added to the table.');
            return;
        }

        // Make the row visible
        utilityRow.show();

        // Hide the 'No data available' message if it is visible
        $('#no-data').hide();
    });

    // Event listener for removing a utility row
    $(document).on('click', '.removeUtilityBtn', function() {
        // Get the id of the utility to remove
        let utilityId = $(this).data('id');

        // Find the corresponding row
        let utilityRow = $('#row_' + utilityId);

        // Reset all input fields in the row
        utilityRow.find('.qty-input').val('0');
        utilityRow.find('.price-input').val('0');
        utilityRow.find('.total-input').val('0');

        // Hide the row
        utilityRow.hide();

        // Check if any rows are still visible
        if ($('#utilitiesTable tbody tr:visible').length === 0) {
            // Show 'No data available' if no rows are visible
            $('#no-data').show();
        }
    });
});
function updateHiddenFields() {
    // Get the selected values
    var pelatihanId = document.getElementById("namaPenlat").value;
    var batchId = document.getElementById("batch").value;

    // Set the hidden input fields
    document.getElementById("pelatihanId").value = pelatihanId;
    document.getElementById("batchId").value = batchId;
}
</script>
@endsection
