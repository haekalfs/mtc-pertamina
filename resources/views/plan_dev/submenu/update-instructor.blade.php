@extends('layouts.main')

@section('active-pd')
active font-weight-bold
@endsection

@section('show-pd')
show
@endsection

@section('instructor')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-male"></i> Update Instructor's Data</h1>
        <p class="mb-4">Update Instructor Data</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('instructor') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
    </div>
</div>
<form action="{{ route ('instructor.update', $data->id) }}" method="post" enctype="multipart/form-data" class="form-horizontal">
    @method('PUT') <!-- Add this to specify the form method as PUT -->
    @csrf
    <div class="animated fadeIn zoom90">
        <div class="row">
            <div class="col-xl-4 col-lg-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card shadow mb-4">
                            <!-- Card Header - Dropdown -->
                            <div class="dropdown">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold">Profile Picture</h6>
                                </div>
                                    <!-- Card Body -->
                                <div class="card-body">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="d-flex align-items-top justify-content-center text-center">
                                                    <label for="file-upload" style="cursor: pointer;">
                                                        <img id="image-preview" src="{{ asset($data->imgFilepath) }}" style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                                             <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                                                    </label>
                                                    <input id="file-upload" type="file" name="profile_picture" style="display: none;" accept="image/*" onchange="previewImage(event)">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card mb-4 shadow">
                            <div class="card-header">
                                <span class="text-danger font-weight-bold">Delete Account</span>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <span>Deleting your account is a permanent action and cannot be undone. If you are sure you want to delete your account, select the button below.</span>
                                </div>
                                <div>
                                    <a data-id="{{ $data->id }}" class="btn btn-outline-danger delete-instructor text-danger">I Understand, delete the account</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold py-1">Instructor Information</h6>
                    </div>
                    <div class="card-body card-block">
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="full_name" class=" form-control-label">Instructor Fullname</label></div>
                            <div class="col-12 col-md-9"><input type="text" id="full_name" name="full_name" placeholder="Full Name" class="form-control" value="{{ $data->instructor_name }}"></div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="email" class=" form-control-label">E-Mail Address</label></div>
                            <div class="col-12 col-md-9"><input type="email" id="email" name="email" placeholder="Enter Email" class="form-control" value="{{ $data->instructor_email }}"></div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="dob" class=" form-control-label">Date of Birth</label></div>
                            <div class="col-12 col-md-9"><input type="date" id="dob" name="dob" class="form-control" value="{{ $data->instructor_dob }}"></div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="address" class=" form-control-label">Address</label></div>
                            <div class="col-12 col-md-9"><textarea name="address" rows="3" class="form-control">{{ $data->instructor_address }}</textarea></div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="working_hour" class=" form-control-label">Jam Mengajar</label></div>
                            <div class="col-12 col-md-9"><input type="number" id="working_hour" name="working_hour" placeholder="Jam Mengajar" class="form-control" value="{{ $data->working_hours }}"></div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="user_status" class=" form-control-label">Instructor Status</label></div>
                            <div class="col-12 col-md-9">
                                <select name="status" id="user_status" class="form-control">
                                    <option disabled {{ is_null($data->status) ? 'selected' : '' }}>Please select</option>
                                    <option value="1" {{ $data->status == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $data->status == '0' ? 'selected' : '' }}>Non Active</option>
                                </select>
                            </div>
                        </div>
                        <h6 class="h6 m-0 font-weight-bold mt-4 mb-4"><i class="fa fa-user"></i> Additional Information</h6>
                        <hr class="sidebar-divider mb-4">
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="cv" class="form-control-label">CV</label></div>
                            <div class="col-12 col-md-9">
                                <input type="file" id="cv" name="cvFilepath" class="form-control">
                                @if($data->cvFilepath)
                                    <a href="{{ asset($data->cvFilepath) }}" target="_blank">View CV</a>
                                @endif
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="ijazah" class="form-control-label">Ijazah</label></div>
                            <div class="col-12 col-md-9">
                                <input type="file" id="ijazah" name="ijazahFilepath" class="form-control">
                                @if($data->ijazahFilepath)
                                    <a href="{{ asset($data->ijazahFilepath) }}" target="_blank">View Ijazah</a>
                                @endif
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col col-md-3"><label for="documentPendukungFilepath" class="form-control-label">Dokument Pendukung</label></div>
                            <div class="col-12 col-md-9">
                                <input type="file" id="documentPendukungFilepath" name="documentPendukungFilepath" class="form-control">
                                @if($data->documentPendukungFilepath)
                                    <a href="{{ asset($data->documentPendukungFilepath) }}" target="_blank">View File</a>
                                @endif
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="certificates" class="form-control-label">Certifications</label></div>
                            <div class="col-12 col-md-9">
                                <select data-placeholder="Certificate..." multiple class="standardSelect form-control" id="certificates" name="certificates[]">
                                    @foreach ($certificate as $item)
                                    <option value="{{ $item->id }}" {{ in_array($item->id, $data->certificates->pluck('certificates_catalog_id')->toArray()) ? 'selected' : '' }}>
                                        {{ $item->certificate_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa fa-dot-circle-o"></i> Submit
                        </button>
                        <button type="reset" class="btn btn-danger btn-sm">
                            <i class="fa fa-ban"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
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
<script>
$(document).ready(function() {
    $('.delete-instructor').click(function(e) {
        e.preventDefault();
        let id = $(this).data('id');

        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this account!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                // Make AJAX request to delete the instructor
                $.ajax({
                    url: '{{ route("instructor.delete", ":id") }}'.replace(':id', id),
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}', // Include the CSRF token
                    },
                    success: function(response) {
                        swal("Success! The account has been deleted!", {
                            icon: "success",
                        }).then(() => {
                            window.location.href = '{{ route("instructor") }}'; // Redirect after deletion
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
</script>
@endsection
