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
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-male"></i> Instructor Registration</h1>
        <p class="mb-4">Managing Users Account</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        {{-- <a class="btn btn-secondary btn-sm shadow-sm mr-2" href="/invoicing/list"><i class="fas fa-solid fa-backward fa-sm text-white-50"></i> Go Back</a> --}}
    </div>
</div>
<form action="{{ route ('instructor.store') }}" method="post" enctype="multipart/form-data" class="form-horizontal">
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
                                                        <img id="image-preview" src="https://via.placeholder.com/150x150/5fa9f8/ffffff"
                                                             style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
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
                        <div class="card mb-4">
                            <div class="card-header">
                                <span class="text-danger font-weight-bold">User Data Verification</span>
                            </div>
                            <div class="card-body" style="background-color: rgb(247, 247, 247);">
                                <h6 class="h6 mb-2 font-weight-bold text-gray-800">General Guidelines</h6>
                                <ul class="ml-4">
                                    <li>Ensure all user data is accurately updated in accordance with company policies.</li>
                                    <li>Verify and validate user information to maintain data integrity.</li>
                                    <li>Unauthorized modifications to user records are strictly prohibited.</li>
                                    <li>Double-check user details for completeness and correctness before saving changes.</li>
                                </ul>
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
                            <div class="col-12 col-md-9"><input type="text" id="full_name" name="full_name" placeholder="Full Name" class="form-control"></div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="email" class=" form-control-label">E-Mail Address</label></div>
                            <div class="col-12 col-md-9"><input type="email" id="email" name="email" placeholder="Enter Email" class="form-control"></div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="dob" class=" form-control-label">Date of Birth</label></div>
                            <div class="col-12 col-md-9"><input type="date" id="dob" name="dob" class="form-control"></div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="address" class=" form-control-label">Address</label></div>
                            <div class="col-12 col-md-9"><textarea name="address" rows="3" class="form-control"></textarea></div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="user_status" class=" form-control-label">Instructor Status</label></div>
                            <div class="col-12 col-md-9">
                                <select name="user_status" id="user_status" class="form-control form-control">
                                    <option disabled selected>Please select</option>
                                    <option value="1">Active</option>
                                    <option value="0">Non Active</option>
                                </select>
                            </div>
                        </div>
                        <h6 class="h6 m-0 font-weight-bold mt-4 mb-4"><i class="fa fa-user"></i> Additional Information</h6>
                        <hr class="sidebar-divider mb-4">
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="cv" class=" form-control-label">CV</label></div>
                            <div class="col-12 col-md-9"><input type="file" id="cv" name="cv" class="form-control"></div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="ijazah" class=" form-control-label">Ijazah</label></div>
                            <div class="col-12 col-md-9"><input type="file" id="ijazah" name="ijazah" class="form-control"></div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="roles" class="form-control-label">Certifications</label></div>
                            <div class="col-12 col-md-9">
                                <select data-placeholder="Certificate..." multiple class="standardSelect form-control" id="certificates" name="certificates[]">
                                    @foreach ($certificate as $item)
                                    <option value="{{ $item->id }}">{{ $item->certificate_name }}</option>
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
@endsection
