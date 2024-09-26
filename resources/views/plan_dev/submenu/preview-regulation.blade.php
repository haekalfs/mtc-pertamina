@extends('layouts.main')

@section('active-pd')
active font-weight-bold
@endsection

@section('show-pd')
show
@endsection

@section('regulation')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h4 mb-2 font-weight-bold text-secondary"><i class="fa fa-warning"></i> {{ $data->description }}</h1>
        <p class="mb-4">Status : <span class="badge {{ $data->statuses->badge }}">{{ $data->statuses->description }}</span></a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('regulation') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
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
        <div class="card" style="min-height: 500px;">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-secondary" id="judul">Document Preview</h6>
                <div class="text-right">
                    @mtd_acc(3)
                    <a class="btn btn-secondary btn-sm text-white mr-2 edit-regulation" data-id="{{ $data->id }}" data-name="{{ $data->description }}" data-status="{{ $data->statuses->id }}" data-filepath="{{ $data->filepath }}"><i class="menu-Logo fa fa-edit"></i> Update</a>
                    @endmtd_acc
                    @mtd_acc(4)
                    <a href="#" class="btn btn-danger btn-sm text-white delete-regulation mr-2" data-id="{{ $data->id }}"><i class="menu-Logo fa fa-trash-o"></i> Delete</a>
                    @endmtd_acc
                    @if($fileExists)
                        <a class="btn btn-primary btn-sm text-white" href="{{ asset($data->filepath) }}" download><i class="menu-Logo fa fa-download"></i> Download</a>
                    @endif
                </div>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 400px; height: 100%;">
                @if(!$fileExists)
                    <div class="alert alert-danger" role="alert">
                        File does not exist.
                    </div>
                @elseif(!$isPdf)
                    <div class="alert alert-warning" role="alert">
                        The file is not in PDF format. Proceed to download the file by clicking download button.
                    </div>
                @else
                    <iframe src="{{ asset($data->filepath) }}" width="100%" style="height:900px; border:none;"></iframe>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editDataModal" tabindex="-1" role="dialog" aria-labelledby="editDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="editDataModalLabel">Edit Regulation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('regulation.update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="regulation_id" id="editRegulationId">
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-12">
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex align-items-start mb-4">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Nama Regulation :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <textarea class="form-control" rows="3" name="regulation_name" id="editRegulationName"></textarea>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Status :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <select class="custom-select" name="status" id="editStatus">
                                                    @foreach($statuses as $status)
                                                    <option value="{{ $status->id }}">{{ $status->description }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Dokumen :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="file" class="form-control" name="file">
                                                <small id="currentFile"></small>
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
                    <button type="submit" class="btn btn-primary">Update Regulation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    $('.delete-regulation').on('click', function (e) {
        e.preventDefault();
        var regulationId = $(this).data('id');

        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this file!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: "{{ route('regulation.destroy', '') }}/" + regulationId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (result) {
                        swal("Poof! Your file has been deleted!", {
                            icon: "success",
                        }).then(() => {
                            window.location.href = "{{ route('regulation') }}";
                        });
                    },
                    error: function (xhr) {
                        swal("Something went wrong!", {
                            icon: "error",
                        });
                    }
                });
            } else {
                swal("Your file is safe!");
            }
        });
    });

    $('.edit-regulation').on('click', function () {
        var regulationId = $(this).data('id');
        var regulationName = $(this).data('name');
        var statusId = $(this).data('status');
        var filePath = $(this).data('filepath');

        // Prefill the modal fields
        $('#editRegulationId').val(regulationId);
        $('#editRegulationName').val(regulationName);
        $('#editStatus').val(statusId);

        if (filePath) {
            $('#currentFile').text('Current file: ' + filePath);
        } else {
            $('#currentFile').text('No file uploaded');
        }

        // Show the modal
        $('#editDataModal').modal('show');
    });
});
</script>
@endsection

