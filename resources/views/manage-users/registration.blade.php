@extends('layouts.main')

@section('active-user')
active font-weight-bold
@endsection

@section('show-user')
show
@endsection

@section('manage-users')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-users"></i> User Registration</h1>
        <p class="mb-4">Managing Users Account</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        {{-- <a class="btn btn-secondary btn-sm shadow-sm mr-2" href="/invoicing/list"><i class="fas fa-solid fa-backward fa-sm text-white-50"></i> Go Back</a> --}}
    </div>
</div>
<form action="{{ route('register.user') }}" method="post" enctype="multipart/form-data" class="form-horizontal" onsubmit="return validateForm('png,jpeg,jpg,svg,gif', 'file-upload')">
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
                                        <div class="d-flex align-items-top justify-content-center text-center">
                                            <label for="file-upload" style="cursor: pointer;">
                                                <img id="image-preview" src="{{ asset('img/default-img.png') }}" style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                                <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                                            </label>
                                            <input id="file-upload" type="file" name="profile_picture" style="display: none;" accept="image/*" onchange="previewImage(event)">
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
                            <div class="col col-md-3"><label class=" form-control-label">Emp. Number <span class="text-danger">*</span></label></div>
                            <div class="col-12 col-md-9"><input type="number" id="employee_id" name="employee_id" placeholder="Employment Number" class="form-control" required></div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label class="form-control-label">User ID <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="text" id="user_id" name="user_id" placeholder="User ID" class="form-control" oninput="validateUserId()" required>
                                <small id="user_id_help" class="help-block form-text text-danger d-none">
                                    User ID cannot contain numbers, symbols, or spaces! Use an underscore if needed...
                                </small>
                                <small id="user_id_taken" class="help-block form-text text-danger d-none">
                                    User ID is already taken!
                                </small>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="full_name" class=" form-control-label">Full Name <span class="text-danger">*</span></label></div>
                            <div class="col-12 col-md-9"><input type="text" id="full_name" name="full_name" placeholder="Full Name" class="form-control" required></div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="email" class=" form-control-label">E-Mail Address <span class="text-danger">*</span></label></div>
                            <div class="col-12 col-md-9"><input type="email" id="email" name="email" placeholder="Enter Email" class="form-control" required></div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label for="password" class="form-control-label">Password <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-12 col-md-9">
                                <div class="input-group">
                                    <input type="password" id="password" name="password" placeholder="Enter Password" class="form-control" oninput="validatePassword()" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-default" type="button" onclick="togglePasswordVisibility()">
                                            <i class="fa fa-eye" id="toggle-password-icon"></i>
                                        </button>
                                        <button class="btn btn-primary" type="button" onclick="generatePassword()">Generate</button>
                                    </div>
                                </div>
                                <small id="password_help" class="help-block form-text text-danger d-none">
                                    Password cannot contain spaces!
                                </small>
                            </div>
                        </div>
                        <h6 class="h6 m-0 font-weight-bold mt-4 mb-4"><i class="fa fa-user"></i> Users Department & Position</h6>
                        <hr class="sidebar-divider mb-4">
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="department" class=" form-control-label">Department <span class="text-danger">*</span></label></div>
                            <div class="col-12 col-md-9">
                                <select name="department" id="department" class="form-control form-control" required>
                                    <option disabled selected>Please select</option>
                                    @foreach ($departments as $item)
                                    <option value="{{ $item->id }}">{{ $item->department_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="position" class=" form-control-label">Position <span class="text-danger">*</span></label></div>
                            <div class="col-12 col-md-9">
                                <select name="position" id="position" class="form-control form-control" required>
                                    <option disabled selected>Please select</option>
                                    @foreach ($positions as $item)
                                    <option value="{{ $item->id }}">{{ $item->position_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3"><label for="user_status" class=" form-control-label">User Status <span class="text-danger">*</span></label></div>
                            <div class="col-12 col-md-9">
                                <select name="user_status" id="user_status" class="form-control form-control" required>
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

        // Ensure no spaces are in the generated password
        if (!password.includes(' ')) {
            document.getElementById('password').value = password;
            document.getElementById('password_help').classList.add('d-none'); // Hide the notification
        }
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

    function validatePassword() {
        const passwordField = document.getElementById('password');
        const passwordHelp = document.getElementById('password_help');

        // Check if the password contains spaces
        if (passwordField.value.includes(' ')) {
            passwordHelp.classList.remove('d-none'); // Show notification
        } else {
            passwordHelp.classList.add('d-none'); // Hide notification
        }
    }

    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('image-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function validateUserId() {
        const userIdInput = document.getElementById('user_id');
        const userIdHelp = document.getElementById('user_id_help');
        const userIdTaken = document.getElementById('user_id_taken');

        // Regex to allow letters and underscores only (no numbers, spaces, or symbols)
        const regex = /^[a-zA-Z_]+$/;

        if (!regex.test(userIdInput.value)) {
            // Show invalid format message
            userIdHelp.classList.remove('d-none');
            userIdInput.classList.add('is-invalid'); // Add red outline
        } else {
            // Hide invalid format message
            userIdHelp.classList.add('d-none');
            userIdInput.classList.remove('is-invalid'); // Remove red outline

            // Check if the User ID is already taken
            checkUserIdAvailability(userIdInput.value);
        }
    }

    function checkUserIdAvailability(userId) {
        const userIdInput = document.getElementById('user_id');
        const userIdTaken = document.getElementById('user_id_taken');

        // AJAX request to check if the User ID is taken
        fetch(`/check-user-id/${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    // Show 'User ID is taken' message and add red outline
                    userIdTaken.classList.remove('d-none');
                    userIdInput.classList.add('is-invalid');
                } else {
                    // Hide 'User ID is taken' message and remove red outline
                    userIdTaken.classList.add('d-none');
                    userIdInput.classList.remove('is-invalid');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    function validateForm(allowedExtensions, ...fileInputIds) {
        const allowedExtArray = allowedExtensions.split(',');

        for (let i = 0; i < fileInputIds.length; i++) {
            const fileInput = document.getElementById(fileInputIds[i]);

            if (!fileInput || fileInput.files.length === 0) {
                alert(`Please upload an image for ${fileInputIds[i]} before submitting.`);
                return false; // Prevent form submission if no file is selected
            }

            const fileName = fileInput.files[0].name;
            const fileExtension = fileName.split('.').pop().toLowerCase();

            if (!allowedExtArray.includes(fileExtension)) {
                alert(`Invalid file type for ${fileInputIds[i]}. Allowed file types are: ${allowedExtensions}`);
                return false; // Prevent form submission if file type is invalid
            }
        }

        return true; // Allow form submission if all file inputs have valid files
    }
</script>
@endsection
