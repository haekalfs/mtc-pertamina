@extends('layouts.main')

@section('active-access')
active
@endsection


@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-users"></i> Manage Access</h1>
        <p class="mb-4">Managing Access based on roles.</a></p>
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
                    <h6 class="m-0 font-weight-bold" id="judul">Access List</h6>
                    <div class="text-right">
                        {{-- <a class="btn btn-primary btn-sm text-white"><i class="fa fa-plus"></i> Assign Role to Access</a> --}}
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('assign.roles.to.page') }}">
                        @csrf
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="email">Page :</label>
                                        <select class="custom-select" id="inputPage" name="inputPage">
                                            <option selected disabled>Choose...</option>
                                            @foreach ($pages as $page)
                                                <option value="{{ $page->id }}">{{ $page->description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Roles :</label>
                                        <select class="custom-select" id="inputRole" name="inputRole">
                                            <option selected disabled>Choose...</option>
                                            @foreach($r_name as $rn)
                                                <option value="{{ $rn->id }}">{{ $rn ->description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex justify-content-center align-items-end">
                                    <div class="form-group">
                                        <button type="submit" id="insert-data-fingerprint" class="btn btn-primary">Insert</button>
                                    </div>
                                </div>
                                <div class="col-md-12"><br>
                                    <div class="alert alert-success alert-success-saving" role="alert" style="display: none;">
                                        Your entry has been saved successfully.
                                    </div>
                                    <div class="alert alert-danger" role="alert" style="display: none;">
                                        An error occurred while saving your entry. Please try again.
                                    </div>
                                    <div class="alert alert-danger alert-success-delete" role="alert" style="display: none;">
                                        Client has been deleted successfully.
                                    </div>
                                    <div class="alert alert-danger alert-danger-delete" role="alert" style="display: none;">
                                        An error occurred while saving your entry. Please try again.
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" id="docLetter" cellspacing="0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Page</th>
                                                    <th>Grant Access</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($access as $userAc)
                                                    <tr>
                                                        <td style="width: 5%;">{{ $userAc['id'] }}</td>
                                                        <td>{{ $userAc['page'] }}</td>
                                                        <td>{{ $userAc['grantTo'] }}</td>
                                                        <td class="text-center" style="width: 10%;">
                                                            <a class="btn btn-danger btn-sm" onclick='isconfirm();' href="/reset-access/{{ $userAc['page_id'] }}"><i class='fas fa-fw fa-undo-alt'></i> Reset</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
