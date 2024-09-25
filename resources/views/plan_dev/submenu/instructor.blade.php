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
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-male"></i> Instruktur</h1>
        <p class="mb-4">Menu Instruktur.</a></p>
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
<style>
    .badge-custom {
        font-size: 0.9rem; /* Adjust the font size */
        padding: 0.5rem 1rem; /* Adjust the padding for height and width */
        border-radius: 0.5rem; /* Optional: Adjust the border radius */
    }
    /* Custom CSS to align the Select2 container */
    .select2-container--default .select2-selection--single {
        height: calc(2.25rem + 2px); /* Adjust this value to match your input height */
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: calc(2.25rem + 2px); /* Adjust this to vertically align the text */
    }

    .select2-container .select2-selection--single {
        height: 100% !important; /* Ensure the height is consistent */
    }

    .select2-container {
        width: 100% !important; /* Ensure the width matches the form control */
    }
</style>
<div class="animated fadeIn zoom90">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="fa fa-user"></i> List Data</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="{{ route('register-instructor') }}"><i class="menu-Logo fa fa-plus"></i> Register Instructor</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row d-flex justify-content-start mb-1 p-1">
                        <div class="col-md-12">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email">Nama Pelatihan :</label>
                                        <select id="penlatSelect" class="form-control" name="penlat">
                                            <option value="-1" {{ $penlatId == '-1' ? 'selected' : '' }}>Show All</option>
                                            @foreach ($penlatList as $item)
                                                <option value="{{ $item->id }}" {{ $item->id == $penlatId ? 'selected' : '' }}>
                                                    {{ $item->description }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email">Status :</label>
                                        <select class="form-control" name="status">
                                            <option value="-1" {{ $statusId == '-1' ? 'selected' : '' }}>Show All</option>
                                            <option value="1" {{ $statusId == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ $statusId == '0' ? 'selected' : '' }}>Non Active</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email">Umur :</label>
                                        <select class="form-control" name="age">
                                            <option value="-1" {{ $umur == '-1' ? 'selected' : '' }}>Show All</option>
                                            <option value="1" {{ $umur == '1' ? 'selected' : '' }}>20 - 30 Tahun</option>
                                            <option value="2" {{ $umur == '2' ? 'selected' : '' }}>30 - 40 Tahun</option>
                                            <option value="3" {{ $umur == '3' ? 'selected' : '' }}>>= 40</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive zoom90">
                        <table id="listInstructors" class="table table-striped mt-4" style="border: 1px solid rgb(229, 229, 229);">
                            <thead class="text-secondary" style="background-color: #ecedee;">
                                <tr>
                                    <th>Avatar</th>
                                    <th>Name</th>
                                    <th>Umur</th>
                                    <th>Gender</th>
                                    <th>Jam Mengajar</th>
                                    <th>Total Feedback</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#penlatSelect').select2({
        placeholder: "Select Pelatihan...",
        width: '100%',
        height: '100%',
        allowClear: true,
    });
});
$('#listInstructors').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('instructor') }}",
        data: function (d) {
            d.penlat = $('#penlatSelect').val();
            d.status = $('select[name="status"]').val();
            d.age = $('select[name="age"]').val();
        }
    },
    columns: [
        {
            data: 'avatar_img',
            name: 'avatar_img',
            orderable: false,
            searchable: false,
            render: function (data, type, full) {
                return '<div class="round-img"><a href="' + full.avatar_url + '"><img class="rounded-circle" src="' + data + '" style="height: 70px; width: 70px; border: 1px solid rgb(202, 202, 202);"></a></div>';
            }
        },
        {
            data: 'instructor_name',
            name: 'instructor_name',
            render: function (data, type, full) {
                // Combine the instructor_name and rate with minimal spacing between them
                return '<div>' + data +
                    '<div style="margin-top: 3px;">' + full.rate + '</div></div>';
            }
        },
        { data: 'age', name: 'age' },
        { data: 'instructor_gender', name: 'instructor_gender' },
        { data: 'working_hours', name: 'working_hours' },
        {
            data: 'feedbacks_count',
            name: 'feedbacks_count',
            render: function (data, type, full) {
                return data;
            }
        },
        {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            render: function(data, type, full) {
                return '<div class="text-center">' + data + '</div>';
            }
        }
    ]
});

// Reload table when filter is changed
$('#penlatSelect, select[name="status"], select[name="age"]').change(function () {
    $('#listInstructors').DataTable().draw();
});
</script>
@endsection

