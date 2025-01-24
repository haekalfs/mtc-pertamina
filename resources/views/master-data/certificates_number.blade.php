@extends('layouts.main')

@section('active-penlat')
active font-weight-bold
@endsection

@section('show-penlat')
show
@endsection

@section('list-certificates')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-certificate mr-1"></i> List Certificates</h1>
        <p class="mb-4">List Certificates.</a></p>
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
                                    <div class="form-group">
                                        <label for="penlat">Nama Pelatihan:</label>
                                        <select class="form-control" name="penlat" id="penlat">
                                            <option value="">Show All</option>
                                            @foreach ($penlatList as $item)
                                                <option value="{{ $item->id }}">{{ $item->description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="batch">Kategori Pelatihan:</label>
                                        <select name="kategori_pelatihan" class="form-control" id="kategori_pelatihan">
                                            <option value="">Show All</option>
                                            @foreach ($penlatList->unique('kategori_pelatihan') as $item)
                                                <option value="{{ $item->kategori_pelatihan }}">{{ $item->kategori_pelatihan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="periode">Tahun:</label>
                                        <select class="form-control" id="periode" name="periode">
                                            <option value="-1" selected>Show All</option>
                                            @foreach(range(date('Y'), date('Y') - 5) as $year)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex align-self-end justify-content-start">
                                    <div class="form-group">
                                        <div class="align-self-center">
                                            <button id="filterButton" class="btn btn-primary"><i class="ti-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table id="listCertificatesMaster" class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Nomor Sertifikat</th>
                                <th>Tgl Terbit</th>
                                <th>Nama Pelatihan</th>
                                <th>Batch</th>
                                <th>Certificate</th>
                                <th>Nama Peserta</th>
                                <th>Dibuat Oleh</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">
                    <span class="text-danger font-weight-bold">Notes</span>
                </div>
                <div class="card-body" style="background-color: rgb(247, 247, 247);">
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">Location Management Guidelines</h6>
                    <ul class="ml-4">
                        <li>Adding new locations will affect the Locations Table.</li>
                        <li>Ensure that each location has a unique location code and an appropriate description.</li>
                        <li>Locations linked to existing assets cannot be deleted; ensure relationships are properly managed before attempting deletion.</li>
                        <li>Follow the correct procedure when updating or registering locations to maintain database integrity.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="qrModalLabel">QR Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="printContainer">
                    <span id="qrTitle" class="p-2 font-weight-bold" style="margin-bottom: 10px;"></span>
                    <div id="qrCodeContainer"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <a id="link" class="btn btn-primary">Validate Certificate</a>
            </div>
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
    $(document).ready(function () {
        $('#penlat').select2({
            placeholder: "Select Pelatihan...",
            width: '100%',
            allowClear: true,
            language: {
                noResults: function() {
                    return "No result match your request... Create new in Master Data Menu!"; // Customize this message as needed
                }
            }
        });

        const table = $('#listCertificatesMaster').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('certificate-number') }}",
                data: function (d) {
                    d.penlat = $('#penlat').val();
                    d.kategori_pelatihan = $('#kategori_pelatihan').val();
                    d.periode = $('#periode').val();
                }
            },
            columns: [
                { data: 'certificate_number', name: 'certificate_number' },
                { data: 'issued_date', name: 'issued_date' },
                { data: 'penlatDescription', name: 'penlatDescription' },
                { data: 'penlatBatch', name: 'penlatBatch' },
                { data: 'certificate', name: 'certificate' },
                { data: 'nama_peserta', name: 'nama_peserta' },
                { data: 'created_by', name: 'created_by' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
            ],
            order: [[0, 'desc']],
        });

        $('#filterButton').on('click', function (e) {
            e.preventDefault();
            table.draw();
        });

        $('#listCertificatesMaster').on('click', '.generateQR', function () {
            var certficateId = $(this).data('id');
            $.ajax({
                url: '{{ route("generate-qr-certificate", "") }}/' + certficateId,
                type: 'GET',
                success: function(response) {
                    $('#qrTitle').text(response.nama_peserta);
                    $('#link').attr('href', response.link);  // Use .attr() to set the href attribute
                    $('#qrCodeContainer').html('<img src="' + response.qr_code + '" alt="QR Code" />');  // Embed the QR code as an image
                    $('#qrModal').modal('show');  // Show the modal
                },
                error: function() {
                    alert('Failed to generate QR code.');
                }
            });
        });
    });
</script>
<script>
    document.getElementById('exportBtn').addEventListener('click', function () {
        // Get filter values
        const penlat = document.getElementById('penlat').value;
        const kategori_pelatihan = document.getElementById('kategori_pelatihan').value;
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
        fetch("{{ route('export.certificate.data') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                penlat: penlat,
                kategori_pelatihan: kategori_pelatihan,
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
            a.download = "Certificate_Master_Data.xlsx";
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
