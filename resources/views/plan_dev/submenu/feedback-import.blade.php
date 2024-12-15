@extends('layouts.main')

@section('active-pd')
active font-weight-bold
@endsection

@section('show-pd')
show
@endsection

@section('feedback-report')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="menu-icon fa fa-trophy"></i> Feedback Report</h1>
        <p class="mb-4">Import Feedback Report.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('feedback-report') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">Upload File</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-icon fa fa-download"></i> Download</a> --}}
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('feedback.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-11">
                                <div class="form-group">
                                    <label for="file">Xlsx File :</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="file" name="file" aria-describedby="file" onchange="displayFileName()">
                                        <label class="custom-file-label" for="file" id="file-label">Choose file</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1 d-flex justify-content-center align-items-end">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary" id="submitButton">Import</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <span class="text-danger font-weight-bold">Data Import Guidelines</span>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" download href="{{ asset('uploads/template/template_feedback.xlsx') }}"><i class="menu-Logo fa fa-download"></i> Download Example</a>
                    </div>
                </div>
                <div class="card-body" style="background-color: rgb(247, 247, 247);">
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">Import Rules</h6>
                    <ul class="ml-4">
                        <li>Ensure all import files are in the correct format (XLSX) as specified in the guidelines.</li>
                        <li>Maximum file size is 50MB.</li>
                        <li>Verify the data in the files for accuracy and completeness before importing.</li>
                        <li>Check for and resolve any data inconsistencies or errors in the file to prevent import issues.</li>
                        <li>Unauthorized changes to import files or procedures are strictly prohibited.</li>
                        <li class="text-danger">Double-check the mapping of data fields to ensure they align with the database schema, The excel should start from row 2, the layout of the data should look like below image.</li>
                    </ul>
                    <img src="{{ asset('uploads/example4.png') }}" class="img-fluid" style="padding: 1rem;">
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('submitButton').addEventListener('click', function (e) {
    e.preventDefault(); // Prevent the default form submission

    swal({
        title: "Confirm Import",
        text: "Please check the following before proceeding:",
        content: {
            element: "div",
            attributes: {
                innerHTML: `
                    <div style="text-align: left;">
                        <label><input type="checkbox" id="checkbox1"> The Column Starts on A</label><br>
                        <label><input type="checkbox" id="checkbox2"> The Row Starts on 3</label><br>
                        <label><input type="checkbox" id="checkbox3"> I Already Converted All Formulas to Actual Values</label><br>
                        <label><input type="checkbox" id="checkbox4"> The sheets is only 1, no hidden sheets!</label><br>
                        <label>To be sure, please check the layout picture below!</label>
                    </div>
                `
            }
        },
        buttons: {
            cancel: {
                text: "Cancel",
                value: false,
                visible: true,
                className: "btn btn-danger",
                closeModal: true,
            },
            confirm: {
                text: "Proceed",
                value: true,
                visible: true,
                className: "btn btn-primary",
            },
        },
    }).then((value) => {
        if (value) {
            // Check if all checkboxes are checked
            if (document.getElementById('checkbox1').checked && document.getElementById('checkbox2').checked && document.getElementById('checkbox3').checked && document.getElementById('checkbox4').checked) {
                document.getElementById('importForm').submit(); // Submit the form
            } else {
                swal({
                    title: "Warning",
                    text: "You must check all the boxes before proceeding!",
                    icon: "warning",
                    button: "Okay",
                });
            }
        }
    });
});
function displayFileName() {
    const input = document.getElementById('file');
    const label = document.getElementById('file-label');
    const file = input.files[0];
    if (file) {
        label.textContent = file.name;
        previewExcel(file);
    }
}
</script>
@endsection
