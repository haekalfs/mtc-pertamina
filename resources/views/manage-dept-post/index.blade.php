@extends('layouts.main')

@section('active-user')
active
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-users"></i> Manage Department & Position</h1>
        <p class="mb-4">Managing Institution Dept. & Position</a></p>
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
                    <h6 class="m-0 font-weight-bold" id="judul">List Department & Position</h6>
                    <div class="text-right">
                        <!-- Button for Register New Department -->
                        <a class="btn btn-primary btn-sm text-white mr-2" href="#" data-toggle="modal" data-target="#registerDepartmentModal"><i class="menu-icon fa fa-plus"></i> Register New Department</a>

                        <!-- Button for Register New Position -->
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#registerPositionModal"><i class="menu-icon fa fa-plus"></i> Register New Position</a>
                    </div>
                </div>
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="custom-nav-home-tab" data-toggle="tab" href="#custom-nav-home" role="tab" aria-controls="custom-nav-home" aria-selected="true">Department</a>
                        <a class="nav-item nav-link" id="custom-nav-profile-tab" data-toggle="tab" href="#custom-nav-profile" role="tab" aria-controls="custom-nav-profile" aria-selected="false">Position</a>
                    </div>
                </nav>
                <div class="card-body">
                    <div class="tab-content pl-3 pt-2" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="custom-nav-home" role="tabpanel" aria-labelledby="custom-nav-home-tab">
                            <table  id="dataPo" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Department Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $noDept = 1; @endphp
                                    @foreach ($departments as $item)
                                        <tr>
                                            <td>{{ $noDept++ }}</td>
                                            <td>{{ $item->department_name }}</td>
                                            <td class="text-center">
                                                <a href="#" class="btn btn-outline-secondary btn-sm btn-details mr-2"><i class="fa fa-info-circle"></i> Edit</a>
                                                <a href="#" class="btn btn-outline-danger btn-sm btn-details mr-2" onclick="event.preventDefault(); document.getElementById('delete-department-form-{{ $item->id }}').submit();"><i class="fa fa-ban"></i> Delete</a>
                                                <form id="delete-department-form-{{ $item->id }}" action="{{ route('department.destroy', $item->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="custom-nav-profile" role="tabpanel" aria-labelledby="custom-nav-profile-tab">
                            <table  id="dataPr" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Position Name</th>
                                        <th>Priority</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $noPost = 1; @endphp
                                    @foreach ($positions as $item)
                                        <tr>
                                            <td>{{ $noPost++ }}</td>
                                            <td>{{ $item->position_name }}</td>
                                            <td>{{ $item->position_level }}</td>
                                            <td class="text-center">
                                                <a href="#" class="btn btn-outline-secondary btn-sm btn-details mr-2"><i class="fa fa-info-circle"></i> Edit</a>
                                                <a href="#" class="btn btn-outline-danger btn-sm btn-details mr-2" onclick="event.preventDefault(); document.getElementById('delete-position-form-{{ $item->id }}').submit();"><i class="fa fa-ban"></i> Delete</a>
                                                <form id="delete-position-form-{{ $item->id }}" action="{{ route('position.destroy', $item->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
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
    </div>
</div>
<div class="modal fade" id="registerDepartmentModal" tabindex="-1" role="dialog" aria-labelledby="registerDepartmentModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header d-flex flex-row align-items-center justify-content-between border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="registerDepartmentModalLabel">Register New Department</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
            <form method="post" action="{{ route('department.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="department_name">Department Name</label>
                        <input type="text" class="form-control" id="department_name" name="department_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="registerPositionModal" tabindex="-1" role="dialog" aria-labelledby="registerPositionModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header d-flex flex-row align-items-center justify-content-between border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Register New Position</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
            <form method="post" action="{{ route('position.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="position_name">Position Name</label>
                        <input type="text" class="form-control" id="position_name" name="position_name" required>
                    </div>
                    <div class="form-group">
                        <label for="priority_level">Grade</label>
                        <select name="priority_level" id="priority_level" class="form-control form-control">
                            <option disabled selected>Please select</option>
                            <option value="1">Highest</option>
                            <option value="2">Low</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
