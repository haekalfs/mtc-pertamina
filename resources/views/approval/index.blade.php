@extends('layouts.main')

@section('active-approval')
active font-weight-bold
@endsection

@section('content')
<!-- Page Heading -->
<h1 class="h4 mb-2 zoom90 text-gray-800 font-weight-bold"><i class="fa fa-file-text-o" aria-hidden="true"></i> Approvals & Submission</h1>
<p class="mb-4">This section displays the approvals and submissions for various requests.</p>

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

@if ($message = Session::get('marquee'))
<div class="alert alert-danger alert-block text-center">
    <button type="button" class="close" data-dismiss="alert" style="opacity: 0.1;">×</button>
    <strong>{!! $message !!}</strong>
</div>
@endif

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif
<div class="row">
    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 timesheet">
        <a href="{{ route('inventory-tools-approval') }}" class="text-decoration-none">
            <div class="card border-left-secondary shadow py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                Inventory Approval</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"> </div>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-fire-extinguisher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

</div>

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<script>
    const cardTimesheet = document.querySelector('.timesheet');
    const cardLeave = document.querySelector('.leave');
    const cardMed = document.querySelector('.medical');
    const cardReimburse = document.querySelector('.reimburse');
    const cardP_assignment = document.querySelector('.p_assignment');
    const approval_holidays = document.querySelector('.approval_holidays');

    approval_holidays.addEventListener('mouseover', function() {
        approval_holidays.style.cursor = 'pointer';
    });
    approval_holidays.addEventListener('mouseout', function() {
        approval_holidays.style.cursor = 'default';
    });
    cardTimesheet.addEventListener('mouseover', function() {
    cardTimesheet.style.cursor = 'pointer';
    });
    cardTimesheet.addEventListener('mouseout', function() {
    cardTimesheet.style.cursor = 'default';
    });
    cardLeave.addEventListener('mouseover', function() {
    cardLeave.style.cursor = 'pointer';
    });
    cardLeave.addEventListener('mouseout', function() {
    cardLeave.style.cursor = 'default';
    });
    cardMed.addEventListener('mouseover', function() {
    cardMed.style.cursor = 'pointer';
    });
    cardMed.addEventListener('mouseout', function() {
    cardMed.style.cursor = 'default';
    });
    cardReimburse.addEventListener('mouseover', function() {
    cardReimburse.style.cursor = 'pointer';
    });
    cardReimburse.addEventListener('mouseout', function() {
    cardReimburse.style.cursor = 'default';
    });

    cardP_assignment.addEventListener('mouseover', function() {
    cardP_assignment.style.cursor = 'pointer';
    });
    cardP_assignment.addEventListener('mouseout', function() {
    cardP_assignment.style.cursor = 'default';
    });
</script>
@endsection
