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
                        {{-- <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Location</a> --}}
                    </div>
                </div>
                <div class="card-body zoom90">
                    <table id="docLetter" class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Nomor Sertifikat</th>
                                <th>Tgl Terbit</th>
                                <th>Nama Pelatihan</th>
                                <th>Batch</th>
                                <th width="250px">Certificate</th>
                                <th>Nama Peserta</th>
                                <th>Dibuat Oleh</th>
                                <th width="50px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listCertificates as $certificate)
                            <tr>
                                <td>{{ $certificate->certificate_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($certificate->issued_date)->format('d-M-Y') }}</td>
                                <td>{{ $certificate->penlatCertificate->batch->penlat->description }}</td>
                                <td>{{ $certificate->penlatCertificate->batch->batch }}</td>
                                <td>{{ $certificate->certificate_number }} / {{ explode('/', $certificate->penlatCertificate->batch->batch)[0] }} / PMTC / {{ explode('/', $certificate->penlatCertificate->batch->batch)[2] }} / {{ explode('/', $certificate->penlatCertificate->batch->batch)[3] }}</td>
                                <td>{{ $certificate->peserta->nama_peserta }}</td>
                                <td>{{ $certificate->penlatCertificate->created_by }}</td>
                                <td class="text-center">
                                    <a class="btn btn-outline-success btn-md mb-2 mr-2 generateQR" href="javascript:void(0)"
                                        data-id="{{ $certificate->id }}">
                                        <i class="fa fa-qrcode"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
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
<script>
    $(document).ready(function () {
        $('#docLetter').on('click', '.generateQR', function () {
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
@endsection
