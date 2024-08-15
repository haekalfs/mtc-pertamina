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

<div class="animated fadeIn">
    <div class="row">
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 timesheet">
            <a href="{{ route('certificate') }}" class="clickable-card">
                <div class="card border-left-primary shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">Certificate Peserta</div>
                                <div class="h6 mb-0 text-gray-800"><span style="font-size: 14px;">Status Sertifikasi Peserta</span></div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-trophy fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="mb-4">
            <div class="outer">
                <div class="inner"></div>
            </div>
        </div>
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-4 col-md-6 medical">
            <a href="{{ route('certificate-instructor') }}" class="clickable-card">
                <div class="card border-left-success shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">
                                    Certificate Instruktur</div>
                                    <div class="h6 mb-0 text-gray-800"><span style="font-size: 14px;">Sertifikasi Trainer/Instruktur</span></div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-male fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
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
                        <select class="form-control" id="year" name="year">
                            <option>2024</option>
                        </select>
                    </div>
                </div>
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="custom-nav-home-tab" data-toggle="tab" href="#custom-nav-home" role="tab" aria-controls="custom-nav-home" aria-selected="true"> Participant Certificate</a>
                        <a class="nav-item nav-link" id="custom-nav-profile-tab" data-toggle="tab" href="#custom-nav-profile" role="tab" aria-controls="custom-nav-profile" aria-selected="false"> Instruktur Certificate</a>
                    </div>
                </nav>
                <div class="card-body zoom90 p-4">
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="custom-nav-home" role="tabpanel" aria-labelledby="custom-nav-home-tab">
                            <table id="docLetter" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Pelatihan</th>
                                        <th>Alias</th>
                                        <th>Batch</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $item)
                                    <tr>
                                        <td>{{ $item->batch->penlat->description}}</td>
                                        <td>{{ $item->batch->penlat->alias }}</td>
                                        <td>{{ $item->batch->batch }}</td>
                                        <td>{{ $item->status }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                        <td>{{ $item->total_issued }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="custom-nav-profile" role="tabpanel" aria-labelledby="custom-nav-profile-tab">
                            <table id="requestForm" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Judul Sertifikat</th>
                                        <th>Issuing Organization</th>
                                        <th>Related To</th>
                                        <th>Keterangan</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($instructorData as $item)
                                    <tr>
                                        <td>{{ $item->certificate_name }}</td>
                                        <td>{{ $item->issuedBy }}</td>
                                        <td>
                                            @php
                                                $badgeColors = ['bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'bg-primary', 'bg-secondary'];
                                            @endphp

                                            @foreach($item->relationOne as $index => $relatedItem)
                                                <span class="badge text-white {{ $badgeColors[$index % count($badgeColors)] }}">{{ $relatedItem->penlat->description }}</span>
                                            @endforeach
                                        </td>
                                        <td>{{ $item->keterangan }}</td>
                                        <td>{{ $item->total_issued }}</td>
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
                    <select class="form-control" name="penlat">
                        @foreach ($penlatList as $item)
                        <option value="{{ $item->id }}">{{ $item->description }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="d-flex align-items-center mb-4">
                <div style="width: 140px;" class="mr-2">
                    <p style="margin: 0;">Batch Penlat :</p>
                </div>
                <div class="flex-grow-1">
                    <select name="stcw" class="form-control" id="stcw">
                        @foreach ($listBatch as $item)
                        <option value="{{ $item->batch }}">{{ $item->batch }}</option>
                        @endforeach
                    </select>
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
                <th>Received</th>
                <th>Date</th>
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

<div class="modal fade zoom90" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('certificate.store') }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 140px;" class="mr-2">
                            <p style="margin: 0;">Nama Pelatihan :</p>
                        </div>
                        <div class="flex-grow-1">
                            <select id="penlatSelect" class="form-control" name="penlat">
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
                                @foreach ($listBatch as $item)
                                    <option value="{{ $item->batch }}">{{ $item->batch }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 140px;" class="mr-2">
                            <p style="margin: 0;">Status :</p>
                        </div>
                        <div class="flex-grow-1">
                            <select class="form-control" id="status" name="status">
                                <option value="On Process" selected>On Process</option>
                                <option value="Issued">Issued</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-4">
                        <div style="width: 140px;" class="mr-2">
                            <p style="margin: 0;">Keterangan :</p>
                        </div>
                        <div class="flex-grow-1">
                            <textarea class="form-control" rows="3" name="keterangan"></textarea>
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
$(document).ready(function() {
    $('#mySelect2').select2({
        dropdownParent: $('#inputDataModal'),
        theme: "classic",
        placeholder: "Select or add a Batch Penlat",
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

<script>
    document.getElementById('penlatSelect').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex].text;
        document.getElementById('programInput').value = selectedOption;
    });
</script>
@endsection
