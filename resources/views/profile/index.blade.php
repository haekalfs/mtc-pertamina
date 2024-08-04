@extends('layouts.main')

@section('active-user')
active font-weight-bold
@endsection

@section('content')

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
.bg-login {
    width: 100%;
    background-image: url(../img/kilang-minyak.png);
    background-size: cover;     /* Ensures the background image covers the entire area */
    background-repeat: no-repeat; /* Prevents the background image from repeating */
    background-position: center;  /* Centers the background image */
    opacity: 0.9; /* Sets the opacity to 90% */
}
</style>
<div class="animated fadeIn zoom90">
    <div class="row">
        <div class="col-lg-8">
            <section class="card">
                <div class="twt-feed bg-login">
                    <div class="corner-ribon black-ribon">
                        {{-- <i class="fa fa-twitter"></i> --}}
                    </div>

                    <div class="media ml-4">
                        <a href="#">
                            @if(Auth::user()->users_detail->profile_pic)
                            <img class="align-self-center rounded-circle mr-3" style="width:85px; height:85px;" alt="" src="{{ asset('/img/avatar/'. Auth::user()->users_detail->profile_pic) }}">
                            @else
                            <div class="align-self-center rounded-circle mr-3"><i class="no-image-text">No Image Available</i></div>
                            @endif
                        </a>
                        <div class="media-body">
                            <h2 class="text-white display-6">{{ Auth::user()->name }}</h2>
                            <p class="text-light">{{ Auth::user()->users_detail->position->position_name }}</p>
                        </div>
                    </div>
                </div>
                <div class="row mt-4 mb-2 text-center">
                    <div class="col-md-6">
                        <a href="#" type="button" data-toggle="modal" data-target="#changePicture" id="changePictureButton"><i class="fa fa-picture-o"></i> Change Picture</a>
                    </div>
                    <div class="col-md-6">
                        <a type="button" data-toggle="modal" data-target="#changePass" id="manButton" href="#"><i class="menu-icon fa fa-lock"></i> Change Password</a>
                    </div>
                </div>
                <hr>
                <div class="twt-write ml-4 mt-4 col-sm-12">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Nomor Pekerja</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ Auth::user()->users_detail->employee_id }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>User Id</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ Auth::user()->id }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Name</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ Auth::user()->name }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Email</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Department</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ Auth::user()->users_detail->department->department_name }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Position</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ Auth::user()->users_detail->position->position_name }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Roles</label>
                        </div>
                        <div class="col-md-6">
                            <p>
                                @php
                                    $badgeColors = ['bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'bg-primary', 'bg-secondary'];
                                @endphp
                                @foreach (Auth::user()->role_id as $index => $usrRole)
                                    @if ($usrRole->role)
                                        <span class="badge text-white {{ $badgeColors[$index % count($badgeColors)] }}">{{ $usrRole->role->description }}</span>
                                    @endif
                                @endforeach
                            </p>
                        </div>
                    </div>
                </div>
                <footer class="twt-footer">
                    {{-- <a href="#"><i class="fa fa-camera"></i></a> --}}
                    All data is confidential
                    {{-- <span class="pull-right">
                        32
                    </span> --}}
                </footer>
            </section>
        </div>
        <div class="col-xl-4 col-lg-4">
            <div class="row">
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
    </div>
</div>

<div class="modal fade" id="changePicture" tabindex="-1" role="dialog" aria-labelledby="changePictureLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between border-bottom-1">
                <h5 class="modal-title m-0 font-weight-bold text-secondary" id="changePictureLabel">Upload Profile Picture</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('change.profile.picture') }}" enctype="multipart/form-data" id="profilForm">
                @csrf
                <div class="modal-body">
                    <div class="col-md-12 zoom90">
                        <div class="form-group d-flex align-items-center">
                            <label for="email" style="margin-bottom: 0;">
                                <span class="text-danger"><i>File :</i></span>
                            </label>
                        </div>
                        <div class="form-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="picture" name="picture" accept="image/*" onchange="changeFileName('picture', 'picture-label')">
                                <label class="custom-file-label" for="picture" id="picture-label">Choose file</label>
                            </div>
                            <small style="color: red;"><i>Only picture type allowed!</i></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="changePass" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Change Password</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="resetPasswordForm" method="POST" action="{{ route('profile.reset.password') }}">
                @csrf
                <div class="modal-body">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password_reset">{{ __('Password :') }}</label>
                                    <input id="password_reset" type="text" class="form-control @error('password_reset') is-invalid @enderror" name="password_reset"
                                    value="{{ old('password_reset') }}" required autocomplete="password_reset" autofocus>
                                    <small id="password_error" class="text-danger" style="display: none;">
                                        Password must contain at least one uppercase letter, one lowercase letter, one number, and one symbol.
                                    </small>
                                    @error('password_reset')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="generatePassword()">Generate Password</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="submit_button" type="submit" class="btn btn-primary">
                        {{ __('Reset Password') }}
                    </button>
                </div>
            </form>
		</div>
	</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('resetPasswordForm');
    const passwordInput = document.getElementById('password_reset');
    const errorElement = document.getElementById('password_error');
    const submitButton = document.getElementById('submit_button');

    function validatePassword(password) {
        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);
        const hasNumber = /\d/.test(password);
        const hasSymbol = /[\W_]/.test(password); // \W matches any non-word character, _ is included to match underscores

        return hasUppercase && hasLowercase && hasNumber && hasSymbol;
    }

    passwordInput.addEventListener('input', function () {
        const password = passwordInput.value;
        if (!validatePassword(password)) {
            errorElement.style.display = 'block';
            submitButton.disabled = true; // Disable submit button
        } else {
            errorElement.style.display = 'none';
            submitButton.disabled = false; // Enable submit button
        }
    });

    form.addEventListener('submit', function (event) {
        const password = passwordInput.value;
        if (!validatePassword(password)) {
            event.preventDefault(); // Prevent form submission
            errorElement.style.display = 'block';
        }
    });

});

function changeFileName(inputId, labelId) {
    var input = document.getElementById(inputId);
    var label = document.getElementById(labelId);
    label.textContent = input.files[0].name;
}
function generatePassword() {
    const errorElement = document.getElementById('password_error');
    const submitButton = document.getElementById('submit_button');
    var length = 12;
    var charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+{}[]|:;<>,.?/~`-=";
    var password = "";
    for (var i = 0; i < length; i++) {
        var randomIndex = Math.floor(Math.random() * charset.length);
        password += charset[randomIndex];
    }
    document.getElementById('password_reset').value = password;
    errorElement.style.display = 'none';
    submitButton.disabled = false; // Enable submit button
}
</script>
@endsection
