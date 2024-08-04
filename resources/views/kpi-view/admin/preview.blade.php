@extends('layouts.main')

@section('active-kpi')
active font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-users"></i> KPI - {{ $kpiItem->indicator }}</h1>
        <p class="mb-4">Managing pencapaian KPI.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a class="btn btn-secondary btn-sm shadow-sm mr-2" href="{{ route('manage-kpi') }}"><i class="fa fa-backward fa-sm text-white-50"></i> Go Back</a>
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
                    <h6 class="m-0 font-weight-bold" id="judul">List Pencapaian - {{ $kpiItem->indicator }}</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-icon fa fa-plus"></i> Insert Pencapaian</a>
                    </div>
                </div>
                <div class="card-body">
                    <table  id="docLetter" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Pencapaian</th>
                                <th>Target Tercapai</th>
                                <th>Periode Start</th>
                                <th>Periode End</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>@php $no = 1; @endphp
                            @foreach ($kpiItem->pencapaian as $item)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $item->pencapaian }}</td>
                                <td>{{ $item->score }} %</td>
                                <td>{{ $item->periode_start }}</td>
                                <td>{{ $item->periode_end }}</td>
                                <td class="text-center">
                                    <a href="{{ route('preview-kpi', ['id' => $item->id]) }}" class="btn btn-outline-secondary btn-sm mr-2"><i class="ti-eye"></i> Edit</a>
                                    <a href="#" class="btn btn-outline-danger btn-sm btn-details" onclick="confirmDelete({{ $item->id }});"><i class="fa fa-ban"></i> Delete</a>
                                    <form id="delete-pencapaian-kpi-{{ $item->id }}" action="{{ route('pencapaian.kpi.destroy', $item->id) }}" method="POST" style="display: none;">
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
                <h5 class="modal-title" id="inputDataModalLabel">Input Data Pencapaian KPI</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{ route('pencapaian.kpi.store', $kpiItem->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="col-md-12 zoom90">
                        <div class="form-group">
                            <label for="pencapaian">Pencapaian</label>
                            <input type="text" class="form-control" id="pencapaian" name="pencapaian" placeholder="Average Test Score..." required>
                        </div>
                        <div class="form-group">
                            <label for="score">Target Tercapai <small class="text-danger"><i>(in percentage)</i></small></label>
                            <input type="text" class="form-control" id="score" name="score" placeholder="85%" required>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="periode_start">Periode Start</label>
                                    <input type="date" class="form-control" id="periode_start" name="periode_start">
                                </div>
                                <div class="col-md-6">
                                    <label for="periode_end">Periode End</label>
                                    <input type="date" class="form-control" id="periode_end" name="periode_end">
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
                document.getElementById('delete-pencapaian-kpi-' + itemId).submit();
            }
        });
    }
</script>
@endsection
