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
                                            <option value="{{ $item->id }}">{{ $item->batch }}</option>
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

<div class="modal fade zoom90" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
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
                                <img id="image-preview" src="https://via.placeholder.com/50x50/5fa9f8/ffffff"
                                     style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                     <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="file-upload" type="file" name="image" style="display: none;" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Nama Pelatihan :</p>
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
                                    <p style="margin: 0;">Nama Program :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" id="programInput" class="form-control" name="program">
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Batch :</p>
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
                                    <p style="margin: 0;">Tgl Pelaksanaan :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="date" class="form-control" name="date">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive zoom90">
                        <table class="table table-bordered mt-4">
                            <thead>
                                <tr>
                                    <th>Tool</th>
                                    <th>Quantity</th>
                                    <th>Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($utilities as $tool)
                                <tr>
                                    <td data-th="Product">
                                        <div class="row">
                                            <div class="col-md-3 text-left">
                                                <img src="{{ asset($tool->filepath) }}" style="height: 100px; width: 100px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow ">
                                            </div>
                                            <div class="col-md-8 text-left mt-sm-2">
                                                <h5>{{ $tool->utility_name }}</h5>
                                                <p class="font-weight-light">Satuan Default ({{$tool->utility_unit}})</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Quantity" style="width:20%">
                                        <input type="number" class="form-control form-control-md text-center" name="qty_{{ $tool->id }}" value="1">
                                    </td>
                                    <td data-th="Price" style="width:20%">
                                        <select class="custom-select form-control form-control-sm" name="unit_{{ $tool->id }}">
                                            <option value="{{ $tool->utility_unit }}" selected>{{ $tool->utility_unit }}</option>
                                            <!-- Add other options if necessary -->
                                        </select>
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
                d.stcw = $('#batch').val();
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
