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
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-cogs"></i> Penlat Utilities Usage</h1>
        <p class="mb-3">Utilities Details Information.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('utility') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
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
                <h6 class="m-0 font-weight-bold text-secondary" id="judul">Detail Batch Penlat</h6>
                <div class="text-right">
                    {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Update Data</a> --}}
                </div>
            </div>
            <div class="card-body">
                <div class="col-md-12 mb-3">
                    <div class="row">
                        <div class="col-md-3 d-flex align-items-center justify-content-center" style="padding-left: 1em;">
                            <img src="{{ $data->filepath ? asset($data->filepath) : asset('img/default-img.png') }}" style="height: 150px; width: 200px; border: 1px solid rgb(202, 202, 202);" class="img-fluid d-none d-md-block rounded mb-2 shadow ">
                        </div>
                        <div class="col-md-9">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th style="width: 200px;">Nama Pelatihan</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->penlat->description }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 200px;">Nama Program</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->nama_program }}</td>
                                </tr>
                                <tr>
                                    <th>Batch</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->batch }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Pelaksanaan</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->date }}</td>
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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">List Fasilitas</h6>
                    <div class="text-right">
                        @if($utilities->isNotEmpty())
                            <button data-id="{{ $data->id }}" class="btn btn-outline-primary btn-sm mr-2" href="#" data-toggle="modal" data-target="#inputDataModal">
                                <i class="fa fa-plus"></i> Add New Utility
                            </button>
                        @endif
                        <button data-id="{{ $data->id }}" class="btn btn-outline-danger btn-outline-danger-usage btn-sm">
                            <i class="fa fa-trash-o"></i> Delete All
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="listUtilities" class="table table-bordered mt-4">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tool</th>
                                    <th>Quantity</th>
                                    <th>Harga Satuan</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data->penlat_usage as $tool)
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-md-4 text-left">
                                                    <img src="{{ asset($tool->utility->filepath) }}" style="height: 100px; width: 100px; border: 1px solid rgb(202, 202, 202);" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
                                                </div>
                                                <div class="col-md-8 text-left mt-sm-2">
                                                    <h5>{{ $tool->utility->utility_name }}</h5>
                                                    <p class="font-weight-light">Satuan Default ({{$tool->utility->utility_unit}})</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="width:10%">
                                            <input type="number" class="form-control form-control-md underline-input qty-input" name="qty_{{ $tool->id }}" value="{{ $tool->amount }}">
                                        </td>
                                        <td style="width:20%">
                                            <input type="text" class="form-control form-control-md underline-input price-input" name="price_{{ $tool->id }}" value="{{ $tool->price ? 'IDR ' . number_format($tool->price, 0, ',', '.') : '-' }}">
                                        </td>
                                        <td style="width:20%">
                                            <input type="text" class="form-control form-control-md underline-input total-input" name="total_{{ $tool->id }}" value="{{ $tool->total ? 'IDR ' . number_format($tool->total, 0, ',', '.') : '-' }}">
                                        </td>
                                        <td class="actions text-center">
                                            <button class="btn btn-white btn-md border-secondary bg-white mr-2 update-utility" data-id="{{ $tool->id }}">
                                                <i class="fa fa-save"></i>
                                            </button>
                                            <button data-id="{{ $tool->id }}" class="btn btn-outline-danger btn-delete-item-usage btn-md">
                                                <i class="fa fa-trash-o"></i>
                                            </button>
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
            <form method="post" enctype="multipart/form-data" action="{{ route('utility.insert', $data->id) }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row d-flex justify-content-right mb-4">
                        <div class="col-md-12">
                            <div class="row align-items-center">
                                <div class="col-md-11">
                                    <div class="d-flex">
                                        <div style="width: 140px;" class="mr-2">
                                            <p style="margin: 0;">Utilities :</p>
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
                                @foreach($utilities as $tool)
                                <tr id="row_{{ $tool->id }}" style="display: none;">
                                    <td>
                                        <div class="row">
                                            <div class="col-md-4 text-left">
                                                <img src="{{ asset($tool->filepath) }}" style="height: 100px; width: 100px;" alt=""
                                                     class="img-fluid d-none d-md-block rounded mb-2 shadow ">
                                            </div>
                                            <div class="col-md-8 text-left mt-sm-2">
                                                <h5>{{ $tool->utility_name }}</h5>
                                                <p class="font-weight-light">Satuan Default ({{$tool->utility_unit}})</p>
                                            </div>
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
<script>
$(document).on('click', '.update-utility', function(e) {
    e.preventDefault();
    let toolId = $(this).data('id');
    let quantity = $('input[name="qty_' + toolId + '"]').val();
    let price = $('input[name="price_' + toolId + '"]').val();
    let total = $('input[name="total_' + toolId + '"]').val();

    // Confirmation using SweetAlert
    swal({
        title: "Are you sure?",
        text: "Do you want to update the quantity?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willUpdate) => {
        if (willUpdate) {
            $.ajax({
                url: '{{ route("utility.update", ":id") }}'.replace(':id', toolId),
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    amount: quantity,
                    price: price,
                    total: total
                },
                success: function(response) {
                    swal("Success!", "Utility usage updated successfully.", "success")
                        .then(() => {
                            location.reload(); // Reload the page after success
                        });
                },
                error: function(xhr, status, error) {
                    let errorMessage = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : "Oops! Something went wrong!";

                    swal("Error!", errorMessage, "error");
                }
            });
        }
    });
});

$(document).on('click', '.btn-delete-item-usage', function() {
    let id = $(this).data('id');
    let url = "{{ route('delete.item.usage', ':id') }}";
    url = url.replace(':id', id);

    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this file!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.status === 'success') {
                        swal("Poof! Usage has been deleted!", {
                            icon: "success",
                        }).then(() => {
                            location.reload(); // Reload the page after success
                        });
                    } else {
                        swal(response.message, {
                            icon: "error",
                        });
                    }
                },
                error: function(response) {
                    swal("An error occurred while deleting the item.", {
                        icon: "error",
                    });
                }
            });
        }
    });
});

$(document).ready(function() {
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
    });
});

$(document).on('click', '.btn-outline-danger-usage', function() {
    let id = $(this).data('id');
    let url = "{{ route('delete.usage', ':id') }}";
    url = url.replace(':id', id);

    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this file!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.status === 'success') {
                        swal("Poof! Usage has been deleted!", {
                            icon: "success",
                        }).then(() => {
                            window.location.href = "{{ route('utility') }}";
                        });
                    } else {
                        swal(response.message, {
                            icon: "error",
                        });
                    }
                },
                error: function(response) {
                    swal("An error occurred while deleting the item.", {
                        icon: "error",
                    });
                }
            });
        }
    });
});

document.querySelectorAll('.price-input, .qty-input').forEach(function(input) {
    input.addEventListener('input', function() {
        calculateTotalForRow(this);
    });
});

function calculateTotalForRow(element) {
    const row = element.closest('tr');
    const priceInput = row.querySelector('.price-input');
    const qtyInput = row.querySelector('.qty-input');
    const totalInput = row.querySelector('.total-input');

    // Format the price as Rupiah
    let priceValue = priceInput.value.replace(/[^0-9]/g, '');
    priceValue = priceValue ? parseFloat(priceValue) : 0;
    priceInput.value = formatRupiah(priceValue, 'IDR ');

    const qty = parseFloat(qtyInput.value) || 0;

    // Calculate total
    const total = priceValue * qty;

    // Format total as Rupiah
    totalInput.value = formatRupiah(total, 'IDR ');
}

function formatRupiah(number, prefix) {
    var number_string = number.toString().replace(/[^,\d]/g, ''),
        split = number_string.split(','),
        remainder = split[0].length % 3,
        rupiah = split[0].substr(0, remainder),
        thousands = split[0].substr(remainder).match(/\d{3}/gi);

    if (thousands) {
        var separator = remainder ? '.' : '';
        rupiah += separator + thousands.join('.');
    }

    rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix === undefined ? rupiah : (rupiah ? prefix + rupiah : '');
}

</script>
@endsection

