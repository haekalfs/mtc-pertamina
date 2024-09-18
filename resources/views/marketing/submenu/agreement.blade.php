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
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-sitemap"></i> Company Agreement</h1>
        <p class="mb-4">Affliated Company.</a></p>
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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> List Data</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Vendor</a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('company-agreement') }}">
                        @csrf
                        <div class="row d-flex justify-content-right mb-4">
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="email">Periode :</label>
                                            <select class="custom-select" id="periode" name="periode">
                                                <option value="-1" {{ request('periode') == '-1' ? 'selected' : '' }}>Show All</option>
                                                @foreach(range(date('Y'), date('Y') - 5) as $year)
                                                    <option value="{{ $year }}" {{ request('periode') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                                @endforeach
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
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered mt-4 zoom90">
                            <thead class="thead-light">
                                <tr>
                                    <th>Agreement</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>
                                    <td data-th="Product">
                                        <div class="row">
                                            <div class="col-md-4 d-flex justify-content-center align-items-center text-center p-3">
                                                <img src="{{ asset($item->img_filepath ? $item->img_filepath : 'img/default-img.png') }}" style="height: 100px; width: 250px;" alt="" class="img-fluid d-none d-md-block rounded mb-2">
                                            </div>
                                            <div class="col-md-8 text-left mt-sm-2">
                                                <h5 class="card-title font-weight-bold">{{ $item->company_name }}</h5>
                                                <div class="ml-2">
                                                    <table class="table table-borderless table-sm">
                                                        <tr>
                                                            <td style="width: 180px;"><i class="ti-minus mr-2"></i> Tipe Dokumen</td>
                                                            <td style="text-align: start;">: @if($item->spk_filepath) SPK @else NON-SPK @endif</span></td>
                                                        </tr>
                                                        @if($item->spk_filepath)
                                                        <tr>
                                                            <td style="width: 180px;"><i class="ti-minus mr-2"></i> Dokumen SPK</td>
                                                            <td style="text-align: start;">: <a href="{{ asset($item->spk_filepath) }}" target="_blank" class="text-secondary"><u>View</u> <i class="fa fa-external-link fa-sm"></i></a></td>
                                                        </tr>
                                                        @endif
                                                        <tr>
                                                            <td><i class="ti-minus mr-2"></i> Release Date</td>
                                                            <td style="text-align: start;">: {{ \Carbon\Carbon::parse($item->date)->format('d-M-Y') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><i class="ti-minus mr-2"></i> Status</td>
                                                            <td style="text-align: start;">: <span class="badge {{ $item->statuses->badge }}">{{ $item->statuses->description }}</span></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="actions text-center">
                                        <div>
                                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('preview-company', $item->id) }}"><i class="fa fa-info-circle"></i> Preview Agreement</a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
            <form method="post" enctype="multipart/form-data" action="{{ route('agreement.store') }}" onsubmit="return validateForm('png,jpeg,jpg,svg,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,odt,ods,odp,rtf', 'file-upload', 'spkFileInput')">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="file-upload" style="cursor: pointer;">
                                <img id="image-preview" src="https://via.placeholder.com/50x50/5fa9f8/ffffff" style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                     <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="file-upload" type="file" name="img" style="display: none;" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Nama Perusahaan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="company_name" required>
                                            </div>
                                        </div>
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
                                        <div class="d-flex align-items-start mb-4">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Informasi Perusahaan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <textarea class="form-control" rows="3" name="company_details"></textarea>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start mb-4">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Agreement Date :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="date" class="form-control" name="agreement_date" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start mb-4">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">SPK/NON-SPK :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <label class="col-md-4">
                                                    <input class="form-radio-input" type="radio" name="type_reimburse" id="projectRadio" value="Project" checked>
                                                    <span class="form-radio-sign">SPK</span>
                                                </label>
                                                <label class="col-md-4">
                                                    <input class="form-radio-input" type="radio" name="type_reimburse" id="othersRadio" value="Others">
                                                    <span class="form-radio-sign">NON-SPK</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start mb-4">
                                            <div style="width: 160px;" class="mr-2">
                                                <p style="margin: 0;">Dokumen SPK :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="file" class="form-control" name="spk_file" id="spkFileInput">
                                                <textarea class="form-control" name="non_spk_details" id="nonSpkTextarea" style="display: none;"></textarea>
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
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectRadio = document.getElementById('projectRadio');
    const othersRadio = document.getElementById('othersRadio');
    const spkFileInput = document.getElementById('spkFileInput');
    const nonSpkTextarea = document.getElementById('nonSpkTextarea');

    function toggleInputFields() {
        if (projectRadio.checked) {
            spkFileInput.style.display = 'block';
            spkFileInput.required = true;
            nonSpkTextarea.style.display = 'none';
            nonSpkTextarea.required = false;
        } else {
            spkFileInput.style.display = 'none';
            spkFileInput.required = false;
            nonSpkTextarea.style.display = 'block';
            nonSpkTextarea.required = true;
        }
    }

    projectRadio.addEventListener('change', toggleInputFields);
    othersRadio.addEventListener('change', toggleInputFields);

    // Initial toggle based on the default checked option
    toggleInputFields();
});
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
            alert(`Please upload a file for ${fileInputIds[i]} before submitting.`);
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

