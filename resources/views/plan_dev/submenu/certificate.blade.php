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
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="menu-icon fa fa-certificate"></i> Certificate</h1>
        <p class="mb-4">Sertifikat Trainee.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        {{-- <a href="{{ route('feedback-report-import-page') }}" class="btn btn-sm btn-primary shadow-sm text-white"><i class="fa fa-file-text fa-sm"></i> Import Data</a> --}}
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
                        {{-- <a id="addApproversBtn" class="btn btn-sm btn-primary shadow-sm text-white"><i class="fa fa-file-text fa-sm"></i> Filter</a>
                        <a id="hideApproversBtn" class="btn btn-sm btn-secondary shadow-sm text-white" style="display: none;"><i class="fa fa-backward fa-sm"></i> Cancel</a> --}}
                    </div>
                </div>
                <div class="card-body zoom80">
                    <form method="GET" action="">
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
                                            <label for="position_id">Batch :</label>
                                            <select name="stcw" class="form-control" id="stcw">
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
                                <th>Alias</th>
                                <th>Nama Penlat</th>
                                <th>Batch</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>BSS</td>
                                <td>Basic Sea Survival</td>
                                <td>....</td>
                                <td>Issued</td>
                                <td>Done</td>
                                <td>10</td>
                                <td class="text-center">
                                    <a class="btn btn-outline-secondary btn-sm" href="#" data-toggle="modal" data-target="#updateProcessModal"><i class="menu-Logo fa fa-eye"></i> Action</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade zoom90" id="updateProcessModal" tabindex="-1" role="dialog" aria-labelledby="updateProcessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header d-flex flex-row align-items-center justify-content-between border-bottom-1">
          <h5 class="modal-title" id="updateProcessModalLabel">Update Process</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form>
            <div class="d-flex align-items-center mb-4">
                <div style="width: 140px;" class="mr-2">
                    <p style="margin: 0;">Nama Penlat :</p>
                </div>
                <div class="flex-grow-1">
                    <input type="text" class="form-control" name="number" required>
                </div>
            </div>
            <div class="d-flex align-items-center mb-4">
                <div style="width: 140px;" class="mr-2">
                    <p style="margin: 0;">Batch Penlat :</p>
                </div>
                <div class="flex-grow-1">
                    <input type="text" class="form-control" name="date_released">
                </div>
            </div>
            <div class="d-flex align-items-center mb-4">
                <div style="width: 140px;" class="mr-2">
                    <p style="margin: 0;">Status :</p>
                </div>
                <div class="flex-grow-1">
                    <select class="form-control" id="status">
                      <option value="option1">Option 1</option>
                      <option value="option2">Option 2</option>
                      <option value="option3">Option 3</option>
                    </select>
                </div>
            </div>
          </form>
          <!-- DataTable -->
          <table id="dataTable" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Nama Peserta</th>
                <th>Checkbox</th>
              </tr>
            </thead>
            <tbody>
              <!-- DataTable rows will be populated here -->
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
</div>
<script>
    document.getElementById('addApproversBtn').addEventListener('click', function() {
        // Hide the "Add Approvers" button
        document.getElementById('addApproversBtn').style.display = 'none';
        // Show the form
        document.getElementById('addApproverForm').style.display = 'block';
        document.getElementById('hideApproversBtn').style.display = 'block';
    });
    document.getElementById('hideApproversBtn').addEventListener('click', function() {
        // Hide the "Add Approvers" button
        document.getElementById('addApproversBtn').style.display = 'block';
        // Show the form
        document.getElementById('addApproverForm').style.display = 'none';
        document.getElementById('hideApproversBtn').style.display = 'none';
    });

    function displayFileName() {
        const input = document.getElementById('file');
        const label = document.getElementById('file-label');
        const file = input.files[0];
        if (file) {
            label.textContent = file.name;
        }
    }
</script>
@endsection
