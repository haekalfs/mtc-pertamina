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
<div class="animated fadeIn">
    <div class="row">
        <div class="col-xl-4 col-md-6 animateBox">
            <a href="{{ route('certificate') }}" class="clickable-card">
                <div class="card border-left-primary shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">Certificate Peserta</div>
                                <div class="h6 mb-0 text-gray-800"><span style="font-size: 14px;">Status Sertifikasi Peserta</span></div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-trophy fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="mb-4">
            <div class="outer">
                <div class="inner"></div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 animateBox">
            <a href="{{ route('certificate-instructor') }}" class="clickable-card">
                <div class="card border-left-success shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-heading mb-1 font-weight-bold">
                                    Certificate Instruktur</div>
                                    <div class="h6 mb-0 text-gray-800"><span style="font-size: 14px;">Sertifikasi Trainer/Instruktur</span></div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-male fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <span class="text-primary font-weight-bold">Panduan Menu Sertifikat</span>
                </div>
                <div class="card-body" style="background-color: rgb(247, 247, 247);">
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">Gambaran Umum</h6>
                    <p>Menu Sertifikat menyediakan dua sub-menu utama untuk mengelola sertifikasi dalam sistem MTC:</p>
                    <ul class="ml-4">
                        <li><strong>Sertifikasi Peserta</strong>: Menu ini digunakan untuk mengelola status sertifikasi peserta yang telah mengikuti pelatihan di MTC. Menu ini memberikan detail tentang kemajuan dan hasil sertifikasi mereka.</li>
                        <li><strong>Sertifikat Instruktur</strong>: Menu ini digunakan untuk mengelola sertifikasi bagi instruktur atau pelatih di MTC. Menu ini mencakup informasi tentang status sertifikasi dan detail relevan lainnya untuk instruktur.</li>
                    </ul>
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">Poin Penting</h6>
                    <ul class="ml-4">
                        <li>Setiap sub-menu dirancang untuk mengelola dan melihat detail sertifikasi yang spesifik baik untuk peserta maupun instruktur.</li>
                        <li>Pastikan semua data sertifikasi yang dimasukkan atau dimodifikasi akurat dan mencerminkan status terkini dari individu yang bersangkutan.</li>
                        <li>Akses ke menu ini harus dibatasi hanya untuk personel yang berwenang guna menjaga integritas data.</li>
                        <li>Perbarui informasi sertifikasi secara berkala agar sesuai dengan hasil pelatihan terbaru dan kualifikasi instruktur.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
