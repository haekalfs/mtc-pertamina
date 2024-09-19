@extends('layouts.main')

@section('active-akhlak')
active font-weight-bold
@endsection

@section('show-akhlak')
show
@endsection

@section('morning-briefing')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="ti-signal"></i> Morning Briefing</h1>
        <p class="mb-4">Kegiatan Briefing MTC.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('morning-briefing') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
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
        <div class="content card">
            <img style="width: 100%; height: 600px;" class="photo" src="{{ asset($data->img_filepath) }}" alt="Main Image">
            <div class="desc mt-4" style="position: relative;">
                <a href="#" data-id="{{ $data->id }}" class="position-absolute edit-briefing" style="top: 1px; right: 15px; z-index: 1;">
                    <i class="fa fa-edit fa-lg ml-2" style="color: rgb(181, 181, 181);"></i>
                </a>
                <h1>{{ $data->briefing_name }}</h1>
                <div class="d-flex justify-content-between align-items-start" style="font-weight: 500;">
                    <!-- Jenis Kegiatan -->
                    <div class="me-4">
                        <strong>Person in Charge</strong>: {{ $data->user->name }}
                    </div>

                    <!-- Tanggal Pelaksanaan -->
                    @php
                        $date = \Carbon\Carbon::parse($data->date);
                    @endphp
                    <div>
                        {{ $date->format('d-M-Y') }}
                    </div>
                </div>
                <p>{!! $data->briefing_result !!}</p>
                <hr>
                <div class="d-flex justify-content-between align-items-start" style="font-weight: 500;">
                    <!-- Jenis Kegiatan -->

                    <div style="font-weight: 500; line-height: 1.5;" class="text-muted">
                        <!-- Informasi Kegiatan -->
                        <div>
                            <strong>Informasi Kegiatan</strong>: {{ $data->briefing_details }}
                        </div>

                    </div>

                    <div class="text-muted">
                        <strong>Last Updated by</strong>: {{ $data->user->name }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade zoom90" id="editCampaignModal" tabindex="-1" role="dialog" aria-labelledby="editCampaignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 900px;" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="editCampaignModalLabel">Edit Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" id="editCampaignForm">
                @csrf
                @method('PUT') <!-- Include PUT method for updates -->
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="file-upload" style="cursor: pointer;">
                                <img id="image-preview" src="{{ asset('img/default-img.png') }}"
                                     style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                     <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="file-upload" type="file" name="img" style="display: none;" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex align-items-center mb-3">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Nama Kegiatan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="activity_name" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Informasi Kegiatan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="activity_info">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">PIC :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <select class="custom-select" id="person_in_charge" name="person_in_charge">
                                                    @foreach($users as $user)
                                                    <option value="{{ $user->id }}" selected>{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Tanggal :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="date" class="form-control" name="activity_date">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group has-success">
                                <p for="summernote" class="control-label mb-2">Hasil Kegiatan :</p>
                                <textarea id="summernote" name="activity_result"></textarea>
                                <span class="help-block field-validation-valid" data-valmsg-for="cc-name" data-valmsg-replace="true"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-danger text-white delete-briefing mr-2" data-id="{{ $data->id }}"><i class="menu-Logo fa fa-trash-o"></i> Delete</a>
                    <button type="submit" class="btn btn-primary">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.delete-briefing').click(function(e) {
        e.preventDefault();
        let id = $(this).data('id');

        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this briefing!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                // Make AJAX request to delete the instructor
                $.ajax({
                    url: '{{ route("delete-briefing", ":id") }}'.replace(':id', id),
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}', // Include the CSRF token
                    },
                    success: function(response) {
                        swal("Success! The account has been deleted!", {
                            icon: "success",
                        }).then(() => {
                            window.location.href = '{{ route("morning-briefing") }}'; // Redirect after deletion
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
document.querySelectorAll('.edit-briefing').forEach(function(button) {
    button.addEventListener('click', function(event) {
        event.preventDefault();
        const id = this.getAttribute('data-id');
        const url = "{{ route('briefing.show', ':id') }}".replace(':id', id);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                // Populate the modal fields
                document.querySelector('#editCampaignForm').setAttribute('action', "{{ route('briefing.update', ':id') }}".replace(':id', id));
                document.querySelector('input[name="activity_name"]').value = data.briefing_name;
                document.querySelector('input[name="activity_info"]').value = data.briefing_details;
                document.querySelector('input[name="activity_date"]').value = data.date;
                // Prefill Summernote
                $('#summernote').summernote('code', data.briefing_result);
                document.querySelector('#image-preview').src = data.img_filepath ? `/${data.img_filepath}` : 'https://via.placeholder.com/50x50/5fa9f8/ffffff';

                // Handle select field for 'PIC'
                let select = document.querySelector('select[name="person_in_charge"]');
                select.value = data.user_id;

                // Open the modal
                $('#editCampaignModal').modal('show');
            });
    });
});

function previewImage(event) {
    const image = document.getElementById('image-preview');
    image.src = URL.createObjectURL(event.target.files[0]);
}

$('#summernote').summernote({
    placeholder: 'Hasil Kegiatan...',
    tabsize: 2,
    height: 220,
    toolbar: [
        ['style', ['style']],
        ['font', ['bold', 'underline', 'clear']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video']],
        ['view', ['fullscreen', 'codeview', 'help']]
    ]
});

</script>
@endsection

