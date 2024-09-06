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
                                        <select class="custom-select" id="namaPenlat" name="namaPenlat">
                                            <option value="-1">Show All</option>
                                            @foreach ($penlatList as $item)
                                            <option value="{{ $item->id }}">{{ $item->description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="batch">Batch :</label>
                                        <select name="batch" class="form-control" id="batch">
                                            <option value="-1">Show All</option>
                                            @foreach ($batchList as $item)
                                            <option value="{{ $item->batch }}">{{ $item->batch }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table id="listUsages" class="table table-bordered">
                        <thead class="bg-secondary text-white">
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

<div class="modal fade" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 900px;" role="document">
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
                                <img id="image-preview" src="https://via.placeholder.com/150x150/5fa9f8/ffffff"
                                     style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                     <small style="font-size: 12px;" class="text-secondary"><i><u>Uploading Image is Optional!</u></i></small>
                            </label>
                            <input id="file-upload" type="file" name="image" style="display: none;" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
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
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Nama Program <span class="text-danger">*</span>:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" id="programInput" class="form-control" name="program">
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Batch <span class="text-danger">*</span>:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <select id="mySelect2" class="form-control" name="batch">
                                        @foreach ($batchList as $item)
                                            <option value="{{ $item->batch }}">{{ $item->batch }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Tgl Pelaksanaan <span class="text-danger">*</span>:</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="date" class="form-control" name="date">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive zoom90">
                        <table class="table table-bordered mt-3">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tool</th>
                                    <th>Harga Satuan</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($utilities as $tool)
                                <tr>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-4 text-left">
                                                <img src="{{ asset($tool->filepath) }}" style="height: 100px; width: 100px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow ">
                                            </div>
                                            <div class="col-md-8 text-left mt-sm-2">
                                                <h5>{{ $tool->utility_name }}</h5>
                                                <p class="font-weight-light">Satuan Default ({{$tool->utility_unit}})</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="width:20%">
                                        <input type="text" class="form-control form-control-md underline-input price-input" data-type="currency" name="price_{{ $tool->id }}" id="price_{{ $tool->id }}">
                                    </td>
                                    <td style="width:20%">
                                        <input type="number" class="form-control form-control-md text-center underline-input qty-input" name="qty_{{ $tool->id }}" id="qty_{{ $tool->id }}" value="1" min="0">
                                    </td>
                                    <td style="width:20%">
                                        <input type="text" class="form-control form-control-md underline-input total-input" name="total_{{ $tool->id }}" id="total_{{ $tool->id }}" readonly>
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
<script>
    document.getElementById('penlatSelect').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex].text;
        document.getElementById('programInput').value = selectedOption;
    });
</script>
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
    // Initialize Select2
    $('#penlatSelect').select2({
        dropdownParent: $('#inputDataModal'),
        theme: "classic",
        placeholder: "Select Pelatihan...",
        width: '100%',
        tags: true,
    });

    // Event listener for change event
    $('#penlatSelect').on('change', function() {
        var selectedOption = $(this).find('option:selected').text();
        $('#programInput').val(selectedOption);
    });
});

$(document).ready(function() {
    $('#mySelect2').select2({
        dropdownParent: $('#inputDataModal'),
        theme: "classic",
        placeholder: "Select or add a Batch",
        width: '100%',
        tags: true,
        createTag: function(params) {
            var term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true // Mark this as a new tag
            };
        },
        templateResult: function(data) {
            // Only show the "Add new" label if it's a new tag
            if (data.newTag) {
                return $('<span><em>Add new: "' + data.text + '"</em></span>');
            }
            return data.text;
        },
        templateSelection: function(data) {
            // Show only the text for the selected item
            return data.text;
        }
    });

    $('#mySelect2').on('select2:select', function(e) {
        if (e.params.data.newTag) {
            // Show a notification that a new record is added

            // After the new option is added, remove the "newTag" property
            var newOption = new Option(e.params.data.text, e.params.data.id, true, true);
            $(this).append(newOption).trigger('change');
        }
    });
});
$(document).ready(function() {
    // Listen for keyup and blur events on the price and quantity fields
    $("input[data-type='currency'], .qty-input").on({
        keyup: function() {
            formatCurrency($(this));
            updateTotal($(this).closest('tr'));  // Calculate total on keyup
        }
    });

    // Function to calculate and update the total
    function updateTotal(row) {
        var priceInput = row.find('.price-input').val().replace(/[IDR,]/g, ''); // Remove IDR and commas
        var qtyInput = row.find('.qty-input').val();

        var price = parseFloat(priceInput) || 0;

        // Calculate the total
        var total = price * qtyInput;

        // Format the total as currency and update the total input field
        row.find('.total-input').val('IDR ' + formatNumber(total.toFixed(2)));
    }

    // Format number function (same as you had before)
    function formatNumber(n) {
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // Format currency function (same as you had before)
    function formatCurrency(input, blur) {
        var input_val = input.val();

        if (input_val === "") { return; }

        var original_len = input_val.length;
        var caret_pos = input.prop("selectionStart");

        if (input_val.indexOf(".") >= 0) {
            var decimal_pos = input_val.indexOf(".");
            var left_side = input_val.substring(0, decimal_pos);
            var right_side = input_val.substring(decimal_pos);

            left_side = formatNumber(left_side);
            right_side = formatNumber(right_side);

            if (blur === "blur") {
                right_side += "00";
            }

            right_side = right_side.substring(0, 2);
            input_val = "IDR " + left_side + "." + right_side;
        } else {
            input_val = formatNumber(input_val);
            input_val = "IDR " + input_val;

            if (blur === "blur") {
                input_val += ".00";
            }
        }

        input.val(input_val);

        var updated_len = input_val.length;
        caret_pos = updated_len - original_len + caret_pos;
        input[0].setSelectionRange(caret_pos, caret_pos);
    }
});
</script>
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('image-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection
