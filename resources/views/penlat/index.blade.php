@extends('layouts.main')

@section('active-penlat')
active font-weight-bold
@endsection

@section('show-penlat')
show
@endsection

@section('penlat')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-list-alt"></i> List Pelatihan</h1>
        <p class="mb-4">List Pelatihan at MTC.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('participant-infographics-import-page') }}" class="btn btn-sm btn-primary shadow-sm text-white"><i class="fa fa-file-text fa-sm"></i> Import Data</a>
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
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Penlat</a>
                    </div>
                </div>
                <div class="card-body zoom90">
                    <form method="GET" action="{{ route('participant-infographics') }}">
                        @csrf
                        <div class="row d-flex justify-content-start mb-4">
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="email">Nama Penlat :</label>
                                            <select class="custom-select" id="namaPenlat" name="namaPenlat">
                                                <option value="1" selected>Show All</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="position_id">STCW/Non :</label>
                                            <select name="stcw" class="form-control" id="stcw">
                                                <option value="1">Show All</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="status">Jenis Penlat :</label>
                                            <select class="form-control" id="jenisPenlat" name="jenisPenlat" required>
                                                <option value="1" selected>Show All</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="position_id">TW :</label>
                                            <select name="tw" class="form-control" id="tw">
                                                <option value="1">Show All</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex align-self-end justify-content-start">
                                        <div class="form-group">
                                            <div class="align-self-center">
                                                <button type="submit" class="btn btn-primary" style="padding-left: 1.2em; padding-right: 1.2em;"><i class="ti-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table id="docLetter" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Display</th>
                                <th>Nama Program</th>
                                <th>Alias</th>
                                <th>Jenis Pelatihan</th>
                                <th>Kategori Program</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                            <tr>
                                <td class="text-center  d-flex flex-row align-items-center justify-content-center">
                                    <a href=""><img src="{{ asset($item->filepath) }}" style="height: 100px; width: 100px;" alt="" class="img-fluid d-none d-md-block rounded mb-2 shadow "></a>
                                </td>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->alias }}</td>
                                <td>{{ $item->jenis_pelatihan }}</td>
                                <td>{{ $item->kategori_pelatihan }}</td>
                                <td class="actions text-center" data-th="">
                                    <div>
                                        <a data-id="{{ $item->id }}" href="#" class="btn btn-outline-secondary btn-md mb-2 mr-2 edit-tool" data-toggle="modal" data-target="#editDataModal">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <button class="btn btn-outline-danger btn-md mb-2">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </div>
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

<div class="modal fade zoom90" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('penlat.store') }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="file-upload" style="cursor: pointer;">
                                <img id="image-preview" src="https://via.placeholder.com/50x50/5fa9f8/ffffff"
                                     style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                     <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="file-upload" type="file" name="display" style="display: none;" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Nama Program :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="nama_program" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Alias :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="alias">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Jenis Pelatihan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="jenis_pelatihan">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Kategori Program :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="kategori_program">
                                            </div>
                                        </div>
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
<div class="modal fade zoom90" id="editDataModal" tabindex="-1" role="dialog" aria-labelledby="editDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="editDataModalLabel">Edit Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body mr-2 ml-2">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="edit-file-upload" style="cursor: pointer;">
                                <img id="edit-image-preview" src="https://via.placeholder.com/50x50/5fa9f8/ffffff"
                                     style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                     <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="edit-file-upload" type="file" name="display" style="display: none;" accept="image/*" onchange="previewEditImage(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Repeat input fields here, but with id's for JS to fill them -->
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Nama Program :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" id="edit_nama_program" name="nama_program" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Alias :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" id="edit_alias" name="alias" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Jenis Pelatihan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" id="edit_jenis_pelatihan" name="jenis_pelatihan" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Kategori Pelatihan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" id="edit_kategori_pelatihan" name="kategori_pelatihan" required>
                                            </div>
                                        </div>
                                        <!-- Continue for alias, jenis_pelatihan, kategori_program -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('image-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function previewEditImage(event) {
        const output = document.getElementById('edit-image-preview');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function () {
            URL.revokeObjectURL(output.src) // Free up memory
        }
    }
</script>
<script>
$('.edit-tool').on('click', function () {
    let id = $(this).data('id');

    $.get('/penlat/' + id + '/edit', function (data) {
        $('#edit_id').val(data.id);
        $('#edit_nama_program').val(data.description);
        $('#edit_alias').val(data.alias);
        $('#edit_jenis_pelatihan').val(data.jenis_pelatihan);
        $('#edit_kategori_pelatihan').val(data.kategori_pelatihan);
        $('#edit-image-preview').attr('src', data.image);
        $('#editForm').attr('action', '/penlat-update/' + id);
        $('#editDataModal').modal('show');
    });
});
</script>
@endsection
