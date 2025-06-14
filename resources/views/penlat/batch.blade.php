@extends('layouts.main')

@section('active-penlat')
active font-weight-bold
@endsection

@section('show-penlat')
show
@endsection

@section('batch-penlat')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-folder"></i> List Batch Penlat</h1>
        <p class="mb-4">List Pelatihan at MTC.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="#" id="refreshBatchBtn" class="btn btn-sm btn-secondary shadow-sm text-white">
            <i class="fa fa-refresh fa-sm"></i> Refresh Data
        </a>
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
                    <h6 class="m-0 font-weight-bold" id="judul">List Data</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register New Batch</a> --}}
                        <button type="button" id="exportBtn" class="btn btn-sm btn-secondary shadow-sm text-white">
                            <i class="fa fa-cloud-download fa-sm"></i> Export Data
                        </button>
                    </div>
                </div>
                <div class="card-body zoom90">
                    <div class="row d-flex justify-content-start mb-4">
                        <div class="col-md-12">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <div class="form-group" id="penlatContainer">
                                        <label for="email">Nama Pelatihan :</label>
                                        <select class="custom-select" id="namaPenlat" name="namaPenlat">
                                            <option value="-1" selected>Show All</option>
                                            @foreach($penlatList as $penlat)
                                                <option value="{{ $penlat->id }}">{{ $penlat->description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="email">Kategori Pelatihan :</label>
                                        <select class="custom-select" id="kategori" name="kategori">
                                            <option value="-1" selected>Show All</option>
                                            @foreach($penlatList->unique('kategori_pelatihan') as $kategori)
                                                <option value="{{ $kategori->kategori_pelatihan }}">{{ $kategori->kategori_pelatihan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="email">Jenis Pelatihan :</label>
                                        <select class="custom-select" id="jenis" name="jenis">
                                            <option value="-1" selected>Show All</option>
                                            @foreach($penlatList->unique('jenis_pelatihan') as $jenis)
                                                <option value="{{ $jenis->jenis_pelatihan }}">{{ $jenis->jenis_pelatihan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="email">Tahun :</label>
                                        <select class="form-control" id="periode" name="periode">
                                            <option value="-1">Show All</option>
                                            @foreach(range(date('Y'), date('Y') - 5) as $year)
                                                <option value="{{ $year }}" @if ($year == date('Y')) selected @endif>{{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex align-self-end justify-content-start">
                                    <div class="form-group">
                                        <div class="align-self-center">
                                            <button id="filterButton" class="btn btn-primary" style="padding-left: 1.2em; padding-right: 1.2em;"><i class="ti-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table id="batchTables" class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Display</th>
                                <th>Nama Pelatihan</th>
                                <th>Batch</th>
                                <th>Jenis Pelatihan</th>
                                <th>Tgl Pelaksanaan</th>
                                <th>Revenue (Master Data)</th>
                                <th width="120px">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">
                    <span class="text-danger font-weight-bold">Notes</span>
                </div>
                <div class="card-body" style="background-color: rgb(247, 247, 247);">
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">General Guidelines</h6>
                    <ul class="ml-4">
                        <li>Batches can be registered in two ways: through <span class="text-danger">Excel import</span> or <span class="text-danger">manual registration</span>.</li>
                        <li>Ensure that you select the appropriate method for batch registration based on your training data needs.</li>
                        <li>Users <span class="text-danger">should not delete batches carelessly</span> if they are already linked with other functions such as Participant Infographics, Participant Certification, Utilities, or the Profit menu.</li>
                        <li>Batches that are linked to other functions will impact related data in the system, so be cautious before proceeding with deletion.</li>
                        <li>You can view detailed information for each batch by clicking on it, which will display more in-depth details, including the list of associated costs.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 900px;" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('batch.store') }}" onsubmit="return validateForm()">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters mb-3">
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="file-upload" style="cursor: pointer;">
                                <img id="image-preview" src="{{ asset('img/default-img.png') }}" style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="file-upload" type="file" name="image" style="display: none;" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Nama Pelatihan :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <select id="penlatSelect" class="form-control select2" name="penlat" required>
                                        <option value="" selected disabled>Select Pelatihan...</option>
                                        @foreach ($penlatList as $item)
                                        <option value="{{ $item->id }}">{{ $item->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Nama Program :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" id="programInput" class="form-control" name="program" required>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Batch :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control" name="batch" required>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div style="width: 140px;" class="mr-2">
                                    <p style="margin: 0;">Tgl Pelaksanaan :</p>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="date" class="form-control" name="date" required>
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
<div class="modal fade" id="editDataModal" tabindex="-1" role="dialog" aria-labelledby="editDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 900px;" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="editDataModalLabel">Edit Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body mr-2 ml-2">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="edit-file-upload" style="cursor: pointer;">
                                <img id="edit-image-preview" src="{{ asset('img/default-img.png') }}" style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="edit-file-upload" type="file" name="edit_display" style="display: none;" accept="image/*" onchange="previewEditImage(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Nama Pelatihan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <select id="edit_penlat_id" name="edit_penlat_id" class="form-control select2" required>
                                                    <option selected disabled>Select Pelatihan...</option>
                                                    @foreach($penlatList as $penlat)
                                                        <option value="{{ $penlat->id }}">{{ $penlat->description }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Nama Program :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" id="edit_nama_program" name="edit_nama_program" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Batch :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" id="edit_batch" name="edit_batch" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Tgl Pelaksanaan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="date" class="form-control" id="edit_tgl_pelaksanaan" name="edit_tgl_pelaksanaan" required>
                                            </div>
                                        </div>
                                        <!-- Continue for alias, jenis_pelatihan, kategori_program -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
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

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('image-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function previewEditImage(event) {
        const output = document.getElementById('edit-image-preview');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function () {
            URL.revokeObjectURL(output.src) // Free up memory
        }
    }
</script>
<script>
$(document).ready(function() {
    $('#namaPenlat').select2({
        placeholder: "Select Pelatihan...",
        width: '100%',
        allowClear: true,
        dropdownParent: $('#penlatContainer'),
        language: {
            noResults: function() {
                return "No result match your request... Create new in Master Data Menu!"; // Customize this message as needed
            }
        }
    });
    var table = $('#batchTables').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        deferRender: true,
        ajax: {
            url: "{{ route('batch-penlat') }}",
            data: function (d) {
                d.namaPenlat = $('#namaPenlat').val();
                d.jenisPenlat = $('#jenis').val();
                d.kategoriPenlat = $('#kategori').val();
                d.periode = $('#periode').val();
            }
        },
        columns: [
            { data: 'display', name: 'display', orderable: false, searchable: false },
            { data: 'nama_pelatihan', name: 'penlat.description' },
            { data: 'batch', name: 'batch' },
            { data: 'jenis_pelatihan', name: 'penlat.jenis_pelatihan' },
            { data: 'tgl_pelaksanaan', name: 'date' },
            { data: 'harga_pelatihan', name: 'harga_pelatihan' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Edit functionality
    $('#batchTables').on('click', '.edit-tool', function () {
        let id = $(this).data('id');

        $.get('/penlat-batch/' + id + '/edit', function (data) {
            $('#edit_id').val(data.id);
            $('#edit_penlat_id').val(data.penlat_id).trigger('change');
            $('#edit_nama_program').val(data.nama_program);
            $('#edit_batch').val(data.batch);
            $('#edit_tgl_pelaksanaan').val(data.date);

            var imageUrl = data.image ? data.image : '{{ asset('img/default-img.png') }}';
            $('#edit-image-preview').attr('src', imageUrl);

            $('#editForm').attr('action', '/penlat-batch-update/' + id);
            $('#editDataModal').modal('show');
        });
    });

    // Delete functionality
    $('#batchTables').on('click', '.btn-outline-danger', function () {
        let id = $(this).data('id');

        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this record!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: '{{ route("delete.batch", ":id") }}'.replace(':id', id),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(result) {
                        swal("Poof! Your record has been deleted!", {
                            icon: "success",
                        });
                        table.draw(); // Redraw the table
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = xhr.responseJSON && xhr.responseJSON.error
                            ? xhr.responseJSON.error
                            : "Oops! Something went wrong!";

                        swal("Error!", errorMessage, "error");
                    }
                });
            } else {
                swal("Your record is safe!");
            }
        });
    });

    // Filter button click event
    $('#filterButton').click(function() {
        table.draw();
    });

    // Initialize Select2
    $('#penlatSelect').select2({
        dropdownParent: $('#inputDataModal'),
        theme: "classic",
        placeholder: "Select Pelatihan...",
        width: '100%',
        language: {
            noResults: function() {
                return "No result match your request... Create new in Master Data Menu!"; // Customize this message as needed
            }
        }
    });

    // Event listener for change event
    $('#penlatSelect').on('change', function() {
        var selectedOption = $(this).find('option:selected').text();
        $('#programInput').val(selectedOption);
    });

    // Initialize Select2
    $('#edit_penlat_id').select2({
        dropdownParent: $('#editDataModal'),
        theme: "classic",
        placeholder: "Select Pelatihan...",
        width: '100%',
        tags: true,
    });
});
function validateForm() {
    const penlatSelect = document.getElementById('penlatSelect');
    if (penlatSelect.value === "") {
        alert("Please select a Pelatihan.");
        return false; // Prevent form submission
    }
    return true; // Allow form submission
}
document.getElementById('refreshBatchBtn').addEventListener('click', function (event) {
    event.preventDefault();

    // Trigger SweetAlert with input for the year
    swal({
        title: "Refresh Data Batches?",
        text: "Enter the year to refresh batches data:",
        content: {
            element: "input",
            attributes: {
                type: "number",
                placeholder: "Enter year",
                min: "1900",
                max: "2500",
            },
        },
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((inputYear) => {
        if (inputYear) {
            // Show loading message before sending the request
            swal({
                title: "Processing...",
                text: "Refreshing Penlat batch data for year " + inputYear,
                icon: "info",
                buttons: false,
                closeOnClickOutside: false
            });

            // Send AJAX request to trigger the batch refresh job with the input year
            $.ajax({
                url: "{{ route('refresh.batch') }}", // Your route to refresh data
                method: "POST", // Use POST to pass the year
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}", // CSRF token for protection
                },
                data: {
                    year: inputYear // Pass the input year to the server
                },
                success: function (data) {
                    if (data.success) {
                        swal({
                            title: "Success!",
                            text: "The Penlat batch data refresh has been started for year " + inputYear,
                            icon: "success",
                            button: "OK",
                        });
                    } else {
                        swal({
                            title: "Error!",
                            text: "Something went wrong while trying to refresh the data.",
                            icon: "error",
                            button: "OK",
                        });
                    }
                },
                error: function (xhr, status, error) {
                    swal({
                        title: "Error!",
                        text: "There was an error processing the request: " + xhr.responseText,
                        icon: "error",
                        button: "OK",
                    });
                }
            });
        }
    });
});
</script>
<script>
    document.getElementById('exportBtn').addEventListener('click', function () {
        // Get filter values
        const penlat = document.getElementById('namaPenlat').value;
        const kategori_pelatihan = document.getElementById('kategori').value;
        const jenis_pelatihan = document.getElementById('jenis').value;
        const periode = document.getElementById('periode').value;

        // Show processing dialog
        swal({
            title: "Processing...",
            text: "Please wait while the data is being exported.",
            icon: "info",
            buttons: false,
            closeOnClickOutside: false,
        });

        // Send POST request
        fetch("{{ route('export.batches.data') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                penlat: penlat,
                kategori_pelatihan: kategori_pelatihan,
                jenis_pelatihan: jenis_pelatihan,
                periode: periode
            })
        })
        .then(response => {
            if (response.ok) {
                return response.blob();
            } else {
                throw new Error('Export failed');
            }
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = "Batches_Master_Data.xlsx";
            document.body.appendChild(a);
            a.click();
            a.remove();

            // Success message
            swal({
                title: "Success!",
                text: "The data has been exported successfully.",
                icon: "success",
            });
        })
        .catch(error => {
            console.error('Error:', error);

            // Error message
            swal({
                title: "Error!",
                text: "Failed to export data. Please try again.",
                icon: "error",
            });
        });
    });
</script>
@endsection
