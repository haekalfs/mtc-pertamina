@extends('layouts.main')

@section('active-finance')
active font-weight-bold
@endsection

@section('show-finance')
show
@endsection

@section('cost')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="menu-icon ti-stats-down"></i> Profits & Loss</h1>
        <p class="mb-4">Profits & Loss : {{ $selectedPenlat->description }}</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('profits') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
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

@if ($message = Session::get('batch-registration'))
<div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{!! $message !!}</strong>
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
                    <h6 class="m-0 font-weight-bold" id="judul">List Batch</h6>
                    <div class="d-flex">
                        {{-- <a id="addApproversBtn" class="btn btn-sm btn-primary shadow-sm text-white"><i class="fa fa-file-text fa-sm"></i> Filter</a>
                        <a id="hideApproversBtn" class="btn btn-sm btn-secondary shadow-sm text-white" style="display: none;"><i class="fa fa-backward fa-sm"></i> Cancel</a> --}}
                    </div>
                </div>
                <div class="card-body zoom80">
                    {{-- <div class="row d-flex justify-content-start mb-4">
                        <div class="col-md-12">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email">Nama Penlat :</label>
                                        <select class="custom-select" id="namaPenlat" name="namaPenlat">
                                            <option value="-1">Show All</option>
                                            @foreach($penlatList as $penlat)
                                                <option value="{{ $penlat->id }}" {{ $penlat->id == $selectedPenlat->id ? 'selected' : '' }}>
                                                    {{ $penlat->description }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Month :</label>
                                        <select class="form-control" id="jenisPenlat" name="jenisPenlat" required>
                                            <option value="-1" selected>Show All</option>
                                            @foreach(range(1, 12) as $month)
                                                <option value="{{ $month }}">{{ date("F", mktime(0, 0, 0, $month, 1)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="position_id">Periode :</label>
                                        <select name="periode" class="form-control" id="periode">
                                            <option value="-1" selected>Show All</option>
                                            @foreach(range(date('Y'), date('Y') - 5) as $year)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <table id="profitsTable" class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Tanggal Pelaksanaan</th>
                                <th>Nama Pelatihan</th>
                                <th>Batch</th>
                                <th>Jumlah Peserta</th>
                                <th>Revenue &nbsp;<i class="fa fa-plus text-success"></i></th>
                                <th>Cost &nbsp;<i class="fa fa-minus text-danger"></i></th>
                                <th>Nett Income</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">Overall Penggunaan Utilitas</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Add Utilities</a> --}}
                    </div>
                </div>
                <div class="card-body">
                    <table id="docLetter" class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:60%">Tool</th>
                                <th style="width:10%">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($listUtilities->isNotEmpty())
                                @foreach($listUtilities as $tool)
                                    <tr>
                                        <td data-th="Product">
                                            <div class="row">
                                                <div class="col-md-4 text-left">
                                                    <img src="{{ asset($tool['filepath']) }}" style="height: 100px; width: 100px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow">
                                                </div>
                                                <div class="col-md-8 text-left mt-sm-2">
                                                    <h5>{{ $tool['utility_name'] }}</h5>
                                                    <p class="font-weight-light">Satuan Default ({{ $tool['utility_unit'] }})</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center" style="width:7%">
                                            {{ $tool['amount'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="text-center">
                                    <td colspan="2">No Data Available</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <span class="text-danger font-weight-bold">Summary</span>
                </div>
                <div class="card-body" style="background-color: rgb(247, 247, 247);">
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">General Guidelines</h6>
                    <ul class="ml-4">
                        <li>Table diatas adalah List Batch beserta Revenue, Cost & Nett Income.</li>
                        <li>Nominal dari table diatas, diambil dari masing-masing revenue, cost & nett Income batch tersebut.</li>
                        <li>List Batch adalah Sub data dari List Pelatihan, batch dibuat berdasarkan Induk Data Pelatihan.</li>
                        <li>Data di samping adalah total penggunaan utilitas dari semua batch diatas.</li>
                        <li>Anda bisa mengklik detail dari pelatihan tersebut dengan mengklik batchnya, yang nanti akan menampilkan informasi lebih detail, beserta list costnya.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="createBatchModal" tabindex="-1" role="dialog" aria-labelledby="createBatchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 900px;" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="createBatchModalLabel">Register Batch</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('batch.store') }}">
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
                                    <input type="text" class="form-control" id="batch" name="batch">
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
$('#createBatchModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var batch = button.data('batch'); // Extract batch info from data-* attributes
    var id = button.data('id'); // Extract id info
    var tgl = button.data('tgl'); // Extract tgl_pelaksanaan info

    var modal = $(this);
    modal.find('.modal-body #batch').val(batch);
    modal.find('.modal-body #item-id').val(id);
    modal.find('.modal-body input[name="date"]').val(tgl); // Pre-fill the date field
});
$(document).ready(function() {
    var table = $('#profitsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('cost', [$selectedPenlat->id, $month, $year]) }}", // Ensure this route points to the controller's costs method
            data: function(d) {
                d.namaPenlat = $('#namaPenlat').val();
                d.jenisPenlat = $('#jenisPenlat').val();
                d.periode = $('#periode').val();
            }
        },
        columns: [
            { data: 'tgl_pelaksanaan', name: 'tgl_pelaksanaan' },
            { data: 'description', name: 'batch.penlat.description', orderable: false, searchable: false },
            { data: 'pelaksanaan', name: 'pelaksanaan' },
            { data: 'jumlah_peserta', name: 'jumlah_peserta' },
            { data: 'revenue', name: 'revenue' },
            { data: 'cost', name: 'cost' },
            { data: 'nett_income', name: 'nett_income' }
        ]
    });

    $('#namaPenlat, #jenisPenlat, #periode').change(function() {
        table.draw();
    });

    // Initialize Select2
    $('#penlatSelect').select2({
        dropdownParent: $('#createBatchModal'),
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
