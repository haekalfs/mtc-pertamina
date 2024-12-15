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
        <p class="mb-4">Costs Report.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('costs.import') }}" class="btn btn-sm btn-primary shadow-sm text-white"><i class="fa fa-file-text fa-sm"></i> Import Data</a>
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

@if ($message = Session::get('error_log'))
<div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{!! $message !!}</strong>
</div>
@endif

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif
<div class="animated fadeIn">
    <div class="row">
        <div class="col-md-12 zoom90">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" id="judul">List Data</h6>
                </div>
                <div class="card-body">
                    <table id="errorTable" class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Description</th>
                                <th>Row</th>
                                <th>Importer</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-12 zoom90">
            <div class="card mb-4">
                <div class="card-header">
                    <span class="text-danger font-weight-bold">User Manual</span>
                </div>
                <div class="card-body" style="background-color: rgb(247, 247, 247);">
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">General Guidelines</h6>
                    <ul class="ml-4" style="line-height:200%">
                        <li>Table diatas adalah List Pelatihan beserta Revenue, Cost & Profit.</li>
                        <li>Nominal dari table diatas, diambil dari semua batch yang dimiliki oleh pelatihan tersebut.</li>
                        <li>List Pelatihan adalah Induk Data dari Penlat, yang mana 1 Pelatihan bisa memiliki banyak batch.</li>
                        <li>Data di samping adalah summary dari semua revenue, cost & profit data pelatihan.</li>
                        <li class="text-danger font-weight-bold">Untuk menampilkan summary & grafik, anda perlu memilih periode tahun.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function () {
    $('#errorTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('finance.error.log') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }, // Auto-increment index
            { data: 'description', name: 'description' },
            { data: 'row', name: 'row' },
            { data: 'importer', name: 'importer' }, // Importer column from relationship
            { data: 'created_at', name: 'created_at' },
        ],
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
