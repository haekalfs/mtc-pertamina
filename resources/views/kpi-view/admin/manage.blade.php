@extends('layouts.main')

@section('active-kpi')
active font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-users"></i> Manage Key Indicators</h1>
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
                    <h6 class="m-0 font-weight-bold" id="judul">List Indicators</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-icon fa fa-plus"></i> Create KPI</a>
                    </div>
                </div>
                <div class="card-body">
                    <table  id="docLetter" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Indicator</th>
                                <th>Target</th>
                                <th>Periode Start</th>
                                <th>Periode End</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>@php $no = 1; @endphp
                            @foreach ($indicators as $item)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $item->indicator }}</td>
                                <td>{{ $item->target }} %</td>
                                <td>{{ $item->periode_start }}</td>
                                <td>{{ $item->periode_end }}</td>
                                <td class="text-center">
                                    <a href="{{ route('preview-kpi', ['id' => $item->id]) }}" class="btn btn-outline-secondary btn-sm mr-2"><i class="ti-eye"></i> Preview</a>
                                    <a href="#" class="btn btn-outline-danger btn-sm btn-details" onclick="confirmDelete({{ $item->id }});"><i class="fa fa-ban"></i> Delete</a>
                                    <form id="delete-kpi-{{ $item->id }}" action="{{ route('kpi.destroy', $item->id) }}" method="POST" style="display: none;">
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

<div class="modal fade" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{ route('kpis.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="col-md-12 zoom90">
                        <div class="form-group">
                            <label for="kpi">KPI</label>
                            <input type="text" class="form-control" id="kpi" name="kpi" placeholder="Average Test Score..." required>
                        </div>
                        <div class="form-group">
                            <label for="target">Target <small class="text-danger"><i>(in percentage)</i></small></label>
                            <input type="text" class="form-control" id="target" name="target" placeholder="85%" required>
                        </div>
                        <div class="form-group">
                            <label for="period">Period</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" class="form-control" id="period_start" name="period_start">
                                </div>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" id="period_end" name="period_end">
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
<script>
    function confirmDelete(itemId) {
        event.preventDefault();
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this KPI!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                document.getElementById('delete-kpi-' + itemId).submit();
            }
        });
    }
</script>
@endsection
