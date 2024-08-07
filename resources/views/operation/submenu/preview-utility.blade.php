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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-cogs"></i> Penlat Utilities Usage</h1>
        <p class="mb-3">Unduh Pencapaian Akhlak.</a></p>
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

.custom-card {
    border: none;
    color: white;
    border-radius: 15px;
}

.card-text {
    margin-bottom: 0;
    color: rgb(0, 0, 0);
}

.out-of-stock {
    background-color: #dc3545;
    color: white;
    font-weight: bold;
}

.card-icons {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-icons .badge {
    background-color: red;
    color: rgb(255, 255, 255);
}

.card-icons i {
    font-size: 20px;
    color: rgb(0, 0, 0);
}

.card-icons a {
    color: rgb(0, 0, 0);
    text-decoration: none;
}

.card-icons a:hover {
    color: lightgray;
}

.img-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
}

.img-container img {
    max-height: 100%;
    max-width: 100%;
    object-fit: cover;
}

</style>
<div class="row zoom90">
    <div class="col-xl-12 col-lg-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> List Data</h6>
                <div class="text-right">
                    <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Update Data</a>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md-12 mb-3">
                    <div class="row">
                        <div class="col-md-2 d-flex align-items-center justify-content-center" style="padding-left: 1em;">
                            <img src="https://via.placeholder.com/50x50/5fa9f8/ffffff" style="height: 150px; width: 150px; border-radius: 15px;" class="card-img" alt="...">
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th style="width: 200px;">Penlat Name</th>
                                    <td style="text-align: start; font-weight:500">: Publish sites without the "Made with Carrd</td>
                                </tr>
                                <tr>
                                    <th>Batch</th>
                                    <td style="text-align: start; font-weight:500">: 1</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Pelaksanaan</th>
                                    <td style="text-align: start; font-weight:500">: 1</td>
                                </tr>
                                <tr>
                                    <th>Reference</th>
                                    <td style="text-align: start; font-weight:500">: N/a</td>
                                </tr>
                              </tr>
                          </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="animated fadeIn zoom90">
    {{-- <div class="row zoom90 mb-4">
        <div class="col-md-6">
            <div class="card custom-card mb-3 bg-white shadow">
                <div class="row no-gutters">
                    <div class="col-md-4 d-flex align-items-center justify-content-center" style="padding-left: 1em;">
                        <img src="{{ asset('img/kilang-minyak.jpg') }}" style="border-radius: 15px;" class="card-img" alt="...">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body text-secondary">
                            <div>
                                <h4 class="card-title font-weight-bold">Nama Penlat</h4>
                                <ul class="ml-3">
                                    <li class="card-text">Kebutuhan Tool 1</li>
                                    <li class="card-text">Kebutuhan Tool 2</li>
                                    <li class="card-text">Kebutuhan Tool 3</li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-icons">
                            <a href="#"><i class="fa fa-cog"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> List Data</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Add Utilities</a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-condensed table-bordered table-responsive mt-4">
                        <thead>
                            <tr>
                                <th style="width:60%">Tool</th>
                                <th style="width:10%">Quantity</th>
                                <th style="width:12%">Satuan</th>
                                <th style="width:16%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td data-th="Product">
                                    <div class="row">
                                        <div class="col-md-3 text-left">
                                            <img src="https://via.placeholder.com/250x250/5fa9f8/ffffff" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow ">
                                        </div>
                                        <div class="col-md-9 text-left mt-sm-2">
                                            <h4>Product Name</h4>
                                            <p class="font-weight-light">Brand &amp; Name</p>
                                        </div>
                                    </div>
                                </td>
                                <td data-th="Quantity">
                                    <input type="number" class="form-control form-control-lg text-center" value="1">
                                </td>
                                <td data-th="Price">$49.00</td>
                                <td class="actions text-center" data-th="">
                                    <div>
                                        <button class="btn btn-white border-secondary bg-white btn-md mb-2">
                                            <i class="fa fa-external-link"></i>
                                        </button>
                                        <button class="btn btn-white border-secondary bg-white btn-md mb-2">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td data-th="Product">
                                    <div class="row">
                                        <div class="col-md-3 text-left">
                                            <img src="https://via.placeholder.com/250x250/5fa9f8/ffffff" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow ">
                                        </div>
                                        <div class="col-md-9 text-left mt-sm-2">
                                            <h4>Product Name</h4>
                                            <p class="font-weight-light">Brand &amp; Name</p>
                                        </div>
                                    </div>
                                </td>
                                <td data-th="Quantity">
                                    <input type="number" class="form-control form-control-lg text-center" value="1">
                                </td>
                                <td data-th="Price">$49.00</td>
                                <td class="actions text-center" data-th="">
                                    <div>
                                        <button class="btn btn-white border-secondary bg-white btn-md mb-2">
                                            <i class="fa fa-external-link"></i>
                                        </button>
                                        <button class="btn btn-white border-secondary bg-white btn-md mb-2">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td data-th="Product">
                                    <div class="row">
                                        <div class="col-md-3 text-left">
                                            <img src="https://via.placeholder.com/250x250/5fa9f8/ffffff" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow ">
                                        </div>
                                        <div class="col-md-9 text-left mt-sm-2">
                                            <h4>Product Name</h4>
                                            <p class="font-weight-light">Brand &amp; Name</p>
                                        </div>
                                    </div>
                                </td>
                                <td data-th="Quantity">
                                    <input type="number" class="form-control form-control-lg text-center" value="1">
                                </td>
                                <td data-th="Price">$49.00</td>
                                <td class="actions text-center" data-th="">
                                    <div>
                                        <button class="btn btn-white border-secondary bg-white btn-md mb-2">
                                            <i class="fa fa-external-link"></i>
                                        </button>
                                        <button class="btn btn-white border-secondary bg-white btn-md mb-2">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

