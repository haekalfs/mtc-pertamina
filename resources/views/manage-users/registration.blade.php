@extends('layouts.main')

@section('active-user')
active
@endsection

@section('breadcumbs')
<div class="breadcrumbs">
    <div class="breadcrumbs-inner">
        <div class="row m-0">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>Manage Users</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="#">Users</a></li>
                            <li><a href="#">Register User</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<form action="{{ route('register.user') }}" method="post" enctype="multipart/form-data" class="form-horizontal">
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
                                                <div class="form-group">
                                                    <label>Profile Picture :</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="profile_picture" name="profile_picture" onchange="changeFileName('profile_picture', 'profile-label')">
                                                        <label class="custom-file-label" for="profile_picture" id="profile-label">Choose file</label>
                                                    </div>
                                                    <img id="profile-preview" src="" class="mt-4" style="max-width: 110px; max-height: 200px; object-fit:fill;"></img>
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
                        <h6 class="m-0 font-weight-bold">User Information</h6>
                    </div>
                    <div class="card-body card-block">
                        <div class="row form-group">
                            <div class="col col-md-3"><label class=" form-control-label">Emp. ID</label></div>
                            <div class="col-12 col-md-9"><input type="text" id="employee_id" name="employee_id" placeholder="Employee ID" class="form-control"></div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label class=" form-control-label">User ID</label></div>
                            <div class="col-12 col-md-9"><input type="text" id="user_id" name="user_id" placeholder="User ID" class="form-control"></div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="full_name" class=" form-control-label">Full Name</label></div>
                            <div class="col-12 col-md-9"><input type="text" id="full_name" name="full_name" placeholder="Full Name" class="form-control"></div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="email" class=" form-control-label">E-Mail Address</label></div>
                            <div class="col-12 col-md-9"><input type="email" id="email" name="email" placeholder="Enter Email" class="form-control"></div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="password" class="form-control-label">Password</label></div>
                            <div class="col-12 col-md-9">
                                <div class="input-group">
                                    <input type="password" id="password" name="password" placeholder="Enter Password" class="form-control">
                                    <div class="input-group-append">
                                        <button class="btn btn-default" type="button" onclick="togglePasswordVisibility()">
                                            <i class="fa fa-eye" id="toggle-password-icon"></i>
                                        </button>
                                        <button class="btn btn-primary" type="button" onclick="generatePassword()">Generate</button>
                                    </div>
                                </div>
                                <small class="help-block form-text">Please enter a complex password!</small>
                            </div>
                        </div>
                        <h6 class="h6 m-0 font-weight-bold mt-4 mb-4"><i class="fa fa-user"></i> Users Department & Position</h6>
                        <hr class="sidebar-divider mb-4">
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="department" class=" form-control-label">Department</label></div>
                            <div class="col-12 col-md-9">
                                <select name="department" id="department" class="form-control form-control">
                                    <option disabled selected>Please select</option>
                                    @foreach ($departments as $item)
                                    <option value="{{ $item->id }}">{{ $item->department_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="position" class=" form-control-label">Position</label></div>
                            <div class="col-12 col-md-9">
                                <select name="position" id="position" class="form-control form-control">
                                    <option disabled selected>Please select</option>
                                    @foreach ($positions as $item)
                                    <option value="{{ $item->id }}">{{ $item->position_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="user_status" class=" form-control-label">User Status</label></div>
                            <div class="col-12 col-md-9">
                                <select name="user_status" id="user_status" class="form-control form-control">
                                    <option disabled selected>Please select</option>
                                    <option value="1">Active</option>
                                    <option value="0">Non Active</option>
                                </select>
                            </div>
                        </div>
                        <h6 class="h6 m-0 font-weight-bold mt-4 mb-4"><i class="fa fa-user"></i> Assign Roles</h6>
                        <hr class="sidebar-divider mb-4">
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="roles" class="form-control-label">Set as</label></div>
                            <div class="col-12 col-md-9">
                                <select data-placeholder="Assign roles..." multiple class="standardSelect form-control" id="roles" name="roles[]">
                                    @foreach ($roles as $item)
                                    <option value="{{ $item->id }}">{{ $item->description }}</option>
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
    function generatePassword() {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
        let password = "";
        for (let i = 0; i < 12; i++) {
            const randomIndex = Math.floor(Math.random() * chars.length);
            password += chars[randomIndex];
        }
        document.getElementById('password').value = password;
    }

    function togglePasswordVisibility() {
        const passwordField = document.getElementById('password');
        const togglePasswordIcon = document.getElementById('toggle-password-icon');
        if (passwordField.type === "password") {
            passwordField.type = "text";
            togglePasswordIcon.classList.remove('fa-eye');
            togglePasswordIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = "password";
            togglePasswordIcon.classList.remove('fa-eye-slash');
            togglePasswordIcon.classList.add('fa-eye');
        }
    }
    function changeFileName(inputId, labelId) {
        var input = document.getElementById(inputId);
        var label = document.getElementById(labelId);
        label.textContent = input.files[0].name;
    }
</script>
@endsection
