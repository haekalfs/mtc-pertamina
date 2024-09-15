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
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-certificate"></i> Preview Certificate</h1>
        <p class="mb-3">Status : @if($data->status == 'Issued') <span class="text-success"><i class="fa fa-check"></i> Issued</span> @else <span><i class="fa fa-spinner"></i> On Process</span> @endif</p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('certificate') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
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
                <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> List Data</h6>
            </div>
            <div class="card-body" style="position: relative;">
                <a href="#" data-toggle="modal" data-target="#editDataModal" class="position-absolute" style="top: 10px; right: 15px; z-index: 10;">
                    <i class="fa fa-edit fa-lg ml-2" style="color: rgb(181, 181, 181);"></i>
                </a>
                <div class="col-md-12 mb-3">
                    <div class="row">
                        <div class="col-md-3 d-flex align-items-center justify-content-center" style="padding-left: 1em;">
                            <img src="{{ $data->batch->filepath ? asset($data->batch->filepath) : asset('img/default-img.png') }}" style="height: 150px; width: 200px; border: 1px solid rgb(202, 202, 202);" class="img-fluid d-none d-md-block rounded mb-2 shadow ">
                        </div>
                        <div class="col-md-9">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th style="width: 200px;">Nama Pelatihan</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->batch->penlat->description }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 200px;">Nama Program</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->batch->nama_program }}</td>
                                </tr>
                                <tr>
                                    <th>Batch</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->batch->batch }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Pelaksanaan</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->batch->date }}</td>
                                </tr>
                                <tr>
                                    <th style="width: 200px;">Keterangan</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->keterangan }}</td>
                                </tr>
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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> List Participants</h6>
                    <div class="text-right">
                        <a id="saveAllChanges" class="btn btn-primary btn-sm text-white">
                            <i class="fa fa-save"></i> Save All Changes
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="listParticipant" class="table table-bordered mt-4">
                        <thead>
                            <tr>
                                <th>Nama Peserta</th>
                                <th>Status</th>
                                <th width="150px">Date Received</th>
                                <th width="150px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data->participant as $item)
                            <tr>
                                <td>{{ $item->peserta->nama_peserta }}</td>
                                <td class="text-center">
                                    <label class="switch switch-3d switch-primary mr-3" style="transform: scale(1.5);">
                                        <input type="checkbox" class="switch-input status-checkbox"
                                            data-id="{{ $item->id }}" {{ $item->status == 'true' ? 'checked' : '' }}>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </td>
                                <td class="d-flex align-items-center">
                                    @if ($item->date_received)
                                        <div class="d-flex align-items-center w-100">
                                            <span class="date-text">{{ \Carbon\Carbon::parse($item->date_received)->format('d-M-Y') }}</span>
                                            <input type="date" class="form-control date-input d-none" name="dateReceived" value="{{ $item->date_received }}">
                                            <i class="fa fa-edit text-secondary toggle-date-input ml-2" style="cursor: pointer;"></i>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center w-100">
                                            <input type="date" class="form-control date-input" name="dateReceived" value="">
                                        </div>
                                    @endif
                                </td>
                                <td class="actions text-center">
                                    <button class="btn btn-outline-secondary btn-md mb-2 mr-2 edit-button" data-id="{{ $item->id }}">
                                        <i class="fa fa-save"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-md mb-2 delete-button" data-id="{{ $item->id }}">
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

<div class="modal fade" id="editDataModal" tabindex="-1" role="dialog" aria-labelledby="editDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="editDataModalLabel">Edit Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('certificate.update', $data->id) }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 140px;" class="mr-2">
                            <p style="margin: 0;">Status :</p>
                        </div>
                        <div class="flex-grow-1">
                            <select class="form-control" id="status" name="status">
                                <option value="On Process" @if($data->status == 'On Process') selected @endif>On Process</option>
                                <option value="Issued" @if($data->status == 'Issued') selected @endif>Issued</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-4">
                        <div style="width: 140px;" class="mr-2">
                            <p style="margin: 0;">Keterangan :</p>
                        </div>
                        <div class="flex-grow-1">
                            <textarea class="form-control" rows="3" name="keterangan">{{ $data->keterangan }}</textarea>
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
    $(document).on('click', '.toggle-date-input', function () {
        var $parentTd = $(this).closest('td');
        var $dateText = $parentTd.find('.date-text');
        var $dateInput = $parentTd.find('.date-input');
        var $icon = $(this);

        // Toggle visibility
        $dateText.toggleClass('d-none');
        $dateInput.toggleClass('d-none');

        // Toggle icon between edit and hide (crossed eye)
        if ($dateInput.hasClass('d-none')) {
            $icon.removeClass('fa-eye-slash text-secondary').addClass('fa-edit text-secondary');
        } else {
            $icon.removeClass('fa-edit text-secondary').addClass('fa-eye-slash text-secondary');
            $dateInput.focus();  // Focus the input field when it becomes visible
        }
    });

    $(document).on('click', '.edit-button', function () {
        var id = $(this).data('id');
        var dateReceived = $(this).closest('tr').find('input[name="dateReceived"]').val();
        var status = $(this).closest('tr').find('.status-checkbox').is(':checked');

        // SweetAlert confirmation for saving changes
        swal({
            title: "Are you sure?",
            text: "Do you want to save changes for this participant?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willSave) => {
            if (willSave) {
                $.ajax({
                    url: '{{ route("receivable.participant.save") }}', // Replace with your route
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        date_received: dateReceived,
                        status: status
                    },
                    success: function (response) {
                        swal("Success!", "Changes have been saved.", "success")
                            .then(() => {
                                location.reload(); // Optionally reload the page
                            });
                    },
                    error: function (xhr) {
                        swal("Error!", "There was an error saving changes.", "error");
                    }
                });
            }
        });
    });

    $(document).on('click', '.delete-button', function () {
        var id = $(this).data('id');

        // SweetAlert confirmation for deletion
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this participant's data!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: '{{ route("receivable.participant.delete") }}', // Replace with your route
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id
                    },
                    success: function (response) {
                        swal("Success!", "Participant data has been deleted.", "success")
                            .then(() => {
                                location.reload(); // Reload the page to reflect the changes
                            });
                    },
                    error: function (xhr) {
                        swal("Error!", "There was an error deleting the participant data.", "error");
                    }
                });
            } else {
                swal("Your item is safe!");
            }
        });
    });

    $(document).on('click', '#saveAllChanges', function () {
        var participantsData = [];

        // Loop through each row to collect data
        $('#listParticipant tbody tr').each(function () {
            var id = $(this).find('.edit-button').data('id');
            var dateReceived = $(this).find('input[name="dateReceived"]').val();
            var status = $(this).find('.status-checkbox').is(':checked');

            participantsData.push({
                id: id,
                date_received: dateReceived,
                status: status
            });
        });

        // SweetAlert confirmation for saving all changes
        swal({
            title: "Are you sure?",
            text: "Do you want to save all changes for the participants?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willSave) => {
            if (willSave) {
                $.ajax({
                    url: '{{ route("receivable.participants.saveAll") }}', // Replace with your route
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        participants: participantsData
                    },
                    success: function (response) {
                        swal("Success!", "All changes have been saved.", "success")
                            .then(() => {
                                location.reload(); // Optionally reload the page
                            });
                    },
                    error: function (xhr) {
                        swal("Error!", "There was an error saving the changes.", "error");
                    }
                });
            }
        });
    });
</script>
@endsection

