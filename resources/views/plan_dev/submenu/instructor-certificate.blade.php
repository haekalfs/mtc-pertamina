@extends('layouts.main')

@section('active-pd')
active font-weight-bold
@endsection

@section('show-pd')
show
@endsection

@section('certificate')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="menu-icon fa fa-certificate"></i> Instructors Certificates</h1>
        <p class="mb-4">Sertifikat Trainer.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('certificate-main') }}" class="btn btn-sm btn-secondary shadow-sm text-white"><i class="fa fa-backward"></i> Go Back</a>
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
                    <div class="d-flex">
                        <div class="text-right">
                            <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Certificate</a>
                        </div>
                    </div>
                </div>
                <div class="card-body zoom90 p-4">
                    <form method="GET" action="">
                        @csrf
                        <div class="row d-flex justify-content-start mb-4">
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="email">Nama Pelatihan :</label>
                                            <select class="form-control form-control" name="penlat">
                                                <option value="1">Show All</option>
                                                @foreach ($penlatList as $item)
                                                <option value="{{ $item->id }}">{{ $item->description }}</option>
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
                    <table id="docLetter" class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Judul Sertifikat</th>
                                <th>Issuing Organization</th>
                                <th>Related To</th>
                                <th>Keterangan</th>
                                <th>Total</th>
                                <th width="160px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                            <tr>
                                <td>{{ $item->certificate_name }}</td>
                                <td>{{ $item->issuedBy }}</td>
                                <td>
                                    @php
                                        $badgeColors = ['bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'bg-primary', 'bg-secondary'];
                                    @endphp

                                    @foreach($item->relationOne as $index => $relatedItem)
                                        <span class="badge text-white {{ $badgeColors[$index % count($badgeColors)] }}">{{ $relatedItem->penlat->description }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $item->keterangan }}</td>
                                <td>{{ $item->total_issued }}</td>
                                <td>
                                    <a class="btn btn-outline-secondary btn-sm mr-2" href="{{ route('preview-certificate-catalog', $item->id) }}"><i class="menu-Logo fa fa-eye"></i> View</a>
                                    <a class="btn btn-outline-danger text-danger btn-sm delete-instructor-certificate" data-id="{{ $item->id }}">
                                        <i class="fa fa-trash-o"></i> Delete
                                    </a>
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

<div class="modal fade" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('certificate-catalog.store') }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 200px;" class="mr-2">
                            <p style="margin: 0;">Judul Sertifikat :</p>
                        </div>
                        <div class="flex-grow-1">
                            <input type="text" id="judulSertifikat" class="form-control" name="judulSertifikat">
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 200px;" class="mr-2">
                            <p style="margin: 0;">Issuing Organization :</p>
                        </div>
                        <div class="flex-grow-1">
                            <input type="text" id="issued_by" class="form-control" name="issued_by">
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-4">
                        <div style="width: 200px;" class="mr-2">
                            <p style="margin: 0;">Keterangan :</p>
                        </div>
                        <div class="flex-grow-1">
                            <textarea class="form-control" rows="3" name="keterangan"></textarea>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div style="width: 200px;" class="mr-2">
                            <p style="margin: 0;">Related To :</p>
                        </div>
                        <div class="flex-grow-1" style="max-width: 530px;">
                            <select data-placeholder="Penlat List..." multiple class="standardSelect form-control" name="penlats[]" multiple="multiple">
                                @foreach ($penlatList as $item)
                                <option value="{{ $item->id }}">{{ $item->description }}</option>
                                @endforeach
                            </select>
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
$(document).ready(function() {
    $('.delete-instructor-certificate').click(function(e) {
        e.preventDefault();

        let certificateId = $(this).data('id'); // Assuming you pass the certificate ID via data attribute

        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this certificate!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                // Make AJAX request to delete the certificate
                $.ajax({
                    url: '{{ route("certificates_catalog.delete", ":id") }}'.replace(':id', certificateId),
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}', // Include the CSRF token
                    },
                    success: function(response) {
                        swal("Success! The certificate and its relations have been deleted!", {
                            icon: "success",
                        }).then(() => {
                            location.reload(); // Reload the page after deletion
                        });
                    },
                    error: function(xhr) {
                        swal("Error! Something went wrong.", {
                            icon: "error",
                        });
                    }
                });
            } else {
                // Show a message if deletion is canceled
                swal("Your certificate is safe!", {
                    icon: "info",
                });
            }
        });
    });
});

</script>
<script>

    function displayFileName() {
        const input = document.getElementById('file');
        const label = document.getElementById('file-label');
        const file = input.files[0];
        if (file) {
            label.textContent = file.name;
        }
    }
</script>

@endsection
