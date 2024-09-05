@extends('layouts.main')

@section('active-user')
active font-weight-bold
@endsection

@section('show-user')
show
@endsection

@section('manage-users')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-users"></i> Manage Users</h1>
        <p class="mb-4">Managing Users Account</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        {{-- <a class="btn btn-secondary btn-sm shadow-sm mr-2" href="/invoicing/list"><i class="fas fa-solid fa-backward fa-sm text-white-50"></i> Go Back</a> --}}
    </div>
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
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul">Data User</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="{{ route('register.users') }}"><i class="menu-icon fa fa-plus"></i> Register User</a>
                    </div>
                </div>
                <div class="card-body">
                    <table  id="docLetter" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width='200px'>Name</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Roles</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->users_detail->department->department_name }}</td>
                                <td class="zoom80">{{ $item->users_detail->position->position_name }}</td>
                                <td>
                                    @php
                                        $badgeColors = ['bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'bg-primary', 'bg-secondary'];
                                    @endphp
                                    @foreach ($item->role_id as $index => $usrRole)
                                        @if ($usrRole->role)
                                            <span class="badge text-white {{ $badgeColors[$index % count($badgeColors)] }}">{{ $usrRole->role->description }}</span>
                                        @endif
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    @if($item->users_detail->employment_status == 1)
                                    <span class="m-0 font-weight-bold text-secondary"><i class="fa fa-check text-primary"></i></span>
                                    @else
                                    <span class="m-0 font-weight-bold text-danger"><i class="fa fa-times-circle" style="color: #ff0000;"></i></span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('preview.user', $item->id) }}" class="btn btn-outline-secondary btn-sm btn-details mr-2"><i class="fa fa-info-circle"></i> Preview</a>
                                    {{-- <a data-toggle="modal" data-target="#editInvoiceModal" data-item-id="{{ $docs->id }}" class="btn btn-danger btn-sm"><i class="fas fa-fw fa-trash-alt"></i> Delete</a> --}}
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

@endsection
