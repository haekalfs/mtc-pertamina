@extends('layouts.main')

@section('active-operation')
active font-weight-bold
@endsection

@section('show-operation')
show
@endsection

@section('participant-infographics')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="menu-icon fa fa-users"></i> Importer Participants Infographics</h1>
        <p class="mb-4">Import Data From Excel.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('participant-infographics') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
    </div>
</div>
<div class="overlay overlay-mid" style="display: none;"></div>

<div class="alert alert-danger alert-success-delete-mid" role="alert" style="display: none;">
</div>

<div class="alert alert-success alert-success-saving-mid" role="alert" style="display: none;">
    Your entry has been saved successfully.
</div>
<div class="alert alert-warning alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>Importing Participants Infographics will also generate new batches in it along with the participants data.</strong>
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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">Upload File</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('infografis_peserta.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-11">
                                <div class="form-group">
                                    <label for="file">Xlsx File :</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="file" name="file" aria-describedby="file" onchange="displayFileName()">
                                        <label class="custom-file-label" for="file" id="file-label">Choose file</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1 d-flex justify-content-center align-items-end">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Import</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <span class="text-danger font-weight-bold">Data Import Guidelines</span>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" download href="{{ asset('uploads/template/template_realisasi.xlsx') }}"><i class="menu-Logo fa fa-download"></i> Download Example</a>
                    </div>
                </div>
                <div class="card-body" style="background-color: rgb(247, 247, 247);">
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">Import Rules</h6>
                    <ul class="ml-4">
                        <li>Ensure all import files are in the correct format (XLSX) as specified in the guidelines.</li>
                        <li>Maximum file size is 50MB.</li>
                        <li>Verify the data in the files for accuracy and completeness before importing.</li>
                        <li>Check for and resolve any data inconsistencies or errors in the file to prevent import issues.</li>
                        <li>Unauthorized changes to import files or procedures are strictly prohibited.</li>
                        <li class="text-danger">Double-check the mapping of data fields to ensure they align with the database schema, the layout of the data should look like below image.</li>
                    </ul>
                    <img src="{{ asset('uploads/example1.jpeg') }}" class="img-fluid" style="padding: 1rem;">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function displayFileName() {
        const input = document.getElementById('file');
        const label = document.getElementById('file-label');
        const file = input.files[0];
        if (file) {
            label.textContent = file.name;
        }
    }
</script>
<script>
    $(document).ready(function () {
      $('#liveToastBtn').click(function () {
        $('.toast').toast('show');
      });
    });
  </script>
@endsection
