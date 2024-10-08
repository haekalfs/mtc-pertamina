@extends('layouts.main')

@section('active-kpi')
active font-weight-bold
@endsection

@section('show-kpi')
show
@endsection

@section('manage-kpi')
font-weight-bold
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-users"></i> Manage Key Indicators</h1>
        <p class="mb-4">Managing Access based on roles.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <select class="form-control" id="yearSelected" name="yearSelected" required onchange="redirectToPage()" style="width: 100px;">
            @foreach (array_reverse($yearsBefore) as $year)
                <option value="{{ $year }}" {{ $year == $yearSelected ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
        </select>
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
                                <th>Goal</th>
                                <th>Target</th>
                                <th>Periode</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>@php $no = 1; @endphp
                            @foreach ($indicators as $item)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $item->indicator }}</td>
                                <td>{{ $item->goal }}</td>
                                <td>{{ number_format($item->target, 0, ',', '.') }}</td>
                                <td>{{ $item->periode }}</td>
                                <td class="text-center">
                                    <a href="{{ route('preview-kpi', ['id' => $item->id]) }}" class="btn btn-outline-secondary btn-sm mr-2"><i class="fa fa-edit"></i> Edit</a>
                                    <a href="#" class="btn btn-outline-danger btn-sm delete-kpi" data-id="{{ $item->id }}">
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
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('kpis.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="col-md-12 zoom90">
                        <div class="form-group">
                            <label for="kpi">KPI</label>
                            <input type="text" class="form-control" id="kpi" name="kpi" placeholder="Yearly Revenue..." required>
                        </div>
                        <div class="form-group">
                            <label for="target">Goal</label>
                            <input type="text" class="form-control" id="goal" name="goal" placeholder="Gain new subscribers..." required>
                        </div>
                        <div class="form-group">
                            <label for="target">Target</label>
                            <input type="text" class="form-control" id="target" name="target_display" oninput="formatAmount(this)" placeholder="Nominal Angka Tercapai..." required>
                            <input type="hidden" id="target_hidden" name="target"> <!-- Unformatted value for submission -->
                        </div>
                        <div class="form-group">
                            <label for="periode">Periode</label>
                            <select class="form-control" id="periode" name="periode" required>
                                @foreach (array_reverse($yearsBefore) as $year)
                                    <option value="{{ $year }}" {{ $year == $yearSelected ? 'selected' : '' }}>{{ $year }}</option>
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
function redirectToPage() {
    var selectedOption = document.getElementById("yearSelected").value;
    var url = "{{ url('/key-performance-indicators/manage-items') }}" + "/" + selectedOption;
    window.location.href = url; // Redirect to the desired page
}
$(document).ready(function() {
    // Trigger the deletion on clicking the button
    $('.delete-kpi').click(function(e) {
        e.preventDefault();

        // Get the KPI ID from the data attribute
        let id = $(this).data('id');

        // Show the confirmation modal using SweetAlert
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this KPI!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                // If the user confirms, send the Ajax request
                $.ajax({
                    url: '{{ route("kpi.destroy", ":id") }}'.replace(':id', id),  // Dynamic URL
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',  // CSRF token for security
                    },
                    success: function(response) {
                        // Show success message and redirect or refresh the page
                        swal("Success! The KPI has been deleted!", {
                            icon: "success",
                        }).then(() => {
                            location.reload();  // Reload the page to reflect changes
                        });
                    },
                    error: function(xhr) {
                        // Handle errors
                        swal("Error! Something went wrong.", {
                            icon: "error",
                        });
                    }
                });
            } else {
                // Handle cancel action
                swal("Your KPI is safe!", {
                    icon: "info",
                });
            }
        });
    });
});
function formatAmount(input) {
    // Remove non-numeric characters for display purposes
    let displayValue = input.value.replace(/[^0-9]/g, '');

    // Add thousands separator (dots) for display
    displayValue = displayValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    // Set the formatted value back to the input (for display)
    input.value = displayValue;

    // Also set the unformatted value in a hidden input for submission
    document.getElementById('target_hidden').value = input.value.replace(/\./g, '');
}
</script>
@endsection
