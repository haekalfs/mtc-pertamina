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
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-user"></i> Preview User : {{ $data->id }}</h1>
        <p class="mb-4">Managing Users Account</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('manage.users') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
    </div>
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
<div class="animated fadeIn zoom90">
    <div class="row">
        <div class="col-lg-8">
            <section class="card">
                <div class="twt-feed bg-login" style="background-image: url('{{ asset('img/kilang-minyak.png') }}');">
                    <div class="corner-ribon black-ribon">
                        {{-- <i class="fa fa-twitter"></i> --}}
                    </div>

                    <div class="media ml-4">
                        <a href="#">
                            @if($data->users_detail->profile_pic)
                            <img class="align-self-center rounded-circle mr-3" style="width:85px; height:85px;" alt="" src="{{ $data->users_detail->profile_pic ? asset($data->users_detail->profile_pic) : asset('img/default-img.png') }}">
                            @else
                            <div class="align-self-center rounded-circle mr-3"><i class="no-image-text">No Image Available</i></div>
                            @endif
                        </a>
                        <div class="media-body">
                            <h2 class="text-white display-6">{{ $data->name }}</h2>
                            <p class="text-light">{{ $data->users_detail->position->position_name }}</p>
                        </div>
                    </div>
                </div>
                <div class="row mt-4 mb-2 text-center">
                    <div class="col-md-6">
                        <a type="button" data-toggle="modal" data-target="#changePass" id="manButton" href="#"><i class="menu-icon fa fa-lock"></i> Reset Password</a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('edit.user', $data->id) }}"><i class="fa fa-edit"></i> Update Data</a>
                    </div>
                </div>
                <hr>
                <div class="twt-write ml-4 mt-4 col-sm-12">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Nomor Pekerja</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $data->users_detail->employee_id }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>User Id</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $data->id }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Name</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $data->name }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Email</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $data->email }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Department</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $data->users_detail->department->department_name }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Position</label>
                        </div>
                        <div class="col-md-6">
                            <p>{{ $data->users_detail->position->position_name }}</p>
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
                                @foreach ($data->role_id as $index => $usrRole)
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
<div class="modal fade" id="changePass" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Change Password</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="resetPasswordForm" method="POST" action="{{ route('reset.user.password', $data->id) }}">
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
