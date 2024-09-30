@extends('layouts.main')

@section('active-penlat')
active font-weight-bold
@endsection

@section('show-penlat')
show
@endsection

@section('penlat')
font-weight-bold
@endsection

@section('content')
<style>

.interface-list {
  list-style-type: none;
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.interface-tag {
  color: rgba(0,0,0,0.6);
  border: 0.5px solid rgba(162, 162, 162, 0.75);
  padding: 0.35rem 0.75rem;
  display: flex;
  gap: 0.75rem;
  border-radius: 10px; /* Added for rounded corners */
}

.interface-close {
  cursor: pointer;
  color: rgba(0,0,0,0.65);
}

.interface-close:active { color: rgba(0,0,0,0.5); }

.interface-footer {
  display: flex;
  align-items: start;
  justify-content: space-between;
}

.interface-remaining {
  margin-left: 1rem;
}

.interface-clear {
  color: white;
  background-color: black;
  border: 1.5px solid black;
  outline: none;
  padding: 0.5rem 1rem;
  cursor: pointer;
}

.interface-clear:active {
  border: 1.5px solid black;
  background-color: transparent;
  color: black;
}
</style>
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-list-alt"></i> List Pelatihan</h1>
        <p class="mb-4">List Pelatihan at MTC.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <a href="{{ route('penlat-import') }}" class="btn btn-sm btn-primary shadow-sm text-white"><i class="fa fa-file-text fa-sm"></i> Import Data</a>
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
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Penlat</a>
                    </div>
                </div>
                <div class="card-body zoom90">
                    <div class="row d-flex justify-content-start mb-4">
                        <div class="col-md-12">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="namaPenlat">Nama Pelatihan :</label>
                                        <select class="custom-select" id="namaPenlat" name="namaPenlat">
                                            <option value="-1" selected>Show All</option>
                                            @foreach($data as $penlat)
                                                <option value="{{ $penlat->id }}">{{ $penlat->description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="jenisPenlat">Jenis Penlat :</label>
                                        <select class="form-control" id="jenisPenlat" name="jenisPenlat">
                                            <option value="-1" selected>Show All</option>
                                            @foreach($data->unique('jenis_pelatihan') as $penlat)
                                                <option value="{{ $penlat->jenis_pelatihan }}">{{ $penlat->jenis_pelatihan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="stcw">STCW/Non :</label>
                                        <select name="stcw" class="form-control" id="stcw">
                                            <option value="-1">Show All</option>
                                            @foreach($data->unique('kategori_pelatihan') as $penlat)
                                                <option value="{{ $penlat->kategori_pelatihan }}">{{ $penlat->kategori_pelatihan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table id="penlatTables" class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Display</th>
                                <th>Nama Pelatihan</th>
                                <th>Aliases</th>
                                <th>Jenis Pelatihan</th>
                                <th>Kategori</th>
                                <th width="100px">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">
                    <span class="text-danger font-weight-bold">Notes</span>
                </div>
                <div class="card-body" style="background-color: rgb(247, 247, 247);">
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">General Guidelines</h6>
                    <ul class="ml-4">
                        <li>Penlat can be registered in two ways: through <span class="text-danger">Excel import</span> or <span class="text-danger">manual registration</span>.</li>
                        <li>Ensure that you select the appropriate method for batch registration based on your training data needs.</li>
                        <li>Users <span class="text-danger">should not delete Penlat carelessly</span> if they are already linked with other functions such as Training Reference, Penlat Requirements, or the Batch Program menu.</li>
                        <li>Penlat that are linked to Batches Menu will impact related data in the system, so be cautious before proceeding with deletion.</li>
                        <li>You can view detailed information for each batch by clicking on it, which will display more in-depth details, including the list of associated costs.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('penlat.store') }}">
                @csrf
                <div class="modal-body mr-2 ml-2">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="file-upload" style="cursor: pointer;">
                                <img id="image-preview" src="{{ asset('img/default-img.png') }}" style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="file-upload" type="file" name="display" style="display: none;" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Nama Pelatihan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="nama_program" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Jenis Pelatihan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="jenis_pelatihan" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Kategori Program :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="kategori_program" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Aliases :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class='interface-top'>
                                                    <input type='text' class='interface-input form-control' placeholder='Press enter or add a comma after each tag'>
                                                    <input type="hidden" name="alias" id="aliasInput">
                                                </div>
                                            </div>
                                        </div>

                                        <div class='interface-bottom mt-2'>
                                            <ul class='interface-list'></ul>
                                        </div>
                                    </div>
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
<div class="modal fade" id="editDataModal" tabindex="-1" role="dialog" aria-labelledby="editDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="editDataModalLabel">Edit Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body mr-2 ml-2">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="row no-gutters">
                        <div class="col-md-3 d-flex align-items-top justify-content-center text-center">
                            <label for="edit-file-upload" style="cursor: pointer;">
                                <img id="edit-image-preview" src="{{ asset('img/default-img.png') }}" style="height: 150px; width: 150px; border-radius: 15px; border: 2px solid #8d8d8d;" class="card-img shadow" alt="..."><br>
                                <small style="font-size: 10px;"><i><u>Click above to upload image!</u></i></small>
                            </label>
                            <input id="edit-file-upload" type="file" name="display" style="display: none;" accept="image/*" onchange="previewEditImage(event)">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Input fields for edit -->
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Nama Program :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" id="edit_nama_program" name="nama_program" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Jenis Pelatihan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" id="edit_jenis_pelatihan" name="jenis_pelatihan" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Kategori Pelatihan :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" id="edit_kategori_pelatihan" name="kategori_pelatihan" required>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Aliases :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <textarea type="text" class="form-control" id="edit_alias" name="alias" required></textarea>
                                                <small id="alias_help" class="help-block form-text text-danger d-none">
                                                    Only letters (A-Z), dashes (-), and commas (,) are allowed.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submitEditForm">Update Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('edit_alias').addEventListener('input', function () {
    const inputField = document.getElementById('edit_alias');
    const helpBlock = document.getElementById('alias_help');

    // Regular expression to allow any character except periods, and replace spaces with commas
    const pattern = /^[^.,]+(?:,[^.,]+)*(?:,[^.,]+)*$/;

    // Replace spaces with commas as the user types
    inputField.value = inputField.value.replace(/ /g, ',');

    // If the input does not match the pattern
    if (!pattern.test(inputField.value)) {
        helpBlock.classList.remove('d-none'); // Show the warning message
        // Remove invalid characters: periods, multiple commas
        inputField.value = inputField.value.replace(/[.]+|,{2,}/g, '');
    } else {
        helpBlock.classList.add('d-none'); // Hide the warning message
    }
});
// Intercept form submission to show SweetAlert confirmation
document.getElementById('submitEditForm').addEventListener('click', function (event) {
    event.preventDefault();  // Prevent form from submitting immediately

    swal({
        title: "Are you sure?",
        text: "Do you really want to update the data? Please be careful with the aliases. Ensure they are in the correct format (e.g., alias1, alias2) without duplicates, spaces, or incorrect characters. Any changes will directly affect integration between Infografis, Profit Menu, and other related systems.",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willUpdate) => {
        if (willUpdate) {
            // Submit the form if the user confirms
            document.getElementById('editForm').submit();
        } else {
            swal("Your changes are safe!");
        }
    });
});
const input = document.querySelector('.interface-input');
const bottom = document.querySelector('.interface-bottom');
const list = document.querySelector('.interface-list');
const remaining = document.querySelector('.interface-remaining');
const limit = 10;

const totalTags = () => list.querySelectorAll('.interface-tag').length;

function overLimit() {
  input.value = '';
  input.placeholder = totalTags() >= limit
    ? `You've reached the limit of 10 tags`
    : `Press enter or add a comma after each tag`;
}

function clearTags() {
  list.innerHTML = '';
  input.value = '';
  setTimeout(() => bottom.classList.remove('active'),500);
}

function updateAliasInput() {
  const tagNames = Array.from(list.querySelectorAll('.interface-tagName')).map(tag => tag.textContent);
  document.getElementById('aliasInput').value = tagNames.join(',');
}

function showTags() {
  if (!input.value.length) return;
  bottom.classList.add('active');
  list.innerHTML += createMarkup();
  input.value = '';
  updateAliasInput(); // Update hidden input
}

function closeTag(e) {
  e.target.parentElement.remove();
  totalTags() === 0 && clearTags();
  overLimit();
  updateAliasInput(); // Update hidden input
}

function isEnter(e) {
  if (e.keyCode === 13) {
    e.preventDefault(); // This will prevent the form submission
    showTags();
  }
}

function createMarkup() {
  const tags = input.value.split(/[, ]/);
  const markup = [];
  if (totalTags() >= limit || !tags[0].length) overLimit();
  for (let i = 0; i < limit - totalTags(); i++) {
    if (!tags[i]) continue;
    markup.push(`
      <li class='interface-tag'>
        <span class='interface-tagName'>${tags[i]}</span>
        <span class='interface-close'>x</span>
      </li>
    `);
  }
  return markup.join('');
}

function init(e) {
  e.target.matches('.interface-clear') && clearTags();
  e.target.matches('.interface-close') && closeTag(e);
  e.target.matches('.interface-btn') && showTags();
}

input.addEventListener('input',createMarkup,false);
input.addEventListener('keydown',isEnter,false);
document.addEventListener('click',init,false);

function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('image-preview');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

function previewEditImage(event) {
    const output = document.getElementById('edit-image-preview');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function () {
        URL.revokeObjectURL(output.src) // Free up memory
    }
}
</script>
<script>
$(document).ready(function() {
    var table = $('#penlatTables').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('penlat') }}",
            data: function (d) {
                d.namaPenlat = $('#namaPenlat').val();
                d.jenisPenlat = $('#jenisPenlat').val();
                d.stcw = $('#stcw').val();
            }
        },
        columns: [
            { data: 'display', name: 'display', orderable: false, searchable: false },
            { data: 'description', name: 'description' },
            { data: 'alias', name: 'alias' },
            { data: 'jenis_pelatihan', name: 'jenis_pelatihan' },
            { data: 'kategori_pelatihan', name: 'kategori_pelatihan' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
    order: [[1, 'asc']]
    });

    // Edit functionality
    $('#penlatTables').on('click', '.edit-tool', function () {
        let id = $(this).data('id');

        $.get('/penlat/' + id + '/edit', function (data) {
            $('#edit_id').val(data.id);
            $('#edit_nama_program').val(data.description);
            $('#edit_alias').val(data.alias);
            $('#edit_jenis_pelatihan').val(data.jenis_pelatihan);
            $('#edit_kategori_pelatihan').val(data.kategori_pelatihan);
            var imageUrl = data.image ? data.image : '{{ asset('img/default-img.png') }}';
            $('#edit-image-preview').attr('src', imageUrl);
            $('#editForm').attr('action', '/penlat-update/' + id);
            $('#editDataModal').modal('show');
        });
    });

    // Delete functionality
    $('#penlatTables').on('click', '.btn-outline-danger', function () {
        let id = $(this).data('id');

        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this record!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: '{{ route("delete.penlat", ":id") }}'.replace(':id', id),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(result) {
                        swal("Poof! Your record has been deleted!", {
                            icon: "success",
                        });
                        table.draw(); // Redraw the table
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = xhr.responseJSON && xhr.responseJSON.error
                            ? xhr.responseJSON.error
                            : "Oops! Something went wrong!";

                        swal("Error!", errorMessage, "error");
                    }
                });
            } else {
                swal("Your record is safe!");
            }
        });
    });

    // Redraw the table based on filter changes
    $('#namaPenlat, #jenisPenlat, #stcw').change(function() {
        table.draw();
    });
});
</script>
@endsection
