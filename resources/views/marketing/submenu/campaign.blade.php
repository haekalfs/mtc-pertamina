@extends('layouts.main')

@section('active-marketing')
active font-weight-bold
@endsection

@section('show-marketing')
show
@endsection

@section('marketing-campaign')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-bullhorn"></i> Marketing Campaign</h1>
        <p class="mb-4">Kegiatan Marketing MTC.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
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
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">List Data</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Event</a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('marketing-campaign') }}">
                        @csrf
                        <div class="row d-flex justify-content-right mb-4">
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="typeSelected">Jenis Kegiatan:</label>
                                            <select class="custom-select" id="typeSelected" name="typeSelected">
                                                <option value="-1" @if ($typeSelected == '-1') selected @endif>Show All</option>
                                                @foreach($campaignType as $type)
                                                    <option value="{{ $type->id }}" @if ($typeSelected == $type->id) selected @endif>{{ $type->description }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Month Dropdown -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="month">Month:</label>
                                            <select class="custom-select" id="month" name="month">
                                                <option value="all" @if ($monthSelected == 'all') selected @endif>Show All</option>
                                                @for ($m = 1; $m <= 12; $m++)
                                                    <option value="{{ $m }}" @if ($monthSelected == $m) selected @endif>
                                                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="year">Year:</label>
                                            <select class="custom-select" id="year" name="year">
                                                <option value="all" @if ($yearSelected == 'all') selected @endif>Show All</option>
                                                @for ($i = $currentYear; $i >= $currentYear - 5; $i--)
                                                    <option value="{{ $i }}" @if ($yearSelected == $i) selected @endif>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex align-self-end justify-content-start">
                                        <div class="form-group">
                                            <div class="align-self-center">
                                                <button type="submit" class="btn btn-primary" style="padding-left: 1.2em; padding-right: 1.2em;"><i class="ti-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="row justify-content-start">
                        @foreach($data as $item)
                        <div class="col-lg-6 col-md-6 d-flex mb-4">
                            <div class="blog-card w-100" style="height: 250px; border: 1px solid rgb(220, 219, 219);">
                                <div class="meta">
                                    <div class="photo" style="background-image: url('{{ asset($item->img_filepath ? $item->img_filepath : 'img/default-img.png') }}');"></div>
                                </div>
                                <div class="description">
                                    <h1>{{ Str::limit($item->campaign_name, 45, '...') }}</h1>
                                    <div style="font-weight: 500; line-height: 1.8; font-size: 13px; margin-top: 15px;">
                                        <!-- Informasi Kegiatan -->
                                        <div>
                                            <strong>Jenis Kegiatan</strong>: {{ Str::limit($item->jenis->description, 30, '...') }}
                                        </div>

                                        <!-- Person in Charge -->
                                        <div>
                                            <strong>PIC</strong>: {{ $item->user->name }}
                                        </div>
                                        <div>
                                            <strong>Tanggal </strong>: {{ $item->date }}
                                        </div>
                                    </div>
                                    @php
                                        $created_at = \Carbon\Carbon::parse($item->updated_at);
                                        $now = \Carbon\Carbon::now();
                                        $diffInDays = $created_at->diffInDays($now);
                                    @endphp
                                    <div class="mt-2">
                                        <small class="text-muted">Last updated
                                            @if($diffInDays < 7)
                                                {{ $created_at->diffForHumans() }}
                                            @else
                                                a long time ago
                                            @endif
                                        </small>
                                    </div>
                                    <p class="read-more">
                                        <a href="{{ route('preview-campaign', $item->id) }}">View Detail</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination Links -->
                    <nav>
                        <ul class="pagination justify-content-center">
                            {{-- Previous Page Link --}}
                            @if ($data->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">Previous</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $data->previousPageUrl() }}" rel="prev">Previous</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($data->links()->elements as $element)
                                {{-- "Three Dots" Separator --}}
                                @if (is_string($element))
                                    <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                                @endif

                                {{-- Array Of Links --}}
                                @if (is_array($element))
                                    @foreach ($element as $page => $url)
                                        @if ($page == $data->currentPage())
                                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                        @else
                                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($data->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $data->nextPageUrl() }}" rel="next">Next</a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">Next</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade zoom90" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 1000px;" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('campaign.store') }}" onsubmit="return validateForm('png,jpeg,jpg,svg,gif', 'file-upload')">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="file-upload" style="cursor: pointer;">
                                <img id="image-preview" src="{{ asset('img/default-img.png') }}" style="height: 150px; width: 200px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="file-upload" type="file" name="img" style="display: none;" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-md-12 ml-2">
                                        <div class="d-flex align-items-center mb-3">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Nama Kegiatan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control underline-input" name="activity_name" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Jenis Kegiatan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <select class="custom-select underline-input" id="jenisKegiatan" name="jenisKegiatan">
                                                    @foreach($campaignType as $type)
                                                        <option value="{{ $type->id }}">{{ $type->description }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Informasi Kegiatan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control underline-input" name="activity_info">
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">PIC :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <select class="custom-select underline-input" id="person_in_charge" name="person_in_charge">
                                                    @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Tanggal :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="date" class="form-control underline-input" name="activity_date">
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
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
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
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('image-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
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

