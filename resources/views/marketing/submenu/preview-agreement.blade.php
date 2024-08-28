@extends('layouts.main')

@section('active-marketing')
active font-weight-bold
@endsection

@section('show-marketing')
show
@endsection

@section('company-agreement')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-sitemap"></i> Preview Agreement</h1>
        <p class="mb-4">Affliated Company.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('company-agreement') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
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
                <div class="text-right">
                    <a href="#" class="btn btn-danger btn-sm text-white delete-agreement mr-2" data-id="{{ $data->id }}"><i class="menu-Logo fa fa-trash-o"></i> Delete</a>
                    <a href="#" class="btn btn-secondary btn-sm text-white edit-agreement" data-id="{{ $data->id }}"><i class="menu-Logo fa fa-edit"></i> Update Data</a>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md-12 mb-3">
                    <div class="row">
                        <div class="col-md-3 d-flex align-items-start justify-content-center">
                            <img src="{{ asset($data->img_filepath) }}" style="height: 100px; width: 250px; border-radius: 15px;" class="card-img" alt="...">
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th style="width: 200px;">Nama Company</th>
                                    <td style="text-align: start; font-weight:500">: {{ $data->company_name }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 180px;"><i class="ti-minus mr-2"></i> Tipe Dokumen</td>
                                    <td style="text-align: start;">: @if($data->spk_filepath) SPK @else NON-SPK @endif</span></td>
                                </tr>
                                <tr>
                                    <td><i class="ti-minus mr-2"></i> Release Date</td>
                                    <td style="text-align: start;">: {{ \Carbon\Carbon::parse($data->date)->format('d-M-Y') }}</td>
                                </tr>
                                <tr>
                                    <td><i class="ti-minus mr-2"></i> Status</td>
                                    <td style="text-align: start;">: <span class="badge {{ $data->statuses->badge }}">{{ $data->statuses->description }}</span></td>
                                </tr>
                          </table>
                        </div>
                    </div>
                    <h6 class="font-weight-bold text-secondary" id="judul">Informasi Perusahaan : </h6>
                    <p class="mt-3">{{ $data->company_details }}</p>
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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">Agreement</h6>
                    <div class="text-right">
                        @if($fileExists)
                            <a class="btn btn-primary btn-sm text-white mr-2" href="{{ asset($data->spk_filepath) }}" download><i class="menu-Logo fa fa-download"></i> Download</a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($data->spk_filepath)
                        @if(!$fileExists)
                            <div class="alert alert-danger" role="alert">
                                File does not exist.
                            </div>
                        @elseif(!$isPdf)
                            <div class="alert alert-warning" role="alert">
                                The file is not in PDF format. Proceed to download the file by clicking download button.
                            </div>
                        @else
                            <iframe src="{{ asset($data->spk_filepath) }}" width="100%" style="height:900px; border:none;"></iframe>
                        @endif
                    @elseif($data->non_spk)
                        <p class="mt-1">{{ $data->non_spk }}</p>
                    @else
                        No File or Agreement Exist!
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade zoom90" id="editAgreementModal" tabindex="-1" role="dialog" aria-labelledby="editAgreementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 1000px;" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="editAgreementModalLabel">Edit Agreement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" id="editAgreementForm">
                @csrf
                @method('PUT')
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="file-upload-edit" style="cursor: pointer;">
                                <img id="image-preview-edit" src="https://via.placeholder.com/50x50/5fa9f8/ffffff"
                                     style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                     <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="file-upload-edit" type="file" name="img" style="display: none;" accept="image/*" onchange="previewImageEdit(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Company Name -->
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Nama Perusahaan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="company_name" required>
                                            </div>
                                        </div>
                                        <!-- Status -->
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Status :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <select class="custom-select" name="status">
                                                    @foreach($statuses as $status)
                                                    <option value="{{ $status->id}}">{{ $status->description }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <!-- Company Details -->
                                        <div class="d-flex align-items-start mb-4">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Informasi Perusahaan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <textarea class="form-control" rows="3" name="company_details"></textarea>
                                            </div>
                                        </div>
                                        <!-- Agreement Date -->
                                        <div class="d-flex align-items-start mb-4">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Agreement Date :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="date" class="form-control" name="agreement_date" required>
                                            </div>
                                        </div>
                                        <!-- SPK/Non-SPK -->
                                        <div class="d-flex align-items-start mb-4">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">SPK/NON-SPK :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <label class="col-md-4">
                                                    <input class="form-radio-input" type="radio" name="type_reimburse" id="projectRadioEdit" value="Project" checked>
                                                    <span class="form-radio-sign">SPK</span>
                                                </label>
                                                <label class="col-md-4">
                                                    <input class="form-radio-input" type="radio" name="type_reimburse" id="othersRadioEdit" value="Others">
                                                    <span class="form-radio-sign">NON-SPK</span>
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Dokumen SPK / Non-SPK Details -->
                                        <div class="d-flex align-items-start mb-4">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Dokumen SPK :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="file" class="form-control" name="spk_file" id="spkFileInputEdit">
                                                <textarea class="form-control" name="non_spk_details" id="nonSpkTextareaEdit" style="display: none;"></textarea>
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
                    <button type="submit" class="btn btn-primary">Update Agreement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.delete-agreement').click(function(e) {
        e.preventDefault();
        let id = $(this).data('id');

        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this agreement!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                // Make AJAX request to delete the instructor
                $.ajax({
                    url: '{{ route("delete-agreement", ":id") }}'.replace(':id', id),
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}', // Include the CSRF token
                    },
                    success: function(response) {
                        swal("Success! The account has been deleted!", {
                            icon: "success",
                        }).then(() => {
                            window.location.href = '{{ route("company-agreement") }}'; // Redirect after deletion
                        });
                    },
                    error: function(xhr) {
                        swal("Error! Something went wrong.", {
                            icon: "error",
                        });
                    }
                });
            } else {
                // Show a message if deletion is canceled
                swal("Your record is safe!", {
                    icon: "info",
                });
            }
        });
    });
});
document.querySelectorAll('.edit-agreement').forEach(function(button) {
    button.addEventListener('click', function(event) {
        event.preventDefault();
        const id = this.getAttribute('data-id');
        const url = "{{ route('agreement.show', ':id') }}".replace(':id', id);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                // Populate the form fields with the data
                document.querySelector('#editAgreementForm').setAttribute('action', "{{ route('agreement.update', ':id') }}".replace(':id', id));
                document.querySelector('input[name="company_name"]').value = data.company_name;
                document.querySelector('textarea[name="company_details"]').value = data.company_details;
                document.querySelector('input[name="agreement_date"]').value = data.date;
                document.querySelector('select[name="status"]').value = data.status;
                document.querySelector('textarea[name="non_spk_details"]').value = data.non_spk;

                // Handle SPK / Non-SPK radio buttons
                if (data.spk_filepath) {
                    document.getElementById('projectRadioEdit').checked = true;
                    document.getElementById('spkFileInputEdit').style.display = 'block';
                    document.getElementById('nonSpkTextareaEdit').style.display = 'none';
                    document.getElementById('currentFile').textContent = 'Current file: ' + data.spk_filepath;
                } else {
                    document.getElementById('othersRadioEdit').checked = true;
                    document.getElementById('spkFileInputEdit').style.display = 'none';
                    document.getElementById('nonSpkTextareaEdit').style.display = 'block';
                    document.getElementById('currentFile').textContent = 'No file uploaded';
                }

                // Handle image preview
                document.querySelector('#image-preview-edit').src = data.img_filepath ? `/${data.img_filepath}` : 'https://via.placeholder.com/50x50/5fa9f8/ffffff';

                // Open the modal
                $('#editAgreementModal').modal('show');
            });
    });
});

function previewImageEdit(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('image-preview-edit');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection

