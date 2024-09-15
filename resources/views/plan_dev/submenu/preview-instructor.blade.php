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
<style>
.bg-login {
    width: 100%;
    background-image: url(./img/kilang-minyak.png);
    background-size: cover;     /* Ensures the background image covers the entire area */
    background-repeat: no-repeat; /* Prevents the background image from repeating */
    background-position: center;  /* Centers the background image */
    opacity: 0.9; /* Sets the opacity to 90% */
}
</style>
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-male"></i> Preview Instructor Biodata</h1>
        <p class="mb-4">Biodata Instruktur.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
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
                            @if($data->imgFilepath)
                            <img class="align-self-center rounded-circle mr-3" style="width:85px; height:85px;" alt="" src="{{ asset($data->imgFilepath) }}">
                            @else
                            <div class="align-self-center rounded-circle mr-3"><i class="no-image-text">No Image Available</i></div>
                            @endif
                        </a>
                        <div class="media-body">
                            <h2 class="text-white display-6">{{ $data->instructor_name }}</h2>
                            <p class="text-light">
                                @php
                                    $roundedScore = round($data->average_feedback_score, 1); // Round to one decimal place
                                    $wholeStars = floor($roundedScore);
                                    $halfStar = ($roundedScore - $wholeStars) >= 0.5;
                                @endphp

                                @for ($i = 0; $i < 5; $i++)
                                    @if ($i < $wholeStars)
                                        <i class="fa fa-star text-warning"></i>
                                    @elseif ($halfStar && $i == $wholeStars)
                                        <i class="fa fa-star-half-o text-warning"></i>
                                    @else
                                        <i class="fa fa-star-o text-warning"></i>
                                    @endif
                                @endfor
                                {{ $roundedScore ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row mt-4 mb-2 text-center">
                    <div class="col-md-6">
                        <a type="button" data-toggle="modal" data-target="#updateHour" href="#"><i class="fa fa-plus"></i> Tambah Jam Mengajar</a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('edit-instructor', $data->id) }}"><i class="fa fa-edit"></i> Update Data</a>
                    </div>
                </div>
                <hr>
                <div class="twt-write ml-5 mt-4 col-sm-12">
                    <div class="row">
                        <div class="col-md-5">
                            <label>Email</label>
                        </div>
                        <div class="col-md-7">
                            <p>: {{ $data->instructor_email }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <label>Umur</label>
                        </div>
                        <div class="col-md-7">
                            <p>: {{ \Carbon\Carbon::parse($data->instructor_dob)->age}} Tahun</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <label>Tanggal Lahir</label>
                        </div>
                        <div class="col-md-7">
                            <p>: {{ $data->instructor_dob }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <label>Gender</label>
                        </div>
                        <div class="col-md-7">
                            <p>: {{ $data->instructor_gender }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <label>Address</label>
                        </div>
                        <div class="col-md-7">
                            <p>: {{ $data->instructor_address }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <label>Total Jam Mengajar</label>
                        </div>
                        <div class="col-md-7">
                            <p>: {{ $data->working_hours }} Jam</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <label>Curriculum Vitae</label>
                        </div>
                        <div class="col-md-7">
                            <p>: <a href="{{ asset($data->cvFilepath) }}" target="_blank"><u><i class="fa fa-download"></i> Download </a></u></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <label>Ijazah</label>
                        </div>
                        <div class="col-md-7">
                            <p>: <a href="{{ asset($data->ijazahFilepath) }}" target="_blank"><u><i class="fa fa-download"></i> Download </a></u></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <label>Dokumen Pendukung</label>
                        </div>
                        <div class="col-md-7">
                            <p>: <a href="{{ asset($data->documentPendukungFilepath) }}" target="_blank"><u><i class="fa fa-download"></i> Download </a></u></p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row ml-2 mt-2 col-sm-12 mb-4">
                    <div class="col-md-3 font-weight-bold">
                        <label>Certificates : </label>
                    </div>
                    <div class="col-md-9 pl-0">
                        <p>
                            @php
                                $badgeColors = ['bg-success'];
                            @endphp

                            @foreach($certificateData as $index => $certificateItem)
                                @if ($certificateItem->catalog)
                                    <span class="badge text-white p-2 mb-1 {{ $badgeColors[$index % count($badgeColors)] }}">
                                        {{ $certificateItem->catalog->certificate_name }}
                                    </span>
                                @endif
                            @endforeach

                            @php
                                $otherBadges = ['bg-secondary'];
                            @endphp

                            @foreach($remainingCerts as $index => $certs)
                                @if ($certs->catalog)
                                    <span class="badge text-white p-2 mb-1 {{ $otherBadges[$index % count($otherBadges)] }}">
                                        {{ $certs->catalog->certificate_name }}
                                    </span>
                                @endif
                            @endforeach
                        </p>
                    </div>
                    <ul class="ml-4">
                        <li class="text-success">Green = Qualified Certificates for selected Penlat.</li>
                        <li class="text-secondary">Grey = Other certificates owned by instructor.</li>
                    </ul>
                </div>
            </section>
        </div>
        <div class="col-xl-4 col-lg-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <span class="text-danger font-weight-bold">Instructor Data Verification</span>
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
<div class="modal fade" id="updateHour" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Update Instructor Working Hours</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
            <form method="POST" action="{{ route('instructor.update.hours', $data->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="col-md-12 zoom90">
                        <div class="d-flex align-items-center mb-4">
                            <div style="width: 140px;" class="mr-2">
                                <p style="margin: 0;">Update Hours :</p>
                            </div>
                            <div class="flex-grow-1 ml-4">
                                <div class="counter">
                                    <span class="down" onclick='decreaseCount(event, this)'><i class="fa fa-minus text-danger"></i></span>
                                    <input class="form-control" name="working_hours" type="text" value="{{ $data->working_hours }}">
                                    <span class="up" onclick='increaseCount(event, this)'><i class="fa fa-plus text-success"></i></span>
                                </div>
                            </div>
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
<script>
function increaseCount(a, b) {
  var input = b.previousElementSibling;
  var value = parseInt(input.value, 10);
  value = isNaN(value) ? 0 : value;
  value++;
  input.value = value;
}

function decreaseCount(a, b) {
  var input = b.nextElementSibling;
  var value = parseInt(input.value, 10);
  if (value > 1) {
    value = isNaN(value) ? 0 : value;
    value--;
    input.value = value;
  }
}
</script>
@endsection

